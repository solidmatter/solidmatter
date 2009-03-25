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
class sbView_portal_portal_contents extends sbView {
	
	protected $bLoginRequired = FALSE;
	
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
				
				if ($_REQUEST->getHandler() == 'portal') {
					$nodeLeaf = $this->getLeaf($this->nodeSubject);
//					if (!$nodeLeaf->isSame($this->nodeSubject)) {
//						$_RESPONSE->redirect($nodeLeaf->getProperty('jcr:uuid'));
//						return;
//					}
				} else {
					$nodeLeaf = $this->nodeSubject;	
				}
				
				$elemCurrent = $_RESPONSE->createElement('node');
				$elemCurrent->setAttribute('label', $nodeLeaf->getProperty('label'));
				$elemCurrent->setAttribute('uuid', $nodeLeaf->getProperty('jcr:uuid'));
				$elemCurrent->setAttribute('nodetype', $nodeLeaf->getPrimaryNodeType());
				$elemCurrent->setAttribute('state', 'current');
				
				$niChildren = $nodeLeaf->loadChildren('menu', TRUE, TRUE, FALSE, TRUE);
				foreach ($niChildren as $nodeChild) {
					$elemChild = $_RESPONSE->createElement('node');
					$elemChild->setAttribute('label', $nodeChild->getProperty('label'));
					$elemChild->setAttribute('uuid', $nodeChild->getProperty('jcr:uuid'));
					$elemChild->setAttribute('nodetype', $nodeChild->getPrimaryNodeType());
					$elemChild->setAttribute('state', 'child');
					$elemCurrent->appendChild($elemChild);
				}
				
				$niAncestors = $nodeLeaf->getAncestors();
//				$niAncestors->reverse();
				foreach ($niAncestors as $nodeAncestor) {
					if ($nodeAncestor->getPrimaryNodeType() == 'sbSystem:Root') {
						continue;	
					}
					$elemAncestor = $_RESPONSE->createElement('node');
					$elemAncestor->setAttribute('label', $nodeAncestor->getProperty('label'));
					$elemAncestor->setAttribute('uuid', $nodeAncestor->getProperty('jcr:uuid'));
					$elemAncestor->setAttribute('nodetype', $nodeAncestor->getPrimaryNodeType());
					$elemAncestor->setAttribute('state', 'ancestor');
					$niChildren = $nodeAncestor->loadChildren('menu', TRUE, TRUE, FALSE, TRUE);
					foreach ($niChildren as $nodeChild) {
//						echo $nodeChild->getName().'|';
						if ($nodeChild->getProperty('jcr:uuid') == $elemCurrent->getAttribute('uuid')) {
							$elemChild = $elemCurrent;
						} else {
							$elemChild = $_RESPONSE->createElement('node');
							$elemChild->setAttribute('label', $nodeChild->getProperty('label'));
							$elemChild->setAttribute('uuid', $nodeChild->getProperty('jcr:uuid'));
							$elemChild->setAttribute('nodetype', $nodeChild->getPrimaryNodeType());
							$elemChild->setAttribute('state', 'ancestorchild');
						}
						$elemAncestor->appendChild($elemChild);
					}
					//$elemAncestor->appendChild($elemCurrent);
					$elemCurrent = $elemAncestor;
				}
				
				$_RESPONSE->addData($elemCurrent, 'menu');
				
				$niContent = $nodeLeaf->getChildren('content');
				$_RESPONSE->addData($niContent, 'pagecontent'); 
				
				
				
//				$niAncestors = $this->nodeSubject->getAncestors();
//				foreach ($niAncestors as $nodeAncestor) {
//					$this->expand($nodeAncestor);
//				}
//				$this->expand($this->nodeSubject);
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
	}
	
	protected function expand($nodeCurrent) {
		$niChildren = $nodeCurrent->loadChildren('menu', FALSE, TRUE, FALSE, TRUE);
		if (count($niChildren) > 0) {
//			var_dumppp($niChildren);
			$bFirst = FALSE;
			foreach ($niChildren as $nodeChild) {
				if (!$bFirst) {
					$this->expand($nodeChild);
					$bFirst = TRUE;
				}
			}
		}
		$nodeCurrent->storeChildren();
	}
	
	protected function getLeaf($nodeCurrent) {
		if ($nodeCurrent->getNumberOfChildren('menu') > 0) { 
			$niChildren = $nodeCurrent->loadChildren('menu', FALSE, TRUE, FALSE, TRUE);
			if ($niChildren->getSize() > 0) {
				foreach ($niChildren as $nodeChild) {
	//				echo $nodeChild->getName().'|';
					return ($this->getLeaf($nodeChild));
				}
			} else {
	//			echo $nodeCurrent->getName().'!!!|';
				return ($nodeCurrent);
			}
		} else {
			return ($nodeCurrent);
		}
	}
	
}

?>