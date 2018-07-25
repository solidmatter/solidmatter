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
	private static $sSystemUUID = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function setSession(sbCR_Session $crSession) {
		self::$crSession = $crSession;
		self::$sSystemUUID = self::$crSession->getRootNode()->getIdentifier();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected static function isUserSpecific(string $sKey) : bool {
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
	* Returns a value to a key from registry.
	* The order in which the possible values are considered is sbSession -> RegistryCache -> user -> system -> default
	* Throws an exception if the registry entry does not exist.
	* @param 
	* @return multiple Converted Value (e.g. "TRUE" is converted to boolean)
	*/
	public static function getValue(string $sKey, bool $bForced = FALSE) {
		
		// check temporary setting first (uses sbSesion storage)
		if (isset(sbSession::$aData['registry'][$sKey])) {
			return (sbSession::$aData['registry'][$sKey]);
		}
		
		// cache
		if (CONFIG::USE_REGISTRYCACHE) {
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

		// 		$stmtGetValue->debug();
// 		var_dumpp($stmtGetValue->fetchAll());

		$mValue = NULL;
		$sType= NULL;
		$bNoValueFound = TRUE;
		
		// there can only be one result row
		foreach ($stmtGetValue as $aRow) {
			$sType = $aRow['e_type'];
			// use values in order user -> system -> default
			if ($aRow['s_uservalue'] != NULL) {
				$mValue = $aRow['s_uservalue'];
			} elseif ($aRow['s_systemvalue'] != NULL) {
				$mValue = $aRow['s_systemvalue'];
			} else {
				$mValue = $aRow['s_defaultvalue'];
			}
		}
		$stmtGetValue->closeCursor();
		
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
		if (CONFIG::USE_REGISTRYCACHE) {
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
	public static function setValue(string $sKey, $mValue, string $sUserID = NULL, bool $bTemporary = FALSE) {
		
		if ($sUserID == NULL) {
			$sUserID = self::$sSystemUUID;
		}
		if ($sUserID != self::$sSystemUUID && !self::isUserSpecific($sKey)) {
			throw new sbException('attempt to set a registry value ('.$sKey.') for a user that is not user-specific');
		}
		
		// if the value should only persist until end of session
		if ($bTemporary) {
			sbSession::$aData['registry'][$sKey] = $mValue;
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
		if (CONFIG::USE_REGISTRYCACHE) {
			$cacheRegistry = CacheFactory::getInstance('registry');
			$cacheRegistry->clear();
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Completely clears the registry cache.
	*/
	public static function clearCache() {
		$cacheRegistry = CacheFactory::getInstance('registry');
		$cacheRegistry->clear();
	}
	
}

?>