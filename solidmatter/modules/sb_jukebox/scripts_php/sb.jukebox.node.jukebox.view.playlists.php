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
class sbView_jukebox_jukebox_playlists extends sbJukeboxView {
	
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
				$stmtGetPlaylists = $this->nodeSubject->getSession()->prepareKnown('sbJukebox/jukebox/playlists/getAll');
				$stmtGetPlaylists->bindValue('jukebox_uuid', $this->getJukebox()->getProperty('jcr:uuid'), PDO::PARAM_STR);
				$stmtGetPlaylists->bindValue('user_uuid', $this->getPivotUUID(), PDO::PARAM_STR);
				$stmtGetPlaylists->execute();
				$_RESPONSE->addData($stmtGetPlaylists->fetchElements('playlists'));
				// add existing playlists
				/*$niPlaylists = $this->nodeSubject->loadChildren('playlists', TRUE, TRUE, TRUE);
				foreach ($niPlaylists as $nodePlaylist) {
					$nodePlaylist->getVote($this->getPivotUUID());
				}
				$this->nodeSubject->storeChildren();*/
				break;
				
			case 'create':
				$nodeParent = $this->getJukebox();
				$_RESPONSE->addData($nodeParent, 'parent');
				$nodeChild = $nodeParent->addNode('temp', 'sbJukebox:Playlist');
				$formCreate = $nodeChild->buildForm('create', $_REQUEST->getParam('parentnode'));
				$formCreate->setAction(System::getRequestURL('-', 'playlists', 'create'));
				$formCreate->recieveInputs();
				if ($_REQUEST->getParam('submit') != NULL && $formCreate->checkInputs()) {
					$aValues = $formCreate->getValues();
					foreach ($aValues as $sName => $mValue) {
						$nodeChild->setProperty($sName, $mValue);
					}
					$nodeParent->save();
					// TODO: integrate authorisation changes into node's save() method
					// TODO: since read auth incorporates download auth playlists can be used to bypass download restrictions
					$nodeChild->setAuthorisation('read');
					$nodeChild->setAuthorisation('write');
					$_RESPONSE->redirect('-', 'playlists');
				} else {
					$formCreate->saveDOM();
					$_RESPONSE->addData($formCreate);
				}
				
				
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
	
		}
		
				
	}
	
}

?>