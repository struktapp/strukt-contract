<?php

namespace Strukt\Contract\Http;

interface SessionInterface{

 	public function get(string $key);
 	public function set(string $key, $val);
}