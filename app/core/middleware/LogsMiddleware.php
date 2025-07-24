<?php

namespace App\core\middleware;

use App\core\DB;
use App\core\Redirect;

class LogsMiddleware {
    public function handle($request, $next) {

        $data = $request->all();
    
        if ($data[0] == "panel") {
            Redirect::to("error");
            exit;
        }

        return $next($request);
    }
}
