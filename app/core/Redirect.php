<?php

namespace App\core;

use App\Controllers\HomeController;

class Redirect
{
	public static function to($url)
	{
		header("Location: " . $_ENV["BASE_URL"] . $url);
		return $url;
	}

	public static function areaProtegida($nombreMetodo, $redirige) {
        $id_sesion_usuario = isset($_SESSION["usuario"][0]["id"]) ? $_SESSION["usuario"][0]["id"] : null;
        $menu = HomeController::obtener_menu_por_id_usuario($id_sesion_usuario);

        foreach ($menu as $item) {
            if ($item["nombre_menu"] === $nombreMetodo && $item["visibilidad_menu"] === "Ocultar") {
                Redirect::to($redirige);
                exit;
            }
        }
    }
}