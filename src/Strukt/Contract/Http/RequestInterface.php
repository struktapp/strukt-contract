<?php

namespace Strukt\Contract\Http;

use Strukt\Contract\UserInterface;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
interface RequestInterface{

	/**
	 * @param \Strukt\Contract\UserInterface $user
	 * 
	 * @return void
	 */
	public function setUser(?UserInterface $user = null):void;

	/**
	 * @return string
	 */
	public function getUser():?string;
}