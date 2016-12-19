<?php

namespace SAREhub\Client;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use SAREhub\Client\Util\LoggerFactory;
use SAREhub\Commons\Misc\TimeProvider;
use SAREhub\Component\Worker\Service\Service;

class BasicClientContextTest extends TestCase {
	
	/**
	 * @var BasicClientContext
	 */
	private $context;
	
	protected function setUp() {
		$this->context = BasicClientContext::newInstance();
	}
	
	public function testCreateThenTimeProviderIsDefault() {
		$context = BasicClientContext::newInstance();
		$this->assertSame(TimeProvider::get(), $context->getTimeProvider());
	}
	
	public function testGetPropertyWhenNotExistsThenThrowException() {
		$this->expectException(\OutOfBoundsException::class);
		$this->context->getProperty('prop');
	}
	
	public function testGetPropertyWhenExistsThenReturnValue() {
		$this->context->setProperty('prop', 123);
		$this->assertEquals(123, $this->context->getProperty('prop'));
	}
	
	public function testHasPropertyWhenNotExistsThenReturnFalse() {
		$this->assertFalse($this->context->hasProperty('prop'));
	}
	
	public function testHasPropertyWhenExistsThenReturnTrue() {
		$this->context->setProperty('prop', 1);
		$this->assertTrue($this->context->hasProperty('prop'));
	}
	
	public function testHasServiceWhenNotRegistered() {
		$this->assertFalse($this->context->hasService('service1'));
	}
	
	public function testGetServiceWhenNotRegisteredThenThrowException() {
		$this->expectException(\OutOfBoundsException::class);
		$this->context->getService('service1');
	}
	
	public function testGetServiceWhenRegisteredThenReturn() {
		$service = $this->createService();
		$this->context->registerService('service1', $service);
		$this->assertSame($service, $this->context->getService('service1'));
	}
	
	public function testRegisterServiceThenRegistered() {
		$this->context->registerService('service1', $this->createService());
		$this->assertTrue($this->context->hasService('service1'));
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|Service
	 */
	private function createService() {
		return $this->createMock(Service::class);
	}
	
	public function testCreateLoggerWhenEmptyLoggerFactoryThenThrowException() {
		$this->expectException(\LogicException::class);
		$this->context->createLogger('test');
	}
	
	public function testCreateLoggerThenLoggerFactoryCreate() {
		$factory = $this->createMock(LoggerFactory::class);
		$this->context->withLoggerFactory($factory);
		$factory->expects($this->once())->method('create')->with('test');
		$this->context->createLogger('test');
	}
	
	public function testCreateLoggerThenReturn() {
		$factory = $this->createMock(LoggerFactory::class);
		$this->context->withLoggerFactory($factory);
		$logger = $this->createMock(LoggerInterface::class);
		$factory->method('create')->willReturn($logger);
		$this->assertSame($logger, $this->context->createLogger('test'));
	}
	
	public function testStartThenServicesStart() {
		$service = $this->createService();
		$service->expects($this->once())->method('start');
		$this->context->registerService('s', $service);
		$this->context->start();
	}
	
	public function testTickThenServicesTick() {
		$service = $this->createService();
		$service->expects($this->once())->method('tick');
		$this->context->registerService('s', $service);
		$this->context->start();
		$this->context->tick();
	}
	
	public function testStopThenServicesStop() {
		$service = $this->createService();
		$service->expects($this->once())->method('stop');
		$this->context->registerService('s', $service);
		$this->context->start();
		$this->context->stop();
	}
}
