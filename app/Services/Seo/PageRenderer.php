<?php

namespace App\Services\Seo;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Facade;
use Throwable;

/**
 * Renders a public page to HTML in-process, so the auditor sees exactly what
 * a crawler gets without an HTTP request back to the site (which shared
 * hosting often blocks). Only routing, model binding and the view run: no
 * session/cookie middleware, so an admin's session is never touched.
 */
class PageRenderer
{
    public function __construct(private Router $router) {}

    public function render(string $path): ?string
    {
        $app = app();
        $original = $app->bound('request') ? $app->make('request') : null;

        $request = Request::create($path, 'GET');
        $request->headers->set('Accept', 'text/html');

        try {
            $route = $this->router->getRoutes()->match($request);
            $request->setRouteResolver(fn () => $route);

            $app->instance('request', $request);
            Facade::clearResolvedInstance('request');

            $route->bind($request);
            $this->router->substituteBindings($route);
            $this->router->substituteImplicitBindings($route);

            $response = Router::toResponse($request, $route->run());

            return $response->getStatusCode() === 200 ? (string) $response->getContent() : null;
        } catch (Throwable $e) {
            report($e);

            return null;
        } finally {
            if ($original) {
                $app->instance('request', $original);
                Facade::clearResolvedInstance('request');
            }
        }
    }
}
