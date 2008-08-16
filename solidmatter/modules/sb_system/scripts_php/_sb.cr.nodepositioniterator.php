<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbCR_NodePositionIterator implements Iterator, ArrayAccess {
	
	private $nodeSubject;
	
	private $aPositions = array();
	private $iPosition = 0;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($nodeSubject) {
		$this->nodeSubject = $nodeSubject;
		$stmtGetInfo = $nodeSubject->getSession()->prepareKnown('sbCR.node.getPositionInfos');
		$stmtGetInfo->bindValue(':node_uuid', $nodeSubject->getProperty('jcr:uuid'), PDO::PARAM_STR);
		$stmtGetInfo->execute();
		foreach ($stmtGetInfo as $aRow) {
				
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	private function fill($aArrayOfNodes = NULL) {
		if (!is_array($aArrayOfNodes)) {
			throw new RepositoryException('no array of nodes');
		} else {
			$this->aNodeArray = $aArrayOfNodes;
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function rewind() {
		$this->iPosition = 0;
		reset($this->aNodeArray);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function current() {
		return (current($this->aNodeArray));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function key() {
		return (key($this->aNodeArray));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function next() {
		$this->iPosition++;
		return (next($this->aNodeArray));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function valid() {
		//return(!is_null(key($this->aNodeArray)));
		return(isset($this->aNodeArray[$this->iPosition]));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function offsetExists($mOffset) {
		return (isset($this->aNodeArray[$mOffset]));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function offsetGet($mOffset) {
		return ($this->aNodeArray[$mOffset]);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function offsetSet($mOffset, $nodeValue) {
		$this->aNodeArray[$mOffset] = $nodeValue;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function offsetUnset($mOffset) {
		unset($this->aNodeArray[$mOffset]);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function reverse() {
		$this->aNodeArray = array_reverse($this->aNodeArray);
		$this->rewind();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function isEmpty() {
		if (count($this->aNodeArray) == 0) {
			return (TRUE);
		} else {
			return (FALSE);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getPosition() {
		return ($this->iPosition);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getSize() {
		return (count($this->aNodeArray));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function skip($iNumElements) {
		$this->iPosition += $iNumElements;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getElement($sName) {
		$elemContainer = $this->aNodeArray[0]->getElement()->ownerDocument->createElement($sName);
		if (count($this->aNodeArray) > 0) {
			foreach ($this->aNodeArray as $nodeCurrent) {
				$elemContainer->appendChild($nodeCurrent->getElement(FALSE));
			}
		}
		return ($elemContainer);
	}
	
	
}

?>