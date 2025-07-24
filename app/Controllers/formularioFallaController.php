<?php

namespace App\Controllers;

use App\core\SessionManager;
use App\core\Token;
use App\core\Request;
use App\core\ArtifyStencil;
use App\core\Redirect;
use App\core\DB;
    
class formularioFallaController {
    public function index()
    {
        $artify = DB::ArtifyCrud();
        $artify-> addCallback("after_insert",[$this,"insertar_ticket"]); //esto es una devolucion de llamada osea hacer algo en respuesta
        $artify->fieldNotMandatory("nombreTecnico");
        $artify->fieldHideLable("nombreTecnico");
        $artify->fieldDataAttr("nombreTecnico", array("style"=>"display:none"));
        $artify->formFieldValue("estado", "Pendiente");//para darle el valor al campo estado como pendiente altiro
        $artify->fieldHideLable("estado");
        $artify->fieldDataAttr("estado", array("style"=>"display:none"));
        $artify->buttonHide("cancel"); // ocultar botón cancelar
        $artify->setLangData("save",'Generar Ticket'); // ocultar botón cancelar
        $artify->relatedData('fallas','fallas','id_falla','nombre_fa');
        $artify->fieldTypes('area','select');//esto es el tipo de campo select, check, radio , imagen, archivos, texto, input
        $artify->fieldDataBinding('area', array('Informática'=>'Informática','Servicios Generales'=>'Servicios Generales','Equipos Medicos'=>'Equipos Medicos'),'','','array');//entrega los datos para el select, arreglado
        $render = $artify->dbTable("funcionarios")->render("insertform");

        $stencil = new ArtifyStencil();
        echo $stencil->render('formularioFalla', [
            'render' =>$render
        ]);
    }
    
    public function insertar_ticket($data, $obj){
        $id = $data;
        $queryfy = $obj->getQueryfyObj();
        $queryfy->where("id", $id);
        $result = $queryfy->select("funcionarios");
        $correo= $result[0]["correo"];

        $emailBody = "Se ha generado el Ticket con número $id satisfactoriamente, a la brevedad un técnico tomará su solicitud";
        $subject = "Ticket Generado";
        $to = $correo;

        //$queryfy->send_email_public($to, 'daniel.telematico@gmail.com', null, $subject, $emailBody);
        DB::PHPMail($to, $correo, $subject, $emailBody);
        return $data;
    }
}
