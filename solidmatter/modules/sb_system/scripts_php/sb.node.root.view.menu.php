<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbView_root_menu extends sbView {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_REQUEST;
		global $_RESPONSE;
		
		$sOpen = $_REQUEST->getParam('open');
		$sClose = $_REQUEST->getParam('close');
		
		$sQuery = '/';
		if ($sOpen != NULL) {
			sbSession::$aData['menu']['expanded'][$sOpen] = TRUE;
			sbSession::commit();
			$sQuery = $sOpen;
		}
		if ($sClose != NULL) {
			unset(sbSession::$aData['menu']['expanded'][$sClose]);
			sbSession::commit();
			$sQuery = $sClose;
		}
		//die($sQuery);
		
		//sleep(5);
		$nodeCurrentRoot = $this->crSession->getNode($sQuery);
		
		// check if node is linked
		$sPrimary = 'TRUE';
		if (substr_count($sQuery, '/') > 1) {
			$sParentPath = substr($sQuery, 0, strrpos($sQuery, '/'));
			$nodePathParent = $this->crSession->getNode($sParentPath);
			$nodeRealParent = $nodeCurrentRoot->getParent();
			DEBUG('Parent:'.$nodePathParent->getIdentifier().' Child:'.$nodeRealParent->getIdentifier(), DEBUG::NODE);
			
			if (!$nodePathParent->isSame($nodeRealParent)) {
				$sPrimary = 'FALSE';
			}
		}
		$nodeCurrentRoot->setAttribute('primary', $sPrimary);
		
		if ($sQuery == '/') {
			$nodeCurrentRoot->setAttribute('mode', 'tree_root');
			$nodeCurrentRoot->setAttribute('path', $sQuery);
			$sQuery = '';
		}
		
		// set mode
		if (Registry::getValue('sb.system.debug.menu.debugmode')) {
			$sMode = 'debug';
		} else {
			$sMode = 'tree';
		}
		if ($sQuery == '' || isset(sbSession::$aData['menu']['expanded'][$sQuery])) {
			$niChildren = $nodeCurrentRoot->loadChildren($sMode, TRUE, TRUE, FALSE, array('read'));
			foreach($niChildren as $nodeChild) {
				$this->expand($nodeChild, $sQuery);
			}
			$nodeCurrentRoot->addContent('menu', $niChildren);
		}
		
		$_RESPONSE->addData($nodeCurrentRoot->getElement(array('content' => TRUE)), 'menu');
		
		return (NULL);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	private function expand($nodeCurrent, $sPath) {
		
		$sCurrentPath = $sPath.'/'.$nodeCurrent->getProperty('name');
		
		if (isset(sbSession::$aData['menu']['expanded'][$sCurrentPath])) {
			
			if (Registry::getValue('sb.system.debug.menu.debugmode')) {
				$sMode = 'debug';
			} else {
				$sMode = 'tree';
			}
			
			$niChildren = $nodeCurrent->loadChildren($sMode, TRUE, TRUE, FALSE, array('read'));
			foreach($niChildren as $nodeChild) {
				$this->expand($nodeChild, $sCurrentPath);
			}
			$nodeCurrent->addContent('menu', $niChildren);
			
		}
		
	}	
	
}

?>