<?php

class NodeFactory {
	
	private static $aNodetypes = NULL;
	private static $aNodecache = array();
	
	private static $repCurrent = NULL;
	
	public final function __construct() { }
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	static public function createInstance($sNodetype, $sName = '', $sLabel = '') {
		
		if (self::$aNodetypes == NULL) {
			self::loadNodetypes();
		}
		
		if (!isset(self::$aNodetypes[$sNodetype])) {
			throw new UnknownNodetypeException('invalid nodetype: '.$sNodetype);
		}
		
		$aNode['uuid'] = uuid();
		$aNode['fk_nodetype'] = $sNodetype;
		$aNode['s_name'] = $sName;
		$aNode['s_label'] = $sLabel;
		$aNode['s_uid'] = NULL;
		$aNode['s_csstype'] = self::$aNodetypes[$sNodetype]['s_csstype'];
		$aNode['s_customcsstype'] = NULL;
		//var_dump($aNode);
		return (self::generateInstanceFromRow($aNode, 'new', $this->repCurrent));
		
	}
	
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	static public function getInstance($sQuery) {
		
		if (self::$aNodetypes == NULL) {
			self::loadNodetypes();
		}
		if (isset(self::$aNodecache[$sQuery])) {
			return (self::$aNodecache[$sQuery]);
		}
		
		// TODO: find damn bug that uses empty query on root contexmenu
		if ($sQuery == '/' || $sQuery == '') {
			$nodeCurrent = self::getRoot();
		} elseif (preg_match('/^[a-z_]+:[a-z_]+$/', $sQuery)) {
			$nodeCurrent = self::getInstanceByUID($sQuery);
		} elseif (substr_count($sQuery, '/') > 0) {
			$nodeCurrent = self::getInstanceByPath($sQuery);
		} else {
			$nodeCurrent = self::getInstanceByID($sQuery);
		}
		
		$sUUID = $nodeCurrent->getUUID();
		self::$aNodecache[$sUUID] = $nodeCurrent;
		self::$aNodecache[$sQuery] = $nodeCurrent;
		
		return ($nodeCurrent);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	static public function getInstanceByID($sQuery) {
		$DB = DBFactory::getInstance('system');
		$stmtInfo = $DB->prepareKnown('sb_system/factory/node/by_id');
		$stmtInfo->bindParam('id', $sQuery, PDO::PARAM_STR);
		$stmtInfo->execute();
		return (self::generateInstance($stmtInfo, $sQuery));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	static public function getInstanceByUID($sQuery) {
		$DB = DBFactory::getInstance('system');
		$stmtInfo = $DB->prepareKnown('sb_system/factory/node/by_uid');
		$stmtInfo->bindParam('uid', $sQuery, PDO::PARAM_STR);
		$stmtInfo->execute();
		return (self::generateInstance($stmtInfo, $sQuery));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	static public function getRoot() {
		$DB = DBFactory::getInstance('system');
		$stmtInfo = $DB->prepareKnown('sb_system/factory/node/by_uid');
		$sUID = 'sb_system:root';
		$stmtInfo->bindParam('uid', $sUID, PDO::PARAM_STR);
		$stmtInfo->execute();
		return (self::generateInstance($stmtInfo, '/'));
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	static public function getInstanceByPath($sQuery) {
		$DB = DBFactory::getInstance('system');
		$iNodeID = self::resolvePath($sQuery);
		if ($iNodeID !== FALSE) {
			$stmtInfo = $DB->prepareKnown('sb_system/factory/node/by_id');
			$stmtInfo->bindParam('id', $iNodeID, PDO::PARAM_STR);
			$stmtInfo->execute();
			//var_dump($stmtInfo);
			return (self::generateInstance($stmtInfo, $sQuery));
		} else {
			throw new NodeNotFoundException('a node with this query does not exist: '.$sQuery);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	static private function generateInstance($stmtInfo, $sQuery) {
		
		$aRows = $stmtInfo->fetchAll(PDO::FETCH_ASSOC);
		$stmtInfo->closeCursor();
		foreach ($aRows as $aRow) {
			$elemInstance = self::generateInstanceFromRow($aRow, $sQuery);
			return ($elemInstance);
		}
		throw new NodeNotFoundException('a node with this query does not exist: '.$sQuery);	
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	static private function generateInstanceFromRow($aRow, $sQuery) {
		
		$elemSubject = ResponseFactory::createElement('sbnode');
		$elemSubject->setAttribute('nodetype', $aRow['fk_nodetype']);
		$elemSubject->setAttribute('uuid', $aRow['uuid']);
		
		if (isset(self::$aNodetypes[$aRow['fk_nodetype']])) {
			$sLibrary = self::$aNodetypes[$aRow['fk_nodetype']]['s_classfile'];
			$sClass = self::$aNodetypes[$aRow['fk_nodetype']]['s_class'];
			import($sLibrary);
			$elemNode = new $sClass($elemSubject);
		} else {
			$elemNode = new sbNode($elemSubject);
		}
		//$elemNode->setProperty('nodetype', $aRow['fk_nodetype']);
		$elemNode->setProperty('name', $aRow['s_name']);
		$elemSubject->setAttribute('label', $aRow['s_label']);
		$elemSubject->setAttribute('uid', $aRow['s_uid']);
		$elemNode->setProperty('query', $sQuery);
		$elemNode->setProperty('csstype', $aRow['s_csstype']);
		if ($aRow['s_customcsstype'] != NULL) {
			$elemNode->setProperty('csstype', $aRow['s_customcsstype']);
		}
		
		return ($elemNode);
			
	}
	
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	static private function loadNodetypes() {
		
		$DB = DBFactory::getInstance('system');
		$stmtLoad = $DB->prepareKnown('sb_system/factory/node/load_nodetypes');
		$stmtLoad->execute();
		
		$aNodetypes = $stmtLoad->fetchAll(PDO::FETCH_ASSOC);
		$stmtLoad->closeCursor();
		
		foreach ($aNodetypes as $aRow) {
			self::$aNodetypes[$aRow['s_type']] = $aRow;
		}
		
		//var_dump(self::$aNodetypes);
		
	}
	
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	static private function resolvePath($sPath) {
		
		$cachePaths = CacheFactory::getInstance('paths');
		$sUUID = $cachePaths->loadData($sPath);
		if ($sUUID !== FALSE) {
			//var_dump($sUUID);
			return ($sUUID);
		}
		
		$aPath = explode('/', $sPath);
		
		/*if ($aPath[count($aPath)-1] == '/') {
			unset($aPath[count($aPath)-1]);
		}*/
		
		unset($aPath[0]);
		
		$nodeRoot = NodeFactory::getRoot();
		$sUUID = self::iteratePath($nodeRoot, &$aPath);
		
		if ($sUUID !== NULL) {
			$cachePaths->storeData($sPath, $sUUID);
			return ($sUUID);
		} else {
			return (FALSE);
		}
		
	}
	
	//------------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	static private function iteratePath($nodeCurrent, $aPath, $iPosition = 1) {
		
		$aChild = $nodeCurrent->getChildByName($aPath[$iPosition]);
		
		if ($aChild === FALSE) {
			return (FALSE);
		}
		
		$nodeChild = NodeFactory::getInstance($aChild['uuid']);
		
		if (isset($aPath[++$iPosition])) {
			return(self::iteratePath($nodeChild, &$aPath, $iPosition));
		}
		return ($aChild['uuid']);
		
	}
	
	static public function setRepository($repCurrent) {
		self::repCurrent = $repCurrent;	
	}
	
	
	
}	



?>