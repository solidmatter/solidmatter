<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.cr.workspace');

//------------------------------------------------------------------------------
/**
*/
class sbCR_Session {
	
	// sbCR objects
	private $crRepository			= NULL;
	private $crWorkspace			= NULL;
	private $crCredentials			= NULL;
	private $crNodetypeManager		= NULL;
	private $crNamespaceRegistry	= NULL;
	
	// temporary namespaces
	private $aNamespaceMapping		= array();
	
	// database connection
	private $DB;
	
	// fingerprint created on init
	private $sFingerprint			= NULL;
	
	// things to do on save
	private $aSaveTasks				= array();
	
	// caching
	private $aNodetypes				= NULL;
	private	$aNodeCache				= array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($DB, $crCredentials, $crRepository, $sWorkspaceName, $sWorkspacePrefix) {
		
		$this->crRepository = $crRepository;
		$this->crCredentials = $crCredentials;
		$this->crWorkspace = new sbCR_Workspace($sWorkspaceName, $sWorkspacePrefix, $this);
		$this->DB = $DB;
		
		$this->sFingerprint = md5(mt_rand().$crCredentials->getUserId());
		
		// TODO: implement namespacemapping
		//$this->aNamespaceMapping = $aNamespaceMapping;
	}
	
	//--------------------------------------------------------------------------
	// namepace related
	// TODO: check against NamespaceRegistry
	//--------------------------------------------------------------------------
	/**
	* Within the scope of this session, rename a persistently registered 
	* namespace URI to the new prefix.
	* @param 
	* @return 
	*/
	public function setNamespacePrefix($sNewPrefix, $sExistingURI) {
		$aTemp = array_flip($this->aNamespaceMapping);
		if (!isset($aTemp[$sExistingURI])) {
			throw new NamespaceExeption('namespace "'.$sExistingURI.'" does not exist');
		}
		if (isset($this->aNamespaceMapping[$sNewPrefix])) {
			throw new NamespaceExeption('namespaceprefix "'.$sExistingURI.'" already exists');
		}
		unset($this->aNamespaceMapping[$aTemp[$sExistingURI]]);
		$this->aNamespaceMapping[$sNewPrefix] = $sExistingURI;
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns all prefixes currently set for this session.
	* @param 
	* @return 
	*/
	public function getNamespacePrefixes() {
		return (array_keys($this->aNamespaceMapping));
	}
	
	//--------------------------------------------------------------------------
	/**
	* For a given prefix, returns the URI to which it is mapped as currently set
	* in this Session.
	* @param 
	* @return 
	*/
	public function getNamespaceURI($sPrefix) {
		if (!isset($this->aNamespaceMapping[$sPrefix])) {
			throw new NamespaceException('namespace "'.$sPrefix.'" does not exist');	
		}
		return ($this->aNamespaceMapping[$sPrefix]);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the prefix to which the given URI is mapped
	* @param 
	* @return 
	*/
	public function getNamespacePrefix($sExistingURI) {
		$aTemp = array_flip($this->aNamespaceMapping);
		if (!isset($aTemp[$sExistingURI])) {
			throw new NamespaceExeption('namespace "'.$sExistingURI.'" does not exist');
		}
		return ($aTemp[$sExistingURI]);
	}
	
	
	
	
	
	//--------------------------------------------------------------------------
	// locking
	//--------------------------------------------------------------------------
	/**
	* Adds the specified lock token to this session.
	* @param 
	* @return 
	*/
	public function addLockToken($sLockToken) {
		throw new LazyBastardException();
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns an array containing all lock tokens currently held by this 
	* session.
	* @param 
	* @return 
	*/
	public function getLockTokens() {
		throw new LazyBastardException();
	}
	
	//--------------------------------------------------------------------------
	/**
	* Removes the specified lock token from this session.
	* @param 
	* @return 
	*/
	public function removeLockToken($sLockToken) {
		throw new LazyBastardException();
	}
	
	
	
	
	
	//--------------------------------------------------------------------------
	// authentication/authorisation
	//--------------------------------------------------------------------------
	/**
	* Returns the value of the named attribute as an Object, or null if no 
	* attribute of the given name exists.
	* @param 
	* @return 
	*/
	public function getAttribute($sName) {
		return ($this->crLoginCredentials->getAttribute($sName));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the names of the attributes set in this session as a result of 
	* the Credentials that were used to acquire it.
	* @param 
	* @return 
	*/
	public function getAttributeNames() {
		return ($this->crLoginCredentials->getAttributeNames());
	}
	
	//--------------------------------------------------------------------------
	/**
	* Gets the user ID that was used to acquire this session.
	* @param 
	* @return 
	*/
	public function getUserID() {
		return ($this->crLoginCredentials->getUserID());
	}
	
	//--------------------------------------------------------------------------
	/**
	* Gets the fingerprint that was created acquiring this session.
	* @param 
	* @return 
	*/
	public function getFingerprint() {
		return ($this->sFingerprint);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns a new session in accordance with the specified (new) Credentials.
	* @param 
	* @return 
	*/
	public function impersonate($crCredentials) {
		return ($this->crRepository->login($crCredentials, $this->crCurrentWorkspace->getName()));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Determines whether this Session has permission to perform the specified 
	* actions at the specified absPath. This method quietly returns if the 
	* access request is permitted, or throws a suitable 
	* AccessControlException otherwise.
	* @param 
	* @return 
	*/
	public function checkPermission($sAbsPath, $sActions) {
		// TODO: implement basic permission checking, for now just return
		return (TRUE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Releases all resources associated with this Session.
	* @param 
	* @return 
	*/
	public function logout() {
		throw new LazyBastardException();
	}
	
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------
	// node acquisition
	//--------------------------------------------------------------------------
	/**
	* Returns the item at the specified absolute path in the workspace.
	* @param 
	* @return 
	*/
	public function getItem($sAbsPath) {
		// TODO: expand this on properties?
		return ($this->getInstanceByPath($sAbsPath));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the node specified by the given UUID.
	* @param 
	* @return 
	*/
	public function getNodeByIdentifier($sUUID, $sParentUUID = NULL) {
		return ($this->getInstanceByUUID($sUUID, $sParentUUID));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the root node of the workspace.
	* TODO: make the root node UUID flexible, e.g. for change to sbUUID
	* @param 
	* @return 
	*/
	public function getRootNode() {
		if (empty($this->aNodeCache['ROOT'])) {
			$stmtInfo = $this->DB->prepareKnown('sbCR/getNode/root');
			$stmtInfo->execute();
			$this->aNodeCache['ROOT'] = $this->generateInstance($stmtInfo, '/');
		}
		return ($this->aNodeCache['ROOT']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if an item exists at absPath; otherwise returns false.
	* @param 
	* @return 
	*/
	public function itemExists($sAbsPath) {
		// TODO: expand this on properties?
		try {
			$nodeCheck = $this->getInstanceByPath($sAbsPath);
			return (TRUE);
		} catch (NodeNotFoundException $e) {
			return (FALSE);
		}
	}
	
	
	
	
	
	//--------------------------------------------------------------------------
	// repository objects
	//--------------------------------------------------------------------------
	/**
	* Returns the Repository object through which this session was acquired.
	* @param 
	* @return 
	*/
	public function getRepository() {
		return ($this->crRepository);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the Workspace attached to this Session.
	* @param 
	* @return 
	*/
	public function getWorkspace() {
		return ($this->crWorkspace);
	}
	
	//--------------------------------------------------------------------------
	/**
	* This method returns a ValueFactory that is used to create Value objects 
	* for use when setting repository properties.
	* @param 
	* @return 
	*/
	public function getValueFactory() {
		throw new UnsupportedRepositoryOperationException('sbCR does not support value objects yet! (...and propably never will)');
	}
	
	//--------------------------------------------------------------------------
	/**
	* TODO: remove this method - will for now break db-caching
	* @param 
	* @return 
	*/
	public function getDatabase() {
		return ($this->DB);
	}
	
	
	
	//--------------------------------------------------------------------------
	// session status
	//--------------------------------------------------------------------------
	/**
	* Returns true if this session holds pending (that is, unsaved) changes;
	* otherwise returns false.
	* @param 
	* @return 
	*/
	public function hasPendingChanges() {
		if (count($this->aSaveTasks) > 0) {
			return (TRUE);
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if this Session object is usable by the client.
	* @param 
	* @return 
	*/
	public function isLive() {
		throw new LazyBastardException();
	}
	
	//--------------------------------------------------------------------------
	/**
	* If keepChanges is false, this method discards all pending changes 
	* currently recorded in this Session and returns all items to reflect the 
	* current saved state.
	* @param 
	* @return 
	*/
	public function refresh($bKeepChanges) {
		if ($bKeepChanges) {
			throw new LazyBastardException('refresh with keeping changes not supported');
		} else {
			$this->aSaveTasks = array();
		}
	}
	
	
	
	
	
	//--------------------------------------------------------------------------
	// save/move
	//--------------------------------------------------------------------------
	/**
	* Moves the node at srcAbsPath (and its entire subtree) to the new location 
	* at destAbsPath.
	* @param 
	* @return 
	*/
	public function move($sSrcAbsPath, $sDestAbsPath) {
		$sParentPath = sbCR_Utilities::removeLastLevelFromPath($sSrcAbsPath);
		$this->moveBranchByPath($sSrcAbsPath, $sParentPath, $sDestAbsPath);
	}
	
	//------------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function moveBranchByPath($sSrcAbsPath, $sParentPath, $sDestAbsPath) {
		$nodeSubject = $this->crSession->getItem($sSrcAbsPath);
		$nodeOldParent = $this->crSession->getItem($sParentPath);
		$nodeNewParent = $this->crSession->getItem($sDestAbsPath);
		$this->addSaveTask('move_branch', array(
			'subject' => $nodeSubject,
			'oldparent' => $nodeOldParent,
			'newparent' => $nodeNewParent,
		));
	}
	
	//------------------------------------------------------------------------------
	/**
	* CUSTOM:
	* @param 
	* @return 
	*/
	public function moveBranchByNodes($nodeSubject, $nodeOldParent, $nodeNewParent) {
		$this->addSaveTask('move_branch', array(
			'subject' => $nodeSubject,
			'oldparent' => $nodeOldParent,
			'newparent' => $nodeNewParent,
		));
	}
	
	//--------------------------------------------------------------------------
	/**
	* Adds a task to perform on call to save(), such as save a changed node 
	* @param 
	* @return 
	*/
	public function addSaveTask($sTaskType, $aOptions = NULL) {
		switch ($sTaskType) {
			case 'save_node':
				$sNodeUUID = $aOptions['subject']->getIdentifier();
				$this->aSaveTasks[$sTaskType][$sNodeUUID] = $aOptions;
				break;
			case 'move_branch':
				$this->aSaveTasks[$sTaskType][] = $aOptions;
				break;
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* removes a task when a node was already saved
	* @param 
	* @return 
	*/
	public function removeSaveTaskForNode($nodeSubject) {
		$sNodeUUID = $nodeSubject->getIdentifier();
		if (isset($this->aSaveTasks['save_node'][$sNodeUUID])) {
			unset ($this->aSaveTasks['save_node'][$sNodeUUID]);
			return (TRUE);
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Validates all pending changes currently recorded in this Session.
	* @param 
	* @return 
	*/
	public function save() {
		
		// anything to do?
		if (count($this->aSaveTasks) == 0) {
			return (FALSE);
		}
		
		// TODO: perform validation first on all pending tasks
		
		// perform all tasks in a transaction
		$this->beginTransaction('sbSession::save');
		
		// process task list
		foreach ($this->aSaveTasks as $sTaskType => $aTasks) {
		
			switch ($sTaskType) {
				
				case 'save_node':
					foreach ($aTasks as $aTask) {
						$aTask['subject']->save();
					}
					unset($this->aTasks['save_node']);
					break;
				
				case 'move_branch':
					
					foreach ($aTasks as $aTask) {
						
						$nodeSubject = $aTask['subject'];
						$nodeOldParent = $aTask['oldparent'];
						$nodeNewParent = $aTask['newparent'];
						
						$sSubjectUUID = $nodeSubject->getIdentifier();
						$sOldParentUUID = $nodeOldParent->getIdentifier();
						$sNewParentUUID = $nodeNewParent->getIdentifier();
						
						// same node? then do nothing
						if ($nodeOldParent->isSame($nodeNewParent)) {
							return (TRUE);
						}
						
						// check hierarchy violation
						// TODO: check violations based on shared sets!
						if ($nodeNewParent->isDescendantOf($nodeSubject)) {
							throw new RepositoryException('new parent is child of subject');	
						}
						
						// get position info
						$stmtGetData = $this->prepareKnown('sbCR/node/moveBranch/getSourceInfo');
						$stmtGetData->bindValue('subject_uuid', $sSubjectUUID, PDO::PARAM_STR);
						$stmtGetData->bindValue('oldparent_uuid', $sOldParentUUID, PDO::PARAM_STR);
						$stmtGetData->execute();
						$aSourceInfo = $stmtGetData->fetchAll(PDO::FETCH_ASSOC);
						if (count($aSourceInfo) == 0) {
							throw new ItemNotFoundException('source node does not exist ('.$aOptions['SourceNode'].')');
						}
						$aSourceInfo = $aSourceInfo[0];
						
						$stmtGetData = $this->prepareKnown('sbCR/node/moveBranch/getDestinationInfo');
						$stmtGetData->bindValue('newparent_uuid', $sNewParentUUID, PDO::PARAM_STR);
						$stmtGetData->execute();
						$aDestinationInfo = $stmtGetData->fetchAll(PDO::FETCH_ASSOC);
						if (count($aDestinationInfo) == 0) {
							throw new ItemNotFoundException('destination node does not exist ('.$aOptions['DestinationNode'].')');
						}
						$aDestinationInfo = $aDestinationInfo[0];
						
						// update position info (preparation)
						$iOldOrder				= $aSourceInfo['n_order'];
						$iOldParentLevel		= $aSourceInfo['n_level'] - 1;
						$iNewOrder				= $aDestinationInfo['n_numchildren']; // starts at 0
						$iNewParentLevel		= $aDestinationInfo['n_level'];
						$iOffsetLevel			= $iNewParentLevel - $iOldParentLevel;
						
						// shift following siblings order
						$stmtOrder = $this->prepareKnown('sbCR/node/hierarchy/moveSiblings');
						$stmtOrder->bindValue('offset', -1, PDO::PARAM_INT);
						$stmtOrder->bindValue('low_position', $aSourceInfo['n_order'], PDO::PARAM_INT);
						$stmtOrder->bindValue('high_position', $aSourceInfo['n_maxorder'], PDO::PARAM_INT);
						$stmtOrder->bindValue('parent_uuid', $sOldParentUUID, PDO::PARAM_STR);
						$stmtOrder->execute();
						
						// update moved branch
						$stmtMoveBranch = $this->prepareKnown('sbCR/node/moveBranch/updateBranch');
						$stmtMoveBranch->bindValue('old_mpath', $nodeSubject->getMPath(), PDO::PARAM_STR);
						$stmtMoveBranch->bindValue('new_mpath', $nodeNewParent->getMPath().substr($nodeSubject->getMPath(), -sbUUID::MPHASH_SIZE), PDO::PARAM_STR);
						$stmtMoveBranch->bindValue('offset_level', $iOffsetLevel, PDO::PARAM_INT);
						$stmtMoveBranch->execute();
						
						// update subject node
						$stmtUpdateLink = $this->prepareKnown('sbCR/node/moveBranch/updateLink');
						$stmtUpdateLink->bindValue('newparent_uuid', $sNewParentUUID, PDO::PARAM_STR);
						$stmtUpdateLink->bindValue('oldparent_uuid', $sOldParentUUID, PDO::PARAM_STR);
						$stmtUpdateLink->bindValue('subject_uuid', $sSubjectUUID, PDO::PARAM_STR);
						$stmtUpdateLink->bindValue('order', $iNewOrder, PDO::PARAM_INT);
						$stmtUpdateLink->bindValue('mpath', $nodeNewParent->getMPath(), PDO::PARAM_STR);
						$stmtUpdateLink->execute();
					
					}
					
					// remove tasks
					unset($this->aSaveTasks['move_branch']);
					
					break;
					
				default:
					throw new RepositoryException('invalid SaveTask "'.$aTask['task_type'].'"');
					break;
				
			}
			
		}
		
		$this->commit('sbSession::save');
		
	}
	
	
	
	
	
	//--------------------------------------------------------------------------
	// import/export
	//--------------------------------------------------------------------------
	/**
	* Deserializes an XML document and adds the resulting item subtree as a 
	* child of the node at parentAbsPath.
	* @param 
	* @return 
	*/
	public function importXML($sParentAbsPath, $ioInputStream, $iUUIDBehaviour) {
		throw new LazyBastardException();
	}
	
	/*
	 void 	exportDocumentView(java.lang.String absPath, org.xml.sax.ContentHandler contentHandler, boolean skipBinary, boolean noRecurse)
	          Serializes the node (and if noRecurse is false, the whole subtree) at absPath into a series of SAX events by calling the methods of the supplied org.xml.sax.ContentHandler.
	 void 	exportDocumentView(java.lang.String absPath, java.io.OutputStream out, boolean skipBinary, boolean noRecurse)
	          Serializes the node (and if noRecurse is false, the whole subtree) at absPath as an XML stream and outputs it to the supplied OutputStream.
	 void 	exportSystemView(java.lang.String absPath, org.xml.sax.ContentHandler contentHandler, boolean skipBinary, boolean noRecurse)
	          Serializes the node (and if noRecurse is false, the whole subtree) at absPath into a series of SAX events by calling the methods of the supplied org.xml.sax.ContentHandler.
	 void 	exportSystemView(java.lang.String absPath, java.io.OutputStream out, boolean skipBinary, boolean noRecurse)
	          Serializes the node (and if noRecurse is false, the whole subtree) at absPath as an XML stream and outputs it to the supplied OutputStream.
	*/
	/*
	 * org.xml.sax.ContentHandler 	getImportContentHandler(java.lang.String parentAbsPath, int uuidBehavior)
	          Returns an org.xml.sax.ContentHandler which can be used to push SAX events into the repository.
	 */
	
	
	
	
	
	
	
	
	//--------------------------------------------------------------------------
	// custom sbCR methods
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getNode($sQuery) {
		return ($this->getInstance($sQuery));
	}
	
	//--------------------------------------------------------------------------
	/**
	* May only be called from nodes!
	* @param 
	* @return 
	*/
	public function createNode($sNodetype, $sName = '', $sLabel = '', $sParentUUID = NULL) {
		
		$this->loadNodetypes();
		if (!isset($this->aNodetypes[$sNodetype])) {
			throw new UnknownNodetypeException('invalid nodetype: '.$sNodetype);
		}
		
		$aNode['uuid'] = uuid();
		$aNode['fk_nodetype'] = $sNodetype;
		$aNode['s_name'] = $sName;
		$aNode['s_label'] = $sLabel;
		$aNode['s_uid'] = NULL;
		if (isset($this->aNodetypes[$sNodetype]['s_displaytype'])) {
			$aNode['s_displaytype'] = $this->aNodetypes[$sNodetype]['s_displaytype'];
		}
		$aNode['fk_parent'] = $sParentUUID;
		// for now always set full inheritance on new nodes
		$aNode['b_inheritrights'] = 'TRUE';
		$aNode['b_bequeathrights'] = 'TRUE';
		$aNode['b_bequeathlocalrights'] = 'TRUE';
		$aNode['s_currentlifecyclestate'] = NULL;
		
		return ($this->generateInstanceFromRow($aNode, 'new'));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getInstance($sQuery) {
		
		// helper array
		$aMatches = array();
		
		// TODO: find damn bug that uses empty query on root contextmenu
		if ($sQuery == '/' || $sQuery == '') {
			$nodeCurrent = $this->getRootNode();
		} elseif (preg_match('!^//\*\[@uid="([a-zA-Z_]+:[a-zA-Z_]+)"\]$!', $sQuery, $aMatches)) {
			$nodeCurrent = $this->getInstanceByUID($aMatches[1]);
		} elseif (substr_count($sQuery, '/') > 0) {
			$nodeCurrent = $this->getInstanceByPath($sQuery);
		} else {
			$nodeCurrent = $this->getInstanceByUUID($sQuery);
		}
		
		return ($nodeCurrent);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getInstanceByUUID($sQuery, $sParentUUID = NULL) {
		// TODO: needs a thinkover, too many references prevent object destruction
		/*if (isset($this->aNodeCache[$sQuery])) {
			if (isset($this->aNodeCache[$sQuery][$sParentUUID])) {
				return ($this->aNodeCache[$sQuery][$sParentUUID]);
			} else {
				foreach ($this->aNodeCache[$sQuery] as $nodeCurrent) {
					return ($nodeCurrent);
				}
			}
		}*/
		$stmtInfo = $this->DB->prepareKnown('sbCR/getNode/byUUID');
		$stmtInfo->bindParam('id', $sQuery, PDO::PARAM_STR);
		$stmtInfo->execute();
		return ($this->generateInstance($stmtInfo, $sQuery, $sParentUUID));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getInstanceByUID($sQuery) {
		$stmtInfo = $this->DB->prepareKnown('sbCR/getNode/byUID');
		$stmtInfo->bindParam('uid', $sQuery, PDO::PARAM_STR);
		$stmtInfo->execute();
		return ($this->generateInstance($stmtInfo, $sQuery));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	private function getInstanceByPath($sQuery) {
		$iNodeID = $this->resolvePath($sQuery);
		if ($iNodeID !== FALSE) {
			$stmtInfo = $this->DB->prepareKnown('sbCR/getNode/byUUID');
			$stmtInfo->bindParam('id', $iNodeID, PDO::PARAM_STR);
			$stmtInfo->execute();
			return ($this->generateInstance($stmtInfo, $sQuery));
		} else {
			throw new NodeNotFoundException('a node with this query does not exist: "'.$sQuery.'"');
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	private function generateInstance($stmtInfo, $sQuery, $sParentUUID = NULL) {
		
		$aRows = $stmtInfo->fetchAll(PDO::FETCH_ASSOC);
		$stmtInfo->closeCursor();
		foreach ($aRows as $aRow) {
			if ($sParentUUID != NULL) {
				if ($sParentUUID != $aRow['fk_parent']) {
					$aRow['b_primary'] = 'FALSE';
				}
				$aRow['fk_parent'] = $sParentUUID;
			}
			$elemInstance = $this->generateInstanceFromRow($aRow, $sQuery);
			return ($elemInstance);
		}
		throw new NodeNotFoundException('a node with this query does not exist: "'.$sQuery.'"');	
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	private function generateInstanceFromRow($aRow, $sQuery) {
		
		// init
		$this->loadNodetypes();
		$elemSubject = ResponseFactory::createElement('sbnode');
		
		// prepare special properties
		if ($aRow['s_currentlifecyclestate'] == NULL) {
			$aRow['s_currentlifecyclestate'] = 'default';
		}
		
		// set properties
		$elemSubject->setAttribute('nodetype', $aRow['fk_nodetype']);
		$elemSubject->setAttribute('uuid', $aRow['uuid']);
		$elemSubject->setAttribute('name', $aRow['s_name']);
		$elemSubject->setAttribute('label', $aRow['s_label']);
		$elemSubject->setAttribute('uid', $aRow['s_uid']);
		$elemSubject->setAttribute('query', $sQuery);
		$elemSubject->setAttribute('displaytype', str_replace(':', '_', $aRow['fk_nodetype']));
		$elemSubject->setAttribute('parent', $aRow['fk_parent']);
		if (isset($aRow['b_primary'])) {
			$elemSubject->setAttribute('primary', $aRow['b_primary']);
		}
		$elemSubject->setAttribute('inheritrights', $aRow['b_inheritrights']);
		$elemSubject->setAttribute('bequeathrights', $aRow['b_bequeathrights']);
		$elemSubject->setAttribute('bequeathlocalrights', $aRow['b_bequeathlocalrights']);
		$elemSubject->setAttribute('currentlifecyclestate', $aRow['s_currentlifecyclestate']);
		
		// create appropriate class instance
		if (isset($this->aNodetypes[$aRow['fk_nodetype']])) {
			$sLibrary = $this->aNodetypes[$aRow['fk_nodetype']]['s_classfile'];
			$sClass = $this->aNodetypes[$aRow['fk_nodetype']]['s_class'];
			import($sLibrary);
			$nodeSubject = new $sClass($elemSubject, $this);
		} else {
			$nodeSubject = new sbCR_Node($elemSubject, $this);
		}
		
		// needs a thinkover, too many references prevent object destruction
		//$this->aNodeCache[$aRow['uuid']][$aRow['fk_parent']] = $nodeSubject;
		
		return ($nodeSubject);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	private function loadNodetypes() {
		
		if ($this->aNodetypes != NULL) {
			return (FALSE);
		}
		
		$stmtLoad = $this->DB->prepareKnown('sbCR/repository/getNodeTypes');
		$stmtLoad->execute();
		
		$aNodetypes = $stmtLoad->fetchAll(PDO::FETCH_ASSOC);
		$stmtLoad->closeCursor();
		
		foreach ($aNodetypes as $aRow) {
			$this->aNodetypes[$aRow['s_type']] = $aRow;
		}
		
		return (TRUE);
		
	}
	
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	private function resolvePath($sPath) {
		
		$cachePaths = CacheFactory::getInstance('paths');
		$sUUID = $cachePaths->loadData($sPath);
		if ($sUUID !== FALSE) {
			return ($sUUID);
		}
		
		$aPath = explode('/', $sPath);
		
		unset($aPath[0]);
		
		$nodeRoot = $this->getRootNode();
		$sUUID = $this->iteratePath($nodeRoot, $aPath);
		
		if ($sUUID !== NULL) {
			$cachePaths->storeData($sPath, $sUUID);
			return ($sUUID);
		} else {
			return (FALSE);
		}
		
	}
	
	//------------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	private function iteratePath($nodeCurrent, &$aPath, $iPosition = 1) {
		
		$nodeChild = $nodeCurrent->getNode($aPath[$iPosition]);
		
		if (isset($aPath[++$iPosition])) {
			return(self::iteratePath($nodeChild, $aPath, $iPosition));
		}
		return ($nodeChild->getIdentifier());
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function prepareKnown($sID) {
		return ($this->DB->prepareKnown($sID));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function lastInsertId() {
		return ($this->DB->lastInsertId());
	}
	
	
	
	
	//--------------------------------------------------------------------------
	// transaction support
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function beginTransaction($sUID) {
		$this->DB->beginTransaction($sUID);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function commit($sUID) {
		$this->DB->commit($sUID);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function rollback() {
		$this->DB->rollback();
	}
	
	
	
}

?>