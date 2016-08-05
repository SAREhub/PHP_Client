<?php

namespace SAREhub\Client\Event;

/**
 * Represents place where events come from
 */
interface EventStreamSource {
	
	/**
	 * Blocking or not blocking method (pushing only to connected sink)
	 */
	public function flow();
	
	/**
	 * Disconnects all sinks from source
	 */
	public function unpipe();
	
	/**
	 * Connects sink to the source(additionally notify previous sink about that)
	 * @param EventStreamSink $sink
	 */
	public function pipe(EventStreamSink $sink);
}