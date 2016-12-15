<?php

namespace SAREhub\Client\Util;

class ClassHelper {
	
	public static function getShortName($class) {
		return (new \ReflectionClass($class))->getShortName();
	}
}