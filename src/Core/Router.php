<?php

namespace Core;

class Router
{
    protected string $base;
    protected string $mount;
    protected array $routes = [];

    /**
     * Create new router
     * @param string $base Router base path
     */
    public function __construct(?string $base = "")
    {
        $this->base = trim($base, "/");
        $this->mount = "/";
        $this->routes = [];
    }

    public static function uriTokenize(string $uri): array
    {
        $parsed = parse_url($uri, PHP_URL_PATH);
        $tokens = preg_split("/\//", $parsed, -1, PREG_SPLIT_NO_EMPTY);

        return $tokens;
    }

    public static function validMethod(array|string $methods)
    {
        $method = strtoupper($_SERVER["REQUEST_METHOD"]);

        return $methods === "any" ||
            $method === $methods ||
            (is_array($methods) && in_array($method, $methods));
    }

    public function routes()
    {
        return $this->routes;
    }

    public function mount(string $path)
    {
        $this->mount = trim($path, "/");
        return $this;
    }

    public function unmount()
    {
        return $this->mount("");
    }

    public function route(
        string $route,
        array|string $methods,
        callable $callback,
    ): Router {
        $route = trim($route, "/");
        $route = "{$this->base}/{$this->mount}/$route";

        if (is_array($methods)) {
            $methods = array_map("strtoupper", $methods);
        } else {
            $methods = strtoupper($methods);
        }

        $this->routes[] = [
            "route" => $route,
            "methods" => $methods,
            "callback" => $callback,
        ];

        return $this;
    }

    public function run()
    {
        $request_uri = $_SERVER["REQUEST_URI"];
        $request_tokens = static::uriTokenize($request_uri);

        foreach ($this->routes as $__ROUTE__) {
            $route_methods = $__ROUTE__["methods"];
            $route_tokens = static::uriTokenize($__ROUTE__["route"]);

            if (count($request_tokens) !== count($route_tokens)) {
                continue;
            }

            $match = true;
            $args = [];

            foreach ($request_tokens as $i => $uri) {
                $route = $route_tokens[$i];

                if ($route === $uri || $route === "*") {
                    continue;
                }

                if ($route[0] === ":") {
                    $values = explode(":", $uri);

                    foreach ($values as $value) {
                        $args[] = $value;
                    }

                    continue;
                }

                $match = false;
                break;
            }

            if (!$match) {
                continue;
            }

            if (!static::validMethod($route_methods)) {
                throw new \Error("Method Not Allowed", 405);
            }

            $callback = $__ROUTE__["callback"];
            $callback(...$args);

            return;
        }

        throw new \Error("Not Found", 404);
    }
}
