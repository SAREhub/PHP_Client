<?php

namespace SAREhub\Client\Event;

/**
 * Represents place where events come from
 */
interface EventStreamSource {
	
	/**
	 * Enabling flow mode in source(pushing events to connected sinks)
	 */
	public function flow();
	
	/**
	 * Connects sink to the source
	 * @param EventStreamSink $sink
	 */
	public function pipe(EventStreamSink $sink);
	
	/**
	 * Disconnects sink from source
	 */
	public function unpipe();
	
	/**
	 * Returns current connected EventStreamSink
	 * @return EventStreamSink
	 */
	public function getSink();
	
}