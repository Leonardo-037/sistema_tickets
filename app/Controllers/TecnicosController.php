<?php

namespace App\Controllers;

use App\core\SessionManager;
use App\core\Token;
use App\core\Request;
use App\core\ArtifyStencil;
use App\core\Redirect;
use App\core\DB;

class TecnicosController
{
    public $token;

    public function __construct()
    {
        SessionManager::startSession();
        $Sesusuario = SessionManager::get('usuario');
        if (!isset($Sesusuario)) {
            Redirect::to('login/index');
        }
        $this->token = Token::generateFormToken('send_message');
    }

    public function index()
    {
        $artify = DB::ArtifyCrud();

        $artify->colRename("id_tecnicos", "ID");

        $artify->relatedData('area','area','id_area','nombre');

        $artify->setSettings("function_filter_and_search", true);
        $artify->setSettings("searchbox", true);
        $artify->setSettings("editbtn", true);
        $artify->setSettings("delbtn", true);
        $artify->buttonHide("submitBtnSaveBack");
        $render = $artify->dbTable("tecnicos")->render();

        $stencil = new ArtifyStencil();
        echo $stencil->render('tecnicos', [
            'render' => $render
        ]);
    }
}