<?php

namespace SAREhub\Client\Amqp;


use PhpAmqpLib\Connection\AMQPStreamConnection;
use SAREhub\Commons\Misc\Parameters;

class AmqpConnectionFactory {
	
	private $parameters;
	
	public function __construct(array $parameters) {
		$this->parameters = new Parameters($parameters);
	}
	
	public function create() {
		return new AMQPStreamConnection(
		  $this->parameters->getRequired('host'),
		  $this->parameters->getRequired('port'),
		  $this->parameters->getRequired('username'),
		  $this->parameters->getRequired('password'),
		  $this->parameters->getRequired('vhost'),
		  false,
		  'AMQPLAIN',
		  null,
		  'en_US',
		  3.0,
		  ($this->parameters->get('heartbeat', 30) * 2) + 1,
		  null,
		  $this->parameters->get('keepalive', true),
		  $this->parameters->get('heartbeat', 30)
		);
	}
}