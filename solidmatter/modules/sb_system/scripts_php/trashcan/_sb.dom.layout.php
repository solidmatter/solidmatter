<?php

class sbDOMLayout extends sbDOMDocument {
	
	public function process() {
		
		$sXPath = '//sb:embed';	
		$xpQuery = new DOMXPath($this);
		$nlSupertokens = $xpQuery->query($sXPath);
		
		foreach ($nlSupertokens as $nodeCurrent) {
			echo ($nodeCurrent->__toString());	
			
		}
		
		
	}
	
		
	
	
} 




?>