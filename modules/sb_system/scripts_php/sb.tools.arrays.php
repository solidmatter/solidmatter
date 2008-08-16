<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Tools
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* Sorts an array of arrays by values of the inner arrays preserving the keys.
* The inner arrays must have a fixed key after which is sorted, and the value
* must be either a string or numeric. When the sort is specified to be strict,
* inapplicable entries are ignored (entries which are no arrays or don't contain
* the fixed key), otherwise the function will return FALSE. Example:
* <code>$aPeople = array (array ('name' => 'Hans', 'weight' => 81), array
* ('name' = 'Peter', 'weight' => 75))); ivsort ($aPeople, 'weight'); print_r
* ($aPeople);
* </code>
* @access public
* @param array the array to be sorted
* @param string the named or indexed key relevant for the sorting
* @param boolean sort natural (same meaning as in the built-in array functions)
* @param boolean strict sorting as described above
* @return multiple the sorted array or FALSE on error
*/
function ivsort(&$aArray, $sKey, $bNaturalSort = FALSE, $bStrict = FALSE, $bCaseSensitive = FALSE) {
	return (ivsort_slave(&$aArray, $sKey, $bNaturalSort, $bStrict, FALSE, $bCaseSensitive));
}

//------------------------------------------------------------------------------
/**
* Works the same as @see ivsort, except that it sorts descending instead of
* ascending.
* @access public
* @param array the array to be sorted
* @param string the named or indexed key relevant for the sorting
* @param boolean sort natural (same meaning as in the built-in array functions)
* @param boolean strict sorting as described above
* @return multiple the sorted array or FALSE on error
*/
function ivrsort(&$aArray, $sKey, $bNaturalSort = FALSE, $bStrict = FALSE, $bCaseSensitive = FALSE) {
	return (ivsort_slave(&$aArray, $sKey, $bNaturalSort, $bStrict, TRUE, $bCaseSensitive));
}

//------------------------------------------------------------------------------
/**
* This is an assisting function to @see ivsort and @see ivrsort and SHOULD NOT
* be called directly!
* @access public
* @param array the array to be sorted
* @param string the named or indexed key relevant for the sorting
* @param boolean sort natural (same meaning as in the built-in array functions)
* @param boolean strict sorting as described above
* @param boolean sort ascending (FALSE) or descending (TRUE)
* @return multiple the sorted array or FALSE on error
*/
function ivsort_slave(&$aArray, $mInnerKey, $bNaturalSort, $bStrict, $bReverse, $bCaseSensitive) {
	
	$aOrdered	= array();
	$aTemp		= array();
	
	foreach ($aArray as $mOuterKey => $aValues) {
		
		if (!is_array($aValues) || !isset($aValues[$mInnerKey])) {
			if ($bStrict) {
				return (FALSE);
			} else {
				continue;
			}
		}
		
		$aTemp[$mOuterKey] = $aValues[$mInnerKey];
		
	}
	
	if ($bNaturalSort) {
		if ($bCaseSensitive) {
			natsort($aTemp);
		} else {
			natcasesort($aTemp);
		}
	} else {
		sort($aTemp);
	}
	
	if ($bReverse) {
		$aTemp = array_reverse($aTemp, TRUE);
	}
	
	reset($aTemp);
	
	foreach ($aTemp as $mOuterKey => $mValue) {
		$aOrdered[] = $aArray[$mOuterKey];
	}
	
	$aArray = $aOrdered;
	
	return (TRUE);
	
}

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
function array_transpose(&$aArray) {
	
	foreach ($aArray as $mRow => $aLine) {
		foreach ($aLine as $mColumn => $mValue) {
			$aTransposed[$mColumn][$mRow] = $mValue;
		}
	}
	
	$aArray = $aTransposed;
	
	return (TRUE);
	
}

?>