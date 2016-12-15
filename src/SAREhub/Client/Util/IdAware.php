<?php

namespace SAREhub\Client\Util;

interface IdAware {
	
	/**
	 * @return String
	 */
	public function getId();
	
	/**
	 * @param String $id
	 */
	public function setId($id);
}