<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.cr.actiondefinition');

//------------------------------------------------------------------------------
/** TODO: complete separate this from sbCR and put into sbSystem?
*/
class sbCR_ViewDefinition {
	
	protected $aViewInformation = array(
		'NodeTypeName' => '',
		'ViewName' => '',
		'Class' => '',
		'ClassFile' => '',
		'isVisible' => FALSE,
		'Priority' => 0,
		'ActionDefinitions' => NULL,
	);
	
	protected $crRepositoryStructure = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($crRepositoryStructure, $sNodeTypeName, $sViewName, $sClass, $sClassFile, $bVisible, $iPriority) {
		
		// store basic info
		$this->crRepositoryStructure = $crRepositoryStructure;
		$this->aViewInformation['NodeTypeName'] = $sNodeTypeName;
		$this->aViewInformation['ViewName'] = $sViewName;
		$this->aViewInformation['Class'] = $sClass;
		$this->aViewInformation['ClassFile'] = $sClassFile;
		$this->aViewInformation['isVisible'] = $bVisible;
		$this->aViewInformation['Priority'] = $iPriority;
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function getNodeTypeName() {
		return ($this->aViewInformation['NodeTypeName']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function getName() {
		return ($this->aViewInformation['ViewName']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function getClass() {
		return ($this->aViewInformation['Class']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function getClassFile() {
		return ($this->aViewInformation['ClassFile']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function isVisible() {
		return ($this->aViewInformation['isVisible']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function getPriority() {
		return ($this->aViewInformation['Priority']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function getActionDefinition($sActionName = NULL) {
		
		$aActionDetails = NULL;
		
		if ($this->getName() == 'security' || $this->getName() == 'debug') { // fixed details
			
			$aActionDetails = $this->getCustomActionDetails($sActionName);
			
		} else { // normal actions based on view
			
			if ($sActionName == NULL) {
				$stmtAction = $this->crRepositoryStructure->getSession()->prepareKnown('sbSystem/node/loadActionDetails/default');
				$stmtAction->bindValue(':nodetype', $this->aViewInformation['NodeTypeName'], PDO::PARAM_STR);
				$stmtAction->bindValue(':view', $this->aViewInformation['ViewName'], PDO::PARAM_STR);
				$stmtAction->execute();
			} else {
				$stmtAction = $this->crRepositoryStructure->getSession()->prepareKnown('sbSystem/node/loadActionDetails/given');
				$stmtAction->bindValue(':nodetype', $this->aViewInformation['NodeTypeName'], PDO::PARAM_STR);
				$stmtAction->bindValue(':view', $this->aViewInformation['ViewName'], PDO::PARAM_STR);
				$stmtAction->bindValue(':action', $sActionName, PDO::PARAM_STR);
				$stmtAction->execute();
			}
			
			foreach ($stmtAction as $aRow) {
				$aActionDetails = $aRow;
			}
			
			$stmtAction->closeCursor();
			
		}
		
		if ($aActionDetails == NULL) {
			throw new ActionUndefinedException('action "'.$sActionName.'" is not defined in view "'.$this->getName().'" of nodetype "'.$this->getNodeTypeName().'"');	
		}
		
		// transform default flag to priority
		$iPriority = 0;
		if (isset($aActionDetails['b_default']) && $aActionDetails['b_default'] == 'TRUE') {
			$iPriority = 1000;
		}
		// overload class
		$sClass = $this->aViewInformation['Class'];
		if (isset($aActionDetails['s_class']) && $aActionDetails['s_class'] != NULL) {
			$sClass = $aRow['s_class'];
		}
		// overload classfile
		$sClassFile = $this->aViewInformation['ClassFile'];
		if (isset($aActionDetails['s_classfile']) && $aActionDetails['s_classfile'] != NULL) {
			$sClassFile = $aRow['s_classfile'];
		}
		// transform uselocale flag
		$bUseLocale = FALSE;
		if ($aActionDetails['b_uselocale'] == 'TRUE') {
			$bUseLocale = TRUE;
		}
		// transform isrecallable flag
		$bIsRecallable = FALSE;
		if ($aActionDetails['b_isrecallable'] == 'TRUE') {
			$bIsRecallable = TRUE;
		}
		
		// create object
		$adCurrentAction = new sbCR_ActionDefinition(
			$this->crRepositoryStructure,
			$this->aViewInformation['NodeTypeName'],
			$this->aViewInformation['ViewName'],
			$aActionDetails['s_action'],
			$sClass,
			$sClassFile,
			$iPriority,
			$aActionDetails['e_outputtype'],
			$aActionDetails['s_stylesheet'],
			$aActionDetails['s_mimetype'],
			$bUseLocale,
			$bIsRecallable
		);
		
		return ($adCurrentAction);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function getDeclaredActionDefinitions() {
		throw new LazyBastardException('not yet implemented, usually you want only one action (but should be implemented to support action aggregation, too!)');
		return ($this->aNodeTypeInformation['ActionDefinitions']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getDefaultAction() {
		throw new LazyBastardException('not yet implemented, use getActionDefinition() without parameter for now');
		return ($this->aNodeTypeInformation['ViewDefinitions']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getCustomActionDetails($sActionName = NULL) {
		
		$aActionDetails = NULL;
		
		if ($this->getName() == 'debug') {
			$aActionDetails = array(
				's_action' => 'debug',
				'e_outputtype' => 'rendered',
				's_stylesheet' => 'sb_system:node.debug.xsl',
				's_mimetype' => 'text/html',
				'b_uselocale' => 'TRUE',
				'b_isrecallable' => 'FALSE',
			);
		}
		
		if ($this->getName() == 'security') {
			$aSecurityActions = array(
				'display' => array(
					's_action' => 'display',
					'e_outputtype' => 'rendered',
					's_stylesheet' => 'sb_system:node.security.xsl',
					's_mimetype' => 'text/html',
					'b_uselocale' => 'TRUE',
					'b_isrecallable' => 'FALSE',
				),
				'changeInheritance' => array(
					's_action' => 'changeInheritance',
					'e_outputtype' => 'rendered',
					's_stylesheet' => 'sb_system:node.security.xsl',
					's_mimetype' => 'text/html',
					'b_uselocale' => 'TRUE',
					'b_isrecallable' => 'FALSE',
				),
				'editAuthorisations' => array(
					's_action' => 'editAuthorisations',
					'e_outputtype' => 'rendered',
					's_stylesheet' => 'sb_system:node.security.editauthorisations.xsl',
					's_mimetype' => 'text/html',
					'b_uselocale' => 'TRUE',
					'b_isrecallable' => 'FALSE',
				),
				'saveAuthorisations' => array(
					's_action' => 'saveAuthorisations',
					'e_outputtype' => 'rendered',
					's_stylesheet' => 'sb_system:node.security.editauthorisations.xsl',
					's_mimetype' => 'text/html',
					'b_uselocale' => 'TRUE',
					'b_isrecallable' => 'FALSE',
				),
				'addUser' => array(
					's_action' => 'addUser',
					'e_outputtype' => 'rendered',
					's_stylesheet' => 'sb_system:node.security.xsl',
					's_mimetype' => 'text/html',
					'b_uselocale' => 'TRUE',
					'b_isrecallable' => 'FALSE',
				),
				'removeUser' => array(
					's_action' => 'removeUser',
					'e_outputtype' => 'rendered',
					's_stylesheet' => 'sb_system:node.security.xsl',
					's_mimetype' => 'text/html',
					'b_uselocale' => 'TRUE',
					'b_isrecallable' => 'FALSE',
				),
			);
			if ($sActionName == NULL) {
				$sActionName = 'display';	
			}
			if (isset($aSecurityActions[$sActionName])) {
				$aActionDetails = $aSecurityActions[$sActionName];
			}
		}
		
		return ($aActionDetails);
		
	}
	
}

?>