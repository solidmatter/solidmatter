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
* 
*/
abstract class Registry {
	
	private static $crSession = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function setSession($crSession) {
		self::$crSession = $crSession;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected static function isUserSpecific($sKey) {
		$stmtGetEntry = self::$crSession->prepareKnown('sbSystem/registry/getEntry');
		$stmtGetEntry->bindValue('key', $sKey, PDO::PARAM_STR);
		$stmtGetEntry->execute();
		$bUserSpecific = NULL;
		foreach ($stmtGetEntry as $aRow) {
			$bUserSpecific = constant($aRow['b_userspecific']);
		}
		$stmtGetEntry->closeCursor();
		if ($bUserSpecific === NULL) {
			throw new sbException('registry entry "'.$sKey.'" does not exist');
		}
		return ($bUserSpecific);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getValue($sKey, $bForced = FALSE) {
		
		// cache
		if (USE_REGISTRYCACHE) {
			$cacheRegistry = CacheFactory::getInstance('registry');
			if (!$bForced) {
				$mValue = $cacheRegistry->loadData($sKey);
				if ($mValue != NULL) {
					return $mValue;
				}
			}
		}
		
		// logic
		$stmtGetValue = self::$crSession->prepareKnown('sbSystem/registry/getValue');
		$stmtGetValue->bindValue('key', $sKey, PDO::PARAM_STR);
		$stmtGetValue->bindValue('user_uuid', User::getUUID(), PDO::PARAM_STR);
		$stmtGetValue->execute();
		
		$bEmpty = TRUE;
		// NOTE: system values are returned first, so looping will suffice
		foreach ($stmtGetValue as $aRow) {
			$mValue = $aRow['s_value'];
			$sType = $aRow['e_type'];
			$bEmpty = FALSE;
		}
		$stmtGetValue->closeCursor();
		
		if ($bEmpty) {
			throw new sbException('Registry value does not exist! ('.$sKey.')');
		}
		//var_dumpp($sKey.'|'.$sType);
		switch ($sType) {
			case 'boolean':
				if ($mValue == 'TRUE') {
					$mValue = TRUE;
				} else {
					$mValue = FALSE;
				}
				break;
			case 'integer':
				$mValue = (integer) $mValue;
				break;
			case 'string':
				if ($mValue == NULL) {
					$mValue = '';
				}
				break;
		}
		
		// cache
		if (USE_REGISTRYCACHE) {
			$cacheRegistry->storeData($sKey, $mValue);
		}
		
		return ($mValue);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function setValue($sKey, $mValue, $sUserID = 'SYSTEM') {
		
		if ($sUserID != 'SYSTEM' && !self::isUserSpecific($sKey)) {
			throw new sbException('attempt to set a registry value ('.$sKey.') for a user that is not user-specific');
		}
		
		// logic
		$sValue = (string) $mValue;
		
		$stmtSetValue = self::$crSession->prepareKnown('sbSystem/registry/setValue');
		$stmtSetValue->bindValue('key', $sKey, PDO::PARAM_STR);
		$stmtSetValue->bindValue('value', $sValue, PDO::PARAM_STR);
		$stmtSetValue->bindValue('user_uuid', $sUserID, PDO::PARAM_STR);
		$stmtSetValue->execute();
		$stmtSetValue->closeCursor();
		
		// cache
		if (USE_REGISTRYCACHE) {
			$cacheRegistry = CacheFactory::getInstance('registry');
			$cacheRegistry->clear();
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function clearCache() {
		$cacheRegistry = CacheFactory::getInstance('registry');
		$cacheRegistry->clear();
	}
	
}

?>