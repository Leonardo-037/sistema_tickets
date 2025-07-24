<?php

        namespace App\Controllers;

        use App\core\SessionManager;
        use App\core\Token;
        use App\core\Request;
        use App\core\DB;
        use App\core\Redirect;
        use App\core\ArtifyStencil;

        class nombre_tablaController
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
                $settings["script_url"] = $_ENV['URL_ArtifyCrud'];
                $_ENV["url_artify"] = "artify/artifycrud.php";
                $settings["url_artify"] = $_ENV['url_artify'];
                $settings["downloadURL"] = $_ENV['DOWNLOAD_URL'];
                $settings["hostname"] = $_ENV['DB_HOST'];
                $settings["database"] = $_ENV['DB_NAME'];
                $settings["username"] = $_ENV['DB_USER'];
                $settings["password"] = $_ENV['DB_PASS'];
                $settings["dbtype"] = $_ENV['DB_TYPE'];
                $settings["characterset"] = $_ENV['CHARACTER_SET'];

                $autoSuggestion = false;
                $artify = DB::ArtifyCrud(false, "", "", $autoSuggestion, $settings);
                $artify->setSettings("pagination", false);
                $artify->setSettings("searchbox", false);
                $artify->setSettings("deleteMultipleBtn", false);
                $artify->setSettings("recordsPerPageDropdown", false);
                $artify->setSettings("recordsPerPageDropdown", false);
                $artify->setSettings("totalRecordsInfo", false);
                $artify->setSettings("addbtn", false);
                $artify->setSettings("editbtn", false);
                $artify->setSettings("viewbtn", false);
                $artify->setSettings("delbtn", false);
                $artify->setSettings("actionbtn", false);
                $artify->setSettings("checkboxCol", false);
                $artify->setSettings("numberCol", false);
                $artify->setSettings("printBtn", false);
                $artify->setSettings("pdfBtn", false);
                $artify->setSettings("csvBtn", false);
                $artify->setSettings("excelBtn", false);
                $render = $artify->dbTable('nombre_tabla')->render();

                $stencil = new ArtifyStencil();
                echo $stencil->render('picornio', [
                    'render' => $render
                ]);
            }
        }