<?php

namespace SAREhub\Client\Event;

/**
 * Base class for event stream sources with one sink connected
 */
abstract class EventStreamSourceBase implements EventStreamSource {
	
	/** @var EventStreamSink */
	private $sink;
	
	public function __construct() {
		$this->sink = new NullEventStreamSink();
	}
	
	public abstract function flow();
	
	public abstract function read();
	
	public function unpipe() {
		$this->pipe(new NullEventStreamSink());
	}
	
	public function pipe(EventStreamSink $sink) {
		$this->sink->onUnpipe($this);
		$this->sink = $sink;
		$this->sink->onPipe($this);
	}
	
	/**
	 * @return EventStreamSink
	 */
	public function getSink() {
		return $this->sink;
	}
}