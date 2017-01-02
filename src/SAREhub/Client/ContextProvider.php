<?php

namespace SAREhub\Client;

/**
 * Context provider for inject some services and properties to ClientContext
 */
interface ContextProvider {
	
	/**
	 * @param ClientContext $c
	 */
	public function register(ClientContext $c);
}