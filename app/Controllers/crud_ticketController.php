<?php

namespace App\Controllers;

use App\core\SessionManager;
use App\core\Token;
use App\core\Request;
use App\core\ArtifyStencil;
use App\core\Redirect;
use App\core\DB;
        
class crud_ticketController {

    public $token;

	public function __construct()
	{
		SessionManager::startSession();
		$Sesusuario = SessionManager::get('usuario');
		if (isset($Sesusuario)) {
			if ($_SERVER['REQUEST_URI'] === "/home/modulos") {
				Redirect::to("modulos");
			}
		} else {
			Redirect::to("login");
		}
        $this->token = Token::generateFormToken('send_message');
	}
    
    public function index(){
        $artify = DB::ArtifyCrud();
        //esto elimina de la grilla el ticket cuando está finalizado
        $artify->where("estado", "completado", "!=");
        //CAMBIAR EL NOMBRE A LA TABLA DE FUNCIONARIO A TICKETS
        $artify->tableHeading('Tickets');

        $artify->colRename("id_funcionarios", "ID");
        $artify->colRename("nombreTecnico", "Asignado a");
        //ELIMINA EL BOTON AGREGAR
        $artify->setSettings("addbtn", false);

        //campo estado
        $artify->bulkCrudUpdate("estado", "select", array("data-cust-attr" =>"some-cust-val"), array(
            array(
                "Pendiente",
                "Pendiente"
            ),
            array(
                "Asignado",
                "Asignado"
            ),
            array(
                "Completado",
                "Completado"
            ))
        );
        $query = DB::Queryfy();
        //para hacer un select dentro del crud llamando a los datos de otra tabla
        $rows = $query->select("tecnicos");  // o con where, orden, etc

        $options = [];
        foreach ($rows as $row) {
            $options[] = [$row['nombre'], $row['nombre']];
        }

        $artify->crudTableCol(array(
            "id_funcionarios",
            "nombre",
            "nombreTecnico",
            "correo", 
            "area",
            "fallas",
            "estado"
        ));

        $artify->subQueryColumn("area", "SELECT nombre as area FROM area WHERE id_area = {area}");
        $artify->subQueryColumn("fallas", "SELECT nombre_fa as fallas FROM fallas WHERE id_falla = {fallas}");

        $artify->bulkCrudUpdate("nombreTecnico", "select", array("data-cust-attr" => "some-cust-val"), $options);
        $artify->setSettings("editbtn", false);
        $artify->setSettings("actionbtn", false);
        $artify->setSettings("searchbox", true);
        $artify->setSettings("function_filter_and_search", true);
        $render= $artify->dbTable("funcionarios")->render();
        // Implementa la lógica del controlador aquí
        $stencil = new ArtifyStencil();
        echo $stencil->render('crud_ticket', [
            'render' => $render
        ]);
    }
}