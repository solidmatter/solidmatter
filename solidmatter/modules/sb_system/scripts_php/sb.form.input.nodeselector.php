<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage sbForm
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbInput_nodeselector extends sbInput {
	
	protected $sType = 'nodeselector';
	
	protected $aConfig = array(
		'style' => 'dropdown',
		'mode' => 'tree',
		'root' => 'sbSystem:Root',
		'omit_root' => 'TRUE',
		'nodetype' => '',
		'required' => 'FALSE'
	);
	
	protected $aOptions = array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getElement() {
		
		$elemInput = $this->domForm->createElement('sbinput');
		$elemInput->setAttribute('name', $this->sName);
		$elemInput->setAttribute('type', $this->sType);
		$elemInput->setAttribute('value', $this->mValue);
		$elemInput->setAttribute('label', $this->sLabelPath);
		foreach ($this->aConfig as $sConfig => $sValue) {
			$elemInput->setAttribute($sConfig, $sValue);
		}
		if ($this->bDisabled) {
			$elemInput->setAttribute('disabled', 'TRUE');
		}
		if ($this->sErrorLabel != '') {
			$elemInput->setAttribute('errorlabel', $this->sErrorLabel);
		}
		
		if ($this->aConfig['style'] == 'dropdown') {
			$nodeCurrentRoot = $this->crSession->getNode('//*[@uid="'.$this->aConfig['root'].'"]');
			$this->expand($nodeCurrentRoot);
			$elemOptions = $this->domForm->importNode($nodeCurrentRoot->getElementTree($this->aConfig['mode']), TRUE);
			if ($this->aConfig['omit_root'] == 'TRUE') {
				// NOTE: crappy DOMNodeList reduces its size when appending, 
				// resulting in too few passes in foreach(), therefore 2 loops
				$aChildNodes = array();
				foreach ($elemOptions->childNodes as $elemChild) {
					$aChildNodes[] = $elemChild;
				}
				foreach ($aChildNodes as $elemChild) {
					$elemInput->appendChild($elemChild);
				}
			} else {
				$elemInput->appendChild($elemOptions);
			}
		}
		
		return ($elemInput);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function expand($nodeCurrent) {
		
		// TODO: remove duplicate elements in result xml (caused by stored nodelists)
		$niChildren = $nodeCurrent->loadChildren($this->aConfig['mode'], TRUE, TRUE);
		foreach($niChildren as $nodeChild) {
			$this->expand($nodeChild);
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInput() {
		
		if ($this->aConfig['required'] == 'FALSE' && $this->mValue == '') {
			return (TRUE);
		} elseif (mb_strlen($this->mValue) == 0 && $this->aConfig['required'] == 'TRUE') {
			$this->sErrorLabel = '$locale/system/formerrors/not_null';
		}
		
		if ($this->aConfig['nodetype'] != '') {
			try {
				$nodeSelected = $this->domForm->getSession()->getNode($this->mValue);
				$aAllowedTypes = explode('|', $this->aConfig['nodetype']);
				if (!in_array($nodeSelected->getPrimaryNodeType(), $aAllowedTypes)) {
					$this->sErrorLabel = '$locale/system/formerrors/wrong_nodetype';
				}
			} catch (NodeNotFoundException $e) {
				// ignore
			}
		}
		
		if ($this->sErrorLabel == '') {
			return (TRUE);
		} else {
			return (FALSE);
		}
		
	}
	
}




?>