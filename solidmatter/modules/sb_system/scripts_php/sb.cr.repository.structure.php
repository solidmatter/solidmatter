<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

define('NODE_CLASS', 1002);
define('NODE_CLASSFILE', 1003);
define('NODE_DISPLAYTYPE', 1005);
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
define('VIEW_CLASS', 3005);
define('VIEW_CLASSFILE', 3006);
define('VIEW_ORDER', 3007);

define('ACT_TYPE', 4001);
define('ACT_DEFAULT', 4002);
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
		'mix:lifecycle' => array(),
		//'mix:versionable' => array(),
		'sbCR:Node' => array(
			'nt:base',
			'mix:created',
			'mix:lastModified',
			'mix:referenceable',
			'mix:lockable',
			'mix:shareable',
			'mix:lifecycle',
		),
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
				PROP_ALIAS => 'created',
			),
			'jcr:createdBy' => array(
				PROP_ALIAS => 'createdby',
			),
			'created' => array(
				PROP_TYPE => 'DATE',
				PROP_MANDATORY => TRUE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'urlsafe',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 'dt_created',
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
				PROP_ALIAS => 'modified',
			),
			'jcr:lastModifiedBy' => array(
				PROP_ALIAS => 'modifiedby',
			),
			'modified' => array(
				PROP_TYPE => 'DATE',
				PROP_MANDATORY => FALSE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'urlsafe',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'EXTENDED',
				PROP_AUXNAME => 'dt_modified',
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
		'sbCR:Node' => array(
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
			'displaytype' => array(
				PROP_TYPE => 'STRING',
				PROP_MANDATORY => FALSE,
				PROP_PROTECTED => TRUE,
				PROP_MULTIPLE => FALSE,
				PROP_INTERNALTYPE => 'string',
				PROP_SHOWINPROPERTIES => FALSE,
				PROP_LABELPATH => '$locale/system/general/labels/',
				PROP_DESCRIPTIONPATH => NULL,
				PROP_STORAGETYPE => 'PRIMARY',
				PROP_AUXNAME => 's_displaytype',
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
	);
	
	private $aNodeTypeAuthorisations = array(
		'sbCR:Node' => array(
			'full' => NULL,
			'read' => 'full',
			'write' => 'full',
			'special' => 'full',
			'grant' => 'full',
		),
	);
	
	private $aPropertyData		= array();
	private $aViewData			= array();
	
	private $aPropertyDefinitionCache	= array();
	
	private $aViewDefinitionCache	= array();
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
				return;
			}
		}
		
		// store nodetype info
		$stmtNodetypes = $this->crSession->prepareKnown('sbCR/repository/getNodeTypes');
		$stmtNodetypes->execute();
		$stmtNodetypes = $stmtNodetypes->fetchAll(PDO::FETCH_ASSOC);
		foreach ($stmtNodetypes as $aRow) {
			if ($aRow['s_type'] == 'sbCR:Node') {
				continue;	
			}
			$this->aNodeTypeHierarchy[$aRow['s_type']][] = 'sbCR:Node';
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
	public function getSession() {
		return ($this->crSession);
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
		if (isset($this->aViewCache[$sNodeTypeName]['supported_views'])) {
			return ($this->aViewCache[$sNodeTypeName]['supported_views']);
		}
		
		// init
		$this->initViewData($sNodeTypeName);
		
		// gather supported views
		$aSupportedViews = array();
		foreach ($this->aViewData[$sNodeTypeName] as $aView) {
			$aSupportedViews[$aView['name']] = array(
				'name' => $aView['name'],
				'priority' => $aView['priority'],
				'visible' => constant($aView['visible']),
			);
		}
		
		// cache views
		$this->aViewCache[$sNodeTypeName]['supported_views'] = $aSupportedViews;
		
		return ($aSupportedViews);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getViewDefinition($sNodeTypeName, $sView) {
		
		// gather view information
		$aSupportedViews = $this->getSupportedViews($sNodeTypeName);
		if (!isset($aSupportedViews[$sView])) {
			throw new RepositoryException('view "'.$sView.'" is not defined for nodetype "'.$sNodeTypeName.'"');	
		}
		
		//var_dumpp($this->aViewData[$sNodeTypeName][$sView]);
		
		$vdCurrentView = new sbCR_ViewDefinition(
			$this,
			$this->aViewData[$sNodeTypeName][$sView]['nodetypename'],
			$this->aViewData[$sNodeTypeName][$sView]['name'],
			$this->aViewData[$sNodeTypeName][$sView]['class'],
			$this->aViewData[$sNodeTypeName][$sView]['classfile'],
			constant($this->aViewData[$sNodeTypeName][$sView]['visible']),
			$this->aViewData[$sNodeTypeName][$sView]['priority']
		);
		
		// gather viewauthorisations
		/*$stmtAuth = $this->crSession->prepareKnown('sbCR/repository/loadViewAuthorisations');
		$stmtAuth->bindParam(':nodetype', $sNodeTypeName, PDO::PARAM_STR);
		$stmtAuth->execute();
		foreach ($stmtAuth as $aRow) {
			$aViews[$aRow['fk_view']]['auth'][] = $aRow['fk_authorisation'];
		}
		$stmtAuth->closeCursor();*/
		
		// cache view
		$this->aViewDefinitionCache[$sNodeTypeName][$sView] = $vdCurrentView;
		
		return ($vdCurrentView);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getDeclaredViewDefinitions($sNodeTypeName) {
		
		throw new LazyBastardException();
		
		// check local cache and 
		if ($this->aDeclaredViewDefinitions == NULL) {
			
			// views defined in nodetype and supertypes
			$this->aDeclaredViewDefinitions = $this->crRepositoryStructure->getSupportedViews($this->aNodeTypeInformation['NodeTypeName']);
			
			
		}
		
		return ($this->aDeclaredViewDefinitions);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getPropertyCache($sNodeTypeName) {
		$this->initPropertyData($sNodeTypeName);
		return(new sbCR_PropertyDefinitionCache($this->aPropertyData[$sNodeTypeName]));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function initViewData($sNodeTypeName) {
		
		// omit predefined mandatory node types
		/*if (isset($this->aNodeTypeHierarchy[$sNodeTypeName])) {
			$this->aViewData[$sNodeTypeName] = array();
			return;	
		}*/
		
		// check local cache
		if (isset($this->aViewData[$sNodeTypeName])) {
			return;
		}
		
		// check cache
		if (Registry::getValue('sb.system.cache.nodetypes.enabled')) {
			$cacheRepos = CacheFactory::getInstance('repository');
			if ($cacheRepos->exists('ViewData:'.$sNodeTypeName)) {
				//echo ('view cache hit');
				$this->aViewData[$sNodeTypeName] = $cacheRepos->loadData('ViewData:'.$sNodeTypeName);
				return;
			}
		}
		
		// gather local views
		$this->aViewData[$sNodeTypeName] = array();
		$stmtViews = $this->crSession->prepareKnown('sbCR/repository/loadViews/supported');
		$stmtViews->bindParam(':nodetype', $sNodeTypeName, PDO::PARAM_STR);
		$stmtViews->execute();
		while ($aRow = $stmtViews->fetch(PDO::FETCH_ASSOC)) {
			$aRow['nodetypename'] = $sNodeTypeName;
			$this->aViewData[$sNodeTypeName][$aRow['name']] = $aRow;
		}
		$stmtViews->closeCursor();
		
		// aggregate views with views from supertypes
		$aSupertypes = $this->getSupertypeNames($sNodeTypeName);
		foreach ($aSupertypes as $sSupertype) {
			// second parameter has priority over first parameter (local views outweight inherited ones)
			//var_dumpp($aViewData);
			/*foreach($this->initViewData($sSupertype) as $aSuperViewData) {
				if (!isset($aViewData[$aSuperViewData['name']])) {
					$aViewData[$aSuperViewData['name'] = $aSuperViewData;
				} else {
					foreach ($aSuperView)
				}
			};*/
			$this->initViewData($sSupertype);
			$this->aViewData[$sNodeTypeName] = array_merge($this->aViewData[$sSupertype], $this->aViewData[$sNodeTypeName]);
		}
		
		// fill cache
		if (Registry::getValue('sb.system.cache.nodetypes.enabled')) {
			$cacheRepos = CacheFactory::getInstance('repository');
			$cacheRepos->storeData('ViewData:'.$sNodeTypeName, $this->aViewData[$sNodeTypeName]);
		}
		
		return;
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function initPropertyData($sNodeTypeName) {
		
		/*static $numcalls = 0;
		echo $numcalls.'|';
		$numcalls++;*/
		
		// already initialized?
		if (isset($this->aPropertyData[$sNodeTypeName])) {
			return;
		}
		
		// check cache
		if (Registry::getValue('sb.system.cache.nodetypes.enabled')) {
			$cacheRepos = CacheFactory::getInstance('repository');
			if ($cacheRepos->exists('PropertyData:'.$sNodeTypeName)) {
				//echo ('view cache hit');
				$this->aPropertyData[$sNodeTypeName] = $cacheRepos->loadData('PropertyData:'.$sNodeTypeName);
				return;
			}
		}
		
		// mandatory properties
		$this->aPropertyData[$sNodeTypeName]['definitions'] = array(
			// primary properties
			'uuid' => array(
				'e_type' => 'STRING',
				's_internaltype' => 'string',
				'b_showinproperties' => 'FALSE',
				's_labelpath' => '$locale/sbSystem/labels/uuid',
				's_descriptionpath' => NULL,
				'b_multiple' => 'FALSE',
				'e_storagetype' => 'PRIMARY',
				's_auxname' => 'uuid',
				'b_protected' => 'TRUE',
				'b_protectedoncreation' => 'FALSE'
			),
			'nodetype' => array(
				'e_type' => 'STRING',
				's_internaltype' => 'string',
				'b_showinproperties' => 'FALSE',
				's_labelpath' => '$locale/sbSystem/labels/nodetype',
				's_descriptionpath' => NULL,
				'b_multiple' => 'FALSE',
				'e_storagetype' => 'PRIMARY',
				's_auxname' => 'fk_nodetype',
				'b_protected' => 'TRUE',
				'b_protectedoncreation' => 'TRUE'
			),
			'label' => array(
				'e_type' => 'STRING',
				's_internaltype' => 'string;minlength=1;maxlength=250;size=60;required=true;',
				'b_showinproperties' => 'TRUE',
				's_labelpath' => '$locale/sbSystem/labels/label',
				's_descriptionpath' => NULL,
				'b_multiple' => 'FALSE',
				'e_storagetype' => 'PRIMARY',
				's_auxname' => 's_label',
				'b_protected' => 'FALSE',
				'b_protectedoncreation' => 'FALSE'
			),
			'name' => array(
				'e_type' => 'STRING',
				's_internaltype' => 'urlsafe;minlength=1;maxlength=100;size=60;required=true;',
				'b_showinproperties' => 'TRUE',
				's_labelpath' => '$locale/sbSystem/labels/urlname',
				's_descriptionpath' => NULL,
				'b_multiple' => 'FALSE',
				'e_storagetype' => 'PRIMARY',
				's_auxname' => 's_name',
				'b_protected' => 'FALSE',
				'b_protectedoncreation' => 'FALSE'
			),
			'displaytype' => array(
				'e_type' => 'STRING',
				's_internaltype' => 'string',
				'b_showinproperties' => 'FALSE',
				's_labelpath' => '$locale/sbSystem/general/labels/',
				's_descriptionpath' => NULL,
				'b_multiple' => 'FALSE',
				'e_storagetype' => 'PRIMARY',
				's_auxname' => 's_displaytype',
				'b_protected' => 'TRUE',
				'b_protectedoncreation' => 'FALSE'
			),
			// extended properties
			'uid' => array(
				'e_type' => 'STRING',
				's_internaltype' => 'string',
				'b_showinproperties' => 'FALSE',
				's_labelpath' => '$locale/sbSystem/general/labels/',
				's_descriptionpath' => NULL,
				'b_multiple' => 'FALSE',
				'e_storagetype' => 'EXTENDED',
				's_auxname' => 's_uid',
				'b_protected' => 'TRUE',
				'b_protectedoncreation' => 'FALSE'
			),
			'inheritrights' => array(
				'e_type' => 'BOOLEAN',
				's_internaltype' => 'checkbox',
				'b_showinproperties' => 'FALSE',
				's_labelpath' => '$locale/sbSystem/general/labels/',
				's_descriptionpath' => NULL,
				'b_multiple' => 'FALSE',
				'e_storagetype' => 'EXTENDED',
				's_auxname' => 'b_inheritrights',
				'b_protected' => 'FALSE',
				'b_protectedoncreation' => 'FALSE'
			),
			'bequeathrights' => array(
				'e_type' => 'BOOLEAN',
				's_internaltype' => 'checkbox',
				'b_showinproperties' => 'FALSE',
				's_labelpath' => '$locale/sbSystem/general/labels/',
				's_descriptionpath' => NULL,
				'b_multiple' => 'FALSE',
				'e_storagetype' => 'EXTENDED',
				's_auxname' => 'b_bequeathrights',
				'b_protected' => 'FALSE',
				'b_protectedoncreation' => 'FALSE'
			),
			'bequeathlocalrights' => array(
				'e_type' => 'BOOLEAN',
				's_internaltype' => 'checkbox',
				'b_showinproperties' => 'FALSE',
				's_labelpath' => '$locale/sbSystem/general/labels/',
				's_descriptionpath' => NULL,
				'b_multiple' => 'FALSE',
				'e_storagetype' => 'EXTENDED',
				's_auxname' => 'b_bequeathlocalrights',
				'b_protected' => 'FALSE',
				'b_protectedoncreation' => 'FALSE'
			),
			'createdby' => array(
				'e_type' => 'WEAKREFERENCE',
				's_internaltype' => 'string',
				'b_showinproperties' => 'FALSE',
				's_labelpath' => '$locale/sbSystem/general/labels/',
				's_descriptionpath' => NULL,
				'b_multiple' => 'FALSE',
				'e_storagetype' => 'EXTENDED',
				's_auxname' => 'fk_createdby',
				'b_protected' => 'TRUE',
				'b_protectedoncreation' => 'TRUE'
			),
			'modifiedby' => array(
				'e_type' => 'WEAKREFERENCE',
				's_internaltype' => 'string',
				'b_showinproperties' => 'FALSE',
				's_labelpath' => '$locale/sbSystem/general/labels/',
				's_descriptionpath' => NULL,
				'b_multiple' => 'FALSE',
				'e_storagetype' => 'EXTENDED',
				's_auxname' => 'fk_modifiedby',
				'b_protected' => 'TRUE',
				'b_protectedoncreation' => 'TRUE'
			),
			'created' => array(
				'e_type' => 'DATE',
				's_internaltype' => 'urlsafe',
				'b_showinproperties' => 'FALSE',
				's_labelpath' => '$locale/sbSystem/general/labels/',
				's_descriptionpath' => NULL,
				'b_multiple' => 'FALSE',
				'e_storagetype' => 'EXTENDED',
				's_auxname' => 'dt_created',
				'b_protected' => 'TRUE',
				'b_protectedoncreation' => 'TRUE'
			),
			'modified' => array(
				'e_type' => 'DATE',
				's_internaltype' => 'urlsafe',
				'b_showinproperties' => 'FALSE',
				's_labelpath' => '$locale/sbSystem/general/labels/',
				's_descriptionpath' => NULL,
				'b_multiple' => 'FALSE',
				'e_storagetype' => 'EXTENDED',
				's_auxname' => 'dt_modified',
				'b_protected' => 'TRUE',
				'b_protectedoncreation' => 'TRUE'
			),
		);
		
		$this->aPropertyData[$sNodeTypeName]['storage'] = array(
			'PRIMARY' => TRUE,
			'EXTENDED' => TRUE,
			'EXTERNAL' => FALSE,
			'AUXILIARY' => FALSE
		);
		
		$stmtProperties = $this->crSession->prepareKnown('sbCR/node/getPropertyDefinitions');
		$stmtProperties->bindValue('nodetype', $sNodeTypeName, PDO::PARAM_STR);
		$stmtProperties->execute();
		while ($aRow = $stmtProperties->fetch(PDO::FETCH_ASSOC)) {
			$this->aPropertyData[$sNodeTypeName]['storage'][$aRow['e_storagetype']] = TRUE;
			$this->aPropertyData[$sNodeTypeName]['definitions'][$aRow['s_attributename']] = $aRow;
		}
		
		// fill cache
		if (Registry::getValue('sb.system.cache.nodetypes.enabled')) {
			$cacheRepos = CacheFactory::getInstance('repository');
			$cacheRepos->storeData('PropertyData:'.$sNodeTypeName, $this->aPropertyData[$sNodeTypeName]);
		}
		
		return;

	}
	
	
	
}

?>