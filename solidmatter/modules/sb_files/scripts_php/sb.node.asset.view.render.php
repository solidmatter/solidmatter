<?php

//import('sb.node.view');

class sbView_asset_render extends sbView {
	
	protected $bLoginRequired = FALSE;
	
	public function execute($sAction) {
		
		switch ($sAction) {
			
			case 'onthefly':
				
				$sMimetype = $this->nodeSubject->getProperty('properties_mimetype');
				if ($sMimetype != NULL) {
					header('Content-type: '.$sMimetype);
				}
				
				$sData = $this->nodeSubject->loadBinaryProperty('properties_content', TRUE);
				echo $sData;
				exit();
				
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
		return ($this->nodeSubject);
		
	}
	
}


?>