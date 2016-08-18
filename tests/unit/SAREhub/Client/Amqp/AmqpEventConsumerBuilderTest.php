<?php

namespace SAREhub\Client\Amqp;

use PHPUnit\Framework\TestCase;
use SAREhub\Client\Event\JsonEventDeserializationService;

class AmqpEventConsumerBuilderTest extends TestCase {
	
	private $sourceMock;
	private $deserializationServiceMock;
	private $processPromiseFactoryMock;
	
	public function setUp() {
		$this->sourceMock = $this->getMockBuilder(BasicAmqpEventStreamSource::class)
		  ->disableOriginalConstructor()
		  ->getMock();
		$this->deserializationServiceMock = $this->getMockBuilder(JsonEventDeserializationService::class)
		  ->disableOriginalConstructor()
		  ->getMock();
		
		$this->processPromiseFactoryMock = $this->getMockBuilder(\stdClass::class)
		  ->setMethods(['__invoke'])
		  ->getMock();
	}
	
	public function testBuild() {
		$builder = new AmqpEventConsumerBuilder();
		$builder->source($this->sourceMock);
		$builder->deserializationService($this->deserializationServiceMock);
		$builder->processPromiseFactory($this->processPromiseFactoryMock);
		$this->assertInstanceOf(\Closure::class, $builder->build());
	}
	
	/**
	 * @expectedException \Respect\Validation\Exceptions\ValidationException
	 */
	public function testBuildWithoutSource() {
		$builder = new AmqpEventConsumerBuilder();
		$builder->deserializationService($this->deserializationServiceMock);
		$builder->processPromiseFactory($this->processPromiseFactoryMock);
		$this->assertInstanceOf(\Closure::class, $builder->build());
	}
	
	/**
	 * @expectedException \Respect\Validation\Exceptions\ValidationException
	 */
	public function testBuildWithoutDeserializationService() {
		$builder = new AmqpEventConsumerBuilder();
		$builder->source($this->sourceMock);
		$builder->processPromiseFactory($this->processPromiseFactoryMock);
		$this->assertInstanceOf(\Closure::class, $builder->build());
	}
	
	/**
	 * @expectedException \Respect\Validation\Exceptions\ValidationException
	 */
	public function testBuildWithoutProcessPromiseFactory() {
		$builder = new AmqpEventConsumerBuilder();
		$builder->source($this->sourceMock);
		$builder->deserializationService($this->deserializationServiceMock);
		$this->assertInstanceOf(\Closure::class, $builder->build());
	}
	
}
