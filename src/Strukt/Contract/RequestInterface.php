<?php

namespace Strukt\Contract;

class RequestInterface{

	public function setUser(UserInterface $user);
	public function getUser();
}