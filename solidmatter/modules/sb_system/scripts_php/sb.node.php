<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.cr.node');
import('sb.pdo.system.queries');

//------------------------------------------------------------------------------
/**
*/
class sbNode extends sbCR_Node {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @var 
	*/
	public $aGetElementFlags		= array(
		'essential_properties' => TRUE, // TODO: currently unused
		'secondary_properties' => TRUE,
		'deep' => FALSE,
		'parents' => FALSE,
		'children' => FALSE,
		'ancestors' => FALSE,
		'views' => FALSE,
		'content' => FALSE,
		'tags' => TRUE,
		'branchtags' => TRUE,
		'votes' => TRUE,
		'auth_supported' => FALSE,
		'auth_user' => FALSE,
		'auth_local' => FALSE,
		'auth_inherited' => FALSE,
	);
	
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @var 
	*/
	protected $aViews				= NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @var 
	*/
	public $niAncestors				= NULL;
	/**
	* 
	* @var 
	*/ 
	public $niParents				= NULL;
	/**
	* child nodes that are stored after acquireing them via getNodes resp. getChildren
	* @var array of sbCR_NodeIterators
	*/ 
	public $aChildNodes				= array();
	/**
	* nodes that are stored as relevant "content" of this node (not necessarily children)
	* @var 
	*/// @var array of sbCR_NodeIterators
	public $aContentNodes			= array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @var 
	*/
	protected $aSupportedAuthorisations = NULL;
	/**
	* 
	* @var 
	*/
	protected $aInheritedAuthorisations = NULL;
	/**
	* 
	* @var 
	*/
	protected $aLocalAuthorisations		= NULL;
	/**
	* 
	* @var 
	*/
	protected $aMergedAuthorisations	= NULL;
	/**
	* 
	* @var 
	*/
	protected $aUserAuthorisations		= NULL; // caution, contains only current user auth!!!
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @var 
	*/
	protected $aVotes 				= NULL;
	/**
	* 
	* @var 
	*/
	protected $aVoteChanges			= array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @var 
	*/
	protected $aTags 				= NULL;
	/**
	* 
	* @var 
	*/
	protected $aNewTags				= array();
	/**
	* 
	* @var 
	*/
	protected $aBranchTags			= NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function __setQueries() {
		
		parent::__setQueries();
		
		// information about the node type
		$this->aQueries['actions/getDetails/given']					= 'sbSystem/node/loadActionDetails/given';
		$this->aQueries['actions/getDetails/default']				= 'sbSystem/node/loadActionDetails/default';
		//$this->aQueries['sbSystem/node/getAllowedSubtypes']		= 'sbSystem/node/getAllowedSubtypes';
		
		// voting
		$this->aQueries['voting/placeVote']							= 'sbSystem/voting/placeVote';
		$this->aQueries['voting/removeVote']						= 'sbSystem/voting/removeVote';
		$this->aQueries['voting/removeAllVotes']					= 'sbSystem/voting/removeAllVotes';
		$this->aQueries['voting/getUserVote']						= 'sbSystem/voting/getVote/byUser';
		$this->aQueries['voting/getAverageVote']					= 'sbSystem/voting/getVote/average';
		$this->aQueries['voting/getAllVotes']						= 'sbSystem/voting/getVotes';
		$this->aQueries['voting/getUserVotes']						= 'sbSystem/voting/getUserVotes';
		
		// tagging
		$this->aQueries['tagging/addTagToNode']						= 'sbSystem/tagging/node/addTag';
		$this->aQueries['tagging/removeTagFromNode']				= 'sbSystem/tagging/node/removeTag';
		$this->aQueries['tagging/removeTagsFromNode']				= 'sbSystem/tagging/node/removeTags';
		$this->aQueries['tagging/getAllNodeTags']					= 'sbSystem/tagging/node/getTags';
		$this->aQueries['tagging/getAllBranchTags']					= 'sbSystem/tagging/node/getBranchTags';
		$this->aQueries['tagging/getBranchNodes']					= 'sbSystem/tagging/getItems/byTagID';
		$this->aQueries['tagging/getTagID']							= 'sbSystem/tagging/tags/getID';
		$this->aQueries['tagging/getTag']							= 'sbSystem/tagging/tags/getTag';
		$this->aQueries['tagging/createNewTag']						= 'sbSystem/tagging/tags/addTag';
		$this->aQueries['tagging/getAllTags']						= 'sbSystem/tagging/tags/getAll';
		$this->aQueries['tagging/increasePopularity']				= 'sbSystem/tagging/tags/increasePopularity';
		
		// relations
		$this->aQueries['relations/getRelations']					= 'sbSystem/relations/getRelations';
		$this->aQueries['relations/getSupportedRelations']			= 'sbSystem/relations/getSupportedRelations';
		$this->aQueries['relations/getPossibleTargets']				= 'sbSystem/relations/getPossibleTargets';
		$this->aQueries['relations/addRelation']					= 'sbSystem/relations/addRelation';
		$this->aQueries['relations/removeRelation']					= 'sbSystem/relations/removeRelation';
		
		// authorisation stuff
		$this->aQueries['loadLocalAuthorisations']					= 'sbSystem/node/loadAuthorisations/local';
		$this->aQueries['loadLocalEntityAuthorisations']			= 'sbSystem/node/loadAuthorisations/local/byEntity';
		//$this->aQueries['setAuthorisation']							= 'sbSystem/node/setAuthorisation';
		
		// special actions
		$this->aQueries['trash/updateNode']							= 'sbSystem/node/moveToTrash/updateNode';
		$this->aQueries['trash/updateChildren']						= 'sbSystem/node/moveToTrash/updateChildren';
		$this->aQueries['trash/recoverInfo']						= 'sbSystem/node/recoverFromTrash/getInfo';
		$this->aQueries['trash/recover']							= 'sbSystem/node/recoverFromTrash';
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function __init() {
		parent::__init();
	}
	
	//--------------------------------------------------------------------------
	// save/move
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function addSaveTask($sTaskType, $aOptions = NULL) {
		switch ($sTaskType) {
			case 'add_tag':
			case 'remove_tag':
				$this->aSaveTasks[$sTaskType][] = $aOptions;
				break;
			case 'move_to_trash':
				$this->aSaveTasks[$sTaskType] = TRUE;
				break;
			case 'recover_from_trash':
				$this->aSaveTasks[$sTaskType] = TRUE;
				break;
			default:
				parent::addSaveTask($sTaskType, $aOptions);
		}
		$this->crSession->addSaveTask('save_node', array('subject' => $this));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Extends the save() method of sbCR_Node.
	* 
	* @param 
	* @return 
	*/
	public function save() {
		
		// first process parent tasks, but wrap this in a new transaction
		$this->crSession->beginTransaction('sbNode::save');
		parent::save();
		
		// anything to do?
		if (count($this->aSaveTasks) == 0) {
			$this->crSession->commit('sbNode::save');
			return (FALSE);
		}
		
		// TODO: first cycle tag and vote tasks to eliminate dupes and unnessessary steps
		
		// work sbNode tasks
		foreach ($this->aSaveTasks as $sTaskType => $aOptions) {
			
			switch ($sTaskType) {
				
				case 'remove_tag':
					
					foreach ($aOptions as $iKey => $aDetails) {
						
						$sTag = $aDetails['tag'];
						$iTagID = $this->getTagID($sTag);
						if (!$iTagID) {
							throw new sbException('tag "'.$sTag.'" does not exist');
						}
						$stmtRemove = $this->prepareKnown('tagging/removeTagFromNode');
						$stmtRemove->bindValue(':subject_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
						$stmtRemove->bindValue(':tag_id', $iTagID, PDO::PARAM_STR);
						$stmtRemove->execute();
						
						unset($this->aTags[$sTag]);
						unset($this->aSaveTasks['remove_tag'][$iKey]);
						
					}
					
					unset($this->aSaveTasks['remove_tag']);
					break;
					
				case 'add_tag':
					
					foreach ($aOptions as $iKey => $aDetails) {
						
						$sTag = $aDetails['tag'];
						$iTagID = $this->getTagID($sTag);
						if (!$iTagID) {
							$iTagID = $this->createNewTag($sTag);
						}
						$stmtAddTag = $this->prepareKnown('tagging/addTagToNode');
						$stmtAddTag->bindValue('subject_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
						$stmtAddTag->bindValue('tag_id', $iTagID, PDO::PARAM_INT);
						$stmtAddTag->execute();
						$this->aTags[$sTag] = TRUE;
						
						unset($this->aNewTags[$sTag]);
						unset($this->aSaveTasks['add_tag'][$iKey]);
						
					}
					
					unset($this->aSaveTasks['add_tag']);
					
					break;
					
				case 'move_to_trash':
					
					$sParentUUID = $this->elemSubject->getAttribute('parent');
					$stmtTrash = $this->prepareKnown('trash/updateNode');
					$stmtTrash->bindValue('user_uuid', User::getUUID(), PDO::PARAM_STR);
					$stmtTrash->bindValue('parent_uuid', $sParentUUID, PDO::PARAM_STR);
					$stmtTrash->bindValue('subject_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
//					$stmtTrash->debug(TRUE);
					$stmtTrash->execute();
					
					$sMPath = $this->getMPath();
					$stmtTrash = $this->prepareKnown('trash/updateChildren');
					$stmtTrash->bindValue('mpath', $sMPath, PDO::PARAM_STR);
					$stmtTrash->execute();
					
					unset($this->aSaveTasks['move_to_trash']);
					
					break;
					
				case 'recover_from_trash':
					
					// TODO: find a way to recover nodes without affecting seperately deleted children (the mpaths overlap)
					
					$sParentUUID = $this->elemSubject->getAttribute('parent');
					$stmtTrash = $this->prepareKnown('trash/recoverInfo');
					$stmtTrash->bindValue('parent_uuid', $sParentUUID, PDO::PARAM_STR);
					$stmtTrash->bindValue('subject_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
					$stmtTrash->execute();
					foreach ($stmtTrash as $aRow) {
						$sMPath = $aRow['mpath'];
					}
					
					$sMPath = $aRow['mpath'].$this->getMPath(TRUE);
					$stmtTrash = $this->prepareKnown('trash/recover');
					$stmtTrash->bindValue('parent_uuid', $sParentUUID, PDO::PARAM_STR);
					$stmtTrash->bindValue('subject_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
					$stmtTrash->bindValue('mpath', $sMPath, PDO::PARAM_STR);
					$stmtTrash->execute();
					
					unset($this->aSaveTasks['recover_from_trash']);
					
					break;
					
			}
		}
		
		$this->crSession->commit('sbNode::save');
		
		return (TRUE);
		
	}
	
	
	//--------------------------------------------------------------------------
	/**
	* Returns a DOM element representation of this node and associated information.
	* @param boolean set to true if getElement should traverse child and content nodes
	* @param boolean 
	* @return 
	*/
	public function getElement($aCustomFlags = NULL) {
		
		// use a copy of the element (might be used multiple times)
		$elemSubject = $this->elemSubject->cloneNode(TRUE);
		
		// apply last minute changes on copy
		$this->getElementModifications($elemSubject);
		
		// determine which content should be included
		foreach ($this->aGetElementFlags as $sKey => $mValue) {
			$aFlag[$sKey] = $mValue;
			if (isset($aCustomFlags[$sKey])) {
				$aFlag[$sKey] = $aCustomFlags[$sKey];
			}
		}
		foreach ($this->aAppendedElements as $elemCurrent) {
			$elemSubject->appendChild($elemCurrent->cloneNode(TRUE));
		}
		
		// remove secondary information if necessary
		if (!$aFlag['secondary_properties']) {
			$elemSubject->removeAttribute('name');
			$elemSubject->removeAttribute('uid');
			$elemSubject->removeAttribute('query');
			$elemSubject->removeAttribute('created');
			$elemSubject->removeAttribute('createdby');
			$elemSubject->removeAttribute('modified');
			$elemSubject->removeAttribute('modifiedby');
			$elemSubject->removeAttribute('inheritrights');
			$elemSubject->removeAttribute('bequeathrights');
			$elemSubject->removeAttribute('bequeathlocalrights');
			$elemSubject->removeAttribute('currentlifecyclestate');
			$elemSubject->removeAttribute('primary');
		}
		
		// first create and store all children as elements
		if ($aFlag['children']) {
			foreach ($this->aChildNodes as $sMode => $niCurrentChildren) {
				$this->storeNodeList($elemSubject, $niCurrentChildren, TRUE, 'children', $sMode, FALSE, $aCustomFlags);
			}
		}
		
		// then treat content nodes in a similar way
		if ($aFlag['content']) {
			foreach ($this->aContentNodes as $sMode => $niCurrentChildren) {
				$this->storeNodeList($elemSubject, $niCurrentChildren, TRUE, 'content', $sMode, FALSE, $aCustomFlags);
			}
		}
		
		// first create and store all children as elements
		if ($aFlag['parents'] && $this->niParents != NULL) {
			$this->storeNodeList($elemSubject, $this->niParents, TRUE, 'parents', NULL);
		}
		
		// first create and store all children as elements
		if ($aFlag['ancestors'] && $this->niAncestors != NULL) {
			$this->storeNodeList($elemSubject, $this->niAncestors, TRUE, 'ancestors', NULL, TRUE);
		}
		
		// store views
		if ($aFlag['views']) {
			$elemViews = $elemSubject->ownerDocument->createElement('views');
			foreach ($this->aViews as $aView) {
				$elemView = $this->elemSubject->ownerDocument->createElement('view');
				// TODO: find cleaner way to distinct non-display views
				if ($aView['visible']) {
					$elemView->setAttribute('name', $aView['name']);
					$elemView->setAttribute('order', $aView['order']);
					$elemView->setAttribute('priority', $aView['priority']);
					$elemViews->appendChild($elemView);
				}
			}
			$elemSubject->appendChild($elemViews);
		}
		
		// store this node's tags
		if ($aFlag['tags']) {
			if (is_array($this->aTags) && count($this->aTags) > 0) {
				$elemTags = $this->elemSubject->ownerDocument->createElement('tags');
				foreach ($this->aTags as $sTag => $iTagID) {
					$elemTag = $this->elemSubject->ownerDocument->createElement('tag', htmlspecialchars($sTag));
					$elemTag->setAttribute('id', $iTagID);
					$elemTags->appendChild($elemTag);
				}
				$elemSubject->appendChild($elemTags);
			}
		}
		
		// store tags of this node's descendents
		if ($aFlag['branchtags']) {
			if (is_array($this->aBranchTags) && count($this->aBranchTags) > 0) {
				$elemTags = $this->elemSubject->ownerDocument->createElement('branchtags');
				foreach ($this->aBranchTags as $iTagID => $aDetails) {
					$elemTag = $this->elemSubject->ownerDocument->createElement('tag', htmlspecialchars($aDetails['tag']));
					$elemTag->setAttribute('id', $iTagID);
					$elemTag->setAttribute('popularity', $aDetails['popularity']);
					$elemTag->setAttribute('numitems', $aDetails['numitems']);
					$elemTag->setAttribute('customweight', $aDetails['customweight']);
					$elemTags->appendChild($elemTag);
				}
				$elemSubject->appendChild($elemTags);
			}
		}
		
		// store authorisation-related information
		if ($aFlag['auth_supported'] && $this->aSupportedAuthorisations != NULL) {
			$this->storeSupportedAuthorisations($elemSubject);
		}
		if ($aFlag['auth_user'] && $this->aUserAuthorisations != NULL) {
			$this->storeUserAuthorisations($elemSubject);
		}
		if ($aFlag['auth_local'] && $this->aLocalAuthorisations != NULL) {
			$this->storeLocalAuthorisations($elemSubject);
		}
		if ($aFlag['auth_inherited'] && $this->aInheritedAuthorisations != NULL) {
			$this->storeInheritedAuthorisations($elemSubject);
		}
		
		return ($elemSubject);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Dummy method to enable last-minute modifications on the DOM elements 
	* passed by getElement in derived classes
	* @return string the module
	*/
	protected function getElementModifications($elemSubject) {
		// do nothing for now, can be overloaded by derived classes
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the module name this node's primary type is associated with.
	* @return string the module
	*/
	public function getModule() {
		return(substr($this->getPrimaryNodeType(), 0, strpos($this->getPrimaryNodeType(), ':')));
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM: 
	* @param 
	* @return 
	*/
	/*public function gatherContent($bPreview = TRUE) {
		$this->loadChildren('gatherContent', TRUE, TRUE, TRUE);
		//$this->storeChildren();
		//$this->setDeepMode(TRUE);
		foreach ($this->aChildNodes['gatherContent'] as $nodeChild) {
			$nodeChild->gatherContent($bPreview);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM: 
	* @param 
	* @return 
	*/
	public function appendElement($elemData) {
		$elemImported = $this->elemSubject->ownerDocument->importNode($elemData, TRUE);
		$this->aAppendedElements[] = $elemImported;
	}
	
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM: 
	* @param 
	* @return 
	*/
	public function addContent($sMode, $niContent) {
		$this->aContentNodes[$sMode] = $niContent;
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM:
	* NOTE: if iterating the node tree recursively, this method should be called
	* at the end of the recursive function, otherwise lower level childen will
	* not be included in the resulting DOM.
	* @param 
	* @return 
	*/
	public function storeChildren($bUseContainer = TRUE) {
		$this->aGetElementFlags['children'] = TRUE;
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM:
	* @param 
	* @return 
	*/
	public function storeAncestors() {
		$this->aGetElementFlags['ancestors'] = TRUE;
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM:
	* @param 
	* @return 
	*/
	public function storeParents() {
		$this->aGetElementFlags['parents'] = TRUE;
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM:
	* @param 
	* @return 
	*/
	public function storeViews() {
		$this->aGetElementFlags['views'] = TRUE;
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM: 
	* @param 
	* @return 
	*/
	protected function storeNodeList($elemSubject, $niList, $bUseContainer = FALSE, $sContainerName = 'nodelist', $sMode = NULL, $bReverse = FALSE, $aCustomFlags = NULL) {
		
		if ($sMode !== NULL) {
			$bUseContainer = TRUE;
		}
		
		if ($niList instanceof sbCR_NodeIterator) {
			
			if ($niList->getSize() > 0) {
				
				if ($bUseContainer) {
					$elemParent = $elemSubject->ownerDocument->createElement($sContainerName);
					if ($sMode !== NULL) {
						$elemParent->setAttribute('mode', $sMode);
					}
					$elemSubject->appendChild($elemParent);
				} else {
					$elemParent = $elemSubject;
				}
				
				if ($bReverse) {
					$niList->reverse();
				}
				foreach ($niList as $nodeCurrent) {
					$elemParent->appendChild($nodeCurrent->getElement($aCustomFlags));
				}
				if ($bReverse) {
					$niList->reverse();
				}
			
			}
			
		} else {
			
			throw new sbException('given list is of type "'.get_class($niList).'" instead of "sbCR_NodeIterator"');	
			
		}
				
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM: 
	* @param string mode used to load children as defined in repository
	* @param boolean stores the retrieved nodes in this node for output
	* @param boolean returns the retrieved nodes in a sbCR_NodeIterator
	* @param boolean flag to indicate if all properties should be loaded initially (see XXX for list of essential properties)
	* @param array an array of strings of authorisations that have to be checked for the current user, effectively filtering the retrieved nodes 
	* @return 
	*/
	public function loadChildren($sMode = 'debug', $bStoreAsNodes = TRUE, $bReturnChildren = FALSE, $bLoadProperties = FALSE, $aRequiredAuthorisations = array()) {
		
		// FIXME: IMPLEMENT DIFFERENT WAY OF CHECKING PRIMARY LINK!!!!!!!
		$niChildren = $this->getChildren($sMode, $aRequiredAuthorisations);
		
		foreach ($niChildren as $nodeChild) {
			/*if ($nodeChild->getParent()->isSame($this)) {
				$sPrimary = 'TRUE';
			} else {
				$sPrimary = 'FALSE';
			}
			$nodeChild->setProperty('primary', $sPrimary);*/
			$iNumChildren = $nodeChild->getNumberOfChildren($sMode);
			// FIXME: setAttribute IS NOT AVAILABLE IN sbCR_Node!!!!!! only in sbNode
			$nodeChild->setAttribute('subnodes', $iNumChildren);
			if ($bLoadProperties) {
				$nodeChild->loadProperties();
			}
		}
		
		if ($bStoreAsNodes) {
			$this->aChildNodes[$sMode] = $niChildren;
		}
		
		if ($bReturnChildren) {
			return ($niChildren);
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Makes the corresponding method of sbCR_Node public
	* @param 
	* @return 
	*/
	public function getChildren($sMode = 'debug', $aRequiredAuthorisations = array()) {
		return (parent::getChildren($sMode, $aRequiredAuthorisations));
	}
	
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getAncestorOfType($sNodetype) {
		try {
			$nodeParent = $this->getParent();
			if ($nodeParent->getPrimaryNodeType() == $sNodetype) {
				return ($nodeParent);
			} else {
				return ($nodeParent->getAncestorOfType($sNodetype));
			}
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM: 
	* @param 
	* @return 
	*/
	public function getElementTree($sMode) {
		
		if (!isset($this->aChildNodes[$sMode])) {
			throw new sbException('no children found for mode "'.$sMode.'"');
		}
		
		$elemSubject = $this->elemSubject->cloneNode();
		$this->getElementModifications($elemSubject);
		if (!$this->aChildNodes[$sMode]->isEmpty()) {
			foreach ($this->aChildNodes[$sMode] as $nodeChild) {
				$elemSubject->appendChild($nodeChild->getElementTree($sMode));
			}
		}
		return ($elemSubject);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM: 
	* @param 
	* @return 
	*/
	public function loadAncestors() {
		
		if ($this->niAncestors == NULL) {
			$this->niAncestors = $this->getAncestors();
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM: 
	* @param 
	* @return 
	*/
	public function loadParents() {
		try {
			if ($this->niParents == NULL) {
				$this->niParents = $this->getParents();
			}
		} catch (ItemNotFoundException $e) {
			return (FALSE);
		}
	}
	
	
	
	//--------------------------------------------------------------------------
	/**
	* Makes the corresponding method of sbCR_Node public.
	* @param Node the node this node is checked against
	* @return boolean true if this node is a descendant of the subject node; false otherwise
	*/
	public function isDescendantOf($nodeSubject) {
		return (parent::isDescendantOf($nodeSubject));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Makes the corresponding method of sbCR_Node public.
	* @param Node the node this node is checked against
	* @return boolean true if this node is an ancestor of the subject node; false otherwise
	*/
	public function isAncestorOf($nodeSubject) {
		return (parent::isAncestorOf($nodeSubject));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function isDeletable() {
		
		// check special nodetypes
		$aKnownMandatoryNodeTypes = array(
			'sbSystem:Root',
			'sbSystem:Preferences',
			'sbSystem:Reports',
			'sbSystem:Maintenance',
			'sbSystem:Reports_DB',
			'sbSystem:Reports_Structure',
			'sbSystem:Trashcan',
			'sbSystem:Useraccounts',
			'sbSystem:Registry',
			'sbSystem:Modules',
			'sbSystem:Module',
			'sbSystem:Debug',
			'sbSystem:Logs',
			'sbSystem:Tags',
		);
		if (in_array($this->getProperty('jcr:primaryType'), $aKnownMandatoryNodeTypes, TRUE)) {
			return (FALSE);
		}
		
		// check special nodes (identified via uid)
		$aKnownMandatoryNodes = array(
			'sbSystem:Guests',
			'sbSystem:Admins',
		);
		if (in_array($this->getProperty('sbcr:uid'), $aKnownMandatoryNodes, TRUE)) {
			return (FALSE);
		}
		
		// check user authorisations
		if (!User::isAuthorised('write', $this)) {
			return (FALSE);
		}
		
		// nothing restricts this node from being deleted
		return (TRUE);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getAllowedSubtypes($sMode) {
		
		$aAllowedSubtypes = array();
		$stmtGetAllowedSubtypes = $this->crSession->prepareKnown('sbSystem/node/getAllowedSubtypes');
		$stmtGetAllowedSubtypes->bindValue('parent_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtGetAllowedSubtypes->bindValue('mode', $sMode, PDO::PARAM_STR);
		$stmtGetAllowedSubtypes->execute();
		foreach ($stmtGetAllowedSubtypes as $aRow) {
			// store types for usage in paste/link section
			$aAllowedSubtypes[$aRow['fk_nodetype']] = $aRow;
		}
		$stmtGetAllowedSubtypes->closeCursor();
		
		return ($aAllowedSubtypes);
		
	}
	
	
	
	
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------
	// views & actions
	//--------------------------------------------------------------------------
	/**
	* Initializes the views that are associated with this node.
	* Basically these are defined for the nodetype, but based on authorisations
	* and configuration there are special views (security, debug) added.
	* @param 
	* @return 
	*/
	public function loadViews() {
		
		static $bViewsStored = FALSE;
		
		$crNodeType = $this->getNodeType();
		$this->aViews = $crNodeType->getSupportedViews();
		
		// special views
		// TODO: find a better way, all view information should come from nodetype
		// add special view if user has the necessary authorisations
		if (User::isAuthorised('grant', $this)) {
			$this->aViews['security'] = array(
				'name' => 'security',
				'order' => '10001',
				'priority' => '1',
				'visible' => TRUE
			);
		}
		// add special view if user has the necessary authorisations and config allows it
		if (Registry::getValue('sb.system.debug.tab.enabled') && User::isAdmin()) {
			$this->aViews['debug'] = array(
				'name' => 'debug',
				'order' => '10002',
				'priority' => '2',
				'visible' => TRUE
			);
		}
		
		if (!$bViewsStored) {
			$bViewsStored = TRUE;
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the default view of this node (the one with the highest priority).
	* @return string the name of the default wiew
	*/
	protected function getDefaultViewName() {
		
		$sDefaultView = NULL;
		$iCurrentPriority = 0;
		foreach ($this->aViews as $aView) {
			if ($aView['priority'] >= $iCurrentPriority) {
				$sDefaultView = $aView['name'];
				$iCurrentPriority = $aView['priority'];
			}
		}
		return ($sDefaultView);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Executes an action on this node.
	* TODO: better comment explaining this vital feature
	* @param string the view's name which defines the action (optional, will call the default view of this node if omitted)
	* @param string the action's name (optional, will execute the default action of the view if omitted)
	* @return 
	*/
	public function callView($sView = NULL, $sAction = NULL) {
		
		// initialize supported views and chose default one if it was not given as parameter
		$this->loadViews();
		if ($sView == NULL) {
			$sView = $this->getDefaultViewName();
		}
		$vdCurrentView = $this->getNodeType()->getViewDefinition($sView);
		// the view does determine the default action if it was not given as parameter
		$adCurrentAction = $vdCurrentView->getActionDefinition($sAction);
		
		DEBUG(__CLASS__.': calling view "'.$sView.'" and action "'.$sAction.'" on node '.$this->getName().' ('.$this->getIdentifier().')', DEBUG::NODE);
		
		// process view & action info
		$sClass = $adCurrentAction->getClass();
		$sLibrary = $adCurrentAction->getClassFile();
		$sAction = $adCurrentAction->getName();
		
		// init module (loads init.php of the module)
		list($sModule, $sFile) = explode(':', $sLibrary);
		import($sModule.':init', FALSE);
		
		// import class file and create instance
		import($sLibrary);
		if (!class_exists($sClass)) {
			throw new sbException('view class "'.$sClass.'" does not exist in library "'.$sLibrary.'"');
		}
		$viewCurrent = new $sClass($this);
		
		global $_RESPONSE;
		
		// store recallable action if possible
		if ($adCurrentAction->isRecallable()) {
			sbSession::addData('last_recallable_action', $_REQUEST->getURI());
		}
		
		// check if login is necessary and user is logged in / session is valid
		if ($viewCurrent->requiresLogin() && (!User::isLoggedIn() || sbSession::isZombie())) {
			throw new SessionTimeoutException();
		}
		$viewCurrent->checkRequirements($sAction);
		
		if (!$_RESPONSE->hasRequestData()) {
			$_RESPONSE->addRequestData($this->getProperty('jcr:uuid'), $sView, $sAction);
		}
		
		// execute action and store data
		$elemView = $viewCurrent->execute($sAction);
		
		// set the default output parameters as defined for the executed action
		$_RESPONSE->setRenderMode($adCurrentAction->getOutputtype(), $adCurrentAction->getMimetype(), $adCurrentAction->getStylesheet());
		$_RESPONSE->setLocaleMode($adCurrentAction->usesLocale());
		if ($adCurrentAction->usesLocale()) {
			$_RESPONSE->addLocale($sModule);
			$_RESPONSE->addLocale($this->getModule());
		}
		
		return ($elemView);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function storeSupertypeNames() {
		$crNodeType = $this->getNodeType();
		$elemContainer = $this->elemSubject->ownerDocument->createElement('supertypes');
		foreach ($crNodeType->getSupertypeNames() as $sSupertype) {
			$elemSupertype = $this->elemSubject->ownerDocument->createElement('nodetype');
			$elemSupertype->setAttribute('name', $sSupertype);
			$elemContainer->appendChild($elemSupertype);
		}
		$this->elemSubject->appendChild($elemContainer);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function storeSupportedLifecycleTransitions() {
		$aTransitions = $this->getAllowedLifecycleTransitions();
		$elemContainer = $this->elemSubject->ownerDocument->createElement('allowedLifecycleTransitions');
		foreach ($aTransitions as $sTransition) {
			$elemTransition = $this->elemSubject->ownerDocument->createElement('transition');
			$elemTransition->setAttribute('state', $sTransition);
			$elemContainer->appendChild($elemTransition);
		}
		$this->elemSubject->appendChild($elemContainer);
	}
	
	
	//--------------------------------------------------------------------------
	/**
	* Returns a sbCR_NodeIterator with all nodes that contain references to this
	* node. If there are no referencing nodes the iterator will be empty.
	* TODO: implement check for auxiliary properties!
	* @return NodeIterator the nodes that have a reference to this node
	*/
	public function getReferencingNodes() {
		
		$stmtGetReferences = $this->crSession->prepareKnown('sbCR/node/getReferences');
		$stmtGetReferences->bindValue('uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtGetReferences->execute();
		$aReferences = $stmtGetReferences->fetchAll();
		
		$aReferencingNodes = array();
		foreach ($aReferences as $aRow) {
			$nodeCurrent = $this->crSession->getNode($aRow['fk_node']);
			$aReferencingNodes[] = $nodeCurrent;
		}
		
		$niReferencingNodes = new sbCR_NodeIterator($aReferencingNodes);
		
		return ($niReferencingNodes);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns a sbCR_NodeIterator with all nodes containing a softlink to this 
	* node. If there are no softlinks the iterator will be empty.
	* TODO: implement check for auxiliary properties!
	* @return NodeIterator the nodes that have a weak reference to this node
	*/
	public function getWeakReferencingNodes() {
		
		$stmtGetSoftlinks = $this->crSession->prepareKnown('sbCR/node/getSoftlinks');
		$stmtGetSoftlinks->bindValue('uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtGetSoftlinks->execute();
		$aSoftlinks = $stmtGetSoftlinks->fetchAll();
		
		$aLinkingNodes = array();
		foreach ($aSoftlinks as $aRow) {
			$nodeCurrent = $this->crSession->getNode($aRow['fk_node']);
			$aLinkingNodes[] = $nodeCurrent;
		}
		
		$niLinkingNodes = new sbCR_NodeIterator($aLinkingNodes);
		
		return ($niLinkingNodes);
		
	}
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------
	// custom sbCR stuff
	//--------------------------------------------------------------------------
	/**
	* Internal redirect without actually doing a HTTP redirect?
	* TODO: check if this is necessary, was used for displaying the login
	* @param 
	* @return 
	*/
	/*private function redirect($iNodeID, $sView = NULL, $sAction = NULL) {
		$nodeCurrent = $this->crSession->getNode($iNodeID);
		$elemViews = $nodeCurrent->loadViews(TRUE);
		$elemData = $nodeCurrent->callView($sView, $sAction);
		return ($elemData);
	}*/
	
	//--------------------------------------------------------------------------
	/**
	* Returns an element defining the context menu of this node.
	* TODO: add description how the data is structured
	* @param string the parent node's uuid (optional, necessary if the node is retrieved via it's uuid)
	* @return DOMElement the context menu data
	*/
	public function getContextMenu($sParentUUID) {
		
		// basic data
		$elemContextMenu = ResponseFactory::createElement('contextmenu');
		//$elemContextMenu->setAttribute('new', 'TRUE');
		$elemContextMenu->setAttribute('uuid', $this->getProperty('jcr:uuid'));
		$elemContextMenu->setAttribute('parent', $sParentUUID);
		$elemContextMenu->setAttribute('refresh', 'TRUE');
		
		// store allowed subtypes
		// TODO: use a special 'paste'-mode instead of 'create'
		$aAllowedSubtypes = $this->getAllowedSubtypes('create');
		
		// create new nodes
		if (User::isAuthorised('write', $this)) {
			foreach ($aAllowedSubtypes as $sNodetype => $aRow) {
				$elemNew = ResponseFactory::createElement('new');
				$elemNew->setAttribute('nodetype', $aRow['fk_nodetype']);
				$elemNew->setAttribute('displaytype', $aRow['displaytype']);
				$elemContextMenu->appendChild($elemNew);
				$sModule = substr($aRow['fk_nodetype'], 0, strpos($aRow['fk_nodetype'], ':'));
				global $_RESPONSE;
				$_RESPONSE->addLocale($sModule);
			}
		}
		
		// clipboard data
		if (isset(sbSession::$aData['clipboard'])) {
			// TODO: it might be that the node in clipboard is already deleted
			// the clipboard should instead be cleaned on deletion... (but consider other user's clipboards!)
			// TODO: implement clipboard for multiple nodes
			try {
				
				$nodeSubject = $this->crSession->getNodeByIdentifier(sbSession::$aData['clipboard']['childnode']);
				
				if (!User::isAuthorised('write', $this)) { // check for permissions
					// do nothing
				} elseif (!isset($aAllowedSubtypes[$nodeSubject->getPrimaryNodeType()])) { // check for allowed subnodes
					// do nothing
				} elseif ($nodeSubject->isAncestorOf($this) || $nodeSubject->isSame($this)) { // check cyclic recursions
					// do nothing
				} else {
					$elemContextMenu->setAttribute('clipboard', 'TRUE');
					$elemContextMenu->setAttribute('clipboard_type', sbSession::$aData['clipboard']['type']);
					$elemContextMenu->setAttribute('clipboard_subject', $nodeSubject->getProperty('label'));
				}
				
			} catch (NodeNotFoundException $e) {
				// ignore
			}
		}
		
		// export/import
		if (User::isAdmin()) {
			$elemContextMenu->setAttribute('export', 'TRUE');
			// TODO: implement import functionality
			$elemContextMenu->setAttribute('import', 'FALSE');
		}
		
		// favorites
		if (User::isAuthorised('read', $this)) {
			$elemContextMenu->setAttribute('add_to_favorites', 'TRUE');
		}
		
		// change primary parent
		// TODO: only display this when acting on a secondary linked node
		if (User::isAdmin()) {
			$elemContextMenu->setAttribute('set_primary', 'TRUE');
		}
		
		// trash
		if ($this->getPrimaryNodeType() == 'sbSystem:Trashcan') {
			$elemContextMenu->setAttribute('purge', 'TRUE');
		}
		
		// delete
		$sDeletable = 'FALSE';
		if ($this->isDeletable()) {
			$sDeletable = 'TRUE';
		}
		$elemContextMenu->setAttribute('delete', $sDeletable);
		
		return ($elemContextMenu);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setPrimaryParent($nodeParent) {
		
		parent::setPrimaryLink($nodeParent);
		return;
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Generates a standard form for this node.
	* Currently the modes 'properties' (editing properties of an existing node)
	* and 'create' (entering primary information for new, unsaved nodes) are
	* supported.
	* The form elements are specified through the property definitions stored in
	* the repository for this node's type.
	* @param string the mode resp. type of form that should be built
	* @param string the parent's uuid (required for the create form, it must know where the new node should be stored)
	* @return sbDOMForm the form based on the given mode
	*/
	public function buildForm($sMode, $sParentUUID = '') {
		
		global $_RESPONSE;
		$this->initPropertyDefinitions();
		
		switch ($sMode) {
			
			case 'properties':
				if (method_exists($this, 'buildPropertiesForm')) {
					return ($this->buildPropertiesForm());
				} else {
					
					// init form
					$formProperties = new sbDOMForm(
						'properties',
						'$locale/sbSystem/labels/properties',
						'/'.$this->getProperty('jcr:uuid').'/properties/save',
						$this->crSession
					);
					
					// add standard inputs for node properties
					foreach ($this->crPropertyDefinitionCache as $sName => $aDetails) {
						if ($aDetails['b_showinproperties'] == 'TRUE' && !$this->crPropertyDefinitionCache->isProtected($sName, $this->isNew())) {
							$formProperties->addInput($sName.';'.$aDetails['s_internaltype'], $aDetails['s_labelpath']);
							try {
								$formProperties->setValue($sName, $this->getProperty($sName));
							} catch (PathNotFoundException $e) {
								// ignore
							}
							if ($aDetails['b_protected'] == 'TRUE') {
								$formProperties->disable($sName);
							}
						}
					}
					
					// add text input for tags
					// TODO: add locale stuff
					if ($this->isTaggable()) {
						$sInputName = 'tags_'.$this->getProperty('jcr:uuid');
						$aTags = $this->getTags();
						$formProperties->addInput($sInputName.';text;maxlength=500;rows=2;', '$locale/sbSystem/labels/tags');
						$formProperties->setValue($sInputName, implode(', ', $aTags));
					}
						
					// finish form and return
					$formProperties->addSubmit('$locale/sbSystem/actions/save');
					$this->modifyForm($formProperties, 'properties');
					return ($formProperties);
					
				}
				
			case 'create':
				
				$_RESPONSE->addLocale($this->getModule());
				
				if (method_exists($this, 'buildCreateForm')) {
					return ($this->buildCreateForm($sParentUUID));
				} else {
					
					$formCreate = new sbDOMForm(
						'create',
						'$locale/sbSystem/actions/create',
						'/-/structure/saveChild/?nodetype='.$this->getProperty('nodetype').'&parentnode='.$sParentUUID,
						$this->crSession
					);
					foreach ($this->crPropertyDefinitionCache as $sName => $aDetails) {
						if ($aDetails['b_showinproperties'] == 'TRUE') {
							$formCreate->addInput($sName.';'.$aDetails['s_internaltype'], $aDetails['s_labelpath']);
							//$formCurrent->setValue($sName, $this->getProperty($aDetails['s_attributename']));
						}
					}
					$_RESPONSE->addMetadata('md_system', 'displaytype', $this->getProperty('displaytype'));
					$formCreate->addSubmit('$locale/sbSystem/actions/create');
					$formCreate->addSubmit('$locale/sbSystem/actions/create_multiple', 'create_multiple');
					$this->modifyForm($formCreate, 'create');
					return ($formCreate);
					
				}
				
				
			
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Optionally modifies a form created by buildForm().
	* This method may be overloaded by custom node classes.
	* @param sbDOMForm the default form
	* @param string the mode the form was created for
	*/
	protected function modifyForm($formCurrent, $sMode) { }
	
	//--------------------------------------------------------------------------
	/**
	* Wraps the sbCR_Node method to return NULL instead of throwing an 
	* exception.
	* @param string the name of the property
	* @return NULL if property does not exist, otherwise property value 
	*/
	public function getProperty($sName) {
		try {
			$mValue = parent::getProperty($sName);
			return ($mValue);
		} catch (PathNotFoundException $e) {
			return (NULL);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* Directly writes to this node's internal DOMElement.
	* This method enables a passthrough access to the element attributes and
	* thus may be used to add information to this node for output resp. data
	* transport. If the given attribute is also a property, the property will be
	* set, too.
	* @param string name of the attribute/property
	* @param multiple the value to set the attribute/property to
	*/
	public function setAttribute($sName, $mValue) {
		$this->initPropertyDefinitions();
		if ($this->crPropertyDefinitionCache->hasProperty($sName)) {
			parent::setProperty($sName, $mValue);
		} else {
			$this->elemSubject->setAttribute($sName, $mValue);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getNumberOfParents() {
		return (parent::getNumberOfParents());
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getNumberOfChildren($sMode = NULL) {
		return (parent::getNumberOfChildren($sMode));
	}
	
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function moveToPosition($nodeNewParent, $nodeOldParent, $iPosition) {
		
		
		
		
		
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function moveToTrash() {
		$this->addSaveTask('move_to_trash');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function recoverFromTrash() {
		$this->addSaveTask('recover_from_trash');
	}
	
	//--------------------------------------------------------------------------
	/**
	* TODO: this is only a TEMPORARY solution until the trashcan is handled otherwise
	* TODO: this can remove primary links before all secondary links are removed!!!
	* @param 
	* @return 
	*/
	public function unlink() {
		$this->deleteLink($this->getParent());
	}
	
	//--------------------------------------------------------------------------
	/**
	* TODO: remove this? may only be needed for content of sbCMS, but may also
	* be used for applications (whether or not incorporated in a sbCMS website)
	* @param 
	* @return 
	*/
	public function getStylesheet($sMode = NULL) {
		$sStylesheet = '<?xml version="1.0" encoding="UTF-8"?>
			<xsl:stylesheet 
				xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
				version="1.0" 
				exclude-result-prefixes="html" 
				xmlns:html="http://www.w3.org/1999/xhtml"
			>
			
			<xsl:template match="/response">
				<em>This node ('.$this->getPrimaryNodeType().') cannot deliver a stylesheet for the required mode ('.$sMode.')</em>
			</xsl:template>

			</xsl:stylesheet>';
		$domStylesheet = new sbDOMDocument();
		$domStylesheet->loadXML($sStylesheet);
		return ($domStylesheet);
	}
	
	
	
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------
	// voting
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function placeVote($sUserUUID = NULL, $iVote) {
		if ($sUserUUID == NULL) {
			throw new sbException('voting needs user uuid');
		}
		$stmtPlaceVote = $this->prepareKnown('voting/placeVote');
		$stmtPlaceVote->bindValue(':subject_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtPlaceVote->bindValue(':user_uuid', $sUserUUID, PDO::PARAM_STR);
		$stmtPlaceVote->bindValue(':vote', $iVote, PDO::PARAM_INT);
		$stmtPlaceVote->execute();
		$this->refreshGlobalVote();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function removeVote($sUserUUID = NULL) {
		if ($sUserUUID == NULL) {
			throw new sbException('voting needs user uuid');
		}
		$stmtPlaceVote = $this->prepareKnown('voting/removeVote');
		$stmtPlaceVote->bindValue(':subject_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtPlaceVote->bindValue(':user_uuid', $sUserUUID, PDO::PARAM_STR);
		$stmtPlaceVote->execute();
		$this->refreshGlobalVote();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function removeAllVotes() {
		$stmtPlaceVote = $this->prepareKnown('voting/removeAllVotes');
		$stmtPlaceVote->bindValue(':subject_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtPlaceVote->execute();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getVote($sUserUUID = NULL) {
		if ($sUserUUID == NULL) {
			$nodeAll = $this->crSession->getRootNode();
			$sUserUUID = $nodeAll->getProperty('jcr:uuid');
		}
		$stmtGetVote = $this->prepareKnown('voting/getUserVote');
		$stmtGetVote->bindValue(':subject_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtGetVote->bindValue(':user_uuid', $sUserUUID, PDO::PARAM_STR);
		$stmtGetVote->execute();
		foreach ($stmtGetVote as $aRow) {
			$this->setAttribute('vote', $aRow['n_vote']);
			return ($aRow['n_vote']);
		}
		return (NULL);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getVotes() {
		$stmtGetVotes = $this->prepareKnown('voting/getAllVotes');
		$stmtGetVotes->bindValue(':subject_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtGetVotes->execute();
		$aVotes = array();
		foreach ($stmtGetVotes as $aRow) {
			$aVotes[] = $aRow;
		}
		return ($aVotes);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getUserVotes() {
		$stmtGetVotes = $this->prepareKnown('voting/getUserVotes');
		$stmtGetVotes->bindValue(':subject_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtGetVotes->execute();
		$aVotes = array();
		foreach ($stmtGetVotes as $aRow) {
			$aVotes[] = $aRow;
		}
		return ($aVotes);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function storeUserVotes() {
		
		$aVotes = $this->getUserVotes();
		$domOwner = $this->elemSubject->ownerDocument;
		$elemVotes = $domOwner->createElement('votes');
		
		foreach ($aVotes as $aVote) {
			$elemVote = $domOwner->createElement('vote');
			$elemVote->setAttribute('vote', $aVote['vote']);
			$elemVote->setAttribute('user_uuid', $aVote['user_uuid']);
			$elemVote->setAttribute('user_label', $aVote['user_label']);
			$elemVotes->appendChild($elemVote);
		}
		
		$this->elemSubject->appendChild($elemVotes);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function storeAllVotes() {
		
		$aVotes = $this->getVotes();
		$domOwner = $this->elemSubject->ownerDocument;
		$elemVotes = $domOwner->createElement('all_votes');
		
		foreach ($aVotes as $aVote) {
			$elemVote = $domOwner->createElement('vote');
			$elemVote->setAttribute('vote', $aVote['vote']);
			$elemVote->setAttribute('user_uuid', $aVote['user_uuid']);
			$elemVotes->appendChild($elemVote);
		}
		
		$this->elemSubject->appendChild($elemVotes);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function refreshGlobalVote() {
		
		$nodeAll = $this->crSession->getRootNode();
		$stmtGetVotes = $this->prepareKnown('voting/getAverageVote');
		$stmtGetVotes->bindValue(':subject_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtGetVotes->bindValue(':ignore_uuid', $nodeAll->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtGetVotes->execute();
		
		$bVotesPresent = FALSE;
		foreach ($stmtGetVotes as $aRow) {
			$stmtPlaceVote = $this->prepareKnown('voting/placeVote');
			$stmtPlaceVote->bindValue(':subject_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
			$stmtPlaceVote->bindValue(':user_uuid', $nodeAll->getProperty('jcr:uuid'), PDO::PARAM_STR);
			$stmtPlaceVote->bindValue(':vote', round($aRow['n_average']), PDO::PARAM_INT);
			$stmtPlaceVote->execute();
			$bVotesPresent = TRUE;
		}
		
		if (!$bVotesPresent) {
			$this->removeAllVotes();
		}
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------
	// tags
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function initTags() {
		if (is_array($this->aTags)) {
			return (FALSE);
		}
		$this->aTags = array();
		if (!$this->isNew()) {
			$stmtGetTags = $this->prepareKnown('tagging/getAllNodeTags');
			$stmtGetTags->bindValue(':subject_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
			$stmtGetTags->execute();
			foreach ($stmtGetTags as $aRow) {
				$this->aTags[$aRow['s_tag']] = $aRow['id'];
			}
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function hasTag($sTag) {
		$this->initTags();
		$sCheckTag = strtolower(trim($sTag));
		$aCheckTags = $this->aTags;
		if (count($this->aNewTags) > 0) {
			$aCheckTags = array_merge($this->aTags, $this->aNewTags);
		}
		foreach ($aCheckTags as $sTag => $iID) {
			if (strtolower($sTag) == $sCheckTag) {
				return (TRUE);
			}
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addTag($sTag) {
		$this->initTags();
		$sTag = trim($sTag);
		if ($sTag != '' && !$this->hasTag($sTag)) {
			$this->aNewTags[$sTag] = TRUE;
			$this->addSaveTask('add_tag', array('tag' => $sTag));
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addTags($aTags) {
		foreach ($aTags as $sTag) {
			$this->addTag($sTag);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* Sets all tags of this node, removing old ones.
	* @param array all new tags as strings.
	*/
	public function setTags($aNewTags) {
		$this->initTags();
		$this->aNewTags = array();
		foreach ($aNewTags as $sKey => $sTag) {
			$aNewTags[$sKey] = trim($sTag);
		}
		foreach ($this->aTags as $sTag => $iID) {
			if (!in_array($sTag, $aNewTags)) {
				$this->removeTag($sTag);
				//echo ' remove "'.$sTag.'"';
			}
		}
		foreach ($aNewTags as $sTag) {
			if (!$this->hasTag($sTag)) {
				$this->addTag($sTag);
				//echo ' add "'.$sTag.'"';
			}
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function removeTag($sTag) {
		$this->initTags();
		if (!$this->hasTag($sTag)) {
			throw new sbException('tag "'.$sTag.'" is not assigned to this node');	
		} else {
			$this->addSaveTask('remove_tag', array('tag' => $sTag));
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getTags() {
		$this->initTags();
		$aCurrentTags = array();
		foreach ($this->aTags as $sTag => $unused) {
			$aCurrentTags[] = $sTag;
		}
		foreach ($this->aNewTags as $sTag => $unused) {
			$aCurrentTags[] = $sTag;
		}
		return ($aCurrentTags);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getBranchTags() {
		if ($this->aBranchTags !== NULL) {
			return ($this->aBranchTags);
		}
		$stmtGetTags = $this->prepareKnown('tagging/getAllBranchTags');
		$stmtGetTags->bindValue('root_mpath', $this->getMpath().'%', PDO::PARAM_STR);
		$stmtGetTags->execute();
		foreach ($stmtGetTags as $aRow) {
			$this->aBranchTags[$aRow['id']] = array(
				'tag' => $aRow['s_tag'],
				'numitems' => $aRow['n_numitemstagged'],
				'popularity' => $aRow['n_popularity'],
				'customweight' => $aRow['n_customweight'],
			);
		}
		return ($this->aBranchTags);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getBranchNodesByTag($iTagID, $bUnique = TRUE) {
		$aTaggedNodes = array();
		$stmtGetNodes = $this->prepareKnown('tagging/getBranchNodes');
		$stmtGetNodes->bindValue('tag_id', $iTagID, PDO::PARAM_INT);
		$stmtGetNodes->bindValue('root_mpath', $this->getMpath().'%', PDO::PARAM_STR);
		$stmtGetNodes->execute();
		foreach ($stmtGetNodes as $aRow) {
			if ($bUnique) {
				if (!isset($aTaggedNodes[$aRow['uuid']])) {
					$aTaggedNodes[$aRow['uuid']] = $this->crSession->getNodeByIdentifier($aRow['uuid']);
				}
			} else {
				$aTaggedNodes[] = $this->crSession->getNodeByIdentifier($aRow['uuid'], $aRow['parent_uuid']);
			}
		}
		
		return (new sbCR_NodeIterator($aTaggedNodes));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getTagID($sTag) {
		$this->initTags();
		if (isset($this->aTags[$sTag])) {
			return ($this->aTags[$sTag]);
		}
		$stmtGetID = $this->prepareKnown('tagging/getTagID');
		$stmtGetID->bindValue('tag', $sTag, PDO::PARAM_STR);
		$stmtGetID->execute();
		foreach ($stmtGetID as $aRow) {
			return ($aRow['id']);
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function createNewTag($sTag) {
		$stmtNew = $this->prepareKnown('tagging/createNewTag');
		$stmtNew->bindValue('tag', $sTag, PDO::PARAM_STR);
		$stmtNew->execute();
		return ($this->crSession->lastInsertId());
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getTag($iTagID) {
		$stmtGetID = $this->prepareKnown('tagging/getTag');
		$stmtGetID->bindValue('tag_id', $iTagID, PDO::PARAM_INT);
		$stmtGetID->execute();
		foreach ($stmtGetID as $aRow) {
			return ($aRow['s_tag']);
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function increaseTagPopularity($iTagID) {
		$stmtIncPop = $this->prepareKnown('tagging/increasePopularity');
		$stmtIncPop->bindValue('tag_id', $iTagID, PDO::PARAM_INT);
		$stmtIncPop->execute();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function isTaggable() {
		if ($this->isNodeType('sbSystem:Taggable')) {
			return (TRUE);
		}
		return (FALSE);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------
	// relations
	//--------------------------------------------------------------------------
	/**
	* 
	* 
	*/
	public function getSupportedRelations() {
		
		$stmtGet = $this->prepareKnown('relations/getSupportedRelations');
		$stmtGet->bindValue('nodetype', $this->getPrimaryNodeType(), PDO::PARAM_STR);
		$stmtGet->execute();
		$aRelations = array();
		foreach ($stmtGet as $aRow) {
			// NOTE: a reverse relation MUST exist, otherwise the array entry will not be set!
			// TODO: check if there is demand for one-way relations
			$aRelations[$aRow['relation']][$aRow['targetnodetype']] = $aRow['reverserelation'];
		}
		
		return ($aRelations);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* 
	*/
	public function storeSupportedRelations() {
		
		$aRelations = $this->getSupportedRelations();
		$domOwner = $this->elemSubject->ownerDocument;
		$elemRelations = $domOwner->createElement('supportedRelations');
		
		foreach ($aRelations as $sRelation => $aNodetypes) {
			$elemRelation = $domOwner->createElement('relation');
			$elemRelation->setAttribute('id', $sRelation);
			foreach ($aNodetypes as $sNodetype => $sReverseRelation) {
				$elemNodetype = $domOwner->createElement('nodetype', $sNodetype);
				$elemRelation->appendChild($elemNodetype);
			}
			$elemRelations->appendChild($elemRelation);
		}
		
		$this->elemSubject->appendChild($elemRelations);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* 
	*/
	public function getRelations() {
		
		$stmtGet = $this->prepareKnown('relations/getRelations');
		$stmtGet->bindValue('source_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtGet->execute();
		$aRelations = array();
		foreach ($stmtGet as $aRow) {
			$aRelations[] = array(
				'relation' => $aRow['relation'],
				'target_uuid' => $aRow['target_uuid'],
				'target_label' => $aRow['target_label'],
				'target_nodetype' => $aRow['target_nodetype'],
			);
		}
		
		return ($aRelations);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* 
	*/
	public function storeRelations() {
		
		$aRelations = $this->getRelations();
		$domOwner = $this->elemSubject->ownerDocument;
		$elemRelations = $domOwner->createElement('existingRelations');
		
		foreach ($aRelations as $aRelation) {
			$elemRelation = $domOwner->createElement('relation');
			$elemRelation->setAttribute('id', $aRelation['relation']);
			$elemRelation->setAttribute('target_uuid', $aRelation['target_uuid']);
			$elemRelation->setAttribute('target_label', $aRelation['target_label']);
			$elemRelation->setAttribute('target_nodetype', $aRelation['target_nodetype']);
			$elemRelations->appendChild($elemRelation);
		}
		
		$this->elemSubject->appendChild($elemRelations);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* TODO: not finished
	*/
	public function getPossibleTargets($sRelation, $sSubstring = NULL) {
		
		if ($sSubstring != NULL) {
			$sSubstring = '%'.$sSubstring.'%';
		}
		
		$stmtGet = $this->prepareKnown('relations/getPossibleTargets');
		$stmtGet->bindValue('relation', $sRelation, PDO::PARAM_STR);
		$stmtGet->bindValue('sourcenodetype', $this->getPrimaryNodeType(), PDO::PARAM_STR);
		$stmtGet->bindValue('substring', $sSubstring, PDO::PARAM_STR);
		$stmtGet->execute();
		$aTargets = array();
		foreach ($stmtGet as $aRow) {
			$aTargets[$aRow['uuid']] = array(
				'label' => $aRow['label'],
				'nodetype' => $aRow['nodetype'],
				// TODO: this attribute is obsolete, remove here and in query
				'displaytype' => $aRow['displaytype'],
			);
		}
		
		return ($aTargets);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* TODO: not finished
	*/
	public function addRelation($sRelation, $nodeTarget) {
		
		// does not work on new, unsaved nodes
		if ($this->isNew() || $nodeTarget->isNew()) {
			throw new RepositoryException('adding relations does only work on persisted nodes, either source or target node is new');	
		}
		
		// prepare
		$aRelations = $this->getSupportedRelations();
		$stmtAdd = $this->prepareKnown('relations/addRelation');
		
		// add relation only if it's valid
		if (isset($aRelations[$sRelation][$nodeTarget->getPrimaryNodeType()])) {
			// add the given relation
			$stmtAdd->bindValue('relation', $sRelation, PDO::PARAM_STR);
			$stmtAdd->bindValue('source_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
			$stmtAdd->bindValue('target_uuid', $nodeTarget->getProperty('jcr:uuid'), PDO::PARAM_STR);
			$stmtAdd->execute();
			// add the reverse relation to the target node
			$stmtAdd->bindValue('relation', $aRelations[$sRelation][$nodeTarget->getPrimaryNodeType()], PDO::PARAM_STR);
			$stmtAdd->bindValue('source_uuid', $nodeTarget->getProperty('jcr:uuid'), PDO::PARAM_STR);
			$stmtAdd->bindValue('target_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
			$stmtAdd->execute();
		}
		
		return;
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* TODO: not finished
	*/
	public function removeRelation($sRelation, $nodeTarget) {
		
		// prepare
		$aRelations = $this->getSupportedRelations();
		$stmtRemove = $this->prepareKnown('relations/removeRelation');
		
		// remove relation only if it's valid
		if (isset($aRelations[$sRelation][$nodeTarget->getPrimaryNodeType()])) {
			// remove the given relation
			$stmtRemove->bindValue('relation', $sRelation, PDO::PARAM_STR);
			$stmtRemove->bindValue('source_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
			$stmtRemove->bindValue('target_uuid', $nodeTarget->getProperty('jcr:uuid'), PDO::PARAM_STR);
			$stmtRemove->execute();
			// remove the reverse relation from the target node
			$stmtRemove->bindValue('relation', $aRelations[$sRelation][$nodeTarget->getPrimaryNodeType()], PDO::PARAM_STR);
			$stmtRemove->bindValue('source_uuid', $nodeTarget->getProperty('jcr:uuid'), PDO::PARAM_STR);
			$stmtRemove->bindValue('target_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
			$stmtRemove->execute();
		}
		
		return;
		
	}
	
	//--------------------------------------------------------------------------
	// authorisations
	//--------------------------------------------------------------------------
	/**
	* 
	* The Autorisation aggregation path looks like:
	* - walk up tree for all entities separately, until root is reached or a 
	* non-inheriting node (gives local auth for all entities, DENY outweights 
	* ALLOW, LOCAL outweights PARENT)
	* - merge all groups for the user (DENY outweights ALLOW)
	* - merge user auth with group auth (DENY outweights ALLOW, USER outweights GROUP)
	* - flatten auth hierarchy (CHILD outweights PARENT)
	* @param 
	* @return 
	*/
	public function isAuthorised($sAuthorisation, $sEntityID = NULL) {
		
		// load full user authorisations
		$this->loadUserAuthorisations();
		
		// check authorisation
		if (isset($this->aUserAuthorisations[$sAuthorisation])) {
			return (TRUE);
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getUserAuthorisations() {
		if ($this->aUserAuthorisations == NULL) {
			$this->loadUserAuthorisations();	
		}
		return ($this->aUserAuthorisations);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function loadUserAuthorisations($bForced = FALSE) {
		
		if (User::isLoggedIn()) {
			$sUserUUID = User::getUUID();
		} else {
			//TODO: handle guests correctly
			$sUserUUID = 'I_HAVE_NO_UUID';
		}
		
		// compute authorisations if necessary
		if ($this->aUserAuthorisations == NULL || $bForced) {
			
//			// check cache
//			if (Registry::getValue('sb.system.cache.authorisations.enabled')) {
//				$cacheAuth = CacheFactory::getInstance('authorisations');
//				$aUserAuth = $cacheAuth->loadAuthorisations($this->getProperty('jcr:uuid'), $sUserUUID, AuthorisationCache::AUTH_EFFECTIVE);
//				if (count($aUserAuth) > 0) {
//					$this->aUserAuthorisations = $aUserAuth;
//				}
//			}
			
			// check again, might be loaded from cache
			if ($this->aUserAuthorisations == NULL) {
				
				if (User::isAdmin()) {
					
					$this->aUserAuthorisations = $this->getSupportedAuthorisations();
					foreach($this->aUserAuthorisations as $sAuthorisation => $unused) {
						$this->aUserAuthorisations[$sAuthorisation] = 'ALLOW';
					}
					
				} else {
					
					// hierarchy-centric preparations
					$this->loadInheritedAuthorisations();
					$this->loadLocalAuthorisations();
					$this->aMergedAuthorisations = $this->mergeAuthInherited($this->aLocalAuthorisations, $this->aInheritedAuthorisations);
					
					// group-centric authorisation stuff
					$aGroupAuth = array();
					foreach (User::getGroupUUIDs() as $sGroupUUID) {
						if (isset($this->aMergedAuthorisations[$sGroupUUID])) {
							$aGroupAuth = $this->mergeAuthGroups($aGroupAuth, $this->aMergedAuthorisations[$sGroupUUID]);
						}
					}
					
					// user-centric authorisation stuff
					$aUserAuth = array();
					if (isset($this->aMergedAuthorisations[$sUserUUID])) {
						$aUserAuth = $this->aMergedAuthorisations[$sUserUUID];
					}
					$aUserAuth = $this->mergeAuthUserGroup($aUserAuth, $aGroupAuth);
					$aUserAuth = $this->mergeAuthHierarchy($aUserAuth);
					
//					var_dumpp($this->getIdentifier());
//					var_dumpp('userauth');
//					var_dumpp($aUserAuth);
					
					foreach ($this->getSupportedAuthorisations() as $sAuthorisation => $sParentAuthorisation) {
						if (isset($aUserAuth[$sAuthorisation])) {
							if ($aUserAuth[$sAuthorisation] == 'ALLOW') {
								$this->aUserAuthorisations[$sAuthorisation] = 'ALLOW';
							}
						} elseif (isset($aUserAuth[$sParentAuthorisation])) {
							if ($aUserAuth[$sParentAuthorisation] == 'ALLOW') {
								$this->aUserAuthorisations[$sAuthorisation] = 'ALLOW';
							}
						} elseif (isset($aUserAuth['full']) && $aUserAuth['full'] == 'ALLOW') {
							if (isset($aUserAuth[$sParentAuthorisation])) {
								if ($aUserAuth[$sParentAuthorisation] != 'DENY') {
									$this->aUserAuthorisations[$sAuthorisation] = 'ALLOW';
								}
							}
						}
						
					}
					
//					var_dumpp('userauth - processed');
//					var_dumpp($this->aUserAuthorisations);
					
					
//					// store in cache
//					if (Registry::getValue('sb.system.cache.authorisations.enabled')) {
//						$cacheAuth = CacheFactory::getInstance('authorisations');
//						$cacheAuth->storeAuthorisations($this->getProperty('jcr:uuid'), $sUserUUID, AuthorisationCache::AUTH_EFFECTIVE, $aUserAuth);
//					}
					
				}
				
			}
			
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function storeUserAuthorisations($elemSubject) {
		
		$elemContainer = $elemSubject->ownerDocument->createElement('user_authorisations');
		foreach ($this->aUserAuthorisations as $sAuthorisation => $unused) {
			$elemAuthorisation = $elemSubject->ownerDocument->createElement('authorisation');
			$elemAuthorisation->setAttribute('name', $sAuthorisation);
			//$elemContainer->setAttribute($sAuthorisation, '');
			$elemContainer->appendChild($elemAuthorisation);
		}
		$elemSubject->appendChild($elemContainer);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function loadSecurityAuthorisations() {
		
		// load authorisations and prepare to store on getElement()
		$this->loadSupportedAuthorisations();
		$this->loadInheritedAuthorisations();
		$this->loadLocalAuthorisations();
		$this->aGetElementFlags['auth_supported'] = TRUE;
		$this->aGetElementFlags['auth_local'] = TRUE;
		$this->aGetElementFlags['auth_inherited'] = TRUE;
		
		// store information about users and groups
		$nodeUseraccounts = $this->crSession->getNode('//*[@uid="sbSystem:Useraccounts"]');
		global $_RESPONSE;
		$aResultNodes['users'] = $nodeUseraccounts->callView('gatherdata', 'users', NULL, $_RESPONSE);
		$aResultNodes['groups'] = $nodeUseraccounts->callView('gatherdata', 'groups', NULL, $_RESPONSE);
		
		return ($aResultNodes);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getInheritedAuthorisations() {
		if ($this->aInheritedAuthorisations == NULL) {
			$this->loadInheritedAuthorisations();	
		}
		return ($this->aInheritedAuthorisations);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function loadInheritedAuthorisations($bForced = FALSE) {
		
		if ($this->aInheritedAuthorisations == NULL || $bForced) {
			
			$aMerged = array();
			
			if ($this->getProperty('sbcr:inheritRights') == 'TRUE') {
				$_CACHE = CacheFactory::getInstance('system');
				if ($_CACHE->exists('authorisations:array/'.$this->getProperty('jcr:uuid'))) {
					$aMerged = $_CACHE->loadData('authorisations:array/'.$this->getProperty('jcr:uuid'));
				} else {
					try {
						$nodeParent = $this->getParent();
						$aLocal = array();
						if ($nodeParent->getProperty('sbcr:bequeathLocalRights') == 'TRUE') {
							$aLocal = $nodeParent->getLocalAuthorisations();
						}
						if ($nodeParent->getProperty('sbcr:bequeathRights') == 'TRUE') {
							$aInherited = $nodeParent->getInheritedAuthorisations();
							$aMerged = $this->mergeAuthInherited($aLocal, $aInherited);
							//$_CACHE->storeData('authorisations:array/'.$this->elemSubject->getAttribute('uuid'), $aMerged);
						}
					} catch (ItemNotFoundException $e) {
						// ignore and and proceed, root is reached
					}
				}
			}
			
			$this->aInheritedAuthorisations = $aMerged;
		
		}
	
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function storeInheritedAuthorisations($elemSubject) {
		$elemContainer = $elemSubject->ownerDocument->createElement('inherited_authorisations');
		foreach ($this->aInheritedAuthorisations as $sEntityUUID => $aAuthorisations) {
			foreach ($aAuthorisations as $sAuthorisation => $sGrantType) {
				$elemAuthorisation = $elemSubject->ownerDocument->createElement('authorisation');
				$elemAuthorisation->setAttribute('uuid', $sEntityUUID);
				$elemAuthorisation->setAttribute('name', $sAuthorisation);
				$elemAuthorisation->setAttribute('grant_type', $sGrantType);
				$elemContainer->appendChild($elemAuthorisation);
			}
		}
		$elemSubject->appendChild($elemContainer);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getLocalAuthorisations() {
		if ($this->aLocalAuthorisations == NULL) {
			$this->loadLocalAuthorisations();	
		}
		return ($this->aLocalAuthorisations);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function loadLocalAuthorisations($bForced = FALSE) {
		
		if ($this->aLocalAuthorisations == NULL || $bForced) {
				
			$stmtAuthorisations = $this->prepareKnown($this->aQueries['loadLocalAuthorisations']);
			$stmtAuthorisations->bindValue(':uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_INT);
			$stmtAuthorisations->execute();
			
			$aAuthorisations = array();
			foreach ($stmtAuthorisations as $aRow) {
				$aAuthorisations[$aRow['fk_userentity']][$aRow['fk_authorisation']] = $aRow['e_granttype'];
			}
			$stmtAuthorisations->closeCursor();
			
			$this->aLocalAuthorisations = $aAuthorisations;
			
		}

	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function storeLocalAuthorisations($elemSubject) {
		$elemContainer = $elemSubject->ownerDocument->createElement('local_authorisations');
		foreach ($this->aLocalAuthorisations as $sEntityUUID => $aEntity) {
			foreach ($aEntity as $sAuthorisation => $sGrantType) {
				$elemAuthorisation = $elemSubject->ownerDocument->createElement('authorisation');
				$elemAuthorisation->setAttribute('uuid', $sEntityUUID);
				$elemAuthorisation->setAttribute('name', $sAuthorisation);
				$elemAuthorisation->setAttribute('grant_type', $sGrantType);
				$elemContainer->appendChild($elemAuthorisation);
			}
		}
		$elemSubject->appendChild($elemContainer);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getSupportedAuthorisations() {
		if ($this->aSupportedAuthorisations == NULL) {
			$this->loadSupportedAuthorisations();	
		}
		return ($this->aSupportedAuthorisations);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function loadSupportedAuthorisations($bForced = FALSE) {
		if ($this->aSupportedAuthorisations == NULL || $bForced) {
			$crNodeTypeManager = $this->crSession->getWorkspace()->getNodeTypeManager();
			$aAuthorisations = $crNodeTypeManager->getNodeType($this->getPrimaryNodeType())->getSupportedAuthorisations();
			$this->aSupportedAuthorisations = $aAuthorisations;
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function storeSupportedAuthorisations($elemSubject) {
		$elemContainer = $elemSubject->ownerDocument->createElement('supported_authorisations');
		foreach ($this->aSupportedAuthorisations as $sAuthorisation => $sParentAuthorisation) {
			$elemAuthorisation = $elemSubject->ownerDocument->createElement('authorisation');
			$elemAuthorisation->setAttribute('name', $sAuthorisation);
			if ($sParentAuthorisation != NULL) {
				$elemAuthorisation->setAttribute('parent', $sParentAuthorisation);
			} else {
				$elemAuthorisation->setAttribute('parent', '');
			}
			$elemContainer->appendChild($elemAuthorisation);
		}
		$elemSubject->appendChild($elemContainer);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function mergeAuthInherited($aLocal, $aInherited) {
		if (count($aInherited) == 0) {
			return ($aLocal);
		}
		foreach ($aInherited as $iID => $aAuthorisations) {
			foreach ($aAuthorisations as $sAuthorisation => $sGrantType) {
				if (isset($aLocal[$iID][$sAuthorisation]) && $aLocal[$iID][$sAuthorisation] == 'DENY') {
					continue;
				} else {
					$aLocal[$iID][$sAuthorisation] = $sGrantType;
				}
			}
		}
		return ($aLocal);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function mergeAuthGroups($aGroup1Auth, $aGroup2Auth) {
		// no authorisations in one group? nothing to merge then... 
		if (count($aGroup1Auth) == 0) {
			return ($aGroup2Auth);
		} elseif (count($aGroup2Auth) == 0) {
			return ($aGroup1Auth);
		}
		foreach ($aGroup2Auth as $sAuthorisation => $sGrantType) {
			// DENY outweights ALLOW
			if (isset($aGroup1Auth[$sAuthorisation]) && $aGroup1Auth[$sAuthorisation] == 'DENY') {
				continue;
			} else {
				$aGroup1Auth[$sAuthorisation] = $sGrantType;
			}
		}
		return ($aGroup1Auth);
	}
	
	//------------------------------------------------------------------------------
	/**
	* Merges two authorisation arrays (should be from a user and a group the 
	* user is member of). Transports all group authorisations to user 
	* @param 
	* @return 
	*/
	protected function mergeAuthUserGroup($aUserAuth, $aGroupAuth) {
		if (count($aGroupAuth) == 0) {
			return ($aUserAuth);
		}
		foreach ($aGroupAuth as $sAuthorisation => $sGrantType) {
			if (isset($aUserAuth[$sAuthorisation]) && $aUserAuth[$sAuthorisation] == 'DENY') {
				continue;
			} else {
				$aUserAuth[$sAuthorisation] = $sGrantType;
			}
		}
		return ($aUserAuth);
	}
	
	//------------------------------------------------------------------------------
	/**
	* Merges the authorisation hierarchy, spreading ALLOWs on child authorisations.
	* @param 
	* @return array 
	*/
	protected function mergeAuthHierarchy($aUserAuth) {
		foreach ($this->getSupportedAuthorisations() as $sAuth => $sParentAuth) {
			if ($sParentAuth != NULL && isset($aUserAuth[$sParentAuth]) && $aUserAuth[$sParentAuth] == 'ALLOW' && (!isset($aUserAuth[$sAuth]) || $aUserAuth[$sAuth] != 'DENY')) {
				$aUserAuth[$sAuth] = 'ALLOW';
			}
		}
		return ($aUserAuth);
	}
	
	//------------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return
	*/
	public function setAuthorisation($sAuthorisation, $sGrantType = 'ALLOW', $sEntityID = NULL) {
		
		$this->loadSupportedAuthorisations();
		if (!isset($this->aSupportedAuthorisations[$sAuthorisation])) {
			throw new sbException('Authorisation "'.$sAuthorisation.'" is not supported by the nodetype "'.$this->getPrimaryNodeType().'"');
		}
		
		// use current user if no entity is specified
		if ($sEntityID == NULL) {
			$sEntityID = User::getUUID();
		}
		
		if ($sGrantType == 'ALLOW' || $sGrantType == 'DENY') {
			$stmtSaveAuth = $this->getSession()->prepareKnown('sbSystem/node/setAuthorisation');
			$stmtSaveAuth->bindValue('entity_uuid', $sEntityID, PDO::PARAM_STR);
			$stmtSaveAuth->bindValue('subject_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
			$stmtSaveAuth->bindValue('authorisation', $sAuthorisation, PDO::PARAM_STR);
			$stmtSaveAuth->bindValue('granttype', $sGrantType, PDO::PARAM_STR);
			$stmtSaveAuth->execute();
		} elseif ($sGrantType == NULL) {
			$stmtRemoveAuth = $this->nodeSubject->getSession()->prepareKnown('sbSystem/node/removeAuthorisation');
			$stmtRemoveAuth->bindValue('entity_uuid', $_REQUEST->getParam('userentity'), PDO::PARAM_STR);
			$stmtRemoveAuth->bindValue('subject_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
			$stmtRemoveAuth->bindValue('authorisation', $sAuthorisation, PDO::PARAM_STR);
			$stmtRemoveAuth->execute();
		} else {
			throw new sbException('Invalid grant type "'.$sGrantType.'"');
		}
		
		// clear cache
		$cacheAuth = CacheFactory::getInstance('authorisations');
		$cacheAuth->clearAuthorisations($sEntityID);
		
		return;
	}
	
}

?>