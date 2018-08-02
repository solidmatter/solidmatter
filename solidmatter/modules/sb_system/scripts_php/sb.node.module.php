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
		
		// nodetypes
		foreach ($domXPath->evaluate('/structure/option[@type="install" and @version="'.$sVersion.'"]/nodetypes/nodetype') as $elemNodetype) {
			$aData = array();
			$aData['nodetype'] = $elemNodetype->getAttribute('id');
			$aData['class'] = $elemNodetype->getAttribute('class') ?: NULL;
			$aData['classfile'] = $elemNodetype->getAttribute('classfile') ?: NULL;
			$aData['type'] = $elemNodetype->getAttribute('type');
			if ($aData['type'] == 'PRIMARY') {
				$aData['class'] = $aData['class'] ?: 'sbNode';
				$aData['classfile'] = $aData['classfile'] ?: 'sb.node';
			}
// 			var_dumpp($aData);
			$crRepository->changeRepositoryDefinition('nodetype', 'add', $aData);
		}
		
		// nodetype hierarchy
		foreach ($domXPath->evaluate('/structure/option[@type="install" and @version="'.$sVersion.'"]/nodetypehierarchy/parent') as $elemParentNodetype) {
			$aData = array();
			$aData['parentnodetype'] = $elemParentNodetype->getAttribute('nodetype');
			foreach ($elemParentNodetype->getElementsByTagName('child') as $elemChildNodetype) {
				$aData['childnodetype'] = $elemChildNodetype->getAttribute('nodetype');
// 				var_dumpp($aData);
				$crRepository->changeRepositoryDefinition('inheritance', 'add', $aData);
			}
		}
		
		// properties
		foreach ($domXPath->evaluate('/structure/option[@type="install" and @version="'.$sVersion.'"]/properties/nodetype') as $elemParentNodetype) {
			$aData = array();
			$aData['nodetype'] = $elemParentNodetype->getAttribute('id');
			foreach ($elemParentNodetype->getElementsByTagName('property') as $elemProperty) {
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
// 				var_dumpp($aData);
				$crRepository->changeRepositoryDefinition('property', 'add', $aData);
			}
		}
		
		// views
		foreach ($domXPath->evaluate('/structure/option[@type="install" and @version="'.$sVersion.'"]/views/view') as $elemView) {
			$aData = array();
			$aData['nodetype'] = $elemView->getAttribute('nodetype');
			$aData['view'] = $elemView->getAttribute('view');
			$aData['display'] = $elemView->getAttribute('display');
			$aData['labelpath'] = $elemView->getAttribute('labelpath') ?: NULL;
			$aData['class'] = $elemView->getAttribute('class');
			$aData['classfile'] = $elemView->getAttribute('classfile');
			$aData['order'] = $elemView->getAttribute('order') ?: NULL;
			$aData['priority'] = $elemView->getAttribute('priority') ?: 0;
// 			var_dumpp($aData);
			$crRepository->changeRepositoryDefinition('view', 'add', $aData);
		}
		
		// actions
		foreach ($domXPath->evaluate('/structure/option[@type="install" and @version="'.$sVersion.'"]/actions/action') as $elemAction) {
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
// 			var_dumpp($aData);
			$crRepository->changeRepositoryDefinition('action', 'add', $aData);
		}
		
		// modes
		foreach ($domXPath->evaluate('/structure/option[@type="install" and @version="'.$sVersion.'"]/modes/mode') as $elemMode) {
			$aData = array();
			$aData['mode'] = $elemMode->getAttribute('type');
			foreach ($elemMode->getElementsByTagName('parent') as $elemParent) {
				$aData['parentnodetype'] = $elemParent->getAttribute('nodetype');
				foreach ($elemParent->getElementsByTagName('child') as $elemChild) {
					$aData['childnodetype'] = $elemChild->getAttribute('nodetype');
					$aData['display'] = 'TRUE';
					$aData['choosable'] = 'TRUE';
// 					var_dumpp($aData);
					$crRepository->changeRepositoryDefinition('mode', 'add', $aData);
				}
			}
		}
		
		// TODO: not yet implemented
		
		foreach ($domXPath->evaluate('/structure/option[@type="install" and @version="'.$sVersion.'"]/lifecycles/transitions') as $elemLifecycle) {
			
		}
		foreach ($domXPath->evaluate('/structure/option[@type="install" and @version="'.$sVersion.'"]/ontology/relation') as $elemRelation) {
			
		}
		foreach ($domXPath->evaluate('/structure/option[@type="install" and @version="'.$sVersion.'"]/registry/entry') as $elemRegistry) {
			
		}
		
		
		foreach ($domXPath->evaluate('/structure/option[@type="install" and @version="'.$sVersion.'"]/version') as $elemVersion) {
			$stmtInstalled = $this->crSession->prepareKnown('sbCR/module/installed');
			$stmtInstalled->bindValue('name', $this->getName());
			$stmtInstalled->bindValue('title', $elemVersion->getAttribute('title'));
			$stmtInstalled->bindValue('mainversion', $elemVersion->getAttribute('main'));
			$stmtInstalled->bindValue('subversion', $elemVersion->getAttribute('sub'));
			$stmtInstalled->bindValue('bugfixversion', $elemVersion->getAttribute('bugfix'));
			$stmtInstalled->bindValue('versioninfo', $elemVersion->getAttribute('info'));
			$stmtInstalled->bindValue('uninstallable', 'TRUE');
			$stmtInstalled->bindValue('active', 'TRUE');
			$stmtInstalled->execute();
		}
		
		$crRepository->changeRepositoryDefinition('commit');
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function uninstall($sVersion) {
		
		$domStructure = $this->getStructure();
		$domXPath = new DOMXPath($domStructure);
		
		$crRepository = $this->crSession->getRepository();
		
		$crRepository->changeRepositoryDefinition('begin');
		
		// nodetypes
		foreach ($domXPath->evaluate('/structure/option[@type="install" and @version="'.$sVersion.'"]/nodetypes/nodetype') as $elemNodetype) {
			$aData = array();
			$aData['nodetype'] = $elemNodetype->getAttribute('id');
			$aData['class'] = $elemNodetype->getAttribute('class') ?: NULL;
			$aData['classfile'] = $elemNodetype->getAttribute('classfile') ?: NULL;
			$aData['type'] = $elemNodetype->getAttribute('type');
			if ($aData['type'] == 'PRIMARY') {
				$aData['class'] = $aData['class'] ?: 'sbNode';
				$aData['classfile'] = $aData['classfile'] ?: 'sb.node';
			}
			// 			var_dumpp($aData);
			$crRepository->changeRepositoryDefinition('nodetype', 'add', $aData);
		}
		
		$crRepository->changeRepositoryDefinition('commit');
		
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param string Property type, ignored and defaults to auxiliary
	 * 
	 * @return
	 */
	public function loadProperties($sType = 'AUXILIARY', $bOnlyProperties = FALSE) {
		
		$this->initPropertyDefinitions();
		
		$stmtGetProperties = $this->prepareKnown($this->aQueries['loadProperties']['auxiliary']);
		$stmtGetProperties->bindValue(':name', $this->getName(), PDO::PARAM_STR);
		$stmtGetProperties->execute();
		$aProperties = $stmtGetProperties->fetch(PDO::FETCH_ASSOC);
		$stmtGetProperties->closeCursor();
		
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