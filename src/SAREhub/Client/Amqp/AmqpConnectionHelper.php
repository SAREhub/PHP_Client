<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Respect\Validation\Validator as v;
use SAREhub\Commons\Misc\Parameters;

class AmqpConnectionHelper {
	
	/**
	 * @param Parameters $config
	 * @return AMQPStreamConnection
	 */
	public static function createConnection(Parameters $config) {
		self::validateConfig($config);
		return new AMQPStreamConnection(
		  $config->getRequired('host'),
		  $config->getRequired('port'),
		  $config->getRequired('username'),
		  $config->getRequired('password'),
		  $config->getRequired('vhost')
		);
	}
	
	
	public static function validateConfig(Parameters $config) {
		v::stringType()->notBlank()->setName('host')->assert($config->getRequired('host'));
		v::intType()->min(1)->max(65535)->setName('port')->assert($config->getRequired('port'));
		v::stringType()->notBlank()->setName('username')->assert($config->getRequired('username'));
		v::stringType()->notBlank()->setName('password')->assert($config->getRequired('password'));
		v::stringType()->notBlank()->setName('vhost')->assert($config->getRequired('vhost'));
	}
}