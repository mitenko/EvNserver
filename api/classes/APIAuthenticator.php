<?php
namespace Evn\classes;

/**
 * Represents a MySQL connection to the database
 */
class APIAuthenticator {
	/**
	 * Header API Key name
	 */
	const apiKeyName = "evnAPIKey";
	
	/**
	 * client API Key
	 */
	const clientAPIKey = "Z4Y7lboqgV6417U27FL712Y7fX3rzu06M37973S847Xv7QOexZH38NKj0h4s8nm4";
	
	/**
	 * UnAuthorized Error Message
	 */
	const UNAUTHORIZED = "Unauthorized Access";
	
	public function authenticate($request) {
		$user = new \Evn\classes\User;
		if ($request->hasHeader(self::apiKeyName)) {
			$apiKey = $request->getHeader(self::apiKeyName)[0];
			if ($apiKey === self::clientAPIKey) {
				$user->setType(\Evn\classes\User::CLIENTTYPE);
			}
		}
		if (!$user->isValid()) {
			throw new \Exception(self::UNAUTHORIZED);
		}
		return $user;
	}
}
	
?>