<?php

namespace App\Controllers;

use App\core\Token;
use App\core\Request;
use App\core\ArtifyStencil;
use App\core\Redirect;
use App\core\DB;

class formularioFallaController {
    public function index()
    {
        date_default_timezone_set("America/Santiago");
        $fecha = date("Y-m-d");

        $artify = DB::ArtifyCrud();
        $artify->addPlugin("select2");
        $artify->addCallback("after_insert", [$this, "insertar_ticket"]);
        $artify->fieldNotMandatory("nombreTecnico");
        $artify->fieldHideLable("nombreTecnico");
        $artify->fieldDataAttr("nombreTecnico", array("style"=>"display:none"));
        $artify->formFieldValue("estado", "Pendiente");
        $artify->formFieldValue("fecha", $fecha);
        $artify->fieldHideLable("estado");
        $artify->fieldDataAttr("estado", array("style"=>"display:none"));
        $artify->fieldTypes("correo", "email");

        $artify->fieldHideLable("fecha");
        $artify->fieldDataAttr("fecha", array("style"=>"display:none"));

        $artify->buttonHide("cancel");
        $artify->setLangData("save",'Generar Ticket');

        $artify->fieldRenameLable("nombre", "Nombre Completo del Funcionario");
        $artify->fieldRenameLable("area", "Unidad");
        $artify->fieldRenameLable("correo", "Correo del Funcionario");
        $artify->fieldRenameLable("sector_funcionario", "Sector Funcionario");

        $artify->relatedData('sector_funcionario','sector','id_sector','nombre_sector');

        $artify->fieldTypes('fallas','select');
        $artify->fieldDataBinding('fallas','fallas','id_falla','nombre_fa', 'db');

        $artify->fieldTypes('area','select');
        $artify->fieldDataBinding("area", "area", "id_area", "nombre", "db");

        $artify->fieldDependent("fallas", "area", "id_area");

        $artify->fieldCssClass("fallas", array("fallas"));

        $artify->fieldCssClass("nombre", array("nombre"));
        $artify->fieldCssClass("correo", array("correo"));
        $artify->fieldCssClass("area", array("area"));
        $artify->fieldCssClass("fallas", array("fallas"));
        $artify->fieldCssClass("sector_funcionario", array("sector_funcionario"));

        $artify->formFields(array("nombre","fecha","correo", "area", "fallas", "sector_funcionario", "estado"));
        
        $render = $artify->dbTable("tickets")->render("insertform");
        $chosen = $artify->loadPluginJsCode("select2",".sector_funcionario, .fallas");

        $stencil = new ArtifyStencil();
        echo $stencil->render('formularioFalla', [
            'render' =>$render,
            'chosen' => $chosen
        ]);
    }

    public function insertar_ticket($data, $obj){
        $id = $data;
        $queryfy = $obj->getQueryfyObj();

        // Obtener datos del ticket
        $queryfy->where("id_tickets", $id);
        $result = $queryfy->select("tickets");
        $correo = $result[0]["correo"];
        $area = $result[0]["area"];
        $fallas = $result[0]["fallas"];

        // Obtener nombre de área y falla
        $queryfy->where("id_area", $area);
        $dbAreas = $queryfy->select("area");
        $nombreArea = substr($dbAreas[0]["nombre"], 0, 4);

        $queryfy->where("id_falla", $fallas);
        $dbFallas = $queryfy->select("fallas");
        $nombreFalla = substr($dbFallas[0]["nombre_fa"], 0, 4);

        // Construir prefijo del ticket
        $prefijoTicket = $nombreArea . $nombreFalla;

        // Buscar tickets existentes que comiencen con este prefijo
        $queryfy->where("n_ticket", '%'. $prefijoTicket . '%', "LIKE");
        $ticketsExistentes = $queryfy->select("tickets");

        // Determinar siguiente número
        $siguienteNumero = 1;
        if(!empty($ticketsExistentes)){
            $numeros = [];
            foreach($ticketsExistentes as $t){
                $n = str_replace($prefijoTicket, '', $t['n_ticket']);
                if(is_numeric($n)){
                    $numeros[] = (int)$n;
                }
            }
            if(!empty($numeros)){
                $siguienteNumero = max($numeros) + 1;
            }
        }
        $numeroTicketFormateado = str_pad($siguienteNumero, 2, '0', STR_PAD_LEFT);

        $n_ticket_final = $prefijoTicket . $numeroTicketFormateado;

        // Actualizar ticket
        $queryfy->where("correo", $correo);
        $queryfy->update("tickets", array("n_ticket" => $n_ticket_final));

        // Enviar correo
        $emailBody = "Se ha generado el Ticket con número $n_ticket_final satisfactoriamente, a la brevedad un técnico tomará su solicitud";
        $subject = "Ticket Generado";
        $to = $correo;

        DB::PHPMail($to, $correo, $subject, $emailBody);

        $obj->setLangData("success", "Se ha generado el Ticket con número $n_ticket_final satisfactoriamente, a la brevedad un técnico tomará su solicitud");
        return $data;
    }

    public function registrar_funcionarios(){
        $artify = DB::ArtifyCrud();
        $artify->buttonHide("cancel");
        $artify->setLangData("save",'Registrar');
        $artify->addCallback("before_insert", [$this, "agregar_funcionarios"]);
        $render = $artify->dbTable("funcionarios")->render("insertform");

        $stencil = new ArtifyStencil();
        echo $stencil->render('registrar_funcionarios', [
            'render' =>$render
        ]);
    }

    public function agregar_funcionarios($data, $obj){
        return $data;
    }
}
