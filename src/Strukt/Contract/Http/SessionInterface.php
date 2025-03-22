<?php

namespace Strukt\Contract\Http;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
interface SessionInterface{

	/**
	 * @param $key
	 * 
	 * @return mixed
	 */
 	public function get($key):mixed;

 	/**
 	 * @param $key
 	 * @param $val
 	 */
 	public function set($key, $val):void;
}