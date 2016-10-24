<?php

namespace SAREhub\Client\Util;


class StreamHelper {
	
	/**
	 * @param resource $stream
	 * @param int $sec
	 * @param int|null $usec
	 * @return int
	 */
	public function select($stream, $sec, $usec = null) {
		$read = [$stream];
		$write = null;
		$expect = null;
		return stream_select($read, $write, $except, $sec, $usec = null);
	}
}