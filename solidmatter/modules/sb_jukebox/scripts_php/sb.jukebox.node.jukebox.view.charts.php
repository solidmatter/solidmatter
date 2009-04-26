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
	
	protected $aRequiredAuthorisations = array(
		'display' => array('read'),
		'changePivot' => array('read'),
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
				
				$formPivot = $this->buildForm();
				$formPivot->saveDOM();
				$_RESPONSE->addData($formPivot);
				
				$aCategories = array(
					'artists' => array(
						'nodetype' => 'sbJukebox:Artist',
						'resultset' => 'topArtists',
						'query' => 'getTop',
					),
					'albums' => array(
						'nodetype' => 'sbJukebox:Album',
						'resultset' => 'topAlbums',
						'query' => 'getTop',
					),
					'tracks' => array(
						'nodetype' => 'sbJukebox:Track',
						'resultset' => 'topTracks',
						'query' => 'getTop',
					),
					'mostPlayed' => array(
						'nodetype' => 'sbJukebox:Track',
						'resultset' => 'mostPlayed',
						'query' => 'getMostPlayed',
					),
				);
				
				$stmtGetTopVoted = $this->crSession->prepareKnown('sbJukebox/jukebox/various/getTop');
				
				foreach ($aCategories as $sCategory => $aCategory) {
					
					if ($_REQUEST->getParam('expand') != NULL) {
						if (!isset($aCategories[$_REQUEST->getParam('expand')])) {
							throw new ParameterException('unknown expand type "'.$sCategory.'"');
						}
						if ($sCategory != $_REQUEST->getParam('expand')) {
							continue;
						}
						$iLimit = Registry::getValue('sb.jukebox.charts.amount.expanded');
					} else {
						$iLimit = Registry::getValue('sb.jukebox.charts.amount.default');
					}
					
					if ($aCategory['query'] == 'getTop') {
						$stmtGetTop = $stmtGetTopVoted;
						$stmtGetTop->bindValue('user_uuid', $this->getPivotUUID(), PDO::PARAM_STR);
						$stmtGetTop->bindValue('nodetype', $aCategory['nodetype'], PDO::PARAM_STR);
					} else {
						if ($this->getPivotUUID() == $this->crSession->getRootNode()->getProperty('jcr:uuid')) {
							$stmtGetTop = $this->crSession->prepareKnown('sbJukebox/history/getTop/allUsers');
						} else {
							$stmtGetTop = $this->crSession->prepareKnown('sbJukebox/history/getTop/byUser');
							$stmtGetTop->bindValue('user_uuid', $this->getPivotUUID(), PDO::PARAM_STR);
						}
						$iTimeframe = 60*60*24*7; // TODO: make this customizable (even on session scope?)
						$stmtGetTop->bindValue('timeframe', $iTimeframe, PDO::PARAM_INT);
					}
					
					$stmtGetTop->bindValue('jukebox_mpath', $this->getJukebox()->getMPath(), PDO::PARAM_STR);
					$stmtGetTop->bindValue('limit', (int) $iLimit, PDO::PARAM_INT);
					$stmtGetTop->execute();
					$_RESPONSE->addData($stmtGetTop->fetchElements(), $aCategory['resultset']);
				}
				
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
			System::getRequestURL('-', 'charts', 'changePivot'),
			$this->crSession
		);
		
		$stmtVoters = $this->crSession->prepareKnown('sbJukebox/jukebox/getVoters');
		$stmtVoters->bindValue(':jukebox_mpath', $this->getJukebox()->getMPath(), PDO::PARAM_STR);
		$stmtVoters->execute();
		
		$sRootUUID = $this->crSession->getRootNode()->getProperty('jcr:uuid');
		
		$aOptions[$sRootUUID] = '$locale/sbJukebox/labels/average';
		foreach ($stmtVoters as $aRow) {
			if ($aRow['fk_nodetype'] != 'sbSystem:Root') {
				$aOptions[$aRow['uuid']] = $aRow['label'];
			}
		}
		
		$formPivot->addInput('target;select;', '$locale/sbSystem/labels/search/title');
		$formPivot->setOptions('target', $aOptions);
		$formPivot->addSubmit('$locale/sbSystem/actions/apply');
		
		$sJukeboxUUID = $this->getJukebox()->getProperty('jcr:uuid');
		$formPivot->setValue('target', $this->getPivotUUID());
		
		return ($formPivot);
		
	}
	
	
}

?>