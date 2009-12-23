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
				
				// add existing playlists
				$stmtGetPlaylists = $this->nodeSubject->getSession()->prepareKnown('sbJukebox/jukebox/playlists/getAll');
				$stmtGetPlaylists->bindValue('jukebox_uuid', $this->getJukebox()->getProperty('jcr:uuid'), PDO::PARAM_STR);
				$stmtGetPlaylists->bindValue('user_uuid', $this->getPivotUUID(), PDO::PARAM_STR);
				$stmtGetPlaylists->execute();
				$_RESPONSE->addData($stmtGetPlaylists->fetchElements('playlists'));
				
				// add form for new playlist
				$formCreate = $this->buildCreateForm();
				$formCreate->saveDOM();
				$_RESPONSE->addData($formCreate);
				
				break;
				
			case 'create':
				import('sb.tools.strings.conversion');
				$formCreate = $this->buildCreateForm();
				$formCreate->recieveInputs();
				if (!$formCreate->checkInputs()) {
					// TODO: getError only returns the locale path, not the actual text
					throw new sbException($formCreate->getError('playlist'));
				}
				$aValues = $formCreate->getValues();
				$nodePlaylist = $this->nodeSubject->addNode(str2urlsafe($aValues['playlist']), 'sbJukebox:Playlist');
				$nodePlaylist->setProperty('label', $aValues['playlist']);
				$this->nodeSubject->save();
				// TODO: integrate authorisation changes into node's save() method
				// TODO: since read auth incorporates download auth playlists can be used to bypass download restrictions
				$nodePlaylist->setAuthorisation('read');
				$nodePlaylist->setAuthorisation('write');
				$_RESPONSE->redirect('-', 'playlists');
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
	
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function buildCreateForm() {
		
		$formCreate = new sbDOMForm(
			'newPlaylist',
			NULL,
			System::getRequestURL($this->nodeSubject, 'playlists', 'create'),
			$this->crSession
		);
		
		$formCreate->addInput('playlist;string;minlength=3;maxlength=60;required=true;');
		$formCreate->addSubmit('$locale/sbSystem/actions/save');
		
		return ($formCreate);
		
	}
	
}

?>