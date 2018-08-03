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
class sbNode_module extends sbNode {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function __setQueries() {
		parent::__setQueries();
		$this->aQueries['loadProperties']['auxiliary'] = 'sbSystem/module/loadProperties/auxiliary';
		$this->aQueries['saveProperties']['auxiliary'] = 'sbSystem/module/saveProperties/auxiliary';
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	protected function __init() {
		$this->loadProperties();
		if ($this->isInstalled()) {
			$this->setProperty('label', $this->getProperty('label').' ('.$this->getProperty('version').')');
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	protected function isPrimaryParent($nodeParent) {
		if ($nodeParent->getNodetype() == 'sbSystem:Modules') {
			return (TRUE);
		} else {
			return (FALSE);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Returns the primary parent of this node, which is always the modules-node.
	 * @return Node the primary parent
	 */
	protected function getPrimaryParent() {
		return ($this->crSession->getNode('sbSystem:Modules'));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function save() {
		
		// TODO: implement logic to override empty default values - also install/uninstall
		
		parent::save();
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function getStructure() {
		
		$sModule = $this->getProperty('name');
		
		// include properties
		$sStructureFile = 'modules/'.$sModule.'/structure.xml';
		$domStructure = new DOMDocument();
		$domStructure->load($sStructureFile);
		
		return ($domStructure);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function install($sVersion) {
		
		$domStructure = $this->getStructure();
		$domXPath = new DOMXPath($domStructure);
		
		$crRepository = $this->crSession->getRepository();
		$crRepository->changeRepositoryDefinition('begin');
		
		// match the correct segment and delegate the actions
		foreach ($domXPath->evaluate('/structure/option[@type="install" and @version="'.$sVersion.'"]/*') as $elemAction) {
			$this->performAction($elemAction);
		}
		
		$crRepository->changeRepositoryDefinition('commit');
		$this->__init();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function uninstall() {
		
		$domStructure = $this->getStructure();
		$domXPath = new DOMXPath($domStructure);
		
		$crRepository = $this->crSession->getRepository();
		$crRepository->changeRepositoryDefinition('begin');
		
		$sVersion = $this->getProperty('version');
		
		// nodetypes
		foreach ($domXPath->evaluate('/structure/option[@type="uninstall" and @version="'.$sVersion.'"]/*') as $elemAction) {
			$this->performAction($elemAction);
		}
		
		$crRepository->changeRepositoryDefinition('commit');
		$this->__init();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function update($sToVersion) {
		
		$domStructure = $this->getStructure();
		$domXPath = new DOMXPath($domStructure);
		
		$crRepository = $this->crSession->getRepository();
		$crRepository->changeRepositoryDefinition('begin');
		
		$sVersion = $this->getProperty('version');
		
		// nodetypes
		foreach ($domXPath->evaluate('/structure/option[@type="update" and @from="'.$sVersion.'" and @to = "'.$sToVersion.'"]/*') as $elemAction) {
			$this->performAction($elemAction);
		}
		
		$crRepository->changeRepositoryDefinition('commit');
		$this->__init();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param string Property type, ignored and defaults to auxiliary
	 * 
	 * @return
	 */
	public function loadProperties($sType = 'AUXILIARY', $bOnlyProperties = FALSE) {
		
		// first clear all dynamic attributes
		$this->elemSubject->removeAttribute('displaytype');
		$this->elemSubject->removeAttribute('installed');
		$this->elemSubject->removeAttribute('version');
		
		// retrieve property data
		$this->initPropertyDefinitions();
		
		$stmtGetProperties = $this->prepareKnown($this->aQueries['loadProperties']['auxiliary']);
		$stmtGetProperties->bindValue(':name', $this->getName(), PDO::PARAM_STR);
		$stmtGetProperties->execute();
		$aProperties = $stmtGetProperties->fetch(PDO::FETCH_ASSOC);
		$stmtGetProperties->closeCursor();
		
		// loop through properties
		foreach ($this->crPropertyDefinitionCache as $sName => $aDetails) {
			if (!isset($this->aModifiedAttributes[$sName]) && $aDetails['e_storagetype'] == 'AUXILIARY') {
				// TODO: skip s_name for now, gives a warning - more elegant way needed
				if ($aDetails['s_auxname'] != 's_name') {
					$this->elemSubject->setAttribute($sName, $aProperties[$aDetails['s_auxname']]);
				}
				if (!isset($aProperties['b_active'])) {
					$this->elemSubject->setAttribute('displaytype', 'sbSystem_Module_inactive');
					$this->elemSubject->setAttribute('installed', 'FALSE');
				} else {
					$this->elemSubject->setAttribute('displaytype', 'sbSystem_Module');
					$this->elemSubject->setAttribute('installed', 'TRUE');
					$sVersion = $aProperties['n_mainversion'].'.'.$aProperties['n_subversion'].'.'.$aProperties['n_bugfixversion'];
					$this->elemSubject->setAttribute('version', $sVersion);
				}
			}
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param DOMElement Element of the action block
	 * @return
	 */
	protected function performAction(DOMElement $elemAction) {
		
		DEBUG(__CLASS__.': performing change '.$elemAction->nodeName.' ('.$elemAction->getAttribute('action').')');
		
		$crRepository = $this->crSession->getRepository();
		
		// nodetypes
		if ($elemAction->nodeName == 'nodetypes' && $elemAction->getAttribute('action') == '') {
			foreach ($elemAction->getElementsByTagName('nodetype') as $elemNodetype) {
				$aData = array();
				$aData['nodetype'] = $elemNodetype->getAttribute('id');
				$aData['class'] = $elemNodetype->getAttribute('class') ?: NULL;
				$aData['classfile'] = $elemNodetype->getAttribute('classfile') ?: NULL;
				$aData['type'] = $elemNodetype->getAttribute('type');
				if ($aData['type'] == 'PRIMARY') {
					$aData['class'] = $aData['class'] ?: 'sbNode';
					$aData['classfile'] = $aData['classfile'] ?: 'sb.node';
				}
				$crRepository->changeRepositoryDefinition('nodetype', 'add', $aData);
			}
		}
		if ($elemAction->nodeName == 'nodetypes' && $elemAction->getAttribute('action') == 'remove') {
			foreach ($elemAction->getElementsByTagName('nodetype') as $elemNodetype) {
				$aData = array();
				$aData['nodetype'] = $elemNodetype->getAttribute('id');
				$crRepository->changeRepositoryDefinition('nodetype', 'remove', $aData);
			}
		}
		
		// nodetype hierarchy
		if ($elemAction->nodeName == 'nodetypehierarchy' && $elemAction->getAttribute('action') == '') {
			foreach ($elemAction->getElementsByTagName('parent') as $elemParentNodetype) {
				$aData = array();
				$aData['parentnodetype'] = $elemParentNodetype->getAttribute('nodetype');
				foreach ($elemParentNodetype->getElementsByTagName('child') as $elemChildNodetype) {
					$aData['childnodetype'] = $elemChildNodetype->getAttribute('nodetype');
					$crRepository->changeRepositoryDefinition('inheritance', 'add', $aData);
				}
			}
		}
		if ($elemAction->nodeName == 'nodetypehierarchy' && $elemAction->getAttribute('action') == 'remove') {
			foreach ($elemAction->getElementsByTagName('parent') as $elemParentNodetype) {
				$aData = array();
				$aData['parentnodetype'] = $elemParentNodetype->getAttribute('nodetype');
				foreach ($elemParentNodetype->getElementsByTagName('child') as $elemChildNodetype) {
					$aData['childnodetype'] = $elemChildNodetype->getAttribute('nodetype');
					$crRepository->changeRepositoryDefinition('inheritance', 'remove', $aData);
				}
			}
		}
		
		// properties
		if ($elemAction->nodeName == 'properties' && $elemAction->getAttribute('action') == '') {
			foreach ($elemAction->getElementsByTagName('nodetype') as $elemNodetype) {
				$aData = array();
				$aData['nodetype'] = $elemNodetype->getAttribute('id');
				foreach ($elemNodetype->getElementsByTagName('property') as $elemProperty) {
					$aData['attributename'] = $elemProperty->getAttribute('name');
					$aData['type'] = $elemProperty->getAttribute('type');
					$aData['internaltype'] = $elemProperty->getAttribute('internaltype') ?: NULL;
					$aData['showinproperties'] = $elemProperty->getAttribute('show') ?: 'TRUE';
					$aData['labelpath'] = $elemProperty->getAttribute('labelpath');
					$aData['storagetype'] = $elemProperty->getAttribute('storagetype') ?: 'EXTERNAL';
					$aData['auxname'] = $elemProperty->getAttribute('auxname') ?: NULL;
					$aData['order'] = $elemProperty->getAttribute('order');
					$aData['protected'] = $elemProperty->getAttribute('protected') ?: 'FALSE';
					$aData['protectedoncreation'] = $elemProperty->getAttribute('protectedoncreation') ?: 'FALSE';
					$aData['multiple'] = 'FALSE';
					$aData['defaultvalues'] = $elemProperty->getAttribute('defaultvalues') ?: NULL;
					$aData['descriptionpath'] = $elemProperty->getAttribute('descriptionpath');
					$crRepository->changeRepositoryDefinition('property', 'add', $aData);
				}
			}
		}
		
		// views
		if ($elemAction->nodeName == 'views' && $elemAction->getAttribute('action') == '') {
			foreach ($elemAction->getElementsByTagName('view') as $elemView) {
				$aData = array();
				$aData['nodetype'] = $elemView->getAttribute('nodetype');
				$aData['view'] = $elemView->getAttribute('view');
				$aData['display'] = $elemView->getAttribute('display');
				$aData['labelpath'] = $elemView->getAttribute('labelpath') ?: NULL;
				$aData['class'] = $elemView->getAttribute('class');
				$aData['classfile'] = $elemView->getAttribute('classfile');
				$aData['order'] = $elemView->getAttribute('order') ?: NULL;
				$aData['priority'] = $elemView->getAttribute('priority') ?: 0;
				$crRepository->changeRepositoryDefinition('view', 'add', $aData);
			}
		}
		
		// actions
		if ($elemAction->nodeName == 'actions' && $elemAction->getAttribute('action') == '') {
			// not good, elemAction overwritten!
			foreach ($elemAction->getElementsByTagName('action') as $elemAction) {
				$aData = array();
				$aData['nodetype'] = $elemAction->getAttribute('nodetype');
				$aData['view'] = $elemAction->getAttribute('view');
				$aData['action'] = $elemAction->getAttribute('action');
				$aData['default'] = $elemAction->getAttribute('default');
				$aData['class'] = NULL;
				$aData['classfile'] = NULL;
				$aData['outputtype'] = $elemAction->getAttribute('outputtype');
				$aData['stylesheet'] = $elemAction->getAttribute('stylesheet') ?: NULL;
				$aData['mimetype'] = $elemAction->getAttribute('mimetype') ?: NULL;
				$aData['uselocale'] = $elemAction->getAttribute('uselocale');
				$aData['isrecallable'] = $elemAction->getAttribute('isrecallable');
				$crRepository->changeRepositoryDefinition('action', 'add', $aData);
			}
		}
		
		// modes
		if ($elemAction->nodeName == 'modes' && $elemAction->getAttribute('action') == '') {
			foreach ($elemAction->getElementsByTagName('mode')  as $elemMode) {
				$aData = array();
				$aData['mode'] = $elemMode->getAttribute('type');
				foreach ($elemMode->getElementsByTagName('parent') as $elemParent) {
					$aData['parentnodetype'] = $elemParent->getAttribute('nodetype');
					foreach ($elemParent->getElementsByTagName('child') as $elemChild) {
						$aData['childnodetype'] = $elemChild->getAttribute('nodetype');
						$aData['display'] = 'TRUE';
						$aData['choosable'] = 'TRUE';
						$crRepository->changeRepositoryDefinition('mode', 'add', $aData);
					}
				}
			}
		}
		
		if ($elemAction->nodeName == 'ontology' && $elemAction->getAttribute('action') == '') {
			foreach ($elemAction->getElementsByTagName('relation') as $elemRelation) {
				$aData = array();
				$aData['sourcenodetype'] = $elemRelation->getAttribute('origin');
				$aData['relation'] = $elemRelation->getAttribute('forward');
				$aData['reverserelation'] = $elemRelation->getAttribute('backward');
				$aData['targetnodetype'] = $elemRelation->getAttribute('destination');
				$crRepository->changeRepositoryDefinition('relation', 'add', $aData);
			}
		}
				
		if ($elemAction->nodeName == 'registry' && $elemAction->getAttribute('action') == '') {
			foreach ($elemAction->getElementsByTagName('entry') as $elemEntry) {
				$aData = array();
				$aData['key'] = $elemEntry->getAttribute('key');
				$aData['type'] = $elemEntry->getAttribute('type');
				$aData['internaltype'] = $elemEntry->getAttribute('internaltype') ?: NULL;
				$aData['userspecific'] = $elemEntry->getAttribute('userspecific') ?: 'FALSE';
				$aData['defaultvalue'] = $elemEntry->getAttribute('defaultvalue');
				$aData['comment'] = $elemEntry->getAttribute('comment') ?: NULL;
				$crRepository->changeRepositoryDefinition('registry', 'add', $aData);
			}
		}
		if ($elemAction->nodeName == 'registry' && $elemAction->getAttribute('action') == 'remove') {
			foreach ($elemAction->getElementsByTagName('entry') as $elemEntry) {
				$aData = array();
				$aData['key'] = $elemEntry->getAttribute('key');
				$crRepository->changeRepositoryDefinition('registry', 'remove', $aData);
			}
		}
		
		// TODO: not yet implemented
		if ($elemAction->nodeName == 'lifecycles' && $elemAction->getAttribute('action') == '') {
			foreach ($elemAction->getElementsByTagName('transition') as $elemTransition) {
				
			}
		}
		
		// change the module itself
		if ($elemAction->nodeName == 'version' && $elemAction->getAttribute('action') == '') {
			$stmtInstalled = $this->crSession->prepareKnown('sbCR/module/installed');
			$stmtInstalled->bindValue('name', $this->getName());
			$stmtInstalled->bindValue('title', $elemAction->getAttribute('title'));
			$stmtInstalled->bindValue('mainversion', $elemAction->getAttribute('main'));
			$stmtInstalled->bindValue('subversion', $elemAction->getAttribute('sub'));
			$stmtInstalled->bindValue('bugfixversion', $elemAction->getAttribute('bugfix'));
			$stmtInstalled->bindValue('versioninfo', $elemAction->getAttribute('info'));
			$stmtInstalled->bindValue('uninstallable', 'TRUE');
			$stmtInstalled->bindValue('active', 'TRUE');
			$stmtInstalled->execute();
		}
		if ($elemAction->nodeName == 'version' && $elemAction->getAttribute('action') == 'remove') {
			$stmtInstalled = $this->crSession->prepareKnown('sbCR/module/uninstalled');
			$stmtInstalled->bindValue('name', $this->getName());
			$stmtInstalled->execute();
		}
		
	}
	
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param 
	 *
	 * @return
	 */
	public function isInstalled() {
		if ($this->elemSubject->getAttribute('installed') == 'TRUE') {
			return (TRUE);
		}
		return (FALSE);
	}
	
}

?>