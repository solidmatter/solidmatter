<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* Checks if an IP fits into a given range resp. set of ranges.
* The IP is expected to be in decimal form, e.g. '127.0.0.1'.
* An IP-range can be a sole IP-address ('192.168.100.33'), a range defined by
* wildcards ('192.168.*.*'), a real range ('192.168.100.20-192.168.100.80') or a
* combination of these, seperated by commas without spaces ('127.0.0.1,192.168.100.*')
* @access public
* @param string the IP that is to be matched
* @param string the IP-range(s)
* @return boolean TRUE, if the IP fits into the range, FALSE otherwise
*/
function match_ipranges($sIPAddress, $sIPRanges) {
	$aIPRanges = explode(',', $sIPRanges);
	reset($aIPRanges);
	while (list( , $sIPRange) = each($aIPRanges)) {
		$sIPRange = trim($sIPRange);
		if (substr_count($sIPRange, '-') != 0) {
			list($sIPRangeStart, $sIPRangeEnd) = explode('-', $sIPRange);
			$iIPAddress 		= str_replace('.', '', $sIPAddress);
			$iIPRangeStart 		= str_replace('.', '', $sIPRangeStart);
			$iIPRangeEnd 		= str_replace('.', '', $sIPRangeEnd);
			if ($iIPAddress < $iIPRangeStart || $iIPAddress > $iIPRangeEnd) {
				return (FALSE);
			}
		} elseif (substr_count($sIPRange, '*') != 0) {
			$aIPAddressComponents 	= explode('.', $sIPAddress);
			$aIPRangeComponents		= explode('.', $sIPRange);
			for ($i=0; $i<count($aIPAddressComponents); $i++) {
				if ($aIPAddressComponents[$i] != $aIPRangeComponents[$i] && $aIPRangeComponents[$i] != '*') {
					return (FALSE);
				}
			}
		} else {
			if ($sIPAddress != $sIPRange) {
				return (FALSE);
			}
		}
	}
	return (TRUE);
}

?>