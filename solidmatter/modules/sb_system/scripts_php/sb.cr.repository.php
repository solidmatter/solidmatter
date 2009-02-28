<?php

//------------------------------------------------------------------------------
/**
* @package solidMatter[sbCR]
* @author	()((() [Oliver Müller]
* @version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.pdo.repository');
import('sb.cr.propertydefinitioncache');

// xml file containing all information on repositories this sbCR instance supports
if (!defined('REPOSITORY_DEFINITION_FILE')) {	define('REPOSITORY_DEFINITION_FILE', 'repositories.xml'); }
// number of characters to use for the pseudo-materialized path on each level
if (!defined('REPOSITORY_MPHASH_SIZE')) {		define('REPOSITORY_MPHASH_SIZE', 5); }

//------------------------------------------------------------------------------
/**
*/
class sbCR_Repository {
	
	private $aDescriptors = array(
		'SPEC_VERSION_DESC' => '1.0',
		'SPEC_NAME_DESC' => 'solidbytes Content Repository for PHP Technology API',
		'REP_VENDOR_DESC' => 'solidbytes',
		'REP_VENDOR_URL_DESC' => 'http://www.solidbytes.net',
		'REP_NAME_DESC' => 'sbCR',
		'REP_VERSION_DESC' => '1.0',
		'LEVEL_1_SUPPORTED' => 'true',
		'LEVEL_2_SUPPORTED' => 'true',
		'OPTION_TRANSACTIONS_SUPPORTED' => 'true',
		'OPTION_VERSIONING_SUPPORTED' => 'false',
		'OPTION_OBSERVATION_SUPPORTED' => 'false',
		'OPTION_LOCKING_SUPPORTED' => 'false',
		'OPTION_LIFECYCLE_SUPPORTED' => 'true',
		'OPTION_QUERY_SQL_SUPPORTED' => 'false',
		'QUERY_XPATH_POS_INDEX' => 'false',
		'QUERY_XPATH_DOC_ORDER' => 'false',
	);
	
	// basic info about existing repositories 
	private $sxmlRepositoryDefinitions = NULL;
	private $elemRepositoryDefinition = NULL;
	
	private $cacheRepository = NULL;
	private $aRepositoryInformation = array();
	
	private $aPropertyDefinitionCache = array();
	
