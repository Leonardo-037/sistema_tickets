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

        if($_SESSION["usuario"][0]["idrol"] == 2){ // tecnico
            $artify->where("nombreTecnico", $_SESSION["usuario"][0]["nombre"], "=");
            $artify->fieldAttributes("hora_inicio", array("readonly" => "true"));
            $artify->fieldTypes("hora_inicio", "input");
            $artify->fieldAttributes("estado", array("value"=> "Iniciado", "readonly" => "true"));
            $artify->editFormFields(array("hora_inicio", "estado"));

        } else if($_SESSION["usuario"][0]["idrol"] == 3){ // asignador
            $artify->fieldAttributes("hora_asignacion", array("readonly" => "true"));
            $artify->fieldAttributes("estado", array("value"=> "Asignado", "readonly" => "true"));
            $artify->editFormFields(array("nombreTecnico","hora_asignacion", "estado", "fallas"));

            $action = "javascript:;";
            $text = '<button class="btn btn-light">Asignar</button>';
            $attr = array("title" => "Asignar", "target"=> "_blank");
            $artify->enqueueBtnActions("artify-actions", $action, "edit", $text, "", $attr);
        }

        //esto elimina de la grilla el ticket cuando está finalizado
        $artify->where("estado", "completado", "!=");
        //CAMBIAR EL NOMBRE A LA TABLA DE FUNCIONARIO A TICKETS
        $artify->tableHeading('Tickets');

        $artify->relatedData('sector_funcionario','sector','id_sector','nombre_sector');

        $artify->colRename("id_tickets", "ID");
        $artify->colRename("n_ticket", "N° de Ticket");
        $artify->colRename("nombreTecnico", "Asignado a");
        //ELIMINA EL BOTON AGREGAR
        $artify->setSettings("addbtn", false);

        /*$artify->fieldTypes("estado", "select");
        $artify->fieldDataBinding("estado", array(
            "Pendiente" => "Pendiente", 
            "Asignado" => "Asignado", 
            "Completado" => "Completado"
        ), "", "", "array");*/

        $artify->buttonHide("submitBtnSaveBack");
        $artify->buttonHide("cancel");

        $artify->crudTableCol(array(
            "id_tickets",
            "n_ticket",
            "nombre",
            "fecha",
            "nombreTecnico",
            "correo", 
            "area",
            "fallas",
            "sector_funcionario",
            "hora_asignacion",
            "hora_inicio",
            "hora_termino",
            "estado",
            "observaciones"
        ));

        $artify->fieldTypes("nombreTecnico", "select");
        $artify->fieldDataBinding("nombreTecnico", "tecnicos", "nombre as tecnicos", "nombre", "db");

        $artify->subQueryColumn("area", "SELECT nombre as area FROM area WHERE id_area = {area}");
        $artify->subQueryColumn("fallas", "SELECT nombre_fa as fallas FROM fallas WHERE id_falla = {fallas}");

        $artify->tableColFormatting("fecha", "date", array("format" =>"d/m/Y"));

        $artify->fieldCssClass("hora_inicio", array("hora_inicio"));
        $artify->fieldCssClass("hora_asignacion", array("hora_asignacion"));

        $artify->fieldHideLable("fallas");
        $artify->fieldDataAttr("fallas", array("style"=>"display:none"));

        $artify->setSettings("refresh", false);
        $artify->setSettings("editbtn", true);
        $artify->setSettings("actionbtn", true);
        $artify->setSettings("searchbox", true);
        $artify->setSettings("function_filter_and_search", true);
        $artify->setSettings("template", "template_crud_ticket");

        $artify->formDisplayInPopup();
        $artify->addCallback("before_update", [$this, "asignar_tickets"]);

        $render= $artify->dbTable("tickets")->render();
       
        $stencil = new ArtifyStencil();
        echo $stencil->render('crud_ticket', [
            'render' => $render
        ]);
    }

    public function asignar_tickets($data, $obj){
        if($_SESSION["usuario"][0]["idrol"] == 3){ 
            $nombreTecnico = $data["tickets"]["nombreTecnico"];
            $fallas = $data["tickets"]["fallas"];

            $queryfy = $obj->getQueryfyObj();
            $queryfy->where("nombre", $nombreTecnico);
            $result = $queryfy->select("tecnicos");

            $correo = $result[0]["correo"];

            $queryfy->where("id_falla", $fallas);
            $dbFallas = $queryfy->select("fallas");
            $nombreFalla = substr($dbFallas[0]["nombre_fa"], 0, 4);

            $emailBody = "Se le ha asignado el Ticket con número $nombreFalla";
            $subject = "Ticket Generado";
            $to = $correo;

            DB::PHPMail($to, $correo, $subject, $emailBody);

            $obj->setLangData("success", "Se le ha asignado el Ticket con número $nombreFalla");
        }

        return $data;
    }

    public function asignacion(){
        $artify = DB::ArtifyCrud();
        $artify->setSettings("function_filter_and_search", true);
        $artify->setSettings("searchbox", true);
        $artify->setSettings("editbtn", true);
        $artify->setSettings("delbtn", true);

        $artify->buttonHide("submitBtnSaveBack");

        $artify->fieldRenameLable("nombre_fa", "Nombre de la Falla");
        $artify->fieldRenameLable("id_area", "Área");

        $artify->colRename("id_falla", "ID");
        $artify->colRename("nombre_fa", "Nombre de la Falla");
        $artify->colRename("id_area", "Área");

        $artify->relatedData('id_area','area','id_area','nombre');
        $render = $artify->dbTable("fallas")->render();

        $stencil = new ArtifyStencil();
        echo $stencil->render('asignacion', [
            'render' => $render
        ]);
    }

    public function completar_tickets(){
        $request = new Request();

        if ($request->getMethod() === 'POST') {
            $param = $request->post('id');

            $artify = DB::ArtifyCrud(true);
            $artify->setPK("id_tickets");
            $artify->fieldAttributes("hora_termino", array("readonly" => "true"));
            $artify->fieldAttributes("estado", array("value"=> "Completado", "readonly" => "true"));
            $artify->formFields(array("hora_termino", "estado"));
            $artify->fieldCssClass("hora_termino", array("hora_termino"));
            $render = $artify->dbTable("tickets")->render("editform", array("id" => $param));

            HomeController::modal("Completar", "", $render);
        }
    }
}