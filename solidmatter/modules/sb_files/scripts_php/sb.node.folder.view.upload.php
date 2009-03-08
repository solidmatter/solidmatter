<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbFiles]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbView_folder_upload extends sbView {
	
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
				
				$formUpload = $this->buildUploadForm();
				$formUpload->saveDOM();
				
				$_RESPONSE->addData($formUpload);
				
				return (NULL);
				
			case 'send':
				
				if (!isset($_FILES['files'])) {
					throw new sbException(__CLASS__.': no files submitted');
				}
					
				foreach ($_FILES['files'] as $aFileInfo) {
					// full file path/name is expected under 'file' array entry
					$aFileInfo['file'] = $aFileInfo['tmp_name'];
					$aFileInfo['name_imported'] = str2urlsafe($aFileInfo['tmp_name']);
					$this->nodeSubject->addFile($aFileInfo);
				}
				
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'), 'upload');
				
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
	private function buildUploadForm() {
		
		$formUpload = new sbDOMForm(
			'upload',
			'$locale/sbSystem/labels/upload_files',
			'/'.$this->nodeSubject->getProperty('jcr:uuid').'/upload/send',
			$this->crSession
		);
		$formUpload->addInput('files;multifileupload;maxfiles=20', '$locale/sbSystem/labels/files');
		$formUpload->addSubmit('$locale/sbSystem/actions/upload');
		
		//$this->extendForm($formUpload);
		
		return ($formUpload);
		
	}
	
}


?>