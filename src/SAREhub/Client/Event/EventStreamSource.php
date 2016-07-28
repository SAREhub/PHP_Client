<?php

namespace SAREhub\Client\Event;

/**
 * Represents place where events come from
 */
abstract class EventStreamSource extends EventStream {
	
	/** @var EventStreamSink */
	private $sink;
	
	public function __construct() {
		$this->sink = new NullEventStreamSink();
	}
	
	/**
	 * Blocking or not blocking method (pushing only to connected sink)
	 */
	public abstract function flow();
	
	/**
	 * Blocking method for get events (additionally pushing to sink)
	 * @return Event
	 */
	public abstract function read();
	
	/**
	 * Disconnects current sink from source
	 */
	public function unpipe() {
		$this->pipe(new NullEventStreamSink());
	}
	
	/**
	 * Connects sink to the source(additionally notify previous sink about that)
	 * @param EventStreamSink $sink
	 */
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