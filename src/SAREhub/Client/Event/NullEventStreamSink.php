<?php


namespace SAREhub\Client\Event;

/**
 * Class used as mock event stream sink
 */
class NullEventStreamSink implements EventStreamSink {
	
	public function write(EventEnvelope $eventEnvelope) {
		
	}
	
	public function onPipe(EventStreamSource $source) {
		
	}
	
	public function onUnpipe(EventStreamSource $source) {
		
	}
}