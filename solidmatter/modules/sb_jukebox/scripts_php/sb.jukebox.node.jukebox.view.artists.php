<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbView_jukebox_jukebox_artists extends sbJukeboxView {
	
	protected $aRequiredAuthorisations = array(
		'display' => array('read'),
		'search' => array('read'),
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
				
				$formSearch = $this->buildSearchForm('artists');
				$formSearch->saveDOM();
				$_RESPONSE->addData($formSearch);
				
				$sRestrict = $_REQUEST->getParam('show');
				if ($sRestrict != NULL) {
					if ($sRestrict == '0-9') {
						$stmtGetArtists = $this->nodeSubject->getSession()->prepareKnown('sbJukebox/jukebox/search/various/numeric');
					} else {
						$stmtGetArtists = $this->nodeSubject->getSession()->prepareKnown('sbJukebox/jukebox/search/various/byLabel');
						$stmtGetArtists->bindValue('searchstring', $sRestrict.'%', PDO::PARAM_STR);
					}
				} else {
					$stmtGetArtists = $this->crSession->prepareKnown('sbJukebox/jukebox/various/getRandom');
					$stmtGetArtists->bindValue('limit', 25, PDO::PARAM_INT);
				}
				$stmtGetArtists->bindValue('nodetype', 'sb_jukebox:artist', PDO::PARAM_STR);
				$stmtGetArtists->bindValue('jukebox_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
				$stmtGetArtists->bindValue('user_uuid', $this->getPivotUUID(), PDO::PARAM_STR);
				$stmtGetArtists->execute();
				
				$_RESPONSE->addData($stmtGetArtists->fetchElements(), 'random');
				
				break;
				
			case 'search':
				$formSearch = $this->buildSearchForm('artists');
				$formSearch->recieveInputs();
				if ($formSearch->checkInputs()) {
					if ($_REQUEST->getParam('searchstring') == NULL) {
						return (NULL);
					}
					$sSearchString = '%'.$_REQUEST->getParam('searchstring').'%';
					if (true) { // search everything
						$stmtSearch = $this->nodeSubject->getSession()->prepareKnown('sbJukebox/jukebox/search/various/byLabel');
						$stmtSearch->bindValue('jukebox_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
						$stmtSearch->bindValue('searchstring', $sSearchString, PDO::PARAM_STR);
						$stmtSearch->bindValue('nodetype', 'sb_jukebox:artist', PDO::PARAM_STR);
						$stmtSearch->bindValue('user_uuid', $this->getPivotUUID(), PDO::PARAM_STR);
						$stmtSearch->execute();
						$_RESPONSE->addData($stmtSearch->fetchElements(), 'searchresult');
					}
				}
				$formSearch->saveDOM();
				$_RESPONSE->addData($formSearch);
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
	}	
	
}


?>