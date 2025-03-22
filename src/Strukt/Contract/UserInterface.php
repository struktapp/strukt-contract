<?php

namespace Strukt\Contract;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
interface UserInterface{

	/**
	 * @param string $username
	 * 
	 * @return void
	 */
	public function setUsername(?string $username):void;
}