<?php

namespace Strukt\Contract;

use Strukt\Contract\Http\RequestInterface;
use Strukt\Contract\Http\ResponseInterface;
use Strukt\Http\Response\Plain

/**
 * @author Moderator <pitsolu@gmail.com>
 */
interface MiddlewareInterface{

	/**
	 * @param \Strukt\Contract\Http\RequestInterface $request
	 * @param \Strukt\Contract\Http\ResponseInterface $response
	 * @param callable $next
	 * 
	 * @return \Strukt\Http\Response\Plain
	 */
 	public function __invoke(RequestInterface $request, 
 								ResponseInterface $response, 
 								callable $next):Plain;
}