<?php

/** Pest configuration shared by the package test suite. */

use Strukt\Contract\Http\RequestInterface;
use Strukt\Contract\Http\ResponseInterface;
use Strukt\Contract\MiddlewareInterface;

pest()->extend(Tests\TestCase::class)->in('Unit');

/**
 * Runs a middleware stack, innermost last, and resolves the final response.
 *
 * Mirrors how a host application wires `process()` links together, so the
 * tests exercise the same call shape a kernel would.
 */
function pipeline(RequestInterface $request, ResponseInterface $response, MiddlewareInterface ...$middleware): ResponseInterface
{
    $next = static fn (): ResponseInterface => $response;

    foreach (array_reverse($middleware) as $link) {
        $next = static fn (): ResponseInterface => $link->process($request, $response, $next);
    }

    return $next();
}
