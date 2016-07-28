<?php

namespace SAREhub\Client\Event;

abstract class EventStreamSink extends EventStream {
	
	public abstract function write(EventEnvelope $eventEnvelope);
	
	public function onPipe(EventStreamSource $source) {
		
	}
	
	public function onUnpipe(EventStreamSource $source) {
		
	}
}