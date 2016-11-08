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
		$connection = new AMQPStreamConnection(
		  $config->getRequired('host'),
		  $config->getRequired('port'),
		  $config->getRequired('username'),
		  $config->getRequired('password'),
		  $config->getRequired('vhost'),
		  false,
		  'AMQPLAIN',
		  null,
		  'en_US',
		  3.0,
		  3.0,
		  null,
		  $config->get('keepalive', true),
		  $config->get('heartbeat', 30)
		);
		
		return $connection;
	}
	
	
	public static function validateConfig(Parameters $config) {
		v::stringType()->notBlank()->setName('host')->assert($config->getRequired('host'));
		v::intType()->min(1)->max(65535)->setName('port')->assert($config->getRequired('port'));
		v::stringType()->notBlank()->setName('username')->assert($config->getRequired('username'));
		v::stringType()->notBlank()->setName('password')->assert($config->getRequired('password'));
		v::stringType()->notBlank()->setName('vhost')->assert($config->getRequired('vhost'));
	}
}