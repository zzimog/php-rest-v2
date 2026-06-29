<?php

namespace Core;

class Router
{
    protected string $base;
    protected string $mount;
    protected array $routes = [];
    protected ?\Closure $notFoundCallback = null;
    protected ?\Closure $notAllowedCallback = null;

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

    private static function uriTokenize(string $uri): array
    {
        $parsed = parse_url($uri, PHP_URL_PATH);
        $tokens = preg_split("/\//", $parsed, -1, PREG_SPLIT_NO_EMPTY);

        return $tokens;
    }

    private static function validMethod(array|string $methods): bool
    {
        $method = strtoupper($_SERVER["REQUEST_METHOD"]);

        return $methods === "any" ||
            $method === $methods ||
            (is_array($methods) && in_array($method, $methods));
    }

    public function routes(): array
    {
        return $this->routes;
    }

    public function mount(string $path): self
    {
        $this->mount = trim($path, "/");
        return $this;
    }

    public function unmount(): self
    {
        return $this->mount("");
    }

    public function notFound(\Closure $callback): self
    {
        $this->notFoundCallback = $callback;
        return $this;
    }

    public function notAllowed(\Closure $callback): self
    {
        $this->notAllowedCallback = $callback;
        return $this;
    }

    public function route(
        string $route,
        array|string $methods,
        callable $callback,
    ): self {
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

    public function run(): void
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
                if ($this->notAllowedCallback !== null) {
                    ($this->notAllowedCallback)();
                    return;
                }

                throw new \Error("Method Not Allowed", 405);
            }

            $callback = $__ROUTE__["callback"];
            $callback(...$args);

            return;
        }

        if ($this->notFoundCallback !== null) {
            ($this->notFoundCallback)();
            return;
        }

        throw new \Error("Not Found", 404);
    }
}