	private $DB = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($sRepositoryID) {
		
		// load definitions
		$this->sxmlRepositoryDefinitions = simplexml_load_file(REPOSITORY_DEFINITION_FILE);
		
		// check in repository exists
		foreach ($this->sxmlRepositoryDefinitions->repository as $elemRepository) {
			if ($elemRepository['id'] == $sRepositoryID) {
				$this->elemRepositoryDefinition = $elemRepository;
			}
		}
		if ($this->elemRepositoryDefinition == NULL) {
			throw new RepositoryException(__CLASS__.': no such repository "'.$this->sRepositoryID.'"');
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getDescriptor($sKey) {
		if (isset($this->aDescriptors[$sKey])) {
			return ($this->aDescriptors[$sKey]);
		} else {
			return (NULL);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getDescriptorKeys() {
		return (array_keys($this->aDescriptors));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function login($crCredentials = NULL, $sWorkspaceName = NULL) {
		
		// credentials and workspace are mandatory
		if ($crCredentials == NULL || $sWorkspaceName == NULL) {
			throw new RepositoryException(__CLASS__.': credentials or workspace missing');	
		}
		
		// check if workspace exists
		$elemWorkspace = NULL;
		foreach ($this->elemRepositoryDefinition->workspaces->workspace as $elemCurrentWorkspace) {
			if ($elemCurrentWorkspace['id'] == $sWorkspaceName) {
				$elemWorkspace = $elemCurrentWorkspace;
				$sWorkspacePrefix = (string) $elemCurrentWorkspace['prefix'];
			}
		}
		if ($elemWorkspace == NULL) {
			throw new NoSuchWorkspaceException(__CLASS__.': workspace "'.$sWorkspaceName.'" not in repository "'.$this->elemRepositoryDefinition['id'].'"');
		}
		
		// check authorisation
		foreach ($elemWorkspace->user as $elemUser) {
			// TODO: really check permissions, not only user existence!
			if ($elemUser['name'] != $crCredentials->getUserID() || $elemUser['pass'] != $crCredentials->getPassword()) {
				throw new AccessDeniedException(__CLASS__.': provided user is not authorised to access workspace "'.$sWorkspaceName.'" in repository "'.$this->elemRepositoryDefinition['id'].'"');
			}
		}
		
		$sRepositoryPrefix = (string) $this->elemRepositoryDefinition['prefix'];
		
		// init database
		if ((string) $this->elemRepositoryDefinition->db['use'] == 'system') {
			$this->DB = System::getDatabase();
			$this->DB->setWorkspace($sRepositoryPrefix, $sWorkspacePrefix);
		} else {
			$this->DB = new sbPDORepository($this->elemRepositoryDefinition->db);
			$this->DB->setWorkspace($sRepositoryPrefix, $sWorkspacePrefix);
			// TODO: keep the frickin system db out of here!
			System::getDatabase()->setWorkspace($sRepositoryPrefix, $sWorkspacePrefix);
		}
		
		// load and store repository infos if necessary
		/*$this->cacheRepository = CacheFactory::getInstance('repository');
		if (FALSE || $this->cacheRepository->exists($this->sRepositoryID)) {
			$this->aRepositoryInformation = $this->cacheRepository->loadData($this->sRepositoryID);
		} else {
			$this->gatherRepositoryInformation();
		}*/
		
		$crSession = new sbCR_Session($this->DB, $crCredentials, $this, $sWorkspaceName, $sWorkspacePrefix);
		
		return ($crSession);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getPropertyDefinitions($sNodetype, $bAsArray = TRUE) {
		
		/*static $numcalls = 0;
		echo $numcalls.'|';
		$numcalls++;*/
		
		if ($bAsArray == FALSE) {
			throw new LazyBastardException('has to be implemented');	
		} else {
			
			// check cache
			if (isset($this->aPropertyDefinitionCache[$sNodetype])) {
				return ($this->aPropertyDefinitionCache[$sNodetype]);
				//echo '*';
			}
			
			// mandatory properties
			$aPropertyDefinitions = array(
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
					's_internaltype' => 'string;minlength=1;maxlength=250;required=true;',
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
					's_internaltype' => 'urlsafe;minlength=1;maxlength=100;required=true;',
					'b_showinproperties' => 'TRUE',
					's_labelpath' => '$locale/sbSystem/labels/urlname',
					's_descriptionpath' => NULL,
					'b_multiple' => 'FALSE',
					'e_storagetype' => 'PRIMARY',
					's_auxname' => 's_name',
					'b_protected' => 'FALSE',
					'b_protectedoncreation' => 'FALSE'
				),
				'csstype' => array(
					'e_type' => 'STRING',
					's_internaltype' => 'string',
					'b_showinproperties' => 'FALSE',
					's_labelpath' => '$locale/sbSystem/general/labels/',
					's_descriptionpath' => NULL,
					'b_multiple' => 'FALSE',
					'e_storagetype' => 'PRIMARY',
					's_auxname' => 's_csstype',
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
				'customcsstype' => array(
					'e_type' => 'STRING',
					's_internaltype' => 'string',
					'b_showinproperties' => 'FALSE',
					's_labelpath' => '$locale/sbSystem/general/labels/',
					's_descriptionpath' => NULL,
					'b_multiple' => 'FALSE',
					'e_storagetype' => 'EXTENDED',
					's_auxname' => 's_customcsstype',
					'b_protected' => 'FALSE',
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
				'deletedby' => array(
					'e_type' => 'WEAKREFERENCE',
					's_internaltype' => 'string',
					'b_showinproperties' => 'FALSE',
					's_labelpath' => '$locale/sbSystem/general/labels/',
					's_descriptionpath' => NULL,
					'b_multiple' => 'FALSE',
					'e_storagetype' => 'EXTENDED',
					's_auxname' => 'fk_deletedby',
					'b_protected' => 'TRUE',
					'b_protectedoncreation' => 'TRUE'
				),
				'createdat' => array(
					'e_type' => 'DATE',
					's_internaltype' => 'urlsafe',
					'b_showinproperties' => 'FALSE',
					's_labelpath' => '$locale/sbSystem/general/labels/',
					's_descriptionpath' => NULL,
					'b_multiple' => 'FALSE',
					'e_storagetype' => 'EXTENDED',
					's_auxname' => 'dt_createdat',
					'b_protected' => 'TRUE',
					'b_protectedoncreation' => 'TRUE'
				),
				'modifiedat' => array(
					'e_type' => 'DATE',
					's_internaltype' => 'urlsafe',
					'b_showinproperties' => 'FALSE',
					's_labelpath' => '$locale/sbSystem/general/labels/',
					's_descriptionpath' => NULL,
					'b_multiple' => 'FALSE',
					'e_storagetype' => 'EXTENDED',
					's_auxname' => 'dt_modifiedat',
					'b_protected' => 'TRUE',
					'b_protectedoncreation' => 'TRUE'
				),
				'deletedat' => array(
					'e_type' => 'DATE',
					's_internaltype' => 'urlsafe',
					'b_showinproperties' => 'FALSE',
					's_labelpath' => '$locale/sbSystem/general/labels/',
					's_descriptionpath' => NULL,
					'b_multiple' => 'FALSE',
					'e_storagetype' => 'EXTENDED',
					's_auxname' => 'dt_deletedat',
					'b_protected' => 'TRUE',
					'b_protectedoncreation' => 'TRUE'
				),
			);
			//var_dumpp('indexed:'.strlen(serialize($aPropertyDefinitions)));
			$stmtProperties = $this->DB->prepareKnown('sbCR/node/getPropertyDefinitions');
			$stmtProperties->bindValue('nodetype', $sNodetype, PDO::PARAM_STR);
			$stmtProperties->execute();
			$aPropertyStorageInfo = array(
				'PRIMARY' => TRUE,
				'EXTENDED' => TRUE,
				'EXTERNAL' => FALSE,
				'AUXILIARY' => FALSE
			);
			while ($aRow = $stmtProperties->fetch(PDO::FETCH_ASSOC)) {
				$aPropertyStorageInfo[$aRow['e_storagetype']] = TRUE;
				$aPropertyDefinitions[$aRow['s_attributename']] = $aRow;
			}
			
			// store in cache
			$this->aPropertyDefinitionCache[$sNodetype] = new sbCR_PropertyDefinitionCache($aPropertyDefinitions, $aPropertyStorageInfo);
			//var_dumpp($this->aPropertyDefinitionCache['arrays'][$sNodetype]);
			return ($this->aPropertyDefinitionCache[$sNodetype]);
			
		}

	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function gatherRepositoryInformation() {
		
		$sRepository = (string) $this->elemRepositoryDefinition['id'];
		$aRepositoryInfo = array();
		
		// get nodetypes
		$stmtNodetypes = $this->DB->prepareKnown('sbCR/repository/getNodetypes');
		$stmtNodetypes->execute();
		$stmtNodetypes = $stmtNodetypes->fetchAll(PDO::FETCH_ASSOC);
		foreach ($stmtNodetypes as $aRow) {
			//var_dumpp($aRow);
			$aRepositoryInfo[$aRow['s_type']]['details'] = $aRow;
		}
		
		// get views
		$aViews = array();
		$stmtViews = $this->DB->prepareKnown('sb_system/repository/getViews');
		$stmtViews->execute();
		$stmtViews = $stmtViews->fetchAll(PDO::FETCH_ASSOC);
		foreach ($stmtViews as $aRow) {
			$aRepositoryInfo[$aRow['fk_nodetype']]['views'][$aRow['s_view']]['details'] = $aRow;
		}
		
		// get views
		$aActions = array();
		$stmtActions = $this->DB->prepareKnown('sb_system/repository/getActions');
		$stmtActions->execute();
		$stmtActions = $stmtActions->fetchAll(PDO::FETCH_ASSOC);
		foreach ($stmtActions as $aRow) {
			$aRepositoryInfo[$aRow['fk_nodetype']]['views'][$aRow['s_view']]['actions'][$aRow['s_action']]['details'] = $aRow;
		}
		
		//var_dumpp($aRepositoryInfo);
		
		// create DOM and store XML 
		$domReposInfo = new sbDOMDocument('1.0');
		$elemRoot = $domReposInfo->createElement('repository');
		$elemNodetypes = $domReposInfo->createElement('nodetypes');
		
		foreach ($aRepositoryInfo as $sNodetype => $aNodetype) {
			if (!isset($aNodetype['details'])) {
				continue;
			}
			$elemNodetype = $domReposInfo->createElement('nodetype');
			foreach ($aNodetype['details'] as $sKey => $sValue) {
				$elemNodetype->setAttribute($sKey, $sValue);
			}
			$elemViews = $domReposInfo->createElement('views');
			if (isset($aNodetype['views'])) {
				foreach ($aNodetype['views'] as $sView => $aView) {
					if (!isset($aView['details'])) {
						//var_dumpp($sView);
						continue;
					}
					$elemView = $domReposInfo->createElement('view');
					foreach ($aView['details'] as $sKey => $sValue) {
						$elemView->setAttribute($sKey, $sValue);
					}
					$elemActions = $domReposInfo->createElement('actions');
					if (isset($aView['actions'])) {
						foreach ($aView['actions'] as $aAction) {
							$elemAction = $domReposInfo->createElement('action');
							foreach ($aAction['details'] as $sKey => $sValue) {
								$elemAction->setAttribute($sKey, $sValue);
							}
							$elemActions->appendChild($elemAction);
						}
						$elemView->appendChild($elemActions);
					}
					$elemViews->appendChild($elemView);
				}
			}
			$elemNodetype->appendChild($elemViews);
			$elemNodetypes->appendChild($elemNodetype);
		}
		
		$elemRoot->appendChild($elemNodetypes);
		$domReposInfo->appendChild($elemRoot);
		
		//var_dumpp ($domReposInfo->saveXML());
		//$domReposInfo->save('repository_structure_'.$sRepository.'.xml');
		return ($domReposInfo);
	}	
	
}

?>