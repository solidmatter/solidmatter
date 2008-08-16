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
		$stmtGetValue->bindValue('user_uuid', 'SYSTEM', PDO::PARAM_STR);
		$stmtGetValue->execute();
		
		$bEmpty = TRUE;
		foreach ($stmtGetValue as $aRow) {
			$mValue = $aRow['s_value'];
			$sType = $aRow['e_type'];
			$bEmpty = FALSE;
		}
		$stmtGetValue->closeCursor();
		
		if ($bEmpty) {
			throw new sbException('Registry value does not exist! ('.$sKey.')');
		}
		
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
	public static function setValue($sKey, $mValue) {
		
		// logic
		$sValue = (string) $mValue;
		
		$stmtGetValue = self::$crSession->prepareKnown('sbSystem/registry/setValue');
		$stmtGetValue->bindValue('key', $sKey, PDO::PARAM_STRING);
		$stmtGetValue->bindValue('value', $sValue, PDO::PARAM_STRING);
		$stmtGetValue->bindValue('user_uuid', 'SYSTEM', PDO::PARAM_STRING);
		$stmtGetValue->execute();
		$stmtGetValue->closeCursor();
		
		// cache
		if (USE_REGISTRYCACHE) {
			$cacheRegistry = CacheFactory::getInstance('registry');
			$cacheRegistry->storeData($sKey, $mValue);
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