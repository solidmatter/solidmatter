<?php

import('sb.node.view');

class sbView_imagefilterstack_preview extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
				break;
				
			case 'outputoriginal':
				
				$imgExample = new Image(Image::FROMFILE, 'modules/sb_system/data/testimage_2.png');
				$imgExample->output(PNG);
				
				break;
			
			case 'outputprocessed':
				
				$imgExample = new Image(Image::FROMFILE, 'modules/sb_system/data/testimage_2.png');
				$this->nodeSubject->applyToImage($imgExample);
				$imgExample->output(PNG);
				
				break;
			
			
		}
		
		return ($this->nodeSubject);
		
	}
	
}


?>