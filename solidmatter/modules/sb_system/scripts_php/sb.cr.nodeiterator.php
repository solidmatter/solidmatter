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
	public function __construct(array $aArrayOfNodes = NULL) {
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
	private function fill(array $aArrayOfNodes = NULL) {
		if (!is_array($aArrayOfNodes)) {
			throw new RepositoryException('no array of nodes');
		} else {
			// it might be that an associative array was given, this class needs a continuitive indexed array
			#PHP7 unset($this->aNodeArray);
			$this->aNodeArray = array();
			foreach ($aArrayOfNodes as $nodeCurrent) {
				$this->aNodeArray[] = $nodeCurrent;
			}
			reset($this->aNodeArray);
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
	public function remove() {
		unset($this->aNodeArray[$iPosition]);
	}
	
	//--------------------------------------------------------------------------
	/**
	* TODO: find more stable way to determine the lasting nodes, not just the last one
	* @param 
	* @return 
	*/
	public function makeUnique() {
		$aTemp = array();
		foreach ($this->aNodeArray as $nodeCurrent) {
			$aTemp[$nodeCurrent->getProperty('jcr:uuid')] = $nodeCurrent;
		}
		$this->fill($aTemp);
		$this->rewind();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function append($mItem) {
		if ($mItem instanceof sbNode) {
			$this->aNodeArray[] = $mItem;
		} elseif ($mItem instanceof sbNodeIterator) {
			foreach ($niSecond as $nodeCurrent) {
				$this->aNodeArray[] = $nodeCurrent;
			}
		}
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
	public function skip(int $iNumElements) {
		$this->iPosition += $iNumElements;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function sortAscending(string $sProperty) {
		$this->sortSlave($sProperty, FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function sortDescending(string $sProperty) {
		$this->sortSlave($sProperty, TRUE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function sortSlave(string $sProperty, bool $bDescending, bool $bNaturalSort = TRUE, bool $bCaseSensitive = FALSE) {
		
		$aOrdered	= array();
		$aTemp		= array();
		
		foreach ($this->aNodeArray as $iKey => $nodeCurrent) {
			$aTemp[$iKey] = $nodeCurrent->getProperty($sProperty);
		}
		
		if ($bNaturalSort) {
			if ($bDescending) {
				natsort($aTemp);
			} else {
				natcasesort($aTemp);
			}
		} else {
			sort($aTemp);
		}
		
		if ($bDescending) {
			$aTemp = array_reverse($aTemp, TRUE);
		}
		
		reset($aTemp);
		
		foreach ($aTemp as $iKey => $mValue) {
			$aOrdered[] = $this->aNodeArray[$iKey];
		}
		
		$this->aNodeArray = $aOrdered;
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getElement(string $sName = NULL) {
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
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function crop(int $iTotalNodes) {
		$i = 0;
		if (count($this->aNodeArray) > $iTotalNodes) {
			foreach ($this->aNodeArray as $sLabel => $nodeCurrent) {
				$i++;
				if ($i > $iTotalNodes) {
					unset($this->aNodeArray[$sLabel]);	
				}
			}
		}
	}
	
	
}

?>