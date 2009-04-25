<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/** This view manages the tags stored in the current repository.
*/
class sbView_tags_manage extends sbView {
	
	//--------------------------------------------------------------------------
	/**
	* Executes the given action.
	* @param string the action 
	* @return multiple data added to response if necessary
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			// list all tags
			case 'list':
				$stmtGetTags = $this->crSession->prepareKnown('sbSystem/tagging/tags/getAllTags/orderByTag');
				$stmtGetTags->execute();
				$_RESPONSE->addData($stmtGetTags->fetchElements('tags'));
				return;
			
			// display form to edit tag details
			case 'edit':
				$iTagID = $_REQUEST->getParam('tagid');
				$formTag = $this->buildForm($iTagID);
				$formTag->saveDOM();
				$_RESPONSE->addData($formTag);
				break;
			
			// save changes
			case 'save':
				$iTagID = $_REQUEST->getParam('tagid');
				$formTag = $this->buildForm($iTagID);
				$formTag->recieveInputs();
				
				if ($formTag->checkInputs()) {
					
					$aData = $formTag->getValues();
					
					$stmtWriteData = $this->crSession->prepareKnown('sbSystem/tagging/tags/updateTag');
					$stmtWriteData->bindValue('tag_id',			$iTagID,				PDO::PARAM_INT);
					$stmtWriteData->bindValue('tag',			$aData['tag'],			PDO::PARAM_STR);
					$stmtWriteData->bindValue('popularity',		$aData['popularity'],	PDO::PARAM_INT);
					$stmtWriteData->bindValue('customweight',	$aData['customweight'],	PDO::PARAM_INT);
					$stmtWriteData->bindValue('visibility',		$aData['visibility'],	PDO::PARAM_STR);
					$stmtWriteData->execute();
					
					$formTag->saveDOM();
					$_RESPONSE->addData($formTag);
					
					return (NULL);
					
				} else {
					
					$formTag->saveDOM();
					$_RESPONSE->addData($formTag);
					return (NULL);
					
				}
			
			// remove currently unused tags from database
			case 'clearUnused':
				$stmtGetTags = $this->crSession->prepareKnown('sbSystem/tagging/clearUnusedTags');
				$stmtGetTags->execute();
				$_RESPONSE->redirect($this->nodeSubject->getIdentifier());
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
	protected function buildForm($iTagID) {
		
		$formTag = new sbDOMForm(
			'edit_tag',
			'$locale/sbSystem/tags/edit_tag',
			System::getRequestURL($this->nodeSubject->getProperty('jcr:uuid'), 'manage', 'save', array('tagid' => $iTagID)),
			$this->crSession
		);
		$formTag->addInput('tag;string;minlength=1;maxlength=100', '$locale/sbSystem/tags/tag');
		$formTag->addInput('popularity;integer;minvalue=0;maxvalue=1000000000', '$locale/sbSystem/tags/popularity');
		$formTag->addInput('customweight;integer;minvalue=0;maxvalue=1000000000', '$locale/sbSystem/tags/customweight');
		$formTag->addInput('visibility;select;', '$locale/sbSystem/tags/visibility');
		$aOptions = array(
			'VISIBLE' => 'VISIBLE',
			'HIDDEN' => 'HIDDEN',
		);
		$formTag->setOptions('visibility', $aOptions);
		$formTag->addSubmit('$locale/sbSystem/actions/save');
		
		$stmtGetData = $this->crSession->prepareKnown('sbSystem/tagging/tags/getTagData');
		$stmtGetData->bindValue('tag_id', $iTagID, PDO::PARAM_INT);
		$stmtGetData->execute();
		foreach ($stmtGetData as $aRow) {
			
			$formTag->setValue('tag', $aRow['s_tag']);
			$formTag->setValue('popularity', $aRow['n_popularity']);
			$formTag->setValue('customweight', $aRow['n_customweight']);
			$formTag->setValue('visibility', $aRow['e_visibility']);
			
			$stmtGetData->closeCursor();
			return ($formTag);
			
		}
		
		throw new sbException('unknown tag id: '.$iTagID);

	}
	
}


?>