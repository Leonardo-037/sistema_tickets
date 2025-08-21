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
            $artify->where("nombreTecnico", "null", "!=", "AND");
            $artify->where("estado", "completado", "!=", "AND");
            $artify->where("nombreTecnico", $_SESSION["usuario"][0]["nombre"]);
            $artify->fieldAttributes("hora_inicio", array("readonly" => "true"));
            $artify->fieldTypes("hora_inicio", "input");
            $artify->fieldHideLable("estado");
            $artify->fieldAttributes("estado", array("value"=> "Iniciado", "style" => "display: none"));
            $artify->editFormFields(array("hora_inicio", "estado"));

        } else if($_SESSION["usuario"][0]["idrol"] == 3){ // asignador
            $artify->fieldAttributes("hora_asignacion", array("readonly" => "true"));
            $artify->fieldHideLable("estado");
            $artify->fieldAttributes("estado", array("value"=> "Asignado", "style" => "display: none"));
            $artify->fieldHideLable("n_ticket");
            $artify->fieldAttributes("n_ticket", array("style" => "display: none"));
            $artify->fieldRenameLable("nombreTecnico", "Nombre Técnico");
            $artify->editFormFields(array("nombreTecnico","hora_asignacion", "estado", "fallas", "n_ticket"));

            $artify->where("nombreTecnico", "", "=", "AND");
            $artify->where("estado", "Pendiente");

            $action = "javascript:;";
            $text = '<button class="btn btn-light">Asignar</button>';
            $attr = array("title" => "Asignar", "target"=> "_blank");
            $artify->enqueueBtnActions("artify-actions", $action, "edit", $text, "", $attr);
        }

        $artify->tableHeading('Tickets');

        $artify->relatedData('sector_funcionario','sector','id_sector','nombre_sector');

        $artify->colRename("id_tickets", "ID");
        $artify->colRename("n_ticket", "N° de Ticket");
        $artify->colRename("nombreTecnico", "Asignado a");
      
        $artify->setSettings("addbtn", false);

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
            $n_ticket = $data["tickets"]["n_ticket"];

            $queryfy = $obj->getQueryfyObj();
            $queryfy->where("nombre", $nombreTecnico);
            $result = $queryfy->select("tecnicos");

            $correo = $result[0]["correo"];

            $emailBody = "Se le ha Asignado un Ticket con N° de Ticket". " " . $n_ticket;
            $subject = "Ticket Generado";
            $to = $correo;

            DB::PHPMail($to, $correo, $subject, $emailBody);

            $obj->setLangData("success", "Ticket Asignado con éxito");
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
            $artify->fieldHideLable("estado");
            $artify->fieldAttributes("estado", array("value"=> "Completado", "style" => "display: none"));
            $artify->formFields(array("hora_termino", "estado", "observaciones"));
            $artify->fieldCssClass("hora_termino", array("hora_termino"));
            $artify->setLangData("success", "Ticket Completado con éxito");
            $artify->fieldNotMandatory("observaciones");
            $artify->addCallback("before_update", [$this, "edicion_completar_tickets"]);
            $render = $artify->dbTable("tickets")->render("editform", array("id" => $param));

            HomeController::modal("Completar", "", $render);
        }
    }

    public function edicion_completar_tickets($data, $obj){
        $observaciones = $data["tickets"]["observaciones"];

        if(empty($observaciones)){
            $data["tickets"]["observaciones"] = "Sin Observaciones";
        }
        return $data;
    }

    public function tickets_completados(){
        $artify = DB::ArtifyCrud();
        $artify->tableHeading("Mis Tickets Completados");
        $artify->where("nombreTecnico", $_SESSION["usuario"][0]["nombre"]);
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
        $artify->setSettings("refresh", false);
        $artify->setSettings("addbtn", false);
        $artify->setSettings("editbtn", false);
        $artify->setSettings("actionbtn", false);
        $artify->setSettings("searchbox", true);
        $artify->setSettings("function_filter_and_search", true);
        $artify->colRename("id_tickets", "ID");
        $artify->colRename("n_ticket", "N° de Ticket");
        $artify->colRename("nombreTecnico", "Asignado a");
        $artify->relatedData('sector_funcionario','sector','id_sector','nombre_sector');
        $artify->subQueryColumn("area", "SELECT nombre as area FROM area WHERE id_area = {area}");
        $artify->subQueryColumn("fallas", "SELECT nombre_fa as fallas FROM fallas WHERE id_falla = {fallas}");
        $artify->where("estado", "completado");
        $render = $artify->dbTable("tickets")->render();

        $stencil = new ArtifyStencil();
        echo $stencil->render('tickets_completados', [
            'render' => $render
        ]);
    }
}