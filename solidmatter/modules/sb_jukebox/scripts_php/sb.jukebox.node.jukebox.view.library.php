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
class sbView_jukebox_jukebox_library extends sbJukeboxView {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'info':
				
				$formSearch = $this->buildSearchForm('jukebox');
				$formSearch->saveDOM();
				$_RESPONSE->addData($formSearch);
				
				$stmtGetLatest = $this->crSession->prepareKnown('sbJukebox/jukebox/albums/getLatest');
				$stmtGetLatest->bindValue('jukebox_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
				$stmtGetLatest->bindValue('limit', 16, PDO::PARAM_INT);
				$stmtGetLatest->bindValue('nodetype', 'sb_jukebox:album', PDO::PARAM_STR);
				$stmtGetLatest->bindValue('user_uuid', $this->getPivotUUID(), PDO::PARAM_STR);
				$stmtGetLatest->execute();
				
				$_RESPONSE->addData($stmtGetLatest->fetchElements(), 'latestAlbums');
				
				break;
				
			case 'search':
				$formSearch = $this->buildSearchForm('jukebox');
				$formSearch->recieveInputs();
				if ($formSearch->checkInputs()) {
					if ($_REQUEST->getParam('searchstring') == NULL) {
						return (NULL);
					}
					$sSearchString = '%'.$_REQUEST->getParam('searchstring').'%';
					if (true) { // search everything
						$stmtSearch = $this->nodeSubject->getSession()->prepareKnown('sbJukebox/jukebox/search/anything/byLabel');
						$stmtSearch->bindValue('jukebox_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
						$stmtSearch->bindValue('searchstring', $sSearchString, PDO::PARAM_STR);
						$stmtSearch->execute();
						$_RESPONSE->addData($stmtSearch->fetchElements(), 'searchresult');
					}
				}
				$formSearch->saveDOM();
				$_RESPONSE->addData($formSearch);
				break;
			
			case 'displayCoverWall':
				// get all albums and store in response 
				$stmtGetAlbums = $this->nodeSubject->getSession()->prepareKnown('sbJukebox/jukebox/albums/getAll');
				$stmtGetAlbums->bindValue('jukebox_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
				$stmtGetAlbums->execute();
				$_RESPONSE->addData($stmtGetAlbums->fetchElements(), 'allAlbums');
				// add default search form
				$formSearch = $this->buildSearchForm('jukebox');
				$formSearch->saveDOM();
				$_RESPONSE->addData($formSearch);
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
	}
	
}

?>