<?php

namespace Strukt\Contract\Http\Error;

Interface HttpErrorInterface{

	public function __construct(string|array $message, array $headers = []);
}