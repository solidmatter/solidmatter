<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* Utility class to generate and convert sbUUIDs, which is an ordered and web-safe Base64 representation of an ordered UUID (following no specific Version
*/
abstract class sbUUID {
	
	private static $sLastUUID = NULL;
	
	//--------------------------------------------------------------------------
	/**
	 * Generates a sbUUID 
	 * @return string a 16 byte UUID (in web-safe Base64)
	 */
	public static function create() {
		self::$sLastUUID = self::base64url_encode(hex2bin(self::getOrderedUUID()));
		return (self::$sLastUUID);
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Generates a UUID after the old model
	 * @return string a 16 byte UUID (
	 */
	public static function createOldUUID($bWithHyphens = FALSE) {
		if ($bWithHyphens) {
			$sFormat = '%04x%04x-%04x-%04x-%04x-%04x%04x%04x';
		} else {
			$sFormat = '%04x%04x%04x%04x%04x%04x%04x%04x';
		}
		return sprintf($sFormat,
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0fff ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
			);
	}
	
	//--------------------------------------------------------------------------
	/**
	 * 
	 * @return string a 16 byte UUID
	 */
	public static function getLastUUID() {
		return (self::$sLastUUID);
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @return
	 */
	protected static function getOrderedUUID() {
		
		static $iCounter = 0;
		static $mtLast = 0;
		
		$mtNow = microtime(true);
		
		// check if generating UUIDs was too fast for microtime(), add 1ms in this case
		if ($mtLast != $mtNow) {
			$mtLast = $mtNow;
			$iCounter = 0;
		} else {
			$mtLast = $mtNow;
			$iCounter++;
		}
		
		// 16 bytes = 32 chars hex
		// 8+6+4+4+4+2
		return sprintf('%08x%06x%04x%04x%04x%04x%02x',
			floor($mtNow),
			(($mtNow-floor($mtNow))*10000000)+$iCounter,
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0fff ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xff )
		);
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Converts a base 16 UUID to a base 64 UUID
	 * @param string ASCII representation of hexadecimal UUID  
	 * @param boolean TRUE if the UUID includes hyphens
	 * @return 
	 */
	public static function convertBase16to64($sUUID, $bUsesHyphens = TRUE) {
		if ($bUsesHyphens) {
			$sUUID = str_replace('-', '', $sUUID);
		}
		return (self::base64url_encode(hex2bin($sUUID)));
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Converts a base 64 UUID to a base 16 UUID
	 * @param 
	 * @return
	 */
	public static function convertBase64to16($sUUID, $bUseHyphens = TRUE) {
		$sUUID = bin2hex(self::base64url_decode($sUUID));
		if ($bUseHyphens) {
			$sUUID = substr_replace($sUUID, '-', 20, 0);
			$sUUID = substr_replace($sUUID, '-', 16, 0);
			$sUUID = substr_replace($sUUID, '-', 12, 0);
			$sUUID = substr_replace($sUUID, '-', 8, 0);
		}
		return ($sUUID);
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Converts a binary String to URL-Safe Base64 (without filler '==' at the end)
	 * @return string a 16 byte UUID
	 */
	protected static function base64url_encode($sInput) {
		return substr(strtr(base64_encode($sInput), '+/', '-.'), 0, -2);
	}
	
	//------------------------------------------------------------------------------
	/**
	 * Converts a sbUUID to a binary string
	 * @return
	 */
	protected static function base64url_decode($sInput) {
		return base64_decode(strtr($sInput.'==', '-.', '+/'));
	}
	
}

?>