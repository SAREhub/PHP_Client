<?php

namespace SAREhub\Client\User;


class StandardUserKeyFactory {
	
	const HUB_PROFILE_ID_KEY_TYPE = 'id';
	const COOKIE_KEY_TYPE = 'cookie';
	const EMAIL_KEY_TYPE = 'email';
	const MOBILE_KEY_TYPE = 'mobile';
	
	/**
	 * @param string $value
	 * @return BasicUserKey
	 */
	public static function hubProfileId($value) {
		return self::createKey(self::HUB_PROFILE_ID_KEY_TYPE, $value);
	}
	
	/**
	 * @param string $value
	 * @return BasicUserKey
	 */
	public static function cookie($value) {
		return self::createKey(self::COOKIE_KEY_TYPE, $value);
	}
	
	/**
	 * @param string $value
	 * @return BasicUserKey
	 */
	public static function email($value) {
		return self::createKey(self::EMAIL_KEY_TYPE, $value);
	}
	
	/**
	 * @param string $value
	 * @return BasicUserKey
	 */
	public static function mobile($value) {
		return self::createKey(self::MOBILE_KEY_TYPE, $value);
	}
	
	/**
	 * @param string $type
	 * @param string $value
	 * @return BasicUserKey
	 */
	public static function createKey($type, $value) {
		return new BasicUserKey($type, $value);
	}
}