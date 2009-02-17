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
class sbView_jukebox_jukebox_albums extends sbJukeboxView {
	
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
				
				$formSearch = $this->buildSearchForm('albums');
				$formSearch->saveDOM();
				$_RESPONSE->addData($formSearch);
				
				$sRestrict = $_REQUEST->getParam('show');
				if ($sRestrict != NULL) {
					if ($sRestrict == '0-9') {
						$stmtGetAlbums = $this->nodeSubject->getSession()->prepareKnown('sbJukebox/jukebox/search/albums/numeric');
					} else {
						$stmtGetAlbums = $this->nodeSubject->getSession()->prepareKnown('sbJukebox/jukebox/search/albums/byLabel');
						$stmtGetAlbums->bindValue('searchstring', $sRestrict.'%', PDO::PARAM_STR);
					}
				} else {
					$stmtGetAlbums = $this->crSession->prepareKnown('sbJukebox/jukebox/albums/getRandom');
					$stmtGetAlbums->bindValue('limit', 10, PDO::PARAM_INT);
				}
				$stmtGetAlbums->bindValue('jukebox_mpath', $this->nodeSubject->getMPath(), PDO::PARAM_STR);
				$stmtGetAlbums->bindValue('user_uuid', $this->getPivotUUID(), PDO::PARAM_STR);
				$stmtGetAlbums->execute();
				
				$_RESPONSE->addData($stmtGetAlbums->fetchElements('albums'));
				
				break;
				
			case 'search':
				$formSearch = $this->buildSearchForm('albums');
				$formSearch->recieveInputs();
				if ($formSearch->checkInputs()) {
					if ($_REQUEST->getParam('searchstring') == NULL) {
						return (NULL);
					}
					$sSearchString = '%'.$_REQUEST->getParam('searchstring').'%';
					if (true) { // search everything
						$stmtSearch = $this->nodeSubject->getSession()->prepareKnown('sbJukebox/jukebox/search/albums/byLabel');
						$stmtSearch->bindValue('jukebox_mpath', $this->nodeSubject->getMPath(), PDO::PARAM_STR);
						$stmtSearch->bindValue('searchstring', $sSearchString, PDO::PARAM_STR);
						$stmtSearch->bindValue('user_uuid', $this->getPivotUUID(), PDO::PARAM_STR);
						$stmtSearch->execute();
						$_RESPONSE->addData($stmtSearch->fetchElements('albums'));
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