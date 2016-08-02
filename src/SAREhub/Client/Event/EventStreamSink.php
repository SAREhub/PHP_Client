<?php

namespace SAREhub\Client\Event;

/**
 * Represents place where events can be puts
 */
interface EventStreamSink {
	
	/**
	 * Puts event to sink
	 * @param EventEnvelope $eventEnvelope
	 */
	public function write(EventEnvelope $eventEnvelope);
	
	/**
	 * Executed in event stream source when this sink was connected to source
	 * @param EventStreamSource $source
	 */
	public function onPipe(EventStreamSource $source);
	
	/**
	 * Executed in event stream source when this sink was disconnected from source
	 * @param EventStreamSource $source
	 */
	public function onUnpipe(EventStreamSource $source);
}