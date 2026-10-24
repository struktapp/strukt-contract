<?php

namespace Strukt\Contract;

/**
 * Describes an HTTP middleware component.
 *
 * A middleware receives the current request and response, performs its work,
 * then hands control to the next middleware. The final link of the chain
 * resolves the response. Return the response rather than mutating it only, so
 * a short-circuiting middleware can stop the chain entirely.
 */
interface MiddlewareInterface
{
    /**
     * Handles a request as one link in the middleware pipeline.
     *
     * @param \Strukt\Contract\Http\RequestInterface $request Incoming request.
     * @param \Strukt\Contract\Http\ResponseInterface $response Response under construction.
     * @param callable $next Invokes the remaining pipeline and returns the response.
     * @return \Strukt\Contract\Http\ResponseInterface Response for the caller.
     */
    public function process(Http\RequestInterface $request, Http\ResponseInterface $response, callable $next): Http\ResponseInterface;
}
