<?php

namespace App\Controllers;

use App\core\ArtifyStencil;

class ErrorController
{
	public function __construct()
	{
	}

	public function index()
	{
		$stencil = new ArtifyStencil();
		echo $stencil->render('error');
	}
}
