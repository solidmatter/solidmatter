<?php

import('sb.node.view');

class sbView_root_ajax_loadchildren extends sbView {
	
	public function execute() {
		
		//$DB = DBFactory::getInstance('system');
		global $_RESPONSE;
		
		$iRequestNodeID = $_GET['requestnode'];
		
		$nodeRequest = NodeFactory::getInstance($iRequestNodeID);
		$nodeRequest->loadChildren('menu', TRUE);
		$_RESPONSE->addData($nodeRequest, 'requestnode');
		
		$_RESPONSE->setStylesheet('root.ajax.loadchildren.xsl');
		
		return (NULL);
		
		/*global $_RESPONSE;
		global $_SBSESSION;
		
		if (isset($_GET['open'])) {
			$_SBSESSION->aData['menu']['expanded'][$_GET['open']] = TRUE;
		}
		if (isset($_GET['close'])) {
			unset($_SBSESSION->aData['menu']['expanded'][$_GET['close']]);
		}
		
		
		
		$domMenu = new sbDOMMenu();
		
		$_RESPONSE->addData($domMenu->lastChild, 'menu');
		
		$_RESPONSE->setStylesheet('root.menu.xsl');
		
		return (NULL);*/
	}
	
}


?>