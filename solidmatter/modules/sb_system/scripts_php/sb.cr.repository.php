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
	public function gatherRepositoryInformation() {
		
		$sRepository = (string) $this->elemRepositoryDefinition['id'];
		$aRepositoryInfo = array();
		
		// get nodetypes
		$stmtNodetypes = $this->DB->prepareKnown('sbCR/repository/getNodeTypes');
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