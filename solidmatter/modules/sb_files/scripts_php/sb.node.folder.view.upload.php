<?php

import('sb.node.view');
import('sb.form');

class sbView_folder_upload extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
				
				$formUpload = $this->buildUploadForm();
				$formUpload->saveDOM();
				
				$_RESPONSE->addData($formUpload);
				
				return (NULL);
				
			case 'send':
				
				import('sb.tools.strings.conversion');
				
				$aMimetypeMapping = $this->getMimetypeMapping();
				
				if (isset($_FILES['files'])) {
					foreach ($_FILES['files'] as $aFile) {
						
						if (isset($aMimetypeMapping[$aFile['type']])) {
							$sNodetype = $aMimetypeMapping[$aFile['type']];
						} else {
							$sNodetype = 'sb_system:asset';
						}
						
						// TODO: behave differently if already existing node is a folder
						//die (str2urlsafe($aFile['name']));
						if ($this->nodeSubject->hasNode(str2urlsafe($aFile['name']))) {
							
							// TODO: implement question what to do first, not just overwrite
							throw new LazyBastardException('node with this name already exists');
														
							$nodeAsset = $this->nodeSubject->getNode(str2urlsafe($aFile['name']));
							$nodeAsset->setProperty('properties_size', $aFile['size']);
							$nodeAsset->setProperty('properties_mimetype', $aFile['type']);
							$fpAsset = fopen($aFile['tmp_name'], 'rb');
							$nodeAsset->saveBinaryProperty('properties_content', $fpAsset);
							fclose($fpAsset);
						} else {
							$nodeNew = $this->nodeSubject->addNode(str2urlsafe($aFile['name']), $sNodetype);
							$nodeNew->setProperty('name', $aFile['name']);
							$nodeNew->setProperty('properties_size', $aFile['size']);
							$nodeNew->setProperty('properties_mimetype', $aFile['type']);
							$this->nodeSubject->save();
							$fpAsset = fopen($aFile['tmp_name'], 'rb');
							$nodeNew->saveBinaryProperty('properties_content', $fpAsset);
							fclose($fpAsset);
						}
					}
				}
				
				$_RESPONSE->redirect($this->nodeSubject->getProperty('jcr:uuid'), 'upload');
				
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
	}
	
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
	
	private function getMimetypeMapping() {
		
		$aMimetypeMapping = array();
		
		$stmtMimetypes = $this->crSession->prepareKnown('sb_system/folder/view/upload/getMimetypeMapping');
		$stmtMimetypes->execute();
		foreach ($stmtMimetypes as $aRow) {
			$aMimetypeMapping[$aRow['s_mimetype']] = $aRow['fk_nodetype'];			
		}
		$stmtMimetypes->closeCursor();
		return ($aMimetypeMapping);
		
	}
	
}


?>