<?php

namespace App\Controllers;

use App\core\SessionManager;
use App\core\Token;
use App\core\Request;
use App\core\ArtifyStencil;
use App\core\Redirect;
use App\core\DB;

class SectorController
{
    public function index()
    {
        $artify = DB::ArtifyCrud();
        $artify->setSettings("function_filter_and_search", true);
        $artify->setSettings("searchbox", true);
        $artify->setSettings("editbtn", true);
        $artify->setSettings("delbtn", true);
        $artify->buttonHide("submitBtnSaveBack");
        $render = $artify->dbTable("sector")->render();

        $stencil = new ArtifyStencil();
        echo $stencil->render('sector', [
            'render' => $render
        ]);
    }
}