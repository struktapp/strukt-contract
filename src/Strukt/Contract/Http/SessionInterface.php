<?php

namespace Strukt\Contract\Http;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
interface SessionInterface{

	/**
	 * @param string $key
	 * @param mixed $default
	 * 
	 * @return mixed
	 */
 	public function get(string $key, mixed $default = null):mixed;

 	/**
 	 * @param string $key
 	 * @param string $val
 	 * 
 	 * @return void
 	 */
 	public function set(string $key, mixed $val): void;
}