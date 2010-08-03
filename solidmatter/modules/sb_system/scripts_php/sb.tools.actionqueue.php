<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Tools
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbActionQueue {
	
	protected $aActions = array();
	
	protected $crSession = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setSession($crSession) {
		$this->crSession = $crSession;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addAction($sType, $nodeSubject, $aOptions) {
		$aAction = array(
			'uuid' => $nodeSubject->getProperty('jcr:uuid'),
			'label' => $nodeSubject->getProperty('label'),
			'nodetype' => $nodeSubject->getPrimaryNodeType(),
			'type' => $sType,
		);		
		$this->aActions[] = array_merge($aAction, $aOptions);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getActions() {
		return ($this->aActions);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute() {
		
		foreach ($this->aActions as $iKey => $aAction) {
			
			if ($aAction['ignore'] == TRUE) {
				continue;
			}
			
			$nodeCurrent = $this->crSession->getNodeByIdentifier($aAction['uuid']);
			
			switch ($aAction['type']) {
				
				case 'relabel':
					$nodeCurrent->setProperty('label', $aAction['new_label']);
					$nodeCurrent->save();
					break;
				
				case 'rename':
					$nodeCurrent->setProperty('name', $aAction['new_name']);
					$nodeCurrent->save();
					break;
				
				case 'change_property':
					// TODO: fix node class so that this triggering becomes unneccessary (loads arbitrary properties)
					$nodeCurrent->getProperty($aAction['property']);
					$nodeCurrent->setProperty($aAction['property'], $aAction['new_content']);
					$nodeCurrent->save();
					break;
					
				case 'rename_file':
					$nodeCurrent->renameFile($aAction['new_filename']);
					$nodeCurrent->save();
					break;
					
				case 'retag_mp3':
					/*$nodeCurrent->setTag($aAction['tag'], $aAction['new_filename']);
					$nodeCurrent->save();*/
					break;
				
			}
			
			$aAction['executed'] = 'TRUE';
			$aAction['success'] = 'TRUE';
			$this->aActions[$iKey] = $aAction;
			
		}
		
	}
	
}

?>