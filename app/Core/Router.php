<?php

namespace App\Core;

class Router
{
    private static $routes = [];

    public static function add($method, $path, $callback)
    {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }

    public static function get($path, $callback)
    {
        self::add('GET', $path, $callback);
    }

    public static function post($path, $callback)
    {
        self::add('POST', $path, $callback);
    }

    public static function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach (self::$routes as $route) {
            // Simple string match or basic parameter logic could go here
            // For now, exact match or simple regex support

            // Convert /user/{id} to regex
            $pattern = "@^" . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_]+)', $route['path']) . "$@D";

            if ($method === $route['method'] && preg_match($pattern, $uri, $matches)) {
                // Remove numeric keys
                foreach ($matches as $key => $value) {
                    if (is_int($key))
                        unset($matches[$key]);
                }

                if (is_callable($route['callback'])) {
                    call_user_func($route['callback'], $matches);
                } elseif (is_array($route['callback'])) {
                    $controller = new $route['callback'][0]();
                    $method = $route['callback'][1];
                    call_user_func([$controller, $method], $matches);
                }
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
