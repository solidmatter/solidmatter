<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.node.view.properties');

//------------------------------------------------------------------------------
/**
*/
class sbView_page_properties extends sbView_properties {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function checkInputs($formProperties) {
		
		$bValid = $formProperties->checkInputs();
		
		if (!$bValid) {
			return (FALSE);
		}
		
		$aValues = $formProperties->getValues();
		//var_dumpp($aValues);
		if ($aValues['theme_template'] != NULL && $aValues['theme_template'] != $this->nodeSubject->getProperty('theme_templates')) {
			$nodeTemplate = $this->crSession->getNodeByIdentifier($aValues['theme_template']);
			$this->copyContentTree($nodeTemplate, $this->nodeSubject);
		}
		
		return (TRUE);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function copyContentTree($nodeSource, $nodeTarget) {
		
		$sNodetype = $nodeSource->getPrimaryNodeType();
		
		if ($sNodetype != 'sb_system:template') {
			$sTargetNodeType = str_replace(':tpl_', ':ctn_', $sNodetype);
			$nodeNew = $nodeTarget->addNode($nodeSource->getProperty('name'), $sTargetNodeType);
			$nodeNew->setProperty('label', $nodeSource->getProperty('label'));
		}
		
		if ($sNodetype == 'sb_system:template' || $sNodetype == 'sb_system:tpl_container') {
			if ($nodeSource->getProperty('config_containertype') == 'AND') {
				$niContentelements = $nodeSource->getNodes();
				foreach ($niContentelements as $nodeElement) {
					if ($sNodetype == 'sb_system:template') {
						$this->copyContentTree($nodeElement, $nodeTarget);
					} else {
						$this->copyContentTree($nodeElement, $nodeNew);
					}
				}
			}
		}
		
	}
	
}

?>