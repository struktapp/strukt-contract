<?php

namespace Strukt\Contract;

interface SessionInterface{

 	public function get(string $key);
 	public function set(string $key, $val);
}