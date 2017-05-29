<?php

namespace SAREhub\Client;

use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SAREhub\Client\Util\LoggerFactory;
use SAREhub\Client\Util\NullLoggerFactory;
use SAREhub\Commons\Misc\TimeProvider;
use SAREhub\Commons\Service\Service;

class TestServiceWithContextAware implements Service, ClientContextAware {
	
	private $context;
	private $logger;
	
	public function getClientContext() {
		return $this->context;
	}
	
	public function setClientContext(ClientContext $context) {
		$this->context = $context;
	}
	
	public function setLogger(LoggerInterface $logger) {
		$this->logger = $logger;
	}
	
	public function start() {
		
	}
	
	public function tick() {
		
	}
	
	
	public function stop() {
		
	}
	
	public function isStarted() {
	}
	
	public function isStopped() {
		
	}
	
	public function isRunning() {
		
	}
	
	public function getLogger() {
		return $this->logger;
	}
}

class BasicClientContextTest extends TestCase {
	
	/**
	 * @var ClientContext
	 */
	private $context;
	
	protected function setUp() {
		$this->context = BasicClientContext::newInstance();
	}
	
	public function testCreateThenTimeProviderIsDefault() {
		$context = BasicClientContext::newInstance();
		$this->assertInstanceOf(TimeProvider::class, $context->getTimeProvider());
	}
	
	public function testCreateThenLoggerFactoryIsNullLoggerFactory() {
		$context = BasicClientContext::newInstance();
		$this->assertInstanceOf(NullLoggerFactory::class, $context->getLoggerFactory());
	}
	
	public function testGetPropertyWhenNotExistsThenThrowException() {
		$this->expectException(\InvalidArgumentException::class);
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
	
	public function testAddServiceThenContextHasProperty() {
		$service = $this->createService();
		$this->context->addService('s', $service);
		$this->assertSame($service, $this->context->getProperty('s'));
	}
	
	public function testAddServiceWhenClientContextAwareThenServiceSetContext() {
		$service = new TestServiceWithContextAware();
		$this->context->addService('test', $service);
		$this->assertSame($this->context, $service->getClientContext());
	}
	
	public function testAddServiceThenServiceSetLogger() {
		$service = new TestServiceWithContextAware();
		$this->context->addService('test', $service);
		$this->assertInstanceOf(NullLogger::class, $service->getLogger());
	}
	
	public function testAddServiceThenServiceStart() {
		$service = $this->createService();
		$service->expects($this->once())->method('start');
		$this->context->addService('service', $service);
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|Service
	 */
	private function createService() {
		return $this->createMock(Service::class);
	}
	
	public function testIsStoppedWhenNotStartedThenReturnTrue() {
		$this->assertTrue($this->context->isStopped());
	}
	
	public function testIsStoppedWhenStartedThenReturnFalse() {
		$this->context->start();
		$this->assertFalse($this->context->isStopped());
	}
	
	public function testIsStartedWhenNotThenReturnFalse() {
		$this->assertFalse($this->context->isStarted());
	}
	
	public function testStartThenIsStartedReturnTrue() {
		$this->context->start();
		$this->assertTrue($this->context->isStarted());
	}
	
	public function testIsRunningWhenNotStartedThenReturnFalse() {
		$this->assertFalse($this->context->isRunning());
	}
	
	public function testIsRunningWhenStartedThenReturnTrue() {
		$this->assertFalse($this->context->isRunning());
	}
	
	public function testTickThenServicesTick() {
		$service = $this->createService();
		$service->expects($this->once())->method('tick');
		$this->context->addService('s', $service);
		$this->context->start();
		$this->context->tick();
	}
	
	public function testStopThenServicesStop() {
		$service = $this->createService();
		$service->expects($this->once())->method('stop');
		$this->context->addService('s', $service);
		$this->context->start();
		$this->context->stop();
	}
	
	public function testInjectLoggerThenLoggerFactoryCreate() {
		$loggerFactory = $this->createMock(LoggerFactory::class);
		$loggerFactory->expects($this->once())
		  ->method('create')
		  ->with('test')
		  ->willReturn($this->createMock(Logger::class));
		$this->context->setLoggerFactory($loggerFactory);
		$this->context->injectLogger('test', $this->createMock(LoggerAwareInterface::class));
	}
	
	public function testInjectLoggerThenAwareSetLogger() {
		$logger = $this->createMock(Logger::class);
		$loggerFactory = $this->createMock(LoggerFactory::class);
		$loggerFactory->method('create')->willReturn($logger);
		$this->context->setLoggerFactory($loggerFactory);
		
		$aware = $this->createMock(LoggerAwareInterface::class);
		$aware->expects($this->once())->method('setLogger')->with($this->identicalTo($logger));
		$this->context->injectLogger('test', $aware);
	}
	
	public function testRegisterContextProviderThenProviderRegister() {
		$provider = $this->createMock(ContextProvider::class);
		$provider->expects($this->once())
		  ->method('register')
		  ->with($this->identicalTo($this->context));
		$this->context->registerContextProvider($provider);
	}
}
