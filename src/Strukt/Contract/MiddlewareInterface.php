<?php

namespace Strukt\Contract;

use Strukt\Contract\Http\RequestInterface;
use Strukt\Contract\Http\ResponseInterface;

interface MiddlewareInterface{

 	public function __invoke(RequestInterface $request, 
 								ResponseInterface $response, 
 								callable $next);
}