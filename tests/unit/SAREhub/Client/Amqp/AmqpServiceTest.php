<?php

namespace SAREhub\Client\Amqp;

use PHPUnit\Framework\TestCase;

class AmqpServiceTest extends TestCase {
	
	private $connectionService;
	
	/**
	 * @var AmqpService
	 */
	private $service;
	
	private $consumer;
	
	protected function setUp() {
		$this->connectionService = $this->createMock(AmqpConnectionService::class);
		$this->service = new AmqpService($this->connectionService);
		
		$this->consumer = $this->createMock(AmqpConsumer::class);
		$this->service->withConsumer($this->consumer);
	}
	
	public function testStartThenConsumerStart() {
		$this->consumer->expects($this->once())->method('start');
		$this->service->start();
	}
	
	public function testTickThenConsumerTick() {
		$this->consumer->expects($this->once())->method('tick');
		$this->service->start();
		$this->service->tick();
	}
	
	public function testTickThenConsumerStop() {
		$this->consumer->expects($this->once())->method('stop');
		$this->service->start();
		$this->service->stop();
	}
}
