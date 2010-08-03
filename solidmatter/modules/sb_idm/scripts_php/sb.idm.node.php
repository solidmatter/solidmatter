<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sbJukebox:sb.pdo.queries');

//------------------------------------------------------------------------------
/**
*/
class sbJukeboxNode extends sbNode {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function fillArtists($formCurrent, $nodeJukebox) {
		
		$stmtGetAllArtists = $this->crSession->prepareKnown('sbJukebox/jukebox/artists/getAll');
		$stmtGetAllArtists->bindValue('jukebox_uuid', $nodeJukebox->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtGetAllArtists->execute();
		$aArtists = array();
		foreach ($stmtGetAllArtists as $aRow) {
			$aArtists[$aRow['uuid']] = $aRow['label'];
		}
		$stmtGetAllArtists->closeCursor();
		
		$formCurrent->setOptions('info_artist', $aArtists);
		
	}
	
}

?>