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
        $artify-> addCallback("after_insert",[$this,"insertar_ticket"]);
        $artify->fieldNotMandatory("nombreTecnico");
        $artify->fieldHideLable("nombreTecnico");
        $artify->fieldDataAttr("nombreTecnico", array("style"=>"display:none"));
        $artify->formFieldValue("estado", "Pendiente");
        $artify->fieldHideLable("estado");
        $artify->fieldDataAttr("estado", array("style"=>"display:none"));
        $artify->buttonHide("cancel");
        $artify->setLangData("save",'Generar Ticket');

        $artify->fieldTypes('fallas','select');
        $artify->fieldDataBinding('fallas','fallas','id_falla','nombre_fa', 'db');

        $artify->fieldTypes('area','select');
        $artify->fieldDataBinding("area", "area", "id_area", "nombre", "db");

        $artify->fieldDependent("fallas", "area", "id_area"); // campo que depende la carga de los datos
        
        $render = $artify->dbTable("funcionarios")->render("insertform");

        $stencil = new ArtifyStencil();
        echo $stencil->render('formularioFalla', [
            'render' =>$render
        ]);
    }
    
    public function insertar_ticket($data, $obj){
        $id = $data;
        $queryfy = $obj->getQueryfyObj();
        $queryfy->where("id_funcionarios", $id);
        $result = $queryfy->select("funcionarios");
        $correo= $result[0]["correo"];

        $emailBody = "Se ha generado el Ticket con número $id satisfactoriamente, a la brevedad un técnico tomará su solicitud";
        $subject = "Ticket Generado";
        $to = $correo;

        DB::PHPMail($to, $correo, $subject, $emailBody);
        return $data;
    }
}
