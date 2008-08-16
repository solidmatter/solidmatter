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
class sbView_jukebox_jukebox_tags extends sbJukeboxView {
	
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
				
				$formWeighting = $this->buildForm();
				$formWeighting->saveDOM();
				$_RESPONSE->addData($formWeighting);
				
				$_RESPONSE->addData($this->getWeighting(), 'weighting');
				
				$this->nodeSubject->getBranchTags();
				
				break;
				
			case 'changeWeighting':
				
				$formWeighting = $this->buildForm();
				$formWeighting->recieveInputs();
				if ($formWeighting->checkInputs()) {
					$sJukeboxUUID = $this->getJukebox()->getProperty('jcr:uuid');
					sbSession::$aData['sbJukebox'][$sJukeboxUUID]['weighting'] = $formWeighting->getValue('target');
					$_RESPONSE->redirect('-', 'tags');
				} else {
					throw new sbException('form entries are invalid');
				}
				
				break;
				
			case 'listItems':
				
				$iTagID = (int) $_REQUEST->getParam('tagid');
				
				$sTag = $this->nodeSubject->getTag($iTagID);
				if (!$sTag) {
					throw new RepositoryException('no tag exists with id "'.$iTagID.'"');	
				}
				$_RESPONSE->addData($sTag, 'currentTag');
				
				if ($this->checkTimeout($iTagID)) {
					$this->nodeSubject->increaseTagPopularity($iTagID);
				}
				
				$aCategories = array(
					'artists' => array(
						'nodetype' => 'sb_jukebox:artist',
						'resultset' => 'taggedArtists',
					),
					'albums' => array(
						'nodetype' => 'sb_jukebox:album',
						'resultset' => 'taggedAlbums',
					),
					'tracks' => array(
						'nodetype' => 'sb_jukebox:track',
						'resultset' => 'taggedTracks',	
					),
				);
				
				$stmtGetItems = $this->crSession->prepareKnown('sbSystem/tagging/getItems/byTagID/byNodetype');
				
				if ($_REQUEST->getParam('expand') != NULL) {
					
					$sCategory = $_REQUEST->getParam('expand');
					
					if (!isset($aCategories[$sCategory])) {
						throw new ParameterException('unknown expand type "'.$sCategory.'"');
					}
					
					$stmtGetItems->bindValue('tag_id', $iTagID, PDO::PARAM_INT);
					$stmtGetItems->bindValue('root_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
					$stmtGetItems->bindValue('limit', 300, PDO::PARAM_INT);
					$stmtGetItems->bindValue('nodetype', $aCategories[$sCategory]['nodetype'], PDO::PARAM_STR);
					$stmtGetItems->execute();
					$_RESPONSE->addData($stmtGetItems->fetchElements(), $aCategories[$sCategory]['resultset']);
						
				} else {
					
					foreach ($aCategories as $aCategory) {
						$stmtGetItems->bindValue('tag_id', $iTagID, PDO::PARAM_INT);
						$stmtGetItems->bindValue('root_uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
						$stmtGetItems->bindValue('limit', 10, PDO::PARAM_INT);
						$stmtGetItems->bindValue('nodetype', $aCategory['nodetype'], PDO::PARAM_STR);
						$stmtGetItems->execute();
						$_RESPONSE->addData($stmtGetItems->fetchElements(), $aCategory['resultset']);
					}	
					
				}
				
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
	protected function buildForm() {
		
		$formWeighting = new sbDOMForm(
			'changeWeighting',
			'',
			System::getURL('-', 'tags', 'changeWeighting'),
			$this->crSession
		);
		
		$aOptions['numItems'] = 'Number of Items';
		$aOptions['popularity'] = 'Popularity';
		//$aOptions['numItems'] = 'Number of Items';
			
		$formWeighting->addInput('target;select;', '$locale/system/general/labels/weighting/title');
		$formWeighting->setOptions('target', $aOptions);
		$formWeighting->addSubmit('$locale/system/general/actions/apply');
		
		$formWeighting->setValue('target', $this->getWeighting());
		
		return ($formWeighting);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getWeighting() {
		
		$sJukeboxUUID = $this->getJukebox()->getProperty('jcr:uuid');
		if (!isset(sbSession::$aData['sbJukebox'][$sJukeboxUUID]['weighting'])) {
			$sDefault = Registry::getValue('sb.jukebox.tags.weighting.default');
			sbSession::$aData['sbJukebox'][$sJukeboxUUID]['weighting'] = $sDefault;
		}
		return (sbSession::$aData['sbJukebox'][$sJukeboxUUID]['weighting']);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function checkTimeout($iTagID) {
		
		$iTimeout = Registry::getValue('sb.jukebox.tags.popularity.timeout');
		$sJukeboxUUID = $this->getJukebox()->getProperty('jcr:uuid');
		
		if (!isset(sbSession::$aData['sbJukebox'][$sJukeboxUUID]['tags'][$iTagID])) {
			sbSession::$aData['sbJukebox'][$sJukeboxUUID]['tags'][$iTagID] = time();
		} else {
			if (time() - sbSession::$aData['sbJukebox'][$sJukeboxUUID]['tags'][$iTagID] > $iTimeout) {
				sbSession::$aData['sbJukebox'][$sJukeboxUUID]['tags'][$iTagID] = time();
			} else {
				// timout is set and not expired
				return (FALSE);	
			}
		}
		// timout is ok in one of the possible ways
		return (TRUE);
		
	}
	
	
}

?>