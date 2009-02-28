<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//define('NODE_MODULE', 1001);
define('NODE_CLASS', 1002);
define('NODE_CLASSFILE', 1003);
//define('NODE_CATEGORY', 1004);
define('NODE_CSSTYPE', 1005);
//define('NODE_FRONTENDACCESS', 1006);
//define('NODE_EXTENSION', 1007);
//define('NODE_CUSTOM', 1008);
define('NODE_ABSTRACT', 1009);
define('NODE_TYPE', 1010);
define('NODE_USESTAGS', 1011);

define('PROP_TYPE', 2001);
define('PROP_MANDATORY', 2002);
define('PROP_PROTECTED', 2003);
define('PROP_MULTIPLE', 2004);
define('PROP_AUTOCREATED', 2005);
define('PROP_CONSTRAINTS', 2006);
define('PROP_ALIAS', 2010);
define('PROP_PROTECTEDONCREATION', 2011);
define('PROP_STORAGETYPE', 2012);
define('PROP_AUXNAME', 2013);
define('PROP_INTERNALTYPE', 2014);
define('PROP_SHOWINPROPERTIES', 2015);
define('PROP_LABELPATH', 2016);
define('PROP_DESCRIPTIONPATH', 2017);

define('VIEW_DEFAULT', 3001);
define('VIEW_DISPLAY', 3002);
define('VIEW_LABELPATH', 3003);
//define('VIEW_MODULE', 3004);
define('VIEW_CLASS', 3005);
define('VIEW_CLASSFILE', 3006);
define('VIEW_ORDER', 3007);

define('ACT_TYPE', 4001);
define('ACT_DEFAULT', 4002);
//define('ACT_MODULE', 4003);
define('ACT_CLASS', 4004);
define('ACT_CLASSFILE', 4005);
define('ACT_OUTPUTTYPE', 4006);
define('ACT_STYLESHEET', 4007);
define('ACT_MIMETYPE', 4008);
define('ACT_USELOCALE', 4009);

//------------------------------------------------------------------------------
/** 
*/
class sbCR_RepositoryStructure {
	
	private $crSession = NULL;
	
	private $aNodeTypeHierarchy = array(
		'nt:base' => array(),
		'mix:created' => array(),
		'mix:language' => array(),
		'mix:lastModified' => array(),
		'mix:lockable' => array(
			'mix:referenceable',
		),
		'mix:mimetype' => array(),
		'mix:referenceable' => array(),
		'mix:shareable' => array(
			'mix:referenceable',
		),
		'mix:simpleVersionable' => array(
			'mix:referenceable',
		),
		'mix:title' => array(),
		//'mix:versionable' => array(),
		'sb:deleted' => array(),
		'sb:node' => array(
			'nt:base',
			'mix:created',
			'mix:lastModified',
			'mix:referenceable',
			'mix:lockable',
			'mix:shareable',
			'mix:lifecycle',
			'sb:deleted',
		),
		'mix:lifecycle' => array(),
	);
	
	private $aNodeTypeProperies = array(
		'mix:referencable' => array(
			'jcr:uuid' => array(
				PROP_ALIAS => 'uuid',
			),
			'uuid' => array(
				PROP_TYPE => 'STRING',
				PROP_MANDATORY => TRUE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'string',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/label',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'PRIMARY',
				PROP_AUXNAME => 'uuid',
				PROP_PROTECTEDONCREATION => FALSE
			),
		
		),
		'mix:created' => array(
			'jcr:created' => array(
				PROP_ALIAS => 'createdat',
			),
			'jcr:createdBy' => array(
				PROP_ALIAS => 'createdby',
			),
			'createdat' => array(
				PROP_TYPE => 'DATE',
				PROP_MANDATORY => TRUE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'urlsafe',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 'dt_createdat',
				PROP_PROTECTEDONCREATION => TRUE
			),
			'createdby' => array(
				PROP_TYPE => 'WEAKREFERENCE',
				PROP_MANDATORY => TRUE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'string',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 'fk_createdby',
				PROP_PROTECTEDONCREATION => TRUE
			),
		),
		'mix:lastModified' => array(
			'jcr:lastModified' => array(
				PROP_ALIAS => 'modifiedat',
			),
			'jcr:lastModifiedBy' => array(
				PROP_ALIAS => 'modifiedby',
			),
			'modifiedat' => array(
				PROP_TYPE => 'DATE',
				PROP_MANDATORY => FALSE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'urlsafe',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 'dt_modifiedat',
				PROP_PROTECTEDONCREATION => TRUE
			),
			'modifiedby' => array(
				PROP_TYPE => 'WEAKREFERENCE',
				PROP_MANDATORY => FALSE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'string',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 'fk_modifiedby',
				PROP_PROTECTEDONCREATION => TRUE
			),
		),
		'mix:lockable' => array(
			'jcr:lockOwner' => array(
				PROP_TYPE => 'STRING',
				PROP_MANDATORY => FALSE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'string',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 'b_bequeathrights',
				PROP_PROTECTEDONCREATION => FALSE
			),
			'jcr:lockIsDeep' => array(
				PROP_TYPE => 'BOOLEAN',
				PROP_MANDATORY => FALSE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'checkbox',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 'b_bequeathrights',
				PROP_PROTECTEDONCREATION => TRUE
			),
		),
		'mix:language' => array(
			'jcr:language' => array(
				PROP_TYPE => 'STRING',
				PROP_MANDATORY => FALSE,
				PROP_PROTECTED => FALSE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'string',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 'b_bequeathrights',
				PROP_PROTECTEDONCREATION => FALSE
			),
		),
		'mix:simpleVersionable' => array(
			'jcr:isCheckedOut' => array(
				PROP_TYPE => 'BOOLEAN',
				PROP_MANDATORY => TRUE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => '',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => '',
				PROP_PROTECTEDONCREATION => TRUE
			),
			'jcr:versionLabels' => array(
				PROP_TYPE => 'NAME',
				PROP_MANDATORY => TRUE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => TRUE,
				PROP_INTERNALTYPE => '',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => '',
				PROP_PROTECTEDONCREATION => TRUE
			),
		),
		'mix:mimeType' => array(
			'jcr:mimeType' => array(
				PROP_TYPE => 'STRING',
				PROP_MANDATORY => FALSE,
				PROP_PROTECTED => FALSE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'string',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 'b_bequeathrights',
				PROP_PROTECTEDONCREATION => FALSE
			),
			'jcr:encoding' => array(
				PROP_TYPE => 'STRING',
				PROP_MANDATORY => FALSE,
				PROP_PROTECTED => FALSE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'string',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 'b_bequeathrights',
				PROP_PROTECTEDONCREATION => TRUE
			),
		),
		'sb:node' => array(
			'nodetype' => array(
				PROP_TYPE => 'STRING',
				PROP_MANDATORY => TRUE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'string',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/nodetype',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'PRIMARY',
				PROP_AUXNAME => 'fk_nodetype',
				PROP_PROTECTEDONCREATION => TRUE
			),
			'label' => array(
				PROP_TYPE => 'STRING',
				PROP_MANDATORY => TRUE,
				PROP_PROTECTED => FALSE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'string;minlength=1;maxlength=250;required=true;',
				PROP_SHOWINPROPERTIES => TRUE,
				PROP_LABELPATH => '$locale/system/general/labels/label',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'PRIMARY',
				PROP_AUXNAME => 's_label',
				PROP_PROTECTEDONCREATION => FALSE
			),
			'name' => array(
				PROP_TYPE => 'STRING',
				PROP_MANDATORY => TRUE,
				PROP_PROTECTED => FALSE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'urlsafe;minlength=1;maxlength=100;required=true;',
				PROP_SHOWINPROPERTIES => TRUE,
				PROP_LABELPATH => '$locale/system/general/labels/urlname',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'PRIMARY',
				PROP_AUXNAME => 's_name',
				PROP_PROTECTEDONCREATION => FALSE
			),
			'csstype' => array(
				PROP_TYPE => 'STRING',
				PROP_MANDATORY => FALSE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'string',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'PRIMARY',
				PROP_AUXNAME => 's_csstype',
				PROP_PROTECTEDONCREATION => FALSE
			),
			// extended properties
			'uid' => array(
				PROP_TYPE => 'STRING',
				PROP_MANDATORY => FALSE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'string',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 's_uid',
				PROP_PROTECTEDONCREATION => FALSE
			),
			'customcsstype' => array(
				PROP_TYPE => 'STRING',
				PROP_MANDATORY => TRUE,
				PROP_PROTECTED => FALSE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'string',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 's_customcsstype',
				PROP_PROTECTEDONCREATION => FALSE
			),
			'inheritrights' => array(
				PROP_TYPE => 'BOOLEAN',
				PROP_MANDATORY => TRUE,
				PROP_PROTECTED => FALSE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'checkbox',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 'b_inheritrights',
				PROP_PROTECTEDONCREATION => FALSE
			),
			'bequeathrights' => array(
				PROP_TYPE => 'BOOLEAN',
				PROP_MANDATORY => TRUE,
				PROP_PROTECTED => FALSE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'checkbox',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 'b_bequeathrights',
				PROP_PROTECTEDONCREATION => FALSE
			),
		),
		'sb:deleted' => array(		
			'deletedby' => array(
				PROP_TYPE => 'WEAKREFERENCE',
				PROP_MANDATORY => FALSE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'string',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 'fk_deletedby',
				PROP_PROTECTEDONCREATION => TRUE
			),
			'deletedat' => array(
				PROP_TYPE => 'DATE',
				PROP_MANDATORY => FALSE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'urlsafe',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 'dt_deletedat',
				PROP_PROTECTEDONCREATION => TRUE
			),
			'deletedfrom' => array(
				PROP_TYPE => 'DATE',
				PROP_MANDATORY => FALSE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'urlsafe',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 'dt_deletedat',
				PROP_PROTECTEDONCREATION => TRUE
			),
		),
	);
	
	private $aNodeTypeAuthorisations = array(
		'sb:node' => array(
			'full' => NULL,
			'read' => 'full',
			'write' => 'full',
			'special' => 'full',
			'grant' => 'full',
		),
	);
	
	private $aViewCache				= array();
	private $aAuthorisationCache	= array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($crSession) {
		
		// bind to session
		$this->crSession = $crSession;
		
		// check cache
		if (Registry::getValue('sb.system.cache.nodetypes.enabled')) {
			$cacheRepos = CacheFactory::getInstance('repository');
			if ($cacheRepos->exists('NodeTypeHierarchy')) {
				$this->aNodeTypeHierarchy = $cacheRepos->loadData('NodeTypeHierarchy');
				//echo ('nodetypehierarchy cache hit');
				return;
			}
		}
		
		// store nodetype info
		$stmtNodetypes = $this->crSession->prepareKnown('sbCR/repository/getNodeTypes');
		$stmtNodetypes->execute();
		$stmtNodetypes = $stmtNodetypes->fetchAll(PDO::FETCH_ASSOC);
		foreach ($stmtNodetypes as $aRow) {
			if ($aRow['s_type'] == 'sb:node') {
				continue;	
			}
			$this->aNodeTypeHierarchy[$aRow['s_type']][] = 'sb:node';
		}
		
		// store nodetype hierarchy
		$stmtNodetypes = $this->crSession->prepareKnown('sbCR/repository/getNodeTypeHierarchy');
		$stmtNodetypes->execute();
		$stmtNodetypes = $stmtNodetypes->fetchAll(PDO::FETCH_ASSOC);
		foreach ($stmtNodetypes as $aRow) {
			$this->aNodeTypeHierarchy[$aRow['fk_childnodetype']][] = $aRow['fk_parentnodetype'];
		}
		
		// fill cache
		if (Registry::getValue('sb.system.cache.nodetypes.enabled')) {
			$cacheRepos = CacheFactory::getInstance('repository');
			$cacheRepos->storeData('NodeTypeHierarchy', $this->aNodeTypeHierarchy);
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getNodeTypeHierarchy() {
		return ($this->aNodeTypeHierarchy);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getNodeType($sNodeTypeName) {
		if (!isset($this->aNodeTypes[$sNodeTypeName])) {
			$this->aNodeTypes[$sNodeTypeName] = new sbCR_NodeType($this, $sNodeTypeName);
		}
		return ($this->aNodeTypes[$sNodeTypeName]);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getDeclaredSupertypeNames($sNodeTypeName) {
		return ($this->aNodeTypeHierarchy[$sNodeTypeName]);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getSupertypeNames($sNodeTypeName, $aSupertypes = array()) {
		foreach ($this->aNodeTypeHierarchy[$sNodeTypeName] as $sSupertype) {
			$aSupertypes[$sSupertype] = $sSupertype;
			$this->getSupertypeNames($sSupertype, &$aSupertypes);
		}
		return ($aSupertypes);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getDeclaredSupertypes($sNodeTypeName) {
		$aSupertypes = array();
		foreach ($this->aNodeTypeHierarchy[$sNodeTypeName] as $sSupertype) {
			$aSupertypes[$sSupertype] = $this->getNodeType($sSupertype);
		}
		return ($aSupertypes);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getSupertypes($sNodeTypeName, $aSupertypes = array()) {
		foreach ($this->aNodeTypeHierarchy[$sNodeTypeName] as $sSupertype) {
			$aSupertypes[$sSupertype] = $this->getNodeType($sSupertype);
			$this->getSupertypes($sSupertype, &$aSupertypes);
		}
		return ($aSupertypes);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getSupportedAuthorisations($sNodeType) {
		
		// check if they are already loaded for this nodetype
		if (isset($this->aAuthorisationCache[$sNodeType])) {
			return ($this->aAuthorisationCache[$sNodeType]);
		}
		
		// default authorisations (always supported)
		$aAuthorisations = array(
			'full' => NULL,
			'read' => 'full',
			'write' => 'full',
			'special' => 'full',
			'grant' => 'full',
		);
		
		// retrieve additional authorisations
		$stmtAuthorisations = $this->crSession->prepareKnown('sbCR/repository/loadAuthorisations/supported');
		$stmtAuthorisations->bindParam(':node_type', $sNodeType, PDO::PARAM_STR);
		$stmtAuthorisations->execute();
		foreach ($stmtAuthorisations as $aRow) {
			$aAuthorisations[$aRow['s_authorisation']] = $aRow['fk_parentauthorisation'];
		}
		$stmtAuthorisations->closeCursor();
		
		// cache Authorisations
		reset($aAuthorisations);
		$this->aAuthorisationCache[$sNodeType] = $aAuthorisations;
		
		return ($aAuthorisations);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getSupportedViews($sNodeTypeName) {
		
		// check if they are already loaded for this nodetype
		/*if (isset($this->aViewCache[$sNodeTypeName])) {
			return ($this->aViewCache[$sNodeTypeName]);
		}*/
		
		// check cache
		if (Registry::getValue('sb.system.cache.nodetypes.enabled')) {
			$cacheRepos = CacheFactory::getInstance('repository');
			if ($cacheRepos->exists('Views:'.$sNodeTypeName)) {
				$aViews = $cacheRepos->loadData('Views:'.$sNodeTypeName);
				//echo ('view cache hit');
				return ($aViews);
			}
		}
		
		$aViews = array();
		
		$aSupertypes = $this->getSupertypes($sNodeTypeName);
		//var_dumpp($aSupertypes);
		
		// gather views
		$stmtViews = $this->crSession->prepareKnown('sbCR/repository/loadViews/supported');
		$stmtViews->bindParam(':nodetype', $sNodeTypeName, PDO::PARAM_STR);
		$stmtViews->execute();
		foreach ($stmtViews as $aRow) {
			$aViews[$aRow['s_view']]['s_classfile'] = $aRow['s_classfile'];
			$aViews[$aRow['s_view']]['s_class'] = $aRow['s_class'];
			$aViews[$aRow['s_view']]['b_default'] = $aRow['b_default'];
			$aViews[$aRow['s_view']]['b_display'] = $aRow['b_display'];
		}
		$stmtViews->closeCursor();
		
		// gather viewauthorisations
		$stmtAuth = $this->crSession->prepareKnown('sbCR/repository/loadViewAuthorisations');
		$stmtAuth->bindParam(':nodetype', $sNodeTypeName, PDO::PARAM_STR);
		$stmtAuth->execute();
		foreach ($stmtAuth as $aRow) {
			$aViews[$aRow['fk_view']]['auth'][] = $aRow['fk_authorisation'];	
		}
		$stmtAuth->closeCursor();
		
		// cache views
		/*reset($aViews);
		$this->aViewCache[$sNodeTypeName] = $aViews;*/
		
		// fill cache
		if (Registry::getValue('sb.system.cache.nodetypes.enabled')) {
			$cacheRepos = CacheFactory::getInstance('repository');
			$cacheRepos->storeData('Views:'.$sNodeTypeName, $aViews);
		}
		
		return ($aViews);
		
	}
	
}

?>