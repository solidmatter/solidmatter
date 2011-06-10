<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.cr.nodeiterator');

//------------------------------------------------------------------------------
/**
* TODO: support full qualified naming including namespaces
*/
class sbCR_Node {
	
	//--------------------------------------------------------------------------
	/**
	* Session object through which this node was acquired
	* @var sbCR_Session
	*/ 
	protected $crSession			= NULL;
	/**
	* @var
	*/
	protected $crPropertyDefinitionCache = NULL;
	/**
	* @var 
	*/
	protected $crNodetype 			= NULL;
	/**
	* @var
	*/
	protected $crLock				= NULL;
	
	//--------------------------------------------------------------------------
	// internal stuff
	/**
	* 
	*/
	protected $elemSubject			= NULL;
	/**
	* 
	*/
	protected $aAppendedElements	= array();
	/**
	* 
	*/
	protected $sPath				= '';
	/**
	* cache for the materialized path of this node
	*/
	protected $sMPath				= '';
	/**
	* 
	*/
	protected $aQueries				= array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	*/
	protected $bIsModified			= FALSE;
	/**
	* 
	*/
	protected $bIsPersisted			= FALSE;
	
	// property definitons for this nodetype

	
	
	
	//--------------------------------------------------------------------------
	// tasks to perform on save()
	/**
	* 
	*/
	protected $aSaveTasks = array();
	/**
	* 
	*/
	protected $aModifiedProperties = array();
	/**
	* 
	*/
	protected $aModifiedChildren = array();
	
	
	
	//--------------------------------------------------------------------------
	// authorisation-related helpers
	/**
	* 
	*/
	protected $aAffectedGroups = array();
	/**
	* 
	*/
	protected $aAffectedUsers = array();
	
	//--------------------------------------------------------------------------
	// property translation table
	/**
	* 
	*/
	protected $aPropertyTranslation = array(
		'jcr:uuid'					=> 'uuid',
		'jcr:created'				=> 'created',
		'jcr:createdBy'				=> 'createdby',
		'jcr:lastModified'			=> 'modified',
		'jcr:lastModifiedBy'		=> 'modifiedby',
		'jcr:currentLifecycleState' => 'currentlifecyclestate',
		'sbcr:label'				=> 'label',
		'sbcr:inheritRights'		=> 'inheritrights',
		'sbcr:bequeathRights'		=> 'bequeathrights',
		'sbcr:bequeathLocalRights'	=> 'bequeathlocalrights',
		'sbcr:uid'					=> 'uid',
	);
	
	//--------------------------------------------------------------------------
	// initialisation
	//--------------------------------------------------------------------------
	/**
	* Constructor for the class, applies basic setup.
	* Calls __setQueries() and __init(), which can be overridden in derived classes.
	* @param DOMElement contains the node info wrapped by the sbCR_Node class 
	* @param cbCR_Session the repository session object that retrieved this node
	* @param string the UUID of the parent that acquired this node, if given
	*/
	public function __construct($elemSubject = NULL, $crSession, $sParentUUID = NULL) {
		
		// store mandatory objects
		$this->elemSubject = $elemSubject;
		$this->crSession = $crSession;
		
		// arbitrary init methods
		$this->__setQueries();
		$this->__init();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Initializes the queries this node uses to interact with the repository.
	* Queries are stored in $this->aQueries in a multidimensional associative 
	* array, each entry containing a string that is the identifier of the real
	* query used with prepareKnown() in sbPDO objects.
	*/
	protected function __setQueries() {
		
		// getting child nodes
		$this->aQueries['loadChildren']['byMode']			= 'sbCR/node/loadChildren/mode/standard/byOrder';
		$this->aQueries['loadChildren']['debug']			= 'sbCR/node/loadChildren/debug';
		$this->aQueries['countChildren']['byMode']			= 'sbCR/node/countChildren/mode';
		$this->aQueries['countChildren']['debug']			= 'sbCR/node/countChildren/debug';
		$this->aQueries['getChild']['byName']				= 'sbCR/node/getChild/byName';
		
		// saving the node and primary properties
		$this->aQueries['save']['new']						= 'sbCR/node/save';
		$this->aQueries['save']['existing']					= 'sbCR/node/save';
		
		// getting parent nodes
		$this->aQueries['getPrimaryParent']					= 'sbCR/node/getPrimaryParent';
		$this->aQueries['getParents']['all']				= 'sbCR/node/getParents/all';
		$this->aQueries['getParents']['byNodetype']			= 'sbCR/node/getParents/byNodetype';
		
		// hierarchy related
		$this->aQueries['hierarchy/getSharedSet']			= 'sbCR/node/getSharedSet';
		$this->aQueries['hierarchy/isAncestorOf']			= 'sbCR/node/checkAncestor';
		
		// lifecycle
		$this->aQueries['lifecycle/getTransitions']			= 'sbCR/node/lifecycle/getAllowedTransitions';
		$this->aQueries['lifecycle/followTransition']		= 'sbCR/node/lifecycle/followTransition';
		
		// linking / nested set stuff
		$this->aQueries['addLink']['getBasicInfo']			= 'sbCR/node/addLink/getBasicInfo';
		$this->aQueries['addLink']['insertNode']			= 'sbCR/node/addLink/insertNode';
		$this->aQueries['delete']['getBasicInfo']			= 'sbCR/node/hierarchy/getInfo';
		$this->aQueries['delete']['shift']					= 'sbCR/node/removeLink/shiftLeft';
		$this->aQueries['removeLink']						= 'sbCR/node/removeLink';
		$this->aQueries['removeDescendantLinks']			= 'sbCR/node/removeDescendantLinks';
		$this->aQueries['reorder']['getBasicInfo']			= 'sbCR/node/orderBefore/getInfo';
		$this->aQueries['reorder']['moveSiblings']			= 'sbCR/node/hierarchy/moveSiblings';
		$this->aQueries['reorder']['moveNode']				= 'sbCR/node/orderBefore/writeOrder/node';
		$this->aQueries['getLinkStatus']					= 'sbCR/node/getLinkStatus';
		$this->aQueries['setLinkStatus']['normal']			= 'sbCR/node/setLinkStatus';
		$this->aQueries['setLinkStatus']['allSecondary']	= 'sbCR/node/setLinkStatus/allSecondary';
		$this->aQueries['setLinkStatus']['newPrimary']		= 'sbCR/node/setLinkStatus/newPrimary';
		$this->aQueries['delete']['forGood']				= 'sbCR/node/delete/forGood';
		
		// property related
		$this->aQueries['loadProperties']['extended']		= 'sbCR/node/loadProperties/extended';
		$this->aQueries['loadProperties']['auxiliary']		= NULL;
		$this->aQueries['loadProperties']['external']		= 'sbCR/node/loadProperties/external';
		$this->aQueries['saveProperties']['auxiliary']		= NULL;
		$this->aQueries['saveProperties']['external']		= 'sbCR/node/saveProperty/external';
		$this->aQueries['saveBinaryAttribute']				= 'sbCR/node/saveBinaryProperty';
		$this->aQueries['loadBinaryAttribute']				= 'sbCR/node/loadBinaryProperty';
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Initialization for this node object.
	* Primarily intended to be used when extending the class to override the
	* defaults.
	*/
	protected function __init() {
		// do nothing for now
	}
	
	//--------------------------------------------------------------------------
	/**
	* currently deactivated for some reason...
	* @param 
	* @return 
	*/
	/*public function cloneElement($sName = NULL, $bDeep = FALSE, $bUseContainer = FALSE) {
		
		$elemSubject = $this->elemSubject->cloneNode();
		if ($this->niChildren != NULL && $bDeep) {
			if ($bUseContainer) {
				$elemContainer = $this->elemSubject->ownerDocument->createElement('children');
				$elemSubject->appendChild($elemContainer);
			} else {
				$elemContainer = $elemSubject;
			}
			foreach ($this->niChildren as $nodeChild) {
				$elemContainer->appendChild($nodeChild->getElement($bDeep, $bUseContainer));
			}
		}
		return ($elemSubject);
	}*/
	
	//--------------------------------------------------------------------------
	/**
	* Prepares a statement based on the node's environment, e.g. current sbCR_Session.
	* @param string the identifier of the query/statement to prepare
	* @return sbPDOStatement the wanted prepared statement
	*/
	protected function prepareKnown($sQueryID) {
		if (isset($this->aQueries[$sQueryID])) {
			$sQueryID = $this->aQueries[$sQueryID];	
		}
		return ($this->crSession->prepareKnown($sQueryID));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns information on this node's childnode with the given name if possible.
	* The following values are available in the returned associative array:
	* - uuid
	* - fk_nodetype
	* - s_name
	* - s_displaytype
	* - s_extension
	* Throws NodeNotFoundException if no or serveral nodes are found.
	* TODO: support same name siblings
	* @param string the child's name
	* @return array contains the info on the found child
	*/
	protected function getChildByName($sName) {
		
		$stmtChild = $this->crSession->prepareKnown($this->aQueries['getChild']['byName']);
		$sUUID = $this->elemSubject->getAttribute('uuid');
		$stmtChild->bindParam(':parent_uuid', $sUUID, PDO::PARAM_STR);
		$stmtChild->bindParam(':name', $sName, PDO::PARAM_STR);
		$stmtChild->execute();
		
		$iCheck = 0;
		foreach ($stmtChild as $aRow) {
			$iCheck++;
			$aChildNode = $aRow;
		}
		$stmtChild->closeCursor();
		
		if ($iCheck > 1) {
			throw new NodeNotFoundException('multiple results for getChildByName("'.$sName.'")');
		} elseif ($iCheck == 0) {
			throw new NodeNotFoundException('no result for getChildByName("'.$sName.'")');
		}
		
		return ($aChildNode);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM:
	* @param 
	* @return 
	*/
	protected function getChildren($sMode = 'debug', $aRequiredAuthorisations = array()) {
		
		if (!isset($this->aChildNodes[$sMode])) { // load children
		
			if ($sMode == 'debug') {
				$stmtChildren = $this->crSession->prepareKnown($this->aQueries['loadChildren']['debug']);
				$mParam = $this->elemSubject->getAttribute('uuid');
				$stmtChildren->bindParam(':parent_uuid', $mParam, PDO::PARAM_STR);
			} else {
				$stmtChildren = $this->crSession->prepareKnown($this->aQueries['loadChildren']['byMode']);
				$mParam = $this->elemSubject->getAttribute('uuid');
				$stmtChildren->bindParam(':parent_uuid', $mParam, PDO::PARAM_STR);
				$stmtChildren->bindParam(':mode', $sMode, PDO::PARAM_INT);
			}
			$stmtChildren->execute();
			$aChildren = $stmtChildren->fetchAll(PDO::FETCH_ASSOC);
			
			// build new NodeIterator
			$aChildNodes = array();
			foreach ($aChildren as $aRow) {
				$nodeCurrentChild = $this->crSession->getNodeByIdentifier($aRow['uuid'], $this->getIdentifier());
				$aChildNodes[] = $nodeCurrentChild;
			}
			$niChildNodes = new sbCR_NodeIterator($aChildNodes);
			
		} else { // cached
			
			$niChildNodes = $this->aChildNodes[$sMode];
			
		}
		
		// filter nodes after retrieval if necessary
		if (count($aRequiredAuthorisations) > 0) {
			$aFilteredChildNodes = array();
			foreach ($niChildNodes as $nodeCurrentChild) {
				$bCheck = TRUE;
				foreach ($aRequiredAuthorisations as $sAuthorisation) {
					if (!User::isAuthorised($sAuthorisation, $nodeCurrentChild)) {
						$bCheck = FALSE;
					}
				}
				if ($bCheck) {
					$aFilteredChildNodes[] = $nodeCurrentChild;
				}
			}
			$niChildNodes = new sbCR_NodeIterator($aFilteredChildNodes);
		}
		
		return ($niChildNodes);
		
	}
	
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------
	// hierarchy
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function addChild($nodeChild) {
		// FIXME: remove this hack! 
		$this->addSaveTask('add_child', array('uuid' => $nodeChild->getIdentifier()));
		// TODO: implement save for same-name-siblings
		$this->aModifiedChildren[$nodeChild->getIdentifier()] = $nodeChild;
		$this->bIsModified = TRUE;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function isPrimaryParent($nodeParent) {
		
		$stmtGetStatus = $this->crSession->prepareKnown($this->aQueries['getLinkStatus']);
		$stmtGetStatus->bindParam('parent_uuid', $nodeParent->getIdentifier(), PDO::PARAM_STR);
		$stmtGetStatus->bindParam('child_uuid', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtGetStatus->execute();
		
		$aRow = $stmtGetStatus->fetch(PDO::FETCH_ASSOC);
		if ($aRow['b_primary'] == 'TRUE') {
			$bPrimary = TRUE;
		} else {
			$bPrimary = FALSE;
		}
		
		$stmtGetStatus->closeCursor();
		
		return ($bPrimary);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getNumberOfParents() {
		
		if ($this->isNew) {
			return (0);
		} else {
			$stmtGetParents = $this->crSession->prepareKnown($this->aQueries['getParents']);
			$stmtGetParents->bindValue('child_uuid', $this->getIdentifier(), PDO::PARAM_STR);
			$stmtGetParents->execute();
			$iNumberOfParents = $stmtGetParents->rowCount();
			$stmtGetParents->closeCursor();
			return ($iNumberOfParents);
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getNumberOfChildren($sMode = NULL) {
		
		if ($this->elemSubject->getAttribute('query') == 'new') {
			// TODO: this is not correct, new nodes might have new children
			throw new RepositoryException('new nodes don\'t have children');
		} else {
			if ($sMode != NULL && $sMode != 'debug') {
				$stmtGetChildren = $this->crSession->prepareKnown($this->aQueries['countChildren']['byMode']);
				$stmtGetChildren->bindValue('parent_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
				$stmtGetChildren->bindValue('mode', $sMode, PDO::PARAM_STR);
				$stmtGetChildren->execute();
			} else {
				$stmtGetChildren = $this->crSession->prepareKnown($this->aQueries['countChildren']['debug']);
				$stmtGetChildren->bindValue('parent_uuid', $this->getProperty('jcr:uuid'), PDO::PARAM_STR);
				$stmtGetChildren->execute();
			}
			$aResults = $stmtGetChildren->fetchAll(PDO::FETCH_ASSOC);
			$stmtGetChildren->closeCursor();
			$iNumberOfChildren = $aResults[0]['num_children'];
			return ($iNumberOfChildren);
			
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* TODO: implement this non-trivial method (options: multiple/unique all/onlyPrimary/onlySecondary all/byMode/byNodetype)
	* @param 
	* @return 
	*/
	protected function getNumberOfDescendats($sMode = NULL) {
		
		
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the descendant relation state of this node to another node.
	* @param Node the node this node is checked against
	* @return boolean true if this node is a descendant of the subject node; false otherwise
	*/
	protected function isDescendantOf($nodeSubject) {
		return ($nodeSubject->isAncestorOf($this));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the ancestor relation state of this node to another node.
	* @param Node the node this node is checked against
	* @return boolean true if this node is an ancestor of the subject node; false otherwise
	*/
	protected function isAncestorOf($nodeSubject) {
		
		try {
			$bIsAncestor = FALSE;
			$niParents = $nodeSubject->getParents(); // consider shared sets, getParent() is not enough
			foreach ($niParents as $nodeParent) {
				if ($nodeParent->isSame($this)) {
					return (TRUE);
				} else {
					$bIsAncestor |= $this->isAncestorOf($nodeParent);
				}
			}
			return $bIsAncestor;
		} catch (ItemNotFoundException $e) {
			// ItemNotFoundException means the top of the tree (the root node) has been reached
			return (FALSE);
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the materialized path of this node.
	* Generation is based on taking the last X chars of the respective node's
	* uuid, climbing up the hierarchy to get the full path. X is defined in
	* sbCR_Repository class file.
	* @return string the full materialized path
	*/
	public function getMPath($bJustThisNode = FALSE) {
		if ($this->sMPath != '') {
			return ($this->sMPath);
		}
		// use last 5 chars
		$sMPath = substr(sha1($this->getIdentifier()), -REPOSITORY_MPHASH_SIZE);
		if ($this->isNodeType('sbSystem:Root') || $bJustThisNode) {
			return ($sMPath);
		} else {
			return ($this->getParent()->getMPath().$sMPath);
		}
	}
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------
	// save/move
	//--------------------------------------------------------------------------
	/**
	* Adds a task that is to be executed when this node is saved.
	* @param string the type of task
	* @param array additional information associated with the task (optional)
	*/
	protected function addSaveTask($sTaskType, $aOptions = NULL) {
		switch ($sTaskType) {
			case 'save_node':
			case 'save_properties':
			case 'remove_node':
			case 'remove_share':
				$this->aSaveTasks[$sTaskType] = TRUE;
				break;
			case 'order_before':
			case 'add_child':
				$this->aSaveTasks[$sTaskType][] = $aOptions;
				break;
			default:
				throw new RepositoryException('unknown task type "'.$sTaskType.'"');
		}
		$this->crSession->addSaveTask('save_node', array('subject' => $this));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Validates all pending changes currently recorded in this Session that
	* apply to this Item  or any of its descendants (that is, the subtree
	* rooted at this Item). If validation of all pending changes succeeds,
	* then this change information is cleared from the Session. If the save
	* occurs outside a transaction, the changes are persisted and thus made
	* visible to other Sessions. If the save occurs within a transaction, the
	* changes are not persisted until the transaction is committed.
	* 
	* If validation fails, then no pending changes are saved and they remain
	* recorded on the Session. There is no best-effort or partial save.
	* 
	* The item in persistent storage to which a transient item is saved is
	* determined by matching identifiers and paths.
	* 
	* An AccessDeniedException will be thrown if any of the changes to be
	* persisted would violate the access privileges of this Session.
	* 
	* If any of the changes to be persisted would cause the removal of a node
	* that is currently the target of a REFERENCE property then a
	* ReferentialIntegrityException is thrown, provided that this Session has
	* read access to that REFERENCE property. If, on the other hand, this
	* Session does not have read access to the REFERENCE property in question,
	* then an AccessDeniedException is thrown instead.
	* 
	* An ItemExistsException will be thrown if any of the changes to be
	* persisted would be prevented by the presence of an already existing item
	* in the workspace.
	* 
	* A ConstraintViolationException will be thrown if any of the changes to be
	* persisted would violate a node type restriction. Additionally, a
	* repository may use this exception to enforce implementation- or
	* configuration-dependant restrictions.
	* 
	* An InvalidItemStateException is thrown if any of the changes to be
	* persisted conflicts with a change already persisted through another
	* session and the implementation is such that this conflict can only be
	* detected at save-time and therefore was not detected earlier, at
	* change-time.
	* 
	* A VersionException is thrown if the save would make a result in a change
	* to persistent storage that would violate the read-only status of a
	* checked-in node.
	* 
	* A LockException is thrown if the save would result in a change to
	* persistent storage that would violate a lock.
	* 
	* A NoSuchNodeTypeException is thrown if the save would result in the
	* addition of a node with an unrecognized node type.
	* 
	* A RepositoryException will be thrown if another error occurs.
	* 
	* TODO: changes are not validate before persisting, instead the save tasks
	* are carried out inside a transaction that may fail
	* NOTE: ACLs are not supported, so there are no AccessDeniedExceptions
	* thrown
	* TODO: this is not close enough to the standard, changes should be saved
	* when save() is called on an ancestor of this node, too. Needs to be
	* figured out, possibly a save should match all pending changes against
	* the node's ancestors based on uuid.
	* TODO: get a number of things closer to the standard, e.g. references
	*/
	public function save() {
		
		// check for pending changes
		if (count($this->aModifiedProperties) > 0) {
			$this->addSaveTask('save_properties');
			$this->addSaveTask('save_node');
			$this->aSaveTasks = array_reverse($this->aSaveTasks);
		}
		
		// anything to do?
		if (count($this->aSaveTasks) == 0) {
			return (FALSE);
		}
		
		// TODO: perform validation first on pending changes?
		
		// perform save in a transaction
		$this->crSession->beginTransaction('sbCR_Node::save');
		
		// should we delete the node? then do it and remove other tasks
		if (isset($this->aSaveTasks['remove_node'])) {
			
			// recursing through children disabled because the nodes might be in use in another workspace
			// TODO: implement connected workspaces, versioning etc... :-|
			foreach ($this->getChildren() as $nodeChild) {
				$nodeChild->remove();
				$nodeChild->save();
			}
			
			// remove the node from the repository
			// TODO: should only remove links (check references first etc.)
			$this->deleteNode();
			
//			// first remove all decendant links
//			$this->deleteDescendantLinks();
//			
//			// then remove all links to this node from repository tree
//			$niParents = $this->getParents();
//			foreach($niParents as $nodeParent) {
//				$this->deleteLink($nodeParent);
//			}
			
			// finally clean up and remove this node
			$this->aModifiedChildren = array();
			$this->aModifiedProperties = array();
			$this->bIsModified = FALSE;
			
		}
		
		// remove this share from shared set
		// TODO: instead of throwing an exception, a remaining link shoud become primary
		if (isset($this->aSaveTasks['remove_share'])) {
			
			$nodePrimaryParent = $this->getPrimaryParent();
			$nodeParent = $this->getParent();
			if ($nodeParent->isSame($nodePrimaryParent)) {
				throw new RepositoryException('currently you can only remove non-primary nodes from shared set');	
			}
			
			$this->deleteLink($nodeParent);
			
			unset($this->aSaveTasks['remove_share']);
			
		}
		
		// save primary node properties first
		if (isset($this->aSaveTasks['save_node'])) {
			$this->saveNode();
			unset($this->aSaveTasks['save_node']);
		}
		
		// then save other properties
		if (isset($this->aSaveTasks['save_properties'])) {
			$this->saveProperties();
			unset($this->aSaveTasks['save_properties']);
		}
		
		// then add all children
		if (isset($this->aSaveTasks['add_child'])) {
			
			foreach ($this->aSaveTasks['add_child'] as $iTaskNumber => $aOptions) {
				
				$sParentUUID = $this->elemSubject->getAttribute('parent');
				$sCurrentUUID = $this->getIdentifier();
				$nodeChild = $this->aModifiedChildren[$aOptions['uuid']];				
				$nodeChild->saveNode();
				$sChildUUID = $nodeChild->getIdentifier();
				
				// get basic info
				$stmtInfo = $this->crSession->prepareKnown($this->aQueries['addLink']['getBasicInfo']);
				$stmtInfo->bindValue('parent_uuid', $sParentUUID, PDO::PARAM_STR);
				$stmtInfo->bindValue('current_uuid', $sCurrentUUID, PDO::PARAM_STR);
				$stmtInfo->bindValue('child_uuid', $sChildUUID, PDO::PARAM_STR);
				$stmtInfo->bindValue('child_name', $nodeChild->getProperty('name'), PDO::PARAM_STR);
				$stmtInfo->execute();
				//var_dumpp($stmtInfo->fetchAll());
				$bFound = FALSE;
				foreach ($stmtInfo as $aRow) {
					$bFound = TRUE;
					$iLevel = $aRow['n_level'];
					$iPosition = $aRow['n_position'];
					$iNumParents = $aRow['n_numparents'];
					$iNumSameNameSiblings = $aRow['n_numsamenamesiblings'];
				}
				$stmtInfo->closeCursor();
				
				if (!$bFound) {
					throw new RepositoryException('no info found on node '.$this->getProperty('label').' ('.$this->getIdentifier().')');
				}
				if ($iNumSameNameSiblings != 0) {
					throw new ItemExistsException('a node with the name "'.$nodeChild->getProperty('name').'" already exists under '.$this->getProperty('label').' ('.$this->getProperty('jcr:uuid').')');	
				}
				
				$sIsPrimary = 'FALSE';
				if ($iNumParents == 0) {
					$sIsPrimary = 'TRUE';
				}
				
				// insert new link for node
				$stmtChild = $this->crSession->prepareKnown($this->aQueries['addLink']['insertNode']);
				$stmtChild->bindValue('child_uuid', $sChildUUID, PDO::PARAM_STR);
				$stmtChild->bindValue('parent_uuid', $sCurrentUUID, PDO::PARAM_STR);
				$stmtChild->bindValue('is_primary', $sIsPrimary, PDO::PARAM_STR);
				$stmtChild->bindValue('order', $iPosition, PDO::PARAM_INT);
				$stmtChild->bindValue('level', $iLevel+1, PDO::PARAM_INT);
				$stmtChild->bindValue('mpath', $this->getMPath(), PDO::PARAM_INT);
				$stmtChild->execute();
				
				$nodeChild->save();
				
				// remove task
				unset($this->aSaveTasks['add_child'][$iTaskNumber]);
				
			}
			
			// remove task type
			unset($this->aSaveTasks['add_child']);
			
		}
		
		// finally reorder the children
		if (isset($this->aSaveTasks['order_before'])) {
			
			foreach ($this->aSaveTasks['order_before'] as $iTaskNumber => $aOptions) {
				
				// same node? then do nothing
				if ($aOptions['SourceNode'] == $aOptions['DestinationNode']) {
					return (TRUE);
				}
				
				$sUUID = $this->getIdentifier();
				
				// get position info
				$stmtGetInfo = $this->prepareKnown($this->aQueries['reorder']['getBasicInfo']);
				
				$stmtGetInfo->bindValue(':parent_uuid', $sUUID, PDO::PARAM_STR);
				$stmtGetInfo->bindValue(':child_name', $aOptions['SourceNode'], PDO::PARAM_STR);
				$stmtGetInfo->execute();
				$aSourceInfo = $stmtGetInfo->fetchAll(PDO::FETCH_ASSOC);
				if (count($aSourceInfo) == 0) {
					throw new ItemNotFoundException('source node does not exist ('.$aOptions['SourceNode'].')');
				}
				$aSourceInfo = $aSourceInfo[0];
				
				$stmtGetInfo->bindValue(':parent_uuid', $sUUID, PDO::PARAM_STR);
				$stmtGetInfo->bindValue(':child_name', $aOptions['DestinationNode'], PDO::PARAM_STR);
				$stmtGetInfo->execute();
				$aDestinationInfo = $stmtGetInfo->fetchAll(PDO::FETCH_ASSOC);
				if (count($aDestinationInfo) == 0) {
					throw new ItemNotFoundException('destination node does not exist ('.$aOptions['DestinationNode'].')');
				}
				$aDestinationInfo = $aDestinationInfo[0];
				
				// update position info on moved siblings
				if ($aDestinationInfo['n_order'] < $aSourceInfo['n_order']) { // moved up
					$iOffset = 1;
					$iLow = $aDestinationInfo['n_order'];
					$iHigh = $aSourceInfo['n_order'];
					$iTargetPosition = $aDestinationInfo['n_order'];
				} else { // moved down
					$iOffset = -1;
					$iLow = $aSourceInfo['n_order'];
					$iHigh = $aDestinationInfo['n_order']-1;
					$iTargetPosition = $aDestinationInfo['n_order']-1;
				}
				$stmtOrder = $this->crSession->prepareKnown($this->aQueries['reorder']['moveSiblings']);
				$stmtOrder->bindValue('offset', $iOffset, PDO::PARAM_INT);
				$stmtOrder->bindValue('low_position', $iLow, PDO::PARAM_INT);
				$stmtOrder->bindValue('high_position', $iHigh, PDO::PARAM_INT);
				$stmtOrder->bindValue('parent_uuid', $sUUID, PDO::PARAM_STR);
				$stmtOrder->execute();
				
				// update position info on moved node
				$stmtOrder = $this->crSession->prepareKnown($this->aQueries['reorder']['moveNode']);
				$stmtOrder->bindValue('target_position', $iTargetPosition, PDO::PARAM_INT);
				$stmtOrder->bindValue('parent_uuid', $sUUID, PDO::PARAM_STR);
				$stmtOrder->bindValue('child_uuid', $aSourceInfo['uuid'], PDO::PARAM_STR);
				$stmtOrder->execute();
				
				// remove task
				unset($this->aSaveTasks['order_before'][$iTaskNumber]);
				
			}
			
			// remove task type
			unset($this->aSaveTasks['order_before']);
			
		}
		
		// all pending changes have been saved within the transaction, now persist them 
		$this->crSession->commit('sbCR_Node::save');
		
		// let the session know the node has been saved
		$this->crSession->removeSaveTaskForNode($this);
		
		// update node state and pending changes
		//$this->aSaveTasks = array();
		$this->aModifiedChildren = array();
		$this->aModifiedProperties = array();
		$this->bIsModified = FALSE;
		
//		if ($this->isNew()) {
//			$this->elemSubject->setAttribute('query', $this->getIdentifier());
//		}
		
		return (TRUE);
		
	}
	
	
	
	//--------------------------------------------------------------------------
	/**
	* Persists the node itself and the basic mandatory properties.
	* @return boolean true if the node was saved successfully; false if wasn't modified
	*/
	protected function saveNode() {
		
		// just return for now if node is not modified
		if (!$this->isModified()) {
			return (FALSE);
		}
		
		if ($this->isNew()) {
			$stmtInsert = $this->crSession->prepareKnown($this->aQueries['save']['new']);
			$stmtInsert->bindValue(':uuid',					$this->getIdentifier(),							PDO::PARAM_STR);
			$stmtInsert->bindValue(':uid',					$this->getProperty('uid'),						PDO::PARAM_STR);
			$stmtInsert->bindValue(':nodetype',				$this->getProperty('nodetype'),					PDO::PARAM_STR);
			$stmtInsert->bindValue(':label',				$this->getProperty('label'),					PDO::PARAM_STR);
			$stmtInsert->bindValue(':name',					$this->getProperty('name'),						PDO::PARAM_STR);
			$stmtInsert->bindValue(':inheritrights',		$this->getProperty('sbcr:inheritRights'),		PDO::PARAM_STR);
			$stmtInsert->bindValue(':bequeathrights',		$this->getProperty('sbcr:bequeathRights'),		PDO::PARAM_STR);
			$stmtInsert->bindValue(':bequeathlocalrights',	$this->getProperty('sbcr:bequeathLocalRights'),	PDO::PARAM_STR);
			$stmtInsert->bindValue(':user_id',				User::getUUID(),								PDO::PARAM_STR);
			$stmtInsert->execute();
			// the node is now persisted, the internal query must be updated so that it is not considered 'new' anymore
			$this->elemSubject->setAttribute('query', $this->getIdentifier());
		} else {
			$stmtInsert = $this->crSession->prepareKnown($this->aQueries['save']['existing']);
			$stmtInsert->bindValue(':uuid',					$this->getIdentifier(),							PDO::PARAM_STR);
			$stmtInsert->bindValue(':uid',					$this->getProperty('uid'),						PDO::PARAM_STR);
			$stmtInsert->bindValue(':label',				$this->getProperty('label'),					PDO::PARAM_STR);
			$stmtInsert->bindValue(':name',					$this->getProperty('name'),						PDO::PARAM_STR);
			$stmtInsert->bindValue(':inheritrights',		$this->getProperty('sbcr:inheritRights'),		PDO::PARAM_STR);
			$stmtInsert->bindValue(':bequeathrights',		$this->getProperty('sbcr:bequeathRights'),		PDO::PARAM_STR);
			$stmtInsert->bindValue(':bequeathlocalrights',	$this->getProperty('sbcr:bequeathLocalRights'),	PDO::PARAM_STR);
			$stmtInsert->bindValue(':user_id',				User::getUUID(),								PDO::PARAM_STR);
			// FIXME: not really needed, but query requires it
			$stmtInsert->bindValue(':nodetype',				$this->getProperty('nodetype'),					PDO::PARAM_STR);
			$stmtInsert->execute();
		}
		$stmtInsert->closeCursor();
		
		return (TRUE);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function saveProperties($sType = 'FULL') {
		
		$this->initPropertyDefinitions();
		if ($sType == 'FULL') {
			// check if aux or ext properties have changed
			$bChangedAux = FALSE;
			$bChangedExt = FALSE;
			foreach ($this->aModifiedProperties as $sProperty => $mValue) {
				if ($this->crPropertyDefinitionCache->getStorageType($sProperty) == 'AUXILIARY') {
					$bChangedAux = TRUE;
				}
				if ($this->crPropertyDefinitionCache->getStorageType($sProperty) == 'EXTERNAL') {
					$bChangedExt = TRUE;
				}
			}
			if ($bChangedAux && $this->crPropertyDefinitionCache->usesStorage('AUXILIARY')) {
				$this->saveProperties('AUXILIARY');
			}
			if ($bChangedExt && $this->crPropertyDefinitionCache->usesStorage('EXTERNAL')) {
				$this->saveProperties('EXTERNAL');
			}
			return (TRUE);
		}
		
		if (!isset($this->aQueries['saveProperties'][strtolower($sType)])) {
			throw new sbException('property storage type not supported: '.$sType);
		}
		
		// TODO: think over this again...
		if ($sType == 'EXTERNAL') {
			$stmtSave = $this->crSession->prepareKnown($this->aQueries['saveProperties'][strtolower($sType)]);
			foreach ($this->crPropertyDefinitionCache as $sName => $aDetails) {
				// TODO: remove empty properties
				if ($aDetails['e_storagetype'] == 'EXTERNAL' && $aDetails['b_protected'] == 'FALSE') {
					$stmtSave->bindValue(':node_id', $this->getIdentifier(), PDO::PARAM_INT);
					$stmtSave->bindValue(':attributename', $sName, PDO::PARAM_STR);
					$stmtSave->bindValue(':content', $this->elemSubject->getAttribute($sName), PDO::PARAM_LOB);
					$stmtSave->execute();
				}
			}
			$stmtSave->closeCursor();
		} elseif ($sType == 'AUXILIARY') {
			$stmtSave = $this->crSession->prepareKnown($this->aQueries['saveProperties']['auxiliary']);
			$stmtSave->bindValue(':node_id', $this->getIdentifier(), PDO::PARAM_INT);
			foreach ($this->crPropertyDefinitionCache as $sName => $aDetails) {
				if ($aDetails['e_storagetype'] == 'AUXILIARY') {
					if ($this->isNew() && $aDetails['b_protectedoncreation'] == 'TRUE') {
						$mValue = $aDetails['s_defaultvalues'];
					} elseif (!$this->isNew() && $aDetails['b_protected'] == 'TRUE') {
						$mValue = $this->elemSubject->getAttribute($sName);
					} else {
						$mValue = $this->elemSubject->getAttribute($sName);
					}
					if (strlen(trim($mValue)) == 0) {
						$mValue = NULL;
						$eParam = PDO::PARAM_NULL;
					} else {
						$eParam = PDO::PARAM_STR;
					}
					$stmtSave->bindValue(':'.$sName, $mValue, $eParam);
				}
			}
			$stmtSave->execute();
			$stmtSave->closeCursor();
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function deleteNode() {
	
		// delete node for good
		$cachePaths = CacheFactory::getInstance('paths');
		$cachePaths->clear($this->getPath());
		
		$stmtDeleteNode = $this->crSession->prepareKnown($this->aQueries['delete']['forGood']);
		$stmtDeleteNode->bindValue('uuid', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtDeleteNode->execute();
		$stmtDeleteNode->closeCursor();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getHierarchyInfo($nodeParent = NULL) {
		
		$sChildUUID = $this->getIdentifier();
		if ($nodeParent == NULL) {
			$sParentUUID = (string) $this->elemSubject->getAttribute('parent');
		} else {
			$sParentUUID = $nodeParent->getProperty('jcr:uuid');
		}
		
		$stmtGetInfo = $this->prepareKnown('sbCR/node/hierarchy/getInfo');
		$stmtGetInfo->bindValue(':parent_uuid', $sParentUUID, PDO::PARAM_STR);
		$stmtGetInfo->bindValue(':child_uuid', $sChildUUID, PDO::PARAM_STR);
		$stmtGetInfo->execute();
		foreach ($stmtGetInfo as $aRow) {
			$aInfo['level'] = $aRow['n_level'];
			$aInfo['order'] = $aRow['n_order'];
			$aInfo['primary'] = constant($aRow['b_primary']);
		}
		$stmtGetInfo->closeCursor();
		if (!isset($aInfo)) {
			throw new sbException('unable to get position for child "'.$sChildUUID.'" and parent "'.$sParentUUID.'"');
		}
		return ($aInfo);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* TODO: update path cache
	* @param 
	* @return 
	*/
	protected function deleteLink($nodeParent) {
		
		// get info
		$aInfo = $this->getHierarchyInfo($nodeParent);
		
		// delete link to parent
		$stmtRemoveLink = $this->crSession->prepareKnown($this->aQueries['removeLink']);
		$stmtRemoveLink->bindValue('child_uuid', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtRemoveLink->bindValue('parent_uuid', $nodeParent->getIdentifier(), PDO::PARAM_STR);
		$stmtRemoveLink->execute();
		
		// shift following nodes
		$stmtShift = $this->crSession->prepareKnown('sbCR/node/hierarchy/moveSiblings');
		$stmtShift->bindValue('parent_uuid', $nodeParent->getIdentifier(), PDO::PARAM_STR);
		$stmtShift->bindValue('offset', -1, PDO::PARAM_INT);
		$stmtShift->bindValue('low_position', $aInfo['order'], PDO::PARAM_INT);
		// FIXME: don't use static value!
		$stmtShift->bindValue('high_position', 1000000, PDO::PARAM_INT);
		$stmtShift->execute();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function setPrimaryLink($nodeParent) {
		
		$this->crSession->beginTransaction('NEW_PRIMARY_PARENT');
		
		$stmtAllSecondary = $this->crSession->prepareKnown($this->aQueries['setLinkStatus']['allSecondary']);
		$stmtAllSecondary->bindValue('child_uuid', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtAllSecondary->execute();
		
		$stmtSetPrimary = $this->crSession->prepareKnown($this->aQueries['setLinkStatus']['normal']);
		$stmtSetPrimary->bindValue('status', 'TRUE', PDO::PARAM_STR);
		$stmtSetPrimary->bindValue('parent_uuid', $nodeParent->getIdentifier(), PDO::PARAM_STR);
		$stmtSetPrimary->bindValue('child_uuid', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtSetPrimary->execute();
		
		$this->crSession->commit('NEW_PRIMARY_PARENT');
		
		return;
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Deletes all links of this node's ancestors from the hierarchy table 
	* TODO: update path cache
	* @param 
	* @return 
	*/
	protected function deleteDescendantLinks() {
		
		$aInfo = $this->getHierarchyInfo($this->getPrimaryParent());
		
		// delete link to parent
		$stmtRemoveLink = $this->crSession->prepareKnown($this->aQueries['removeDescendantLinks']);
		$stmtRemoveLink->bindValue('mpath', $aInfo['left'], PDO::PARAM_STR);
		$stmtRemoveLink->execute();
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	







	
	//--------------------------------------------------------------------------
	// Item methods
	//--------------------------------------------------------------------------
	/**
	* Accepts an ItemVistor. Calls the appropriate ItemVistor  visit method of 
	* the according to whether this  Item is a Node or a Property.
	* @param 
	* @return 
	*/
	public function accept($crVisitor) {
		throw new UnsupportedRepositoryException();
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the ancestor of the specified depth. An ancestor of depth x is the 
	* Item that is x levels down along the path from the root node to this 
	* Item. 
	* TODO: test this!
	* @param 
	* @return 
	*/
	public function getAncestor($iDepth) {
		
		$stmtGetAncestor = $this->crSession->prepareKnown('sb_system/node/getAncestors');
		$stmtGetAncestor->bindValue(':child_uuid', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtGetAncestor->bindValue(':depth', $iDepth, PDO::PARAM_INT);
		$stmtGetAncestor->execute();
		
		$sAncestorUUID = NULL;
		foreach ($stmtGetAncestor as $aRow) {
			$sAncestorUUID = $aRow['fk_parent'];
		}
		if ($sAncestorUUID === NULL) {
			throw new ItemNotFoundException();	
		}
		
		return ($this->crSession->getNode($sAncestorUUID));
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM!!!
	* @param 
	* @return 
	*/
	public function getAncestors($aChildNodes = array()) {
		
		if (TRUE || Registry::getValue('sb.system.repository.mode.dependable')) { // climb tree
			try {
				$nodeParent = $this->getParent();
				$aChildNodes[] = $nodeParent;
				return ($nodeParent->getAncestors($aChildNodes));
			} catch (ItemNotFoundException $e) {
				$niAncestors = new sbCR_NodeIterator($aChildNodes);
				return ($niAncestors);
			}
		} /*else { // get all ancestors at once
			$stmtGetAncestors = $this->crSession->prepareKnown('sb_system/node/getAncestors');
			$stmtGetAncestors->bindValue(':child_uuid', $this->getIdentifier(), PDO::PARAM_STR);
			$stmtGetAncestors->execute();
			$aAncestors = array();
			$aAncestorUUIDs = $stmtGetAncestors->fetchAll();
			//var_dump($aUUIDs);
			foreach ($aAncestorUUIDs as $aRow) {
				$aAncestors[] = $this->crSession->getNode($aRow['fk_parent']);
			}
			$niAncestors = new sbCR_NodeIterator($aAncestors);
			return ($niAncestors);
		}*/
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the depth of this Item in the workspace tree. Returns the depth 
	* below the root node of this Item  (counting this Item itself).
	* @param 
	* @return 
	*/
	public function getDepth() {
		
		
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the name of this Item. 
	* The name of an item is the last element in its path, minus any 
	* square-bracket index that may exist. If this Item is the root node of the
	* workspace (i.e., if this.getDepth() == 0), an empty string will be 
	* returned.
	* NOTE: nodes in a shared set currently all have the same name
	* @return string this node's name
	*/
	public function getName() {
		return ($this->elemSubject->getAttribute('name'));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns this node's parent node.
	* Depending on how this node was retrieved, this may be the primary parent
	* or a parent of the shared set. Retrieving nodes via the identifier will 
	* register the primary parent, retrieving via the hierarchy (i.e. getNode())
	* will register the parent through which it was retrieved.
	* @return Node the parent node
	*/
	public function getParent() {
		
		$sParentUUID = $this->elemSubject->getAttribute('parent');
		
		if ($sParentUUID == '00000000000000000000000000000000') {
			$nodeParent = $this->crSession->getRootNode();
			return ($nodeParent);
		} elseif ($sParentUUID != NULL) {
			$nodeParent = $this->crSession->getNode($sParentUUID);
			return ($nodeParent);
		} else {
			throw new ItemNotFoundException('the node "'.$this->getProperty('label').'" has no parent, which should never be the case!');
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the primary parent of this node.
	* Every node has a single primary parent, in case of nodes in a shared set
	* the additional nodes have also secondary parents. The primary parent is by
	* default the node under which the first node instance in the set was saved.
	* @return Node the primary parent
	*/
	protected function getPrimaryParent() {
		
		$stmtGetParent = $this->crSession->prepareKnown($this->aQueries['getPrimaryParent']);
		$stmtGetParent->bindValue(':child_uuid', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtGetParent->execute();
		
		$sParentUUID = NULL;
		foreach ($stmtGetParent as $aRow) {
			$sParentUUID = $aRow['fk_parent'];
		}
		$stmtGetParent->closeCursor();
		
		if ($sParentUUID == '00000000000000000000000000000000') {
			$nodeParent = $this->crSession->getRootNode();
			return ($nodeParent);
		} elseif ($sParentUUID != NULL) {
			$nodeParent = $this->crSession->getNode($sParentUUID);
			return ($nodeParent);
		} else {
			throw new ItemNotFoundException();
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns a NodeIterator containing all parents of this node.
	* In case this node is part of a shared set, the NodeIterator will contain
	* multiple nodes.
	* TODO: will not work for new, unsaved nodes -> check and rely on unpersisted information
	* @return NodeIterator the node iterator containing all parent nodes
	*/
	public function getParents() {
		
		$stmtGetParents = $this->crSession->prepareKnown($this->aQueries['getParents']['all']);
		$stmtGetParents->bindValue(':child_uuid', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtGetParents->execute();
		
		$aParentUUIDs = array();
		foreach ($stmtGetParents as $aRow) {
			$aParentUUIDs[] = $aRow['fk_parent'];
		}
		$stmtGetParents->closeCursor();
		
		if (count($aParentUUIDs) > 0) {
			foreach ($aParentUUIDs as $sParentUUID) {
				$nodeCurrent = $this->crSession->getNode($sParentUUID);
				$aParentNodes[] = $nodeCurrent;
			}
			$niParentNodes = new sbCR_NodeIterator($aParentNodes);
		} else {
			$niParentNodes = new sbCR_NodeIterator();
		}
		
		return ($niParentNodes);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getParentsByNodetype($sNodetype) {
		
		$stmtGetParents = $this->crSession->prepareKnown($this->aQueries['getParents']['byNodetype']);
		$stmtGetParents->bindValue('child_uuid', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtGetParents->bindValue('nodetype', $sNodetype, PDO::PARAM_STR);
		$stmtGetParents->execute();
		
		$aParentUUIDs = array();
		foreach ($stmtGetParents as $aRow) {
			$aParentUUIDs[] = $aRow['fk_parent'];
		}
		$stmtGetParents->closeCursor();
		
		if (count($aParentUUIDs) > 0) {
			
			foreach ($aParentUUIDs as $sParentUUID) {
				$nodeCurrent = $this->crSession->getNode($sParentUUID);
				$aParentNodes[] = $nodeCurrent;
			}
			
			$niParentNodes = new sbCR_NodeIterator($aParentNodes);
			return ($niParentNodes);
			
		} else {
			$niParentNodes = new sbCR_NodeIterator();
		}
		
		return ($niParentNodes);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the absolute path to this item.
	* If the path includes items that are same-name sibling nodes properties 
	* then those elements in the path will include the appropriate 
	* "square bracket" index notation (for example, /a/b[3]/c).
	* 
	* NOTE: same name siblings are not supported. Also, the path returned will
	* be specified through the primary parents for shared sets, except the 
	* relation of this node (the node on which getPath() is called).
	* 
	* TODO: move this to item class
	* @param string a property name that should be used for the path (optional)
	* @return string the generated path
	*/
	public function getPath($sProperty = 'name') {
		if ($this->sPath != '') {
			return ($this->sPath);
		}
		$nodeParent = $this->getParent();
		if ($nodeParent->isNodeType('sbSystem:Root')) {
			$sPath = '/'.$this->getProperty($sProperty);
		} else {
			$sPath = $nodeParent->getPath($sProperty).'/'.$this->getProperty($sProperty);
		}
		$this->sPath = $sPath;
		return ($sPath);
	}
	
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM:
	* @param 
	* @return 
	*/
	public function getPaths() {
		
		throw new LazyBastardException('to be implemented');
		
		/*$niParents = $this->getParents();
		
		foreach ($niParents as $nodeParent) {
			if ($nodeParent->isNodeType('sbSystem:Root')) {
				$sPath = '/'.$this->getProperty('name');
			} else {
				$sPath = $nodeParent->getPath().'/'.$this->getProperty('name');
			}
		}
		$this->sPath = $sPath;
		return ($sPath);*/
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if this Item has been saved but has subsequently been 
	* modified through the current session and therefore the state of this item
	* as recorded in the session differs from the state of this item as saved.
	* Within a transaction, isModified on an Item may return false (because the
	* Item has been saved since the modification) even if the modification in
	* question is not in persistent storage (because the transaction has not
	* yet been committed).
	* 
	* TODO: verify the behavior is correct
	* TODO: move this to item class
	* @return boolean true if this item is modified; false otherwise.
	*/
	public function isModified() {
		return ($this->bIsModified);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if this is a new item, meaning that it exists only in
	* transient storage on the Session and has not yet been saved. Within a
	* transaction, isNew on an Item may return false (because the item has been
	* saved) even if that Item is not in persistent storage (because the
	* transaction has not yet been committed).
	* 
	* Note that if an item returns true on isNew, then by definition is parent
	* will return true on isModified.
	* 
	* Note that in level 1 (that is, read-only) implementations, this method
	* will always return false.
	* 
	* TODO: verify the behavior is correct (parent isModified won't work now)
	* TODO: move this to item class
	* @return boolean true if this item is new; false otherwise.
	*/
	public function isNew() {
		if ($this->elemSubject->getAttribute('query') == 'new') {
			return (TRUE);
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Indicates whether this Item is a Node or a Property. Returns true if this
	* Item is a Node; Returns false if this Item is a Property.
	* TODO: move this to item class
	* @return boolean true if this Item is a Node, false if it is a Property.
	*/
	public function isNode() {
		return (TRUE); // items not supported by now
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if this Item object (the Java object instance) represents
	* the same actual workspace item as the object otherItem.
	* 
	* Two Item objects represent the same workspace item if all the following
	* are true:
	* - Both objects were acquired through Session objects that were created by
	*   the same Repository object.
	* - Both objects were acquired through Session objects bound to the same
	*   repository workspace.
	* - The objects are either both Node objects or both Property objects.
	* - If they are Node objects, they have the same correspondence identifier.
	*   Note that this is the identifier used to determine whether two nodes in
	*   different workspaces correspond but obviously it is also true that any
	*   node has the same correspondence identifier as itself. Hence, this 
	*   identifier is used here to determine whether two different Java Node 
	*   objects actually represent the same workspace node.
	* - If they are Property objects they have identical names and isSame is 
	*   true of their parent nodes.
	* 
	* This method does not compare the states of the two items. For example, 
	* if two Item objects representing the same actual workspace item have been
	* retrieved through two different sessions and one has been modified, then 
	* this method will still return true when comparing these two objects. Note
	* that if two Item objects representing the same workspace item are
	* retrieved through the same session they will always reflect the same state
	* (see section 5.1.3 Reflecting Item State in the JSR 283 specification
	* document) so comparing state is not an issue. 
	* 
	* TODO: verify the behavior is correct
	* TODO: move this to item class
	* @param 
	* @return 
	*/
	public function isSame($itemOtherItem) {
		if ($this->getIdentifier() == $itemOtherItem->getIdentifier()) {
			return (TRUE);
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* If keepChanges is false, this method discards all pending changes
	* currently recorded in this Session that apply to this Item or any of its
	* descendants (that is, the subtree rooted at this Item)and returns all
	* items to reflect the current saved state. Outside a transaction this
	* state is simple the current state of persistent storage. Within a
	* transaction, this state will reflect persistent storage as modified by
	* changes that have been saved but not yet committed.
	* 
	* If keepChanges is true then pending change are not discarded but items
	* that do not have changes pending have their state refreshed to reflect the
	* current saved state, thus revealing changes made by other sessions.
	* 
	* An InvalidItemStateException is thrown if this Item object represents a
	* workspace item that has been removed (either by this session or another).
	* 
	* TODO: verify the behavior is correct
	* TODO: move this to item class
	* @param boolean flag defining if unsaved changes should be kept
	*/
	public function refresh($bKeepChanges = FALSE) {
		// TODO: actually load current state from repository!!!
		if ($bKeepChanges) {
			throw new LazyBastardException('not implemented yet');
		} else {
			$this->aSaveTasks = array();
			$this->aModifiedChildren = array();
			$this->aModifiedProperties = array();	
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Removes this item (and its subtree).
	* 
	* To persist a removal, a save must be performed that includes the (former)
	* parent of the removed item within its scope.
	* 
	* If a node with same-name siblings is removed, this decrements by one the
	* indices of all the siblings with indices greater than that of the removed
	* node. In other words, a removal compacts the array of same-name siblings
	* and causes the minimal re-numbering required to maintain the original
	* order but leave no gaps in the numbering.
	* 
	* A ReferentialIntegrityException will be thrown on save if this item or an
	* item in its subtree is currently the target of a REFERENCE property
	* located in this workspace but outside this item's subtree and the current
	* Session has read access to that REFERENCE property.
	* 
	* An AccessDeniedException will be thrown on save if this item or an item in
	* its subtree is currently the target of a REFERENCE property located in
	* this workspace but outside this item's subtree and the current Session
	* does not have read access to that REFERENCE property.
	* 
	* A ConstraintViolationException will be thrown either immediately or on
	* save, if removing this item would violate a node type or
	* implementation-specific constraint. Implementations may differ on when
	* this validation is performed.
	* 
	* A VersionException will be thrown either immediately or on save, if the
	* parent node of this item is versionable and checked-in or is
	* non-versionable but its nearest versionable ancestor is checked-in.
	* Implementations may differ on when this validation is performed.
	* 
	* A LockException will be thrown either immediately or on save if a lock
	* prevents the removal of this item. Implementations may differ on when this
	* validation is performed. 
	* 
	* NOTE: the removal is currently persisted if this node or the session is
	* saved, not when a parent or ancestor is saved!
	* TODO: rework behavior
	* TODO: move this to item class
	* FIXME: current crSession should know that this node is about to be
	* removed! (crSession->save())
	* @param 
	* @return 
	*/
	public function remove() {
		if ($this->isNew()) {
			//unset($this); // does not work in PHP5 anymore
			//$this->getParent()->refresh(); // might be better, but affects all other changes, too
			throw new LazyBastardException('unsaved nodes cannot be removed yet');
		} else {
			$this->addSaveTask('remove_node');
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns an iterator over all nodes that are in the shared set of this
	* node. If this node is not shared then the returned iterator contains
	* only this node.
	* @return NodeIterator the nodes in the shared set
	*/
	public function getSharedSet() {
		
		// get the shared set (only persisted nodes, will also include unshared nodes, i.e. this node)
		$stmtGetShares = $this->prepareKnown('hierarchy/getSharedSet');
		$stmtGetShares->bindValue(':child_uuid', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtGetShares->execute();
		
		// fill node iterator with shared set nodes
		$aShares = array();
		foreach ($stmtGetShares as $aRow) {
			$aShares[] = $this->crSession->getNodeByIdentifier($this->getIdentifier(), $aRow['fk_parent']);
		}
		$stmtGetShares->closeCursor();
		$niSharedSet = new sbCR_NodeIterator($aShares);
		
		return ($niSharedSet);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* A special kind of remove() that removes this node, but does not remove
	* any other node in the shared set of this node.
	* 
	* All of the exceptions defined for remove() apply to this function. In
	* addition, a RepositoryException is thrown if this node cannot be removed
	* without removing another node in the shared set of this node.
	* 
	* If this node is not shared this method removes only this node.
	*/
	public function removeShare() {
		$this->addSaveTask('remove_share');
	}

	
	
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getCorrespondingNodePath($sWorkspaceName) {
		throw new Exception('not implemented');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getDefinition() {
		throw new Exception('not implemented');
	}
	
	//--------------------------------------------------------------------------
	/**
	* This method returns the index of this node within the ordered set of its 
	* same-name sibling nodes. This index is the one used to address same-name 
	* siblings using the square-bracket notation, e.g., /a[3]/b[4]. Note that 
	* the index always starts at 1 (not 0), for compatibility with XPath. As a 
	* result, for nodes that do not have same-name-siblings, this method will 
	* always return 1.
	* @param 
	* @return 
	*/
	public function getIndex() {
		return (1);
	}
	
	
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getNode($sRelativePath) {
		
		// TODO: implement XPath interpreter
		
		//$aMatches = array();
		//preg_match('/^([a-z0-9_]+(\[(.+)\])*$/iU', $sRelativePath, $aMatches);
		
		//var_dump($aMatches);
		if ($sRelativePath == '') {
			
			throw new RepositoryException('path is an empty string');
			
		} elseif (preg_match('/^[a-z0-9_:\.]+$/i', $sRelativePath)) {
			
			// check unsaved nodes first
			if ($this->hasUnsavedNode($sRelativePath)) {
				return ($this->getUnsavedNode($sRelativePath));
			}
			// check persisted nodes
			$aChildNode = $this->getChildByName($sRelativePath);
			$nodeChild = $this->crSession->getNodeByIdentifier($aChildNode['uuid'], $this->getIdentifier());
			return ($nodeChild);
			
		} else {
			
			throw new LazyBastardException('XPath interpreter not done yet ('.$sRelativePath.')');
			
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns all child nodes of this node accessible through the current 
	* Session. Does not include properties of this Node. 
	* The same reacquisition semantics apply as with getNode(String). 
	* If this node has no accessible child nodes, then an empty iterator is 
	* returned.
	* Gets all child nodes of this node accessible through the current Session 
	* that match namePattern. The pattern may be a full name or a partial name 
	* with one or more wildcard characters ("*"), or a disjunction (using the 
	* "|" character to represent logical OR) of these.
	* @param 
	* @return 
	*/
	public function getNodes($sNamePattern = NULL) {
		
		if ($sNamePattern == NULL) { // get all child nodes
		
			$niChildNodes = $this->loadChildren('debug', FALSE, TRUE, FALSE);
		
		} else { // get all child nodes that match the pattern
			
			if (substr_count($sNamePattern, '/') != 0) {
				throw new sbException('paths not supported by now');
			}
			
			// TODO: convert namepattern to sensible WHERE patterns
			$aPatterns = array();
			$niChildNodes = $this->loadChildren('debug', FALSE, TRUE, FALSE, NULL, $aPatterns);
			
		}
		
		return ($niChildNodes);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getPrimaryItem() {
		throw new Exception('not implemented');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getIdentifier() {
		return ($this->elemSubject->getAttribute('uuid'));
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------
	// workspace/repository related
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function merge($sSourceWorkspace, $bBestEffort) {
		throw new UnsupportedRepositoryOperationException();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function cancelMerge($vVersion) {
		throw new UnsupportedRepositoryOperationException();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function doneMerge($vVersion) {
		throw new UnsupportedRepositoryOperationException();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getSession() {
		return ($this->crSession);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------
	// hierarchy information
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function hasOrderableChildNodes() {
		// TODO: actually check this
		return (TRUE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function hasNode($sRelativePath) {
		// TODO: implement XPath interpreter
		if (preg_match('/^[a-z0-9_\.:]+$/i', $sRelativePath)) {
			// check unsaved nodes first
			if ($this->hasUnsavedNode($sRelativePath)) {
				return (TRUE);
			}
			// check persisted nodes
			$stmtCheck = $this->crSession->prepareKnown('sbCR/node/countChildrenByName/debug');
			$stmtCheck->bindValue('parent_uuid', $this->getIdentifier(), PDO::PARAM_STR);
			$stmtCheck->bindValue('child_name', $sRelativePath, PDO::PARAM_STR);
			$stmtCheck->execute();
			$aRow = $stmtCheck->fetch(PDO::FETCH_ASSOC);
			$stmtCheck->closeCursor();
			if ($aRow['num_children'] > 0) {
				return (TRUE);
			}
			return (FALSE);
		} else {
			throw new LazyBastardException('XPath interpreter not done yet');
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function hasNodes() {
		throw new Exception('not implemented');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function hasUnsavedNode($sRelativePath) {
		foreach ($this->aModifiedChildren as $nodeChild) {
			if ($nodeChild->getName() == $sRelativePath) {
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
	protected function getUnsavedNode($sRelativePath) {
		foreach ($this->aModifiedChildren as $nodeChild) {
			if ($nodeChild->getName() == $sRelativePath) {
				return ($nodeChild);
			}
		}
		throw new RepositoryException('unsaved node "'.$sRelativePath.'" not found');
	}
	
	
	
	
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------
	// hierarchy modification
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function orderBefore($sSourceChildRelativePath, $sDestinationChildRelativePath) {
		
		// TODO: implement orderable/unorderable distinction
		
		if (!$this->hasOrderableChildNodes()) {
			throw new UnsupportedRepositoryOperationException();
		}
		
		$this->addSaveTask('order_before', array(
				'SourceNode' => $sSourceChildRelativePath,
				'DestinationNode' => $sDestinationChildRelativePath
			)
		);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addNode($sRelativePath, $sPrimaryNodeType = NULL) {
		if ($sPrimaryNodeType == NULL) {
			throw new RepositoryException('adding nodes without nodetype not supported');
		}
		$nodeNew = $this->crSession->createNode($sPrimaryNodeType, $sRelativePath, $sRelativePath, $this->getIdentifier(), $this);
		$this->addChild($nodeNew);
		$this->bIsModified = TRUE;
//		var_dumpp($nodeNew->getProperty('query'));
		return ($nodeNew);
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM: 
	* @param 
	* @return 
	*/
	public function addExistingNode($nodeSubject) {
		$this->addChild($nodeSubject);
		$this->bIsModified = TRUE;
	}
	
	
	
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------
	// nodetype related
	//--------------------------------------------------------------------------
	/**
	* Removes the specified mixin node type from this node and removes mixinName 
	* from this node's jcr:mixinTypes property. Both the semantic change in 
	* effective node type and the persistence of the change to the 
	* jcr:mixinTypes property occur on save.
	* 
	* If this node does not have the specified mixin, a NoSuchNodeTypeException 
	* is thrown either immediately or on save. Implementations may differ on 
	* when this validation is done.
	* 
	* A ConstraintViolationException will be thrown either immediately or on 
	* save if the removal of a mixin is not allowed. Implementations are free to 
	* enforce any policy they like with regard to mixin removal and may differ 
	* on when this validation is done.
	* 
	* A VersionException is thrown either immediately or on save if this node is 
	* versionable and checked-in or is non-versionable but its nearest 
	* versionable ancestor is checked-in. Implementations may differ on when 
	* this validation is done.
	* 
	* A LockException is thrown either immediately or on save if a lock prevents 
	* the removal of the mixin. Implementations may differ on when this 
	* validation is done. 
	* @param 
	* @return 
	*/
	public function removeMixin($sMixinName) {
		throw new UnsupportedRepositoryOperationException('mixin types not implemented');
	}
	
	//--------------------------------------------------------------------------
	/**
	* Adds the specified mixin node type to this node and adds mixinName to this 
	* node's jcr:mixinTypes property. Semantically, the new node type may take 
	* effect immediately and must take effect on save. Whichever behavior is 
	* adopted it must be the same as the behavior adopted for 
	* setPrimaryType(java.lang.String) and the behavior that occurs when a 
	* node is first created.
	* 
	* A ConstraintViolationException is thrown either immediately or on save if 
	* a conflict with another assigned mixin or the primary node type or for an 
	* implementation-specific reason. Implementations may differ on when this 
	* validation is done.
	* 
	* In some implementations it may only be possible to add mixin types before 
	* a a node is first saved, and not after. I such cases any later calls to 
	* addMixin will throw a ConstraintViolationException either immediately or 
	* on save.
	* 
	* A NoSuchNodeTypeException is thrown either immediately or on save if the 
	* specified mixinName is not recognized. Implementations may differ on when 
	* this validation is done.
	* 
	* A VersionException is thrown either immediately or on save if this node is 
	* versionable and checked-in or is non-versionable but its nearest 
	* versionable ancestor is checked-in. Implementations may differ on when 
	* this validation is done.
	* 
	* A LockException is thrown either immediately or on save if a lock prevents 
	* the addition of the mixin. Implementations may differ on when this 
	* validation is done. 
	* @param 
	* @return 
	*/
	public function addMixin($sMixinName) {
		throw new UnsupportedRepositoryOperationException('mixin types not implemented');
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if this node is of the specified primary node type or mixin 
	* type, or a subtype thereof. Returns false otherwise.
	* 
	* This method respects the effective node type of the node. Note that this 
	* may differ from the node type implied by the node's jcr:primaryType 
	* property or jcr:mixinTypes property if that property has recently been 
	* created or changed and has not yet been saved. 
	* @param 
	* @return 
	*/
	public function isNodeType($sNodeTypeName) {
		$crNodeType = $this->getNodeType();
		return ($crNodeType->isNodeType($sNodeTypeName));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getNodeType() {
		$this->crNodeType = $this->crSession->getWorkspace()->getNodeTypeManager()->getNodeType($this->getPrimaryNodeType());	
		return ($this->crNodeType);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the primary node type in effect for this node. Note that this may 
	* differ from the node type implied by the node's jcr:primaryType property 
	* if that property has recently been created or changed and has not yet been 
	* saved. Which NodeType is returned when this method is called on the root 
	* node of a workspace is up to the implementation, though the returned type 
	* must, of course, be consistent with the child nodes and properties of the 
	* root node.
	* @param 
	* @return 
	*/
	public function getPrimaryNodeType() {
		return ($this->elemSubject->getAttribute('nodetype'));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns an array of NodeType objects representing the mixin node types in 
	* effect for this node. This includes only those mixin types explicitly 
	* assigned to this node. It does not include mixin types inherited through 
	* the addition of supertypes to the primary type hierarchy or through the 
	* addition of supertypes to the type hierarchy of any of the declared mixin 
	* types. Note that this may differ from the node types implied by the node's 
	* jcr:mixinTypes property if that property has recently been created or 
	* changed and has not yet been saved.
	* @param 
	* @return 
	*/
	public function getMixinNodeTypes() {
		// currently no mixins can be added to specific nodes, only to nodetypes in general
		return (array());
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if the specified mixin node type, mixinName, can be added to 
	* this node. Returns false otherwise. A result of false must be returned in 
	* each of the following cases:
	* 
    * - The mixin's definition conflicts with an existing primary or mixin node 
    *   type of this node.
    * - This node is versionable and checked-in or is non-versionable and its 
    *   nearest versionable ancestor is checked-in.
    * - This node is protected (as defined in this node's NodeDefinition, found 
    *   in the node type of this node's parent).
    * - An access control restriction would prevent the addition of the mixin.
    * - A lock would prevent the addition of the mixin.
    * - An implementation-specific restriction would prevent the addition of 
    *   the mixin.
    * 
    * A NoSuchNodeTypeException is thrown if the specified mixin node type name is not recognized.
	* @param 
	* @return 
	*/
	public function canAddMixin($sMixinName) {
		// currently no mixins can be added to specific nodes, only to nodetypes in general
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function isDeletable() {
		
		// for now, every node except the root node is deletable
		if ($this->getProperty('jcr:primaryType') == 'sbSystem:Root') {
			return (FALSE);
		}
		
		// nothing restricts this node from being deleted
		return (TRUE);
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------
	// property access related
	//--------------------------------------------------------------------------
	/**
	* Indicates whether this node has properties. Returns true if this node has 
	* one or more properties accessible through the current Session; false 
	* otherwise.
	* @param 
	* @return 
	*/
	public function hasProperties() {
		// TODO: what to do here? mandatory properties exist in any case
		throw new LazyBastardException();
	}
	
	//--------------------------------------------------------------------------
	/**
	* Indicates whether a property exists at relPath. Returns true if a property 
	* accessible through the current Session exists at relPath and false 
	* otherwise.
	* @param 
	* @return 
	*/
	public function hasProperty($sRelativePath) {
		$this->initPropertyDefinitions();
		if ($this->crPropertyDefinitionCache->hasProperty($sRelativePath)) {
			return (TRUE);
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the property at relPath relative to this  node. The same 
	* reacquisition semantics apply as with getNode(String). If no property 
	* exists at relPath a PathNotFoundException is thrown. This exception is 
	* also thrown if the current Session does not have read access to the 
	* specified property.
	* @param 
	* @return 
	*/
	public function getProperty($sRelativePath) {
		
		$this->initPropertyDefinitions();
		
		if (isset($this->aPropertyTranslation[$sRelativePath])) {
			$sRelativePath = $this->aPropertyTranslation[$sRelativePath];
		}
		
		if ($sRelativePath == 'jcr:uuid') {
			return ($this->getIdentifier());
		} elseif ($sRelativePath == 'jcr:primaryType') {
			return ($this->getPrimaryNodeType());
		} elseif ($sRelativePath == 'jcr:mixinTypes') {
		// TODO: implement multivalue properties and mixin types
//		} elseif ($sRelativePath == 'jcr:lockOwner') {
			/*if ($this->holdsLock()) {
				$this->getLock();
			}
			return ($this->crLock->getLockOwner());*/
//		} elseif ($sRelativePath == 'jcr:lockIsDeep') {
			/*if ($this->holdsLock()) {
				$this->getLock();
			}
			return ($this->crLock->isDeep());*/

		} elseif ($sRelativePath == 'sbcr:isDeletable') {
			return ($this->isDeletable());
		} elseif (isset($this->aModifiedProperties[$sRelativePath])) {
			return ($this->aModifiedProperties[$sRelativePath]);
		} elseif ($this->elemSubject->hasAttribute($sRelativePath)) {
			return ((string) $this->elemSubject->getAttribute($sRelativePath));
		} elseif ($this->crPropertyDefinitionCache->hasProperty($sRelativePath)) {
			$this->loadProperties($this->crPropertyDefinitionCache->getStorageType($sRelativePath));
			return ($this->elemSubject->getAttribute($sRelativePath));
		} else {
			throw new PathNotFoundException('in Node \''.$this->getIdentifier().'\' for path \''.$sRelativePath.'\'');
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns all properties of this node accessible through the current 
	* Session. Does not include child nodes of this node. The same reacquisition 
	* semantics apply as with getNode(String). If this node has no accessible 
	* properties, then an empty iterator is returned.
	* 
	* WITH NAMEPATTERN!!!!:
	* Gets all properties of this node accessible through the current Session 
	* that match namePattern. The pattern may be a full name or a partial name 
	* with one or more wildcard characters ("*"), or a disjunction (using the 
	* "|" character to represent logical OR) of these. For example,
	* 
	* N.getProperties("jcr:* | myapp:name | my doc")
	* 
	* would return a PropertyIterator holding all accessible properties of N 
	* that are either called 'myapp:name', begin with the prefix 'jcr:' or are 
	* called 'my doc'.
	* 
	* Note that leading and trailing whitespace around a disjunct is ignored, 
	* but whitespace within a disjunct forms part of the pattern to be matched.
	* 
	* The EBNF for namePattern is:
	* 
	* namePattern ::= disjunct {'|' disjunct}
	* disjunct ::= name [':' name]
	* name ::= '*' | ['*'] fragment {'*' fragment} ['*']
	* fragment ::= char {char}
	* char ::= nonspace | ' '
	* nonspace ::= Any XML Char (See http://www.w3.org/TR/REC-xml/) except: '/', 
	* ':', '[', ']', '*', ''', '"', '|' or any whitespace character
	* 
	* The pattern is matched against the names (not the paths) of the immediate 
	* child properties of this node.
	* 
	* If this node has no accessible matching properties, then an empty iterator 
	* is returned.
	* 
	* The same reacquisition semantics apply as with getNode(String). 
	* @param 
	* @return 
	*/
	public function getProperties($sNamePattern = NULL) {
		// TODO:implement namepattern and adjust to api standard
		
		$this->initPropertyDefinitions();
		
		$aProperties = array();
		foreach ($this->crPropertyDefinitionCache as $sName => $aDetails) {
			$aProperties[$sName] = $this->getProperty($sName);
		}
		
		return ($aProperties);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setProperty($sName, $mValue) {
		
		if (isset($this->aPropertyTranslation[$sName])) {
			$sName = $this->aPropertyTranslation[$sName];
		}
		
		if ($sName == 'jcr:uuid' && $this->isNew()) {
			$this->elemSubject->setAttribute('uuid', $mValue);
			return (TRUE);
		}
		
		$this->initPropertyDefinitions();
		if (!$this->crPropertyDefinitionCache->hasProperty($sName)) {
			throw new RepositoryException('the property "'.$sName.'" does not exist in node type "'.$this->getPrimaryNodeType().'"');
		}
		if ($this->crPropertyDefinitionCache->isProtected($sName, $this->isNew())) {
			throw new ConstraintViolationException('the property "'.$sName.'" is protected in current node state ('.$this->isNew().')');
		}
		if (!is_scalar($mValue) && !is_null($mValue)) {
			throw new RepositoryException('property "'.$sName.'" can only accept scalar types');
		}
		$this->elemSubject->setAttribute($sName, $mValue);
		$this->aModifiedProperties[$sName] = $mValue;
		$this->bIsModified = TRUE;
		
		// store tasks
		$this->addSaveTask('save_node');
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* TODO: apply checks 
	* @param 
	* @return 
	*/
	public function saveBinaryProperty($sProperty, $mData) {
		
		// TODO: caution! binary properties in aux are not considered
		$stmtAttribute = $this->prepareKnown($this->aQueries['saveBinaryAttribute']);
		$stmtAttribute->bindValue(':node_id', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtAttribute->bindValue(':attributename', $sProperty, PDO::PARAM_STR);
		$stmtAttribute->bindValue(':content', $mData, PDO::PARAM_LOB);
		$stmtAttribute->execute();
		$stmtAttribute->closeCursor();
		
		// update lastModified
		$this->saveNode();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* TODO: apply checks 
	* @param 
	* @return 
	*/
	public function loadBinaryProperty($sProperty) {
		
		$stmtAttribute = $this->prepareKnown($this->aQueries['loadBinaryAttribute']);
		$streamData = NULL;
		$stmtAttribute->bindValue(':node_id', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtAttribute->bindParam(':property', $sProperty, PDO::PARAM_STR);
		$stmtAttribute->execute();
		//$stmtAttribute->bindColumn('m_content', $streamData, PDO::PARAM_LOB);
		//$stmtAttribute->fetch(PDO::FETCH_BOUND);
		//var_dump($streamData);
		$aRow = $stmtAttribute->fetch();
		$stmtAttribute->closeCursor();
		return ($aRow['m_content']);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function loadProperties($sType = 'FULL', $bOnlyProperties = FALSE) {
		
		$this->initPropertyDefinitions();
		
		if ($sType == 'FULL') {
			$this->loadProperties('EXTENDED');
			if ($this->crPropertyDefinitionCache->usesStorage('AUXILIARY')) {
				$this->loadProperties('AUXILIARY');
			}
			if ($this->crPropertyDefinitionCache->usesStorage('EXTERNAL')) {
				$this->loadProperties('EXTERNAL');
			}
			return(NULL);
		}
		if (!isset($this->aQueries['loadProperties'][strtolower($sType)])) {
			var_dumpp($this->crPropertyDefinitionCache);
			var_dumpp($this->aQueries);
			var_dumpp($this->getPrimaryNodeType());
			throw new sbException('property view not supported: '.$sType);
		}
		$stmtGetProperties = $this->prepareKnown($this->aQueries['loadProperties'][strtolower($sType)]);
		$stmtGetProperties->bindValue(':node_id', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtGetProperties->execute();
		
		if ($sType == 'EXTERNAL') {
			foreach ($stmtGetProperties as $aRow) {
				if (!isset($this->aModifiedAttributes[$aRow['s_attributename']])) {
					$this->elemSubject->setAttribute($aRow['s_attributename'], $aRow['s_value']);
				}
			}
			$stmtGetProperties->closeCursor();
		} else {
			$aProperties = $stmtGetProperties->fetch(PDO::FETCH_ASSOC);
			$stmtGetProperties->closeCursor();
			// TODO: $aProperties != null because of a bug when reading newly saved nodes (investigate!!!)
			if ($sType == 'EXTENDED' && $aProperties != NULL) {
				foreach ($this->crPropertyDefinitionCache as $sName => $aDetails) {
					if (!isset($this->aModifiedAttributes[$sName]) && $aDetails['e_storagetype'] == 'EXTENDED') {
						$this->elemSubject->setAttribute($sName, $aProperties[$aDetails['s_auxname']]);
					}
				}
			} elseif ($sType == 'AUXILIARY') {
				foreach ($this->crPropertyDefinitionCache as $sName => $aDetails) {
					if (!isset($this->aModifiedAttributes[$sName]) && $aDetails['e_storagetype'] == 'AUXILIARY') {
						$this->elemSubject->setAttribute($sName, $aProperties[$aDetails['s_auxname']]);
					}
				}
			}
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Loads the node's stored property definitions from the associated repository. 
	*/
	protected function initPropertyDefinitions() {
		if ($this->crPropertyDefinitionCache == NULL) {
			$this->crPropertyDefinitionCache = $this->getNodeType()->getPropertyCache();
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------
	// versioning related
	//--------------------------------------------------------------------------
	/**
	* 
	* TODO: match to both Java methods
	* @param 
	* @return 
	*/
	public function restore($vVersion, $bRemoveExisting) {
		throw new UnsupportedRepositoryOperationException('versioning not implemented');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function restoreByLabel($sVersionLabel, $bRemoveExisting) {
		throw new UnsupportedRepositoryOperationException('versioning not implemented');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function isCheckedOut() {
		throw new UnsupportedRepositoryOperationException('versioning not implemented');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getVersionHistory() {
		throw new UnsupportedRepositoryOperationException('versioning not implemented');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkin() {
		throw new UnsupportedRepositoryOperationException('versioning not implemented');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkout() {
		throw new UnsupportedRepositoryOperationException('versioning not implemented');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getBaseVersion() {
		throw new UnsupportedRepositoryOperationException('versioning not implemented');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------
	// locking related
	//--------------------------------------------------------------------------
	/**
	* Returns true if this node is locked either as a result of a lock held by 
	* this node or by a deep lock on a node above this node; otherwise returns 
	* false.
	* @param 
	* @return 
	*/
	public function isLocked() {
		
		return (FALSE);
		
		/*$DB = DBFactory::getInstance('system');
		$sNodeUUID = $this->getUUID();
		$stmtClearLocks = $DB->prepareKnown('sb_system/node/locking/clearLocks');
		$stmtClearLocks->execute();
		$stmtClearLocks->close();
		$stmtCheckLockLocal = $DB->prepareKnown('sb_system/node/locking/clearLocks');
		$stmtCheckLockLocal->bindParam('node_uuid', $sNodeUUID, PDO::PARAM_STR);
		$stmtCheckLockLocal->execute();
		foreach ($stmtCheckLockLocal as $unused) {
			*/
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if this node holds a lock; otherwise returns false. To hold 
	* a lock means that this node has actually had a lock placed on it 
	* specifically, as opposed to just having a lock apply to it due to a deep 
	* lock held by a node above.
	* @param 
	* @return 
	*/
	public function holdsLock() {
		$stmtCheckLock = $this->prepareKnown('sbCR/node/locking/checkLock');
		$stmtCheckLock->bindValue('node_uuid', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtCheckLock->execute();
		$bHoldsLock = FALSE;
		foreach ($stmtCheckLock as $aRow) {
			$bHoldsLock = TRUE;
			$this->crLock = new sbCR_Lock($this->getIdentifier());
		}
		$stmtCheckLock->closeCursor();
		return ($bHoldsLock);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the Lock object that applies to this node. This may be either a 
	* lock on this node itself or a deep lock on a node above this node.
	* 
	* If the current session holds the lock token for this lock, then the 
	* returned Lock object contains that lock token (accessible through 
	* Lock.getLockToken). If this Session does not hold the applicable lock 
	* token, then the returned Lock object will not contain the lock token (its 
	* Lock.getLockToken method will return null).
	* 
	* If this node is not locked (no lock applies to this node), a LockException 
	* is thrown.
	* 
	* If the current session does not have sufficient privileges to get the 
	* lock, an AccessDeniedException is thrown.
	* 
	* An UnsupportedRepositoryOperationException is thrown if this 
	* implementation does not support locking.
	* 
	* A RepositoryException is thrown if another error occurs. 
	* @param 
	* @return 
	*/
	public function getLock() {
		if (!$this->isLocked()) {
			throw new LockException('node is not locked');
		}
		
		// TODO: query for lock
	}
	
	//--------------------------------------------------------------------------
	/**
	* Places a lock on this node. If successful, this node is said to hold the 
	* lock.
	* 
	* If isDeep is true then the lock applies to this node and all its 
	* descendant nodes; if false, the lock applies only to this, the holding 
	* node.
	* 
	* If isSessionScoped is true then this lock will expire upon the expiration 
	* of the current session (either through an automatic or explicit 
	* Session.logout); if false, this lock does not expire until explicitly 
	* unlocked or automatically unlocked due to a implementation-specific 
	* limitation, such as a timeout.
	* 
	* Returns a Lock object reflecting the state of the new lock.
	* 
	* If the lock is open-scoped the returned lock will include a lock token.
	* 
	* The lock token is also automatically added to the set of lock tokens held 
	* by the current Session.
	* 
	* If successful, then the property jcr:lockOwner is created and set to the 
	* value of Session.getUserID for the current session and the property 
	* jcr:lockIsDeep is set to the value passed in as isDeep. These changes are 
	* persisted automatically; there is no need to call save.
	* 
	* Note that it is possible to lock a node even if it is checked-in (the 
	* lock-related properties will be changed despite the checked-in status).
	* 
	* If this node is not of mixin node type mix:lockable then an LockException 
	* is thrown.
	* 
	* If this node is already locked (either because it holds a lock or a lock 
	* above it applies to it), a LockException is thrown.
	* 
	* If isDeep is true and a descendant node of this node already holds a lock, 
	* then a LockException is thrown.
	* 
	* If this node does not have a persistent state (has never been saved or 
	* otherwise persisted), a LockException is thrown.
	* 
	* If the current session does not have sufficient privileges to place the 
	* lock, an AccessDeniedException is thrown.
	* 
	* An UnsupportedRepositoryOperationException is thrown if this 
	* implementation does not support locking.
	* 
	* An InvalidItemStateException is thrown if this node has pending unsaved 
	* changes.
	* 
	* A RepositoryException is thrown if another error occurs. 
	* @param 
	* @return 
	*/
	public function lock($bIsDeep, $bIsSessionScoped = FALSE) {
		
		// check basic requirements
		if ($this->isLocked()) {
			throw new LockException();
		}
		if ($this->isNewNode()) {
			throw new LockException();
		}
		if ($this->isModified()) {
			throw new InvalidItemStateException();	
		}
		if ($bIsSessionScoped) {
			throw new UnsupportedRepositoryException();
		}
		
		// check locks on ancestors and descendents
		if ($bIsDeep && $this->hasLockedDescendents()) {
			throw new LockException();
		}
		if ($this->hasLockedAncestors()) {
			throw new LockException();
		}
		
		$sIsDeep = 'FALSE';
		if ($bIsDeep) {
			$sIsDeep = 'TRUE';
		}
		// TODO: separate timetolive from registry?
		$iTimeToLive = Registry::getValue('sb.system.locks.timetolive.default');
		$stmtPlaceLock = $this->prepareKnown('sbCR/node/locking/placeLock');
		$stmtPlaceLock->bindValue('node_uuid', $this->getIdentifier(), PDO::PARAM_STR);
		// TODO: remove user object, use session
		$stmtPlaceLock->bindValue('user_uuid', User::getUUID(), PDO::PARAM_STR);
		$stmtPlaceLock->bindValue('deep', $sIsDeep, PDO::PARAM_STR);
		$stmtPlaceLock->bindValue('timetolive', $iTimeToLive, PDO::PARAM_INT);
		$stmtPlaceLock->execute();
		$stmtPlaceLock->closeCursor();
	}
	
	//--------------------------------------------------------------------------
	/**
	* Removes the lock on this node. Also removes the properties jcr:lockOwner 
	* and jcr:lockIsDeep from this node. These changes are persisted 
	* automatically; there is no need to call save. As well, the corresponding 
	* lock token is removed from the set of lock tokens held by the current 
	* Session.
	* 
	* If this node does not currently hold a lock or holds a lock for which this 
	* Session is not the owner, then a LockException is thrown. Note however 
	* that the system may give permission to a non-owning session to unlock a 
	* lock. Typically such "lock-superuser" capability is intended to facilitate 
	* administrational clean-up of orphaned open-scoped locks.
	* 
	* Note that it is possible to unlock a node even if it is checked-in (the 
	* lock-related properties will be changed despite the checked-in status).
	* 
	* If the current session does not have sufficient privileges to remove the 
	* lock, an AccessDeniedException is thrown.
	* 
	* An InvalidItemStateException is thrown if this node has pending unsaved 
	* changes.
	* 
	* An UnsupportedRepositoryOperationException is thrown if this 
	* implementation does not support locking.
	* 
	* A RepositoryException is thrown if another error occurs. 
	* @param 
	* @return 
	*/
	public function unlock() {
		if ($this->isModified()) {
			throw new InvalidItemStateException('node has pending unsaved changes');
		}
		if (!$this->holdsLock()) {
			throw new LockException('node is not locked');
		}
		$stmtPlaceLock = $this->prepareKnown('sb_system/node/locking/removeLock/byNode');
		$stmtPlaceLock->bindParam('node_uuid', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtPlaceLock->execute();
		$stmtPlaceLock->closeCursor();
	}
	
	//--------------------------------------------------------------------------
	/**
	* Checks if this node has locked descendents.
	* @param 
	* @return 
	*/
	protected function hasLockedDescendents() {
		$stmtCheckLock = $this->prepareKnown('sb_system/node/locking/checkLocks/descendents');
		$stmtCheckLock->bindParam('left', $crPositionInfo->getLeft(), PDO::PARAM_STR);
		$stmtCheckLock->bindParam('right', $crPositionInfo->getRight(), PDO::PARAM_STR);
		$stmtCheckLock->execute();
		
		$stmtCheckLock->closeCursor();
	}
	
	//--------------------------------------------------------------------------
	/**
	*
	* @param 
	* @return 
	*/
	protected function hasLockedAncestors($crPositionInfo) {
		$stmtCheckLock = $this->prepareKnown('sb_system/node/locking/checkLocks/ancestors');
		$stmtCheckLock->bindParam('left', $crPositionInfo->getLeft(), PDO::PARAM_STR);
		$stmtCheckLock->bindParam('right', $crPositionInfo->getRight(), PDO::PARAM_STR);
		$stmtCheckLock->execute();
		
		$stmtCheckLock->closeCursor();
	}
	
	//--------------------------------------------------------------------------
	/**
	* TODO: move to session?
	* @param 
	* @return 
	*/
	protected function clearExpiredLocks() {
		$stmtClearLocks = $this->prepareKnown('sbCR/node/locking/clearLocks');
		$stmtClearLocks->execute();
		$stmtClearLocks->closeCursor();
	}
	
	
	
	
	//--------------------------------------------------------------------------
	// lifecycle related
	//--------------------------------------------------------------------------
	/**
	* Causes the lifecycle state of this node to undergo the specified transition.
	* This method may change the value of the jcr:currentLifecycleState property, 
	* in most cases it is expected that the implementation will change the value 
	* to that of the passed transition parameter, though this is an 
	* implementation-specific issue. If the jcr:currentLifecycleState property 
	* is changed the change is persisted immediately, there is no need to call 
	* save.
	* 
	* Throws an UnsupportedRepositoryOperationException if this implementation 
	* does not support lifecycle actions or if this node does not have the 
	* mix:lifecycle mixin.
	* 
	* Throws InvalidLifecycleTransitionException if the lifecycle transition is 
	* not successful. 
	* 
	* @param 
	* @return 
	*/
	public function followLifecycleTransition($sTransition) {
		/*if (!in_array('mix:lifecycle', $this->crSession->getWorkspace()->getNodeTypeManager()->getMixinNodeTypes())) {
			throw new UnsupportedRepositoryOperationException('node '.$this->getIdentifier().' does not have the mix:lifecycle mixin type');	
		}*/
		if (!in_array($sTransition, $this->getAllowedLifecycleTransitions())) {
			throw new InvalidLifecycleTransitionException('node '.$this->getIdentifier().' does not support the lifecycle transition from "'.$this->getProperty('jcr:currentLifecycleState').'" to "'.$sTransition.'"');
		}
		$stmtSetTransition = $this->crSession->prepareKnown($this->aQueries['lifecycle/followTransition']);
		$stmtSetTransition->bindValue('node_uuid', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtSetTransition->bindValue('state', $sTransition, PDO::PARAM_STR);
		$stmtSetTransition->execute();
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the list of valid state transitions for this node. 
	* @param 
	* @return 
	*/
	public function getAllowedLifecycleTransitions() {
		/*if (!in_array('mix:lifecycle', $this->crSession->getWorkspace()->getNodeTypeManager()->getMixinNodeTypes())) {
			throw new UnsupportedRepositoryOperationException('node '.$this->getIdentifier().' does not have the mix:lifecycle mixin type');	
		}*/
		$sCurrentState = $this->getProperty('jcr:currentLifecycleState');
		if ($sCurrentState == NULL) {
			$sCurrentState = 'default';
		}
		$stmtGetTransitions = $this->crSession->prepareKnown($this->aQueries['lifecycle/getTransitions']);
		$stmtGetTransitions->bindValue('nodetype', $this->getPrimaryNodeType(), PDO::PARAM_STR);
		$stmtGetTransitions->bindValue('state', $sCurrentState, PDO::PARAM_STR);
		$stmtGetTransitions->execute();
		$aTransitions = array();
		foreach ($stmtGetTransitions as $aTransition) {
			$aTransitions[] = $aTransition['transition'];
		}
		return ($aTransitions);
	}
	
	
	
	//--------------------------------------------------------------------------
	// other
	//--------------------------------------------------------------------------
	/**
	* If this node does have a corresponding node in the workspace srcWorkspace, 
	* then this replaces this node and its subtree with a clone of the 
	* corresponding node and its subtree.
	* 
	* If this node does not have a corresponding node in the workspace 
	* srcWorkspace, then the update method has no effect.
	* 
	* If the update succeeds the changes made are persisted immediately, there 
	* is no need to call save.
	* 
	* Note that update does not respect the checked-in status of nodes. An 
	* update may change a node even if it is currently checked-in (This fact is 
	* only relevant in an implementation that supports versioning).
	* 
	* If the specified srcWorkspace does not exist, a NoSuchWorkspaceException 
	* is thrown.
	* 
	* If the current session does not have sufficient rights to perform the 
	* operation, then an AccessDeniedException is thrown.
	* 
	* An InvalidItemStateException is thrown if this Session (not necessarily 
	* this Node) has pending unsaved changes.
	* 
	* Throws a LockException if a lock prevents the update.
	* 
	* A RepositoryException is thrown if another error occurs. 
	* @param 
	* @return 
	*/
	public function update($sWorkspaceName) {
		throw new Exception('not implemented');
	}
	
	//--------------------------------------------------------------------------
	/**
	* Matches a repository level property type to the corresponding PDO type
	* @param string property type
	* @return integer defined constant of corresponding PDO type
	*/
	protected function getPDOType($sCRType) {
		switch ($sCRType) {
			case 'STRING':
			case 'REFERENCE':
			case 'WEAKREFERENCE':
			case 'PATH': 
			case 'URI':
			case 'NAME': return (PDO::PARAM_STR);
			case 'LONG': return (PDO::PARAM_INT);
			case 'DOUBLE': return (PDO::PARAM_STR); // no float type?
		}
	}
	
}

?>