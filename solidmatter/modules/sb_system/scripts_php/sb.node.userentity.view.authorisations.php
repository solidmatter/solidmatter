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
class sbView_userentity_authorisations extends sbView {
	
	private $aQueries = array();
	
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
				$stmtGetAuthorisations = $this->nodeSubject->getSession()->prepareKnown('sb_system/userentity/getAuthorisations');
				$stmtGetAuthorisations->bindValue('uuid', $this->nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
				$stmtGetAuthorisations->execute();
				$aNodeUUIDs = $stmtGetAuthorisations->fetchAll();
				$elemContainer = $_RESPONSE->createElement('authorisations');
				foreach ($aNodeUUIDs as $aRow) {
					$nodeCurrent = $this->nodeSubject->getSession()->getInstance($aRow['fk_subject']);
					$elemCurrent = $nodeCurrent->getElement();
					$elemCurrent->setAttribute('path', $nodeCurrent->getPath('label'));
					$elemCurrent->setAttribute('granttype', $aRow['e_granttype']);
					$elemCurrent->setAttribute('authorisation', $aRow['fk_authorisation']);
					$elemContainer->appendChild($elemCurrent);
				}
				$_RESPONSE->addData($elemContainer);
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
	}
	
}

?>