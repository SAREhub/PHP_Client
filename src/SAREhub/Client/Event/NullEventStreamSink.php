<?php


namespace SAREhub\Client\Event;

/**
 * Class used as mock event stream sink
 */
class NullEventStreamSink extends EventStreamSink {
	
	public function write(EventEnvelope $eventEnvelope) {
		
	}
}