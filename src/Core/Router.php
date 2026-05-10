<?php
// src/Core/Router.php — Lightweight HTTP router for xAI CMS

// PHP 7.3 polyfills
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool {
        return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
    }
}
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}

class Router {
    private $routes = [];
    private $prefix = '';
    private $middleware = [];

    /**
     * Register a GET route.
     *
     * @param string $pattern  Exact path ('/login') or regex ('#^/user/scheme/(\d+)$#')
     * @param callable|array|string $handler  Closure, [Class, 'method'], or 'Class@method'
     */
    public function get(string $pattern, $handler): self {
        return $this->add('GET', $pattern, $handler);
    }

    /**
     * Register a POST route.
     */
    public function post(string $pattern, $handler): self {
        return $this->add('POST', $pattern, $handler);
    }

    /**
     * Register a route for any HTTP method.
     */
    public function any(string $pattern, $handler): self {
        return $this->add('*', $pattern, $handler);
    }

    /**
     * Group routes under a common prefix with optional middleware.
     *
     * @param string $prefix      URL prefix, e.g. '/admin'
     * @param callable $callback  Function(Router) that registers child routes
     * @param callable|null $middleware  Called before each child route executes
     */
    public function group(string $prefix, callable $callback, ?callable $middleware = null): self {
        $child = new self();
        $child->prefix = $this->prefix . $prefix;
        if ($middleware) {
            $child->middleware = array_merge($this->middleware, [$middleware]);
        } else {
            $child->middleware = $this->middleware;
        }
        $callback($child);
        $this->routes = array_merge($this->routes, $child->routes);
        return $this;
    }

    private function add(string $method, string $pattern, $handler): self {
        if ($this->prefix && str_starts_with($pattern, '#')) {
            // Regex pattern inside a group: inject prefix into the regex
            $prefixQuoted = preg_quote($this->prefix, '#');
            $pattern = preg_replace('/^(#\^?)/', '$1' . $prefixQuoted, $pattern);
        } else {
            $pattern = $this->prefix . $pattern;
        }
        $this->routes[] = [
            'method'  => $method,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $this->middleware,
        ];
        return $this;
    }

    /**
     * Dispatch the request.
     *
     * @param string $method  HTTP method (GET, POST, etc.)
     * @param string $uri     Request URI path
     * @return mixed          Whatever the handler returns, or false if no match
     */
    public function dispatch(string $method, string $uri) {
        // Normalize URI
        $uri = rawurldecode($uri);
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        foreach ($this->routes as $route) {
            // Method check
            if ($route['method'] !== '*' && strtoupper($method) !== strtoupper($route['method'])) {
                continue;
            }

            $pattern = $route['pattern'];
            $params = [];

            // Try regex match first
            if (str_starts_with($pattern, '#') && @preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match
                $params = $matches;
            }
            // Then exact match
            elseif ($uri === $pattern) {
                // No params
            }
            // Then prefix/sub-path match for hierarchical routes like /admin/articles/edit/123
            elseif (str_ends_with($pattern, '/*')) {
                $base = rtrim($pattern, '/*');
                if (str_starts_with($uri, $base)) {
                    $remaining = substr($uri, strlen($base));
                    $remaining = ltrim($remaining, '/');
                    $params = [$remaining];
                } else {
                    continue;
                }
            }
            else {
                continue;
            }

            // Run middleware chain
            $mwResults = [];
            foreach ($route['middleware'] as $mw) {
                $result = $mw($params);
                if ($result === false) {
                    return false; // Middleware rejected
                }
                if ($result !== null && $result !== true) {
                    $mwResults[] = $result;
                }
            }

            // Execute handler — prepend middleware results to route params
            $handler = $route['handler'];
            $allParams = array_merge($mwResults, $params);
            if ($handler instanceof Closure) {
                return $handler(...$allParams);
            }
            if (is_array($handler) && count($handler) === 2) {
                [$class, $method] = $handler;
                return (new $class())->$method(...$allParams);
            }
            if (is_string($handler) && str_contains($handler, '@')) {
                [$class, $method] = explode('@', $handler);
                return (new $class())->$method(...$allParams);
            }
        }

        return false;
    }
}
