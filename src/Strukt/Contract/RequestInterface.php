<?php

namespace Strukt\Contract;

interface RequestInterface{

	public function setUser(UserInterface $user);
	public function getUser();
}