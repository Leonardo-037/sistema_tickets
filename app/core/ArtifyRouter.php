<?php

namespace App\core;

class ArtifyRouter {
    private $routes = [];
    private $namedRoutes = [];
    private $currentRouteKey;
    private $middlewares = [];

    public function middleware($middlewares) {
        if (!is_array($middlewares)) {
            $middlewares = [$middlewares];
        }
        $this->middlewares[$this->currentRouteKey] = $middlewares;
        return $this;
    }

    public function get($uri, $action) {
        return $this->addRoute('GET', $uri, $action);
    }

    public function post($uri, $action) {
        return $this->addRoute('POST', $uri, $action);
    }

    public function put($uri, $action) {
        return $this->addRoute('PUT', $uri, $action);
    }

    public function delete($uri, $action) {
        return $this->addRoute('DELETE', $uri, $action);
    }

    private function addRoute($method, $uri, $action) {
        if (!in_array($method, ['GET', 'POST'])) {
            throw new \InvalidArgumentException("Invalid HTTP method: $method");
        }
        
        $routeKey = count($this->routes); // Guarda el índice de la ruta actual
        $this->routes[$routeKey] = [
            'method' => $method,
            'uri' => $this->formatUri($uri),
            'action' => $action
        ];
        
        $this->currentRouteKey = $routeKey; // Establece la ruta actual
        return $this; // Permite encadenar métodos
    }

    // Método para asignar nombre a la última ruta agregada
    public function name($name) {
        if ($this->currentRouteKey === null) {
            throw new \LogicException("No route available to name.");
        }

        $this->namedRoutes[$name] = $this->routes[$this->currentRouteKey]['uri'];
        return $this; // Permite seguir encadenando si es necesario
    }

    // Método para obtener la URL de una ruta por su nombre
    public function url($name, $params = []) {
        $uri = $this->namedRoutes[$name];

        if (preg_match_all('/\{(\w+)\}/', $uri, $matches)) {
            foreach ($matches[1] as $param) {
                if (!isset($params[$param])) {
                    throw new \InvalidArgumentException("Missing parameter '$param' for route '$name'.");
                }
                $uri = str_replace("{{$param}}", $params[$param], $uri);
            }
        }

        return '/' . $uri;
    }

    private function formatUri($uri) {
        return trim($uri, '/');
    }

    public function dispatch(Request $request) {
        $requestMethod = $request->getMethod();
        $requestUri = $this->formatUri(str_replace($_ENV["BASE_URL"], '', $_SERVER['REQUEST_URI']));
    
        foreach ($this->routes as $key => $route) { 
            if ($route['method'] === $requestMethod && preg_match($this->convertToRegex($route['uri']), $requestUri, $matches)) {
                 $this->currentRouteKey = $key;
                // Combina parámetros de ruta y solicitud en un solo array
                $params = array_merge($matches, $request->all());

                // Evita duplicados: solo añade `paramX` si no hay una clave de nombre equivalente
                foreach ($matches as $key => $value) {
                    if (is_int($key) && $key > 0 && !isset($params["param{$key}"])) {
                        $params["param{$key}"] = $value;
                    }

                    if(isset($params["Restp"])){
                        if (count($matches) === 3) {
                            // Ruta con {tabla} y {token}
                            $params['tabla'] = $matches[1];
                            $params['token'] = $matches[2];
                        } elseif (count($matches) === 4) {
                            // Ruta con {tabla}, {filtro_url} y {token}
                            $params['tabla'] = $matches[1];
                            $params['filtro_url'] = $matches[2];
                            $params['token'] = $matches[3];
                        }
            
                        // Eliminar índices numéricos innecesarios
                        unset($params[0], $params[1], $params[2], $params[3], $params["param1"], $params["param2"], $params["param3"]);
                    }
                }

                // Aquí crea un nuevo objeto Request con los parámetros
                $newRequest = new Request($requestMethod, $_SERVER['REQUEST_URI']);
                $newRequest->initialize($params);
    
                // Ejecuta la acción del controlador
                $this->runMiddlewareStack($route, $newRequest);
                return;
            }
        }
    
        // Manejo de error 404 si no se encuentra ninguna ruta
        http_response_code(404);
        Redirect::to("error");
    }            

    private function convertToRegex($routeUri) {
        $routeUri = preg_replace('/\{(\w+)\}/', '([^/]+)', $routeUri);
        return '#^' . $routeUri . '$#';
    }

    private function executeAction($action, Request $request, $route = []) {
        list($controller, $method) = explode('@', $action);
        $controller = "App\\Controllers\\$controller";

        if (!class_exists($controller) || !method_exists($controller, $method)) {
            http_response_code(404);
            Redirect::to("error");
            return;
        }

        if (isset($route['middleware'])) {
            $middlewareClass = $route['middleware'];
            $middleware = new $middlewareClass();

            // Ejecutamos el middleware
            $middleware->handle($request, function ($req) use ($controller, $method) {
                $controllerInstance = new $controller();
                $controllerInstance->$method($req);
            });
        } else {
            // Sin middleware, ejecutamos directamente el controlador
            $controllerInstance = new $controller();
            $controllerInstance->$method($request);
        }
    }

    private function runMiddlewareStack($route, Request $request) {
        $middlewares = $this->middlewares[$this->currentRouteKey] ?? [];

        $action = function ($req) use ($route) {
            $this->executeAction($route['action'], $req);
        };

        $pipeline = array_reduce(
            array_reverse($middlewares),
            function ($next, $middlewareClass) {
                return function ($request) use ($middlewareClass, $next) {
                    // Agrega namespace si no está presente
                    if (!class_exists($middlewareClass) && strpos($middlewareClass, '\\') === false) {
                        $middlewareClass = 'App\\core\\middleware\\' . $middlewareClass;
                    }

                    $middleware = new $middlewareClass();
                    return $middleware->handle($request, $next);
                };
            },
            $action
        );

        $pipeline($request);
    }
}
