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
class sbCR_NodeIterator implements Iterator, ArrayAccess {
	
	private $aNodeArray = array();
	private $iPosition = 0;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($aArrayOfNodes = NULL) {
		if ($aArrayOfNodes !== NULL) {
			$this->fill($aArrayOfNodes);
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
	public function getElement($sName = NULL) {
		if ($sName == NULL) {
			throw new sbException('a name is required');	
		}
		if (count($this->aNodeArray) == 0) {
			return (NULL);	
		}
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