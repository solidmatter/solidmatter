<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbView_logs_events extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
				
				$formFilter = $this->buildFilterForm();
				$formFilter->saveDOM();
				$_RESPONSE->addData($formFilter);
				
				$stmtGetEvents = $this->crSession->prepareKnown('sbSystem/eventLog/getEntries/filtered');
				$stmtGetEvents->bindValue('module', '%', PDO::PARAM_STR);
				$stmtGetEvents->bindValue('type', '%', PDO::PARAM_STR);
				$stmtGetEvents->execute();
				$_RESPONSE->addData($stmtGetEvents->fetchElements('events'));
				
				break;
				
			case 'filter':
				
				$formFilter = $this->buildFilterForm();
				$formFilter->recieveInputs();
				$aInputs = $formFilter->getValues();
				
				$stmtGetEvents = $this->crSession->prepareKnown('sbSystem/eventLog/getEntries/filtered');
				$stmtGetEvents->bindValue('module', '%'.$aInputs['module'].'%', PDO::PARAM_STR);
				$stmtGetEvents->bindValue('type', '%'.$aInputs['type'].'%', PDO::PARAM_STR);
				$stmtGetEvents->execute();
				$_RESPONSE->addData($stmtGetEvents->fetchElements('events'));
				
				$formFilter->saveDOM();
				$_RESPONSE->addData($formFilter);
				
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
	}
	
	private function buildFilterForm() {
		
		$formFilter = new sbDOMForm(
			'filter_events',
			'$locale/sbSystem/labels/filter',
			System::getRequestURL($this->nodeSubject, 'events', 'filter'),
			$this->crSession
		);
		
		$formFilter->addInput('type;select;', '$locale/sbSystem/labels/type');
		$formFilter->addInput('module;select;', '$locale/sbSystem/labels/module');
		$formFilter->addSubmit('$locale/sbSystem/actions/filter');
		
		$aOptions = array(
			'' => '',
			'SECURITY' => 'SECURITY',
			'ERROR' => 'ERROR',
			'WARNING' => 'WARNING',
			'MAINTENANCE' => 'MAINTENANCE',
			'INFO' => 'INFO',
			'DEBUG' => 'DEBUG',
		);
		$formFilter->setOptions('type', $aOptions);
		
		$aOptions = array('' => '');
		
		foreach ($this->crSession->getNode('//*[@uid="sbSystem:Modules"]')->getNodes() as $nodeModule) {
			$aOptions[$nodeModule->getProperty('label')] = $nodeModule->getProperty('label');
		}
		$formFilter->setOptions('module', $aOptions);
		
		return ($formFilter);
		
	}
	
}

?>