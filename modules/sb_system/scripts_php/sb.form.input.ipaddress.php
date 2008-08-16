<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage sbForm
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.form.input.string');

//------------------------------------------------------------------------------
/**
*/
class sbInput_ipaddress extends sbInput_string {
	
	protected $sType = 'ipaddress';
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInput() {
		
		$aValues = explode('.', $this->mValue);
		
		for ($i=0; $i<4; $i++) {
			if (!isset($aValues[$i]) || !is_numeric($aValues[$i])) {
				$this->sErrorLabel = '$locale/system/formerrors/no_ipaddress';
				return (FALSE);
			} else {
				if ($aValues[$i] < 0 || $aValues[$i] > 254) {
					$this->sErrorLabel = '$locale/system/formerrors/no_ipaddress';
					return (FALSE);
				}
			}
		}
		if (isset($aValues[4])) {
			$this->sErrorLabel = '$locale/system/formerrors/no_ipaddress';
			return (FALSE);
		}
		
		return (parent::checkInput());
		
	}
	
}

?>