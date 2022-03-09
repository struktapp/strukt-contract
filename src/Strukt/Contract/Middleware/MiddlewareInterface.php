<?php

namespace Strukt\Contract\Middleware;

// use Strukt\Http\Request;
use Strukt\Contract\Http\RequestInterface;
use Strukt\Contract\Http\ResponseInterface;

interface MiddlewareInterface{

 	// public function __invoke(Request $request, 
 	//								ResponseInterface $response, 
 	//								callable $next);

 	public function __invoke(RequestInterface $request, 
 								ResponseInterface $response, 
 								callable $next);
}