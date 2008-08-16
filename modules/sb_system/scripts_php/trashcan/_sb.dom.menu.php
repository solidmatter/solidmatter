<?php

import('sb.dom.document');
import('sb.node.root');

class sbDOMMenu extends sbDOMDocument {
	
	private $sView = 'menu';
	
	//--------------------------------------------------------------------------
	public function __construct($iSubjectNodeID = 0) {
		
		DOMDocument::__construct();
		
		if ($iSubjectNodeID == 0) {
			$elemRoot = $this->createElement('sbnode');
			$elemRoot->setAttribute('type', 'root');
			$this->loadChildren($elemRoot, $this->sView);
		} else {
			$elemRoot = NodeFactory::getInstance($iSubjectNodeID);
			$elemRoot->loadChildren('menu');
		}
		//echo $elemRoot->ownerDocument->saveXML($elemRoot);
		//echo $elemRoot->hasChildNodes();
		
		foreach($elemRoot->childNodes as $elemChild) {
			$this->expand($elemChild);
		}
		
		$this->AppendChild($elemRoot);
		
		
	}
	
	private function expand($elemCurrent) {
		global $_SBSESSION;
		if (isset($_SBSESSION->aData['menu']['expanded'][$elemCurrent->getAttribute('nodeid')])) {
			$this->loadChildren($elemCurrent, $this->sView);
			foreach($elemCurrent->childNodes as $elemChild) {
				$this->expand($elemChild);
			}
		}
		
	}
		
	
}



?>
