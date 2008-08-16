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
class sbView_jukebox_jukebox_charts extends sbJukeboxView {
	
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
				
				$formPivot = $this->buildForm();
				$formPivot->saveDOM();
				$_RESPONSE->addData($formPivot);
				
				$aCategories = array(
					'artists' => array(
						'nodetype' => 'sb_jukebox:artist',
						'resultset' => 'topArtists',
					),
					'albums' => array(
						'nodetype' => 'sb_jukebox:album',
						'resultset' => 'topAlbums',
					),
					'tracks' => array(
						'nodetype' => 'sb_jukebox:track',
						'resultset' => 'topTracks',	
					),
				);
				
				$stmtGetTop = $this->crSession->prepareKnown('sbJukebox/jukebox/various/getTop');
				
				if ($_REQUEST->getParam('expand') != NULL) {
					
					$sCategory = $_REQUEST->getParam('expand');
					
					if (!isset($aCategories[$sCategory])) {
						throw new ParameterException('unknown expand type "'.$sCategory.'"');
					}
					
					$stmtGetTop->bindValue('jukebox_uuid', $this->getJukebox()->getProperty('jcr:uuid'), PDO::PARAM_STR);
					$stmtGetTop->bindValue('limit', 100, PDO::PARAM_INT);
					$stmtGetTop->bindValue('nodetype', $aCategories[$sCategory]['nodetype'], PDO::PARAM_STR);
					$stmtGetTop->bindValue('user_uuid', $this->getPivotUUID(), PDO::PARAM_STR);
					$stmtGetTop->execute();
					$_RESPONSE->addData($stmtGetTop->fetchElements(), $aCategories[$sCategory]['resultset']);
						
				} else {
					
					foreach ($aCategories as $aCategory) {
						$stmtGetTop->bindValue('jukebox_uuid', $this->getJukebox()->getProperty('jcr:uuid'), PDO::PARAM_STR);
						$stmtGetTop->bindValue('limit', 10, PDO::PARAM_INT);
						$stmtGetTop->bindValue('nodetype', $aCategory['nodetype'], PDO::PARAM_STR);
						$stmtGetTop->bindValue('user_uuid', $this->getPivotUUID(), PDO::PARAM_STR);
						$stmtGetTop->execute();
						$_RESPONSE->addData($stmtGetTop->fetchElements(), $aCategory['resultset']);
					}	
					
				}
				
				//var_dumpp(sbSession::$aData); die();
				
				break;
			
			case 'changePivot':
				
				$formPivot = $this->buildForm();
				$formPivot->recieveInputs();
				if ($formPivot->checkInputs()) {
					
					$sJukeboxUUID = $this->getJukebox()->getProperty('jcr:uuid');
					$sTargetUUID = $formPivot->getValue('target');
					
					if ($sTargetUUID == 'average') {
						$nodeRoot = $this->crSession->getRootNode();
						sbSession::$aData['sbJukebox'][$sJukeboxUUID]['pivot'] = $nodeRoot->getProperty('jcr:uuid');
					} else {
						sbSession::$aData['sbJukebox'][$sJukeboxUUID]['pivot'] = $sTargetUUID;
					}
					
					$_RESPONSE->redirect('-', 'charts');
						
				} else {
					
					throw new sbException('form entries are invalid');
					
				}
				
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
	}
	
	
	protected function buildForm() {
		
		$formPivot = new sbDOMForm(
			'changePivot',
			'',
			System::getURL('-', 'charts', 'changePivot'),
			$this->crSession
		);
		
		$stmtVoters = $this->crSession->prepareKnown('sbJukebox/jukebox/getVoters');
		$stmtVoters->bindValue(':jukebox_uuid', $this->getJukebox()->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtVoters->execute();
		
		$sRootUUID = $this->crSession->getRootNode()->getProperty('jcr:uuid');
		
		$aOptions[$sRootUUID] = 'Average';
		foreach ($stmtVoters as $aRow) {
			if ($aRow['fk_nodetype'] != 'sb_system:root') {
				$aOptions[$aRow['uuid']] = $aRow['label'];
			}
		}
		
		$formPivot->addInput('target;select;', '$locale/system/general/labels/search/title');
		$formPivot->setOptions('target', $aOptions);
		$formPivot->addSubmit('$locale/system/general/actions/apply');
		
		$sJukeboxUUID = $this->getJukebox()->getProperty('jcr:uuid');
		$formPivot->setValue('target', $this->getPivotUUID());
		
		return ($formPivot);
		
	}
	
	
}

?>