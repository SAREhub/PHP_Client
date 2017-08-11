<?php

namespace SAREhub\Client\Util;

use Monolog\Formatter\JsonFormatter;

class DefaultJsonLogFormatter extends JsonFormatter {
	
	const DATE_TIME_FORMAT = \DateTime::ATOM;
	
	public function __construct() {
		parent::__construct();
		$this->includeStacktraces(true);
	}
	
	public function format(array $record) {
		$record["datetime"] = $this->normalizeDateTime($record["datetime"]);
		foreach ($record["context"] as &$value) {
			if (is_object($value)) {
				$value = $this->normalizeObject($value);
			}
		}
		
		return parent::format($record);
	}
	
	public function normalizeException($e) {
		return parent::normalizeException($e);
	}
	
	public function normalizeDateTime(\DateTime $dateTime): string {
		return $dateTime->format(\DateTime::ATOM);
	}
	
	public function normalizeObject($value) {
		
		if ($value instanceof \JsonSerializable) {
			return $value;
		}
		
		if ($value instanceof \Throwable) {
			return $value;
		}
		
		if (method_exists($value, '__toString')) {
			return (string)$value;
		}
		
		return "object of class: ".get_class($value)." can't be serialized to json";
	}
	
}