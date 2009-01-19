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
	* 
	*/
	protected $crPropertyDefinitionCache = NULL;
	/**
	* 
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
	protected $aQueries				= array();
	/**
	* 
	*/
	protected $sPath				= '';
	
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
		'jcr:uuid'				=> 'uuid',
		'jcr:created'			=> 'createdat',
		'jcr:createdBy'			=> 'createdby',
		'jcr:lastModified'		=> 'modifiedat',
		'jcr:lastModifiedBy'	=> 'modifiedby',
		'sbcr:label'			=> 'label',
		'sbcr:inheritrights'	=> 'inheritrights',
		'sbcr:bequeathrights'	=> 'bequeathrights',
		'sbcr:deleted'			=> 'deletedat',
		'sbcr:deletedBy'		=> 'deletedby',
		'sbcr:deletedFrom'		=> 'deletedfrom',
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
		$this->aQueries['save']['new']						= 'sbCR/node/save/new';
		$this->aQueries['save']['existing']					= 'sbCR/node/save/existing';
		
		// getting parent nodes
		$this->aQueries['getPrimaryParent']					= 'sbCR/node/getPrimaryParent';
		$this->aQueries['getParents']['all']				= 'sbCR/node/getParents/all';
		$this->aQueries['getParents']['byNodetype']			= 'sbCR/node/getParents/byNodetype';
		
		// hierarchy related
		$this->aQueries['hierarchy/getSharedSet']			= 'sbCR/node/getSharedSet';
		$this->aQueries['getAncestor']['byUUID']			= 'sbCR/node/getAncestor/byUUID';
		
		// linking / nested set stuff
		$this->aQueries['addLink']['getBasicInfo']			= 'sbCR/node/addLink/getBasicInfo';
		$this->aQueries['addLink']['updateRight']			= 'sbCR/node/addLink/updateRight';
		$this->aQueries['addLink']['updateLeft']			= 'sbCR/node/addLink/updateLeft';
		$this->aQueries['addLink']['insertNode']			= 'sbCR/node/addLink/insertNode';
		$this->aQueries['delete']['getBasicInfo']			= 'sbCR/node/hierarchy/getInfo';
		$this->aQueries['delete']['shift']					= 'sbCR/node/removeLink/shiftLeft';
		$this->aQueries['removeLink']						= 'sbCR/node/removeLink';
		$this->aQueries['removeDescendantLinks']			= 'sbCR/node/removeDescendantLinks';
		$this->aQueries['reorder']['getBasicInfo']			= 'sbCR/node/orderBefore/getInfo';
		$this->aQueries['reorder']['writeNestedSet']		= 'sbCR/node/orderBefore/writeNestedSet';
		$this->aQueries['reorder']['writeOrder']			= 'sbCR/node/orderBefore/writeOrder';
		$this->aQueries['reorder']['setLock']				= 'sbCR/node/orderBefore/setLock';
		$this->aQueries['getLinkStatus']					= 'sbCR/node/getLinkStatus';
		$this->aQueries['setLinkStatus']['normal']			= 'sbCR/node/setLinkStatus';
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
	* - s_csstype
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
	protected function getChildren($sMode = 'debug', $bOnlyReadable = FALSE) {
		
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
			
			$aChildNodes = array();
			
			foreach ($aChildren as $aRow) {
				$nodeCurrentChild = $this->crSession->getNodeByIdentifier($aRow['uuid'], $this->getIdentifier());
				if ($bOnlyReadable && !User::isAuthorised('read', $nodeCurrentChild)) {
					continue;
				}
				$aChildNodes[] = $nodeCurrentChild;
			}
			
			$niChildNodes = new sbCR_NodeIterator($aChildNodes);
			
		} else { // cached
			
			$niChildNodes = $this->aChildNodes[$sMode];
			
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
	public function getNumberOfParents() {
		
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
	* CUSTOM:
	* @param 
	* @return 
	*/
	protected function isDescendantOf($nodeSubject) {
		
		$stmtCheck = $this->crSession->prepareKnown($this->aQueries['getAncestor']['byUUID']);
		$stmtCheck->bindValue(':child_uuid', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtCheck->bindValue(':parent_uuid', $nodeSubject->getIdentifier(), PDO::PARAM_STR);
		$stmtCheck->execute();
		
		$bCheck = FALSE;
		foreach ($stmtCheck as $aRow) {
			$bCheck = TRUE;
		}
		
		$stmtCheck->closeCursor();
		
		return ($bCheck);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* CUSTOM:
	* @param 
	* @return 
	*/
	protected function isAncestorOf($nodeSubject) {
		return ($nodeSubject->isDescendantOf($this));
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
	* TODO: get closer to JCR API with this, also regarding hierarchy
	* @param 
	* @return 
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
			
			// remove this node? for now, removing the links is enough!
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
				throw new RepositoryException('currently you can only remove secondary nodes from shared set');	
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
				
				$sParentUUID = $this->getIdentifier();
				$nodeChild = $this->aModifiedChildren[$aOptions['uuid']];				
				$nodeChild->saveNode();
				$sChildUUID = $nodeChild->getIdentifier();
				
				// get basic info
				$stmtChild = $this->crSession->prepareKnown($this->aQueries['addLink']['getBasicInfo']);
				$stmtChild->bindValue('parent_uuid', $sParentUUID, PDO::PARAM_STR);
				$stmtChild->bindValue('child_uuid', $sChildUUID, PDO::PARAM_STR);
				$stmtChild->bindValue('child_name', $nodeChild->getProperty('name'), PDO::PARAM_STR);
				$stmtChild->execute();
				foreach ($stmtChild as $aRow) {
					$iRight = $aRow['n_right'];
					$iLevel = $aRow['n_level'];
					$iPosition = $aRow['n_position'];
					$iNumParents = $aRow['n_numparents'];
					$iNumSameNameSiblings = $aRow['n_numsamenamesiblings'];
				}
				$stmtChild->closeCursor();
				
				if (!isset($iRight)) {
					throw new RepositoryException('no info found on node '.$this->getProperty('label').' ('.$this->getIdentifier().')');	
				}
				if ($iNumSameNameSiblings != 0) {
					throw new RepositoryException('a node with the name "'.$nodeChild->getProperty('name').'" already exists under '.$this->getProperty('label').' ('.$this->getProperty('jcr:uuid').')');	
				}
				
				$sIsPrimary = 'FALSE';
				if ($iNumParents == 0) {
					$sIsPrimary = 'TRUE';
				}
				
				// update nested sets - right
				$stmtChild = $this->crSession->prepareKnown($this->aQueries['addLink']['updateRight']);
				$stmtChild->bindParam('right', $iRight, PDO::PARAM_STR);
				$stmtChild->execute();
				
				// update nested sets - left
				$stmtChild = $this->crSession->prepareKnown($this->aQueries['addLink']['updateLeft']);
				$stmtChild->bindParam('right', $iRight, PDO::PARAM_STR);
				$stmtChild->execute();
				
				// insert new link for node
				$stmtChild = $this->crSession->prepareKnown($this->aQueries['addLink']['insertNode']);
				$stmtChild->bindParam('child_uuid', $sChildUUID, PDO::PARAM_STR);
				$stmtChild->bindParam('parent_uuid', $sParentUUID, PDO::PARAM_STR);
				$stmtChild->bindParam('is_primary', $sIsPrimary, PDO::PARAM_STR);
				$stmtChild->bindParam('right', $iRight, PDO::PARAM_INT);
				$stmtChild->bindParam('order', $iPosition, PDO::PARAM_INT);
				$stmtChild->bindParam('level', $iLevel, PDO::PARAM_INT);
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
				$stmtGetData = $this->crSession->prepareKnown($this->aQueries['reorder']['getBasicInfo']);
				
				$stmtGetData->bindParam('child_name', $aOptions['SourceNode'], PDO::PARAM_STR);
				$stmtGetData->bindParam('parent_uuid', $sUUID, PDO::PARAM_STR);
				$stmtGetData->execute();
				$aSourceInfo = $stmtGetData->fetchAll(PDO::FETCH_ASSOC);
				if (count($aSourceInfo) == 0) {
					throw new ItemNotFoundException(__CLASS__.': source node does not exist ('.$aOptions['SourceNode'].')');
				}
				$aSourceInfo = $aSourceInfo[0];
				
				$stmtGetData->bindParam('child_name', $aOptions['DestinationNode'], PDO::PARAM_STR);
				$stmtGetData->bindParam('parent_uuid', $sUUID, PDO::PARAM_STR);
				$stmtGetData->execute();
				$aDestinationInfo = $stmtGetData->fetchAll(PDO::FETCH_ASSOC);
				if (count($aDestinationInfo) == 0) {
					throw new ItemNotFoundException(__CLASS__.': destination node does not exist ('.$aOptions['DestinationNode'].')');
				}
				$aDestinationInfo = $aDestinationInfo[0];
				
				// update position info (preparation)
				$iOffsetNestedset = 0;
				$iOffsetOrder = 0;
				$iLeft = 0;
				$iRight = 0;
				$sState = '';
				
				$stmtMove = $this->crSession->prepareKnown($this->aQueries['reorder']['writeNestedSet']);
				$stmtMove->bindParam('offset_nestedset', $iOffsetNestedset, PDO::PARAM_INT);
				$stmtMove->bindParam('left', $iLeft, PDO::PARAM_INT);
				$stmtMove->bindParam('right', $iRight, PDO::PARAM_INT);
				$stmtMove->bindParam('state', $sState, PDO::PARAM_INT);
				
				$stmtOrder = $this->crSession->prepareKnown($this->aQueries['reorder']['writeOrder']);
				$stmtOrder->bindParam('offset_order', $iOffsetOrder, PDO::PARAM_INT);
				$stmtOrder->bindParam('left', $iLeft, PDO::PARAM_INT);
				$stmtOrder->bindParam('right', $iRight, PDO::PARAM_INT);
				$stmtOrder->bindParam('parent_uuid', $sUUID, PDO::PARAM_STR);
				$stmtOrder->bindParam('state', $sState, PDO::PARAM_INT);
				
				$stmtLock = $this->crSession->prepareKnown($this->aQueries['reorder']['setLock']);
				$stmtLock->bindParam('state', $sState, PDO::PARAM_STR);
				$stmtLock->bindParam('left', $iLeft, PDO::PARAM_INT);
				$stmtLock->bindParam('right', $iRight, PDO::PARAM_INT);
				
				// lock source tree
				$sState = 'TRUE';
				$iLeft = $aSourceInfo['n_left'];
				$iRight = $aSourceInfo['n_right'];
				$stmtLock->execute();
				//var_dumpp($stmtLock->rowCount());
				
				// update destination and siblings
				$sState = 'FALSE';
				$iSourceRange = $aSourceInfo['n_right'] - $aSourceInfo['n_left'] + 1;
				if ($aSourceInfo['n_order'] < $aDestinationInfo['n_order']) { // source node left of destination
					$iLeft = $aSourceInfo['n_right'] + 1;
					$iRight = $aDestinationInfo['n_left'] - 1;
					$iOffsetOrder = -1;
					$iOffsetNestedset = 0 - $iSourceRange;
				} else { // source node right of destination
					$iLeft = $aDestinationInfo['n_left'];
					$iRight = $aSourceInfo['n_left'] - 1;
					$iOffsetOrder = 1;
					$iOffsetNestedset = $iSourceRange;
				}
				$stmtOrder->execute();
				$stmtMove->execute();
				
				// move source tree
				$sState = 'TRUE';
				if ($aSourceInfo['n_order'] < $aDestinationInfo['n_order']) { // source node left of destination
					$iOffsetNestedset = $aDestinationInfo['n_left'] - $aSourceInfo['n_right'] - 1;
					$iOffsetOrder = $aDestinationInfo['n_order'] - $aSourceInfo['n_order'] - 1;
				} else { // source node right of destination
					$iOffsetNestedset = $aDestinationInfo['n_left'] - $aSourceInfo['n_left'];
					$iOffsetOrder = $aDestinationInfo['n_order'] - $aSourceInfo['n_order'];
				}
				$iLeft = $aSourceInfo['n_left'];
				$iRight = $aSourceInfo['n_right'];
				$stmtOrder->execute();
				$stmtMove->execute();
				
				// unlock source tree
				$sState = 'FALSE';
				$iLeft = $aSourceInfo['n_left'] + $iOffsetNestedset;
				$iRight = $aSourceInfo['n_right'] + $iOffsetNestedset;
				$stmtLock->execute();
				
				// remove task
				unset($this->aSaveTasks['order_before'][$iTaskNumber]);
				
			}
			
			// remove task type
			unset($this->aSaveTasks['order_before']);
			
		}
		
		$this->crSession->removeSaveTaskForNode($this);
		$this->crSession->commit('sbCR_Node::save');
		
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
	* 
	* @param 
	* @return 
	*/
	protected function saveNode() {
		
		// just return for now if node is not modified
		if (!$this->isModified()) {
			return (FALSE);
		}
		
		if ($this->isNew()) {
			$stmtInsert = $this->crSession->prepareKnown($this->aQueries['save']['new']);
			$stmtInsert->bindValue(':uuid',				$this->getIdentifier(),						PDO::PARAM_STR);
			$stmtInsert->bindValue(':uid',				$this->getProperty('uid'),					PDO::PARAM_STR);
			$stmtInsert->bindValue(':nodetype',			$this->getProperty('nodetype'),				PDO::PARAM_STR);
			$stmtInsert->bindValue(':label',			$this->getProperty('label'),				PDO::PARAM_STR);
			$stmtInsert->bindValue(':name',				$this->getProperty('name'),					PDO::PARAM_STR);
			$stmtInsert->bindValue(':customcsstype',	$this->getProperty('customcsstype'),		PDO::PARAM_STR);
			$stmtInsert->bindValue(':inheritrights',	$this->getProperty('sbcr:inheritrights'),	PDO::PARAM_STR);
			$stmtInsert->bindValue(':bequeathrights',	$this->getProperty('sbcr:bequeathrights'),	PDO::PARAM_STR);
			$stmtInsert->bindValue(':user_id',			User::getUUID(),							PDO::PARAM_STR);
			$stmtInsert->execute();
			$this->elemSubject->setAttribute('query', $this->getIdentifier());
		} else {
			$stmtInsert = $this->crSession->prepareKnown($this->aQueries['save']['existing']);
			$stmtInsert->bindValue(':uuid',				$this->getIdentifier(),						PDO::PARAM_STR);
			$stmtInsert->bindValue(':uid',				$this->getProperty('uid'),					PDO::PARAM_STR);
			$stmtInsert->bindValue(':label',			$this->getProperty('label'),				PDO::PARAM_STR);
			$stmtInsert->bindValue(':name',				$this->getProperty('name'),					PDO::PARAM_STR);
			$stmtInsert->bindValue(':customcsstype',	$this->getProperty('customcsstype'),		PDO::PARAM_STR);
			$stmtInsert->bindValue(':inheritrights',	$this->getProperty('sbcr:inheritrights'),	PDO::PARAM_STR);
			$stmtInsert->bindValue(':bequeathrights',	$this->getProperty('sbcr:bequeathrights'),	PDO::PARAM_STR);
			$stmtInsert->bindValue(':user_id',			User::getUUID(),							PDO::PARAM_STR);
			$stmtInsert->execute();
		}
		$stmtInsert->closeCursor();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function saveProperties($sType = 'FULL') {
		
		$this->initPropertyDefinitions();
//		var_dumpp($this->crPropertyDefinitionCache);
		if ($sType == 'FULL') {
			// check if aux or ext properties have changed
			$bChangedAux = FALSE;
			$bChangedExt = FALSE;
			foreach ($this->aModifiedProperties as $sProperty => $mValue) {
				//var_dumpp($sProperty.':'.$mValue);
				//var_dumpp($this->crPropertyDefinitionCache->getStorageType($sProperty));
				if ($this->crPropertyDefinitionCache->getStorageType($sProperty) == 'AUXILIARY') {
					$bChangedAux = TRUE;
				}
				if ($this->crPropertyDefinitionCache->getStorageType($sProperty) == 'EXTERNAL') {
					$bChangedExt = TRUE;
				}
			}
			//var_dumpp($this->crPropertyDefinitionCache->usesStorage('AUXILIARY'));
			//var_dumpp($bChangedAux);
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
			//var_dumpp($this->crPropertyDefinitionCache);
			foreach ($this->crPropertyDefinitionCache as $sName => $aDetails) {
				if ($aDetails['e_storagetype'] == 'AUXILIARY') {
					//var_dumpp($this->isNew());
					if ($this->isNew() && $aDetails['b_protectedoncreation'] == 'TRUE') {
						//echo 'new&protectedoncreation ';
						//var_dumpp($sName); var_dumpp($aDetails['s_defaultvalues']); 
						$mValue = $aDetails['s_defaultvalues'];
					} elseif (!$this->isNew() && $aDetails['b_protected'] == 'TRUE') {
						//echo 'notnew&protected ';
						//continue;
						$mValue = $this->elemSubject->getAttribute($sName);
					} else {
						$mValue = $this->elemSubject->getAttribute($sName);
					}
					//var_dumpp($sName.'|'.$this->elemSubject->getAttribute($sName));
					/*if ($sName == 'security_expires') {
						var_dumpp($aDetails);
						var_dumpp($this->elemSubject->getAttribute($sName));
					}*/
					
					
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
	* CAUTION: will not update the nested sets
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
			$sParentUUID = $this->getPrimaryParent()->getProperty('jcr:uuid');
		}
		$stmtGetInfo = $this->prepareKnown('sbCR/node/hierarchy/getInfo');
		$stmtGetInfo->bindValue(':parent_uuid', $sParentUUID, PDO::PARAM_STR);
		$stmtGetInfo->bindValue(':child_uuid', $sChildUUID, PDO::PARAM_STR);
		$stmtGetInfo->execute();
		foreach ($stmtGetInfo as $aRow) {
			$aInfo['left'] = $aRow['n_left'];
			$aInfo['right'] = $aRow['n_right'];
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
	protected function deleteLink($nodeParent = NULL) {
		
		// get info
		$aInfo = $this->getHierarchyInfo($nodeParent);
		
		// delete link to parent
		$stmtRemoveLink = $this->crSession->prepareKnown($this->aQueries['removeLink']);
		$stmtRemoveLink->bindValue('child_uuid', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtRemoveLink->bindValue('parent_uuid', $nodeParent->getIdentifier(), PDO::PARAM_STR);
		$stmtRemoveLink->execute();
		//$stmtRemoveLink->closeCursor();
		
		// shift following nodes
		$stmtShift = $this->crSession->prepareKnown('sbCR/node/removeLink/shiftLeft');
		$stmtShift->bindValue('left', $aInfo['left'], PDO::PARAM_INT);
		$stmtShift->bindValue('distance', $aInfo['right'] - $aInfo['left'] + 1, PDO::PARAM_INT);
		$stmtShift->execute();
		//$stmtShift->closeCursor();
		
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
		$stmtRemoveLink->bindValue('left', $aInfo['left'], PDO::PARAM_STR);
		$stmtRemoveLink->bindValue('right', $aInfo['right'], PDO::PARAM_STR);
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
		
		if (Registry::getValue('sb.system.repository.mode.dependable')) { // climb tree
			try {
				$nodeParent = $this->getParent();
				$aChildNodes[] = $nodeParent;
				return ($nodeParent->getAncestors($aChildNodes));
			} catch (ItemNotFoundException $e) {
				$niAncestors = new sbCR_NodeIterator($aChildNodes);
				return ($niAncestors);
			}
		} else { // get all ancestors at once
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
		}
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
	* Returns the name of this Item. The name of an item is the last element in 
	* its path, minus any square-bracket index that may exist. If this Item is 
	* the root node of the workspace (i.e., if this.getDepth() == 0), an empty 
	* string will be returned.
	* @param 
	* @return 
	*/
	public function getName() {
		return ($this->elemSubject->getAttribute('name'));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
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
			throw new ItemNotFoundException();
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
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
	* 
	* @param 
	* @return 
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
	* 
	* @param 
	* @return 
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
	* 
	* @return 
	*/
	public function isModified() {
		return ($this->bIsModified);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @return 
	*/
	public function isNew() {
		if ($this->elemSubject->getAttribute('query') == 'new') {
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
	public function isNode() {
		return (TRUE); // items not supported by now
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
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
	* 
	* @param 
	* @return 
	*/
	public function refresh($bKeepChanges) {
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
	* 
	* @param 
	* @return 
	*/
	public function remove() {
		if ($this->isNew()) {
			unset($this);
		} else {
			$this->addSaveTask('remove_node');
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getSharedSet() {
		$stmtGetShares = $this->prepareKnown('hierarchy/getSharedSet');
		$stmtGetShares->bindValue(':child_uuid', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtGetShares->execute();
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
	* 
	* @param 
	* @return 
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
				throw new sbException(__CLASS__.': paths not supported by now');
			}
			
			// TODO: convert namepattern to sensible WHERE patterns
			$aPatterns = array();
			$niChildNodes = $this->loadChildren('debug', FALSE, TRUE, FALSE, FALSE, $aPatterns);
			
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
		$nodeNew = $this->crSession->createNode($sPrimaryNodeType, $sRelativePath, $sRelativePath, $this->getIdentifier());
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
		
		if ($sRelativePath == 'jcr:primaryType') {
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
			// TODO: remove this almost dirty hack, implement special attributes instead
			$aKnownMandatoryNodes = array(
				'sbSystem:Root',
				'sbSystem:Preferences',
				'sbSystem:Reports',
				'sbSystem:Maintenance',
				'sbSystem:Modules',
				'sbSystem:Reports_db',
				'sbSystem:Reports_structure',
				'sbSystem:Trashcan',
				'sbSystem:Useraccounts',
				'sbSystem:Registry',
				'sbSystem:Module',
				'sbSystem:Debug',
				'sbSystem:Logs'
			);
			if (in_array($this->getProperty('jcr:primaryType'), $aKnownMandatoryNodes, TRUE)) {
				return (FALSE);
			}
			return (TRUE);
		} elseif (isset($this->aModifiedProperties[$sRelativePath])) {
			return ($this->aModifiedProperties[$sRelativePath]);
		} elseif ($this->elemSubject->hasAttribute($sRelativePath)) {
//			var_dumpp('attribute|'.$sRelativePath.'|'.(string) $this->elemSubject->getAttribute($sRelativePath));
			return ((string) $this->elemSubject->getAttribute($sRelativePath));
		} elseif ($this->crPropertyDefinitionCache->hasProperty($sRelativePath)) {
			$this->loadProperties($this->crPropertyDefinitionCache->getStorageType($sRelativePath));
			return ($this->elemSubject->getAttribute($sRelativePath));
		} else {
//		if (!$this->elemSubject->hasAttribute($sRelativePath)) {
			throw new PathNotFoundException('in Node \''.$this->getIdentifier().'\' for path \''.$sRelativePath.'\'');
		}
//		return ($this->elemSubject->getAttribute($sRelativePath));
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
			throw new RepositoryException(__CLASS__.': the property "'.$sName.'" does not exist in node type "'.$this->getPrimaryNodeType().'"');
		}
		if ($this->crPropertyDefinitionCache->isProtected($sName, $this->isNew())) {
			throw new ConstraintViolationException(__CLASS__.': the property "'.$sName.'" is protected in current node state ('.$this->isNew().')');
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
			throw new sbException('property view not supported: '.$sType);
		}
		$stmtGetProperties = $this->prepareKnown($this->aQueries['loadProperties'][strtolower($sType)]);
		$stmtGetProperties->bindValue(':node_id', $this->getIdentifier(), PDO::PARAM_STR);
		$stmtGetProperties->execute();
		
		if ($sType == 'EXTERNAL') {
			foreach ($stmtGetProperties as $aRow) {
//				if ($bOnlyProperties && $aPropDef[$aRow['s_attributename']]['b_showinproperties'] == 'FALSE') {
//					continue;
//				}
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
				//var_dumpp($aProperties);
				foreach ($this->crPropertyDefinitionCache as $sName => $aDetails) {
					//DEBUG('loadProperties:Extended:before', $sName.'='.$this->elemSubject->getAttribute($sName));
					if (!isset($this->aModifiedAttributes[$sName]) && $aDetails['e_storagetype'] == 'EXTENDED') {
						//DEBUG('loadProperties:Extended:after', $sName.'='.$aProperties[$aDetails['s_auxname']]);
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
			$this->crPropertyDefinitionCache = $this->crSession->getRepository()->getPropertyDefinitions($this->getPrimaryNodeType());
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
			throw new LockException(__CLASS__.':'.__METHOD__.': node is not locked');
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
			throw new InvalidItemStateException(__CLASS__.': node has pending unsaved changes');
		}
		if (!$this->holdsLock()) {
			throw new LockException(__CLASS__.': node is not locked');
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