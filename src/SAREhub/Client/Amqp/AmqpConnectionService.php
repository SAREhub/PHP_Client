<?php

namespace SAREhub\Client\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use SAREhub\Commons\Misc\Parameters;

class AmqpConnectionService {
	
	/**
	 * @var Parameters
	 */
	private $config;
	
	/**
	 * @var AMQPStreamConnection
	 */
	private $connection;
	
	public function __construct(Parameters $config) {
		$this->config = $config;
		$this->createConnection();
	}
	
	protected function createConnection() {
		$config = $this->getConfig();
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
		  ($config->get('heartbeat', 30) * 2) + 1,
		  null,
		  $config->get('keepalive', true),
		  $config->get('heartbeat', 30)
		);
		
		return $connection;
	}
	
	/**
	 * @return AmqpChannelWrapper
	 */
	public function createChannel() {
		$channel = new AMQPChannel($this->getConnection());
		return new AmqpChannelWrapper($channel, $this);
	}
	
	/**
	 * @return Parameters
	 */
	public function getConfig() {
		return $this->config;
	}
	
	/**
	 * @return AMQPStreamConnection
	 */
	public function getConnection() {
		return $this->connection;
	}
}