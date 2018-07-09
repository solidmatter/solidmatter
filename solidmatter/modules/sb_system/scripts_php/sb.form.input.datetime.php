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
import('sb.tools.forms');

//------------------------------------------------------------------------------
/**
*/
class sbInput_datetime extends sbInput_string {
	
	protected $sType = 'datetime';
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function recieveInput() {
		
		// don't do anything if disabled (e.g. Firefox will submit nothing for disabled form elements)
		if ($this->bDisabled) {
			return;
		}
		
		// Note: time input does not really support seconds, so these are set to 00 fixed
		global $_REQUEST;
		$this->mValue = NULL;
		if($_REQUEST->getParam($this->sName.'_date') != NULL) {
			if ($_REQUEST->getParam($this->sName.'_time') != NULL) {
				$this->mValue = $_REQUEST->getParam($this->sName.'_date').' '.$_REQUEST->getParam($this->sName.'_time').':00';
			}
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInput() {
		
		if (strlen($this->mValue) == 0 && $this->aConfig['required'] == 'TRUE') {
			$this->sErrorLabel = '$locale/sbSystem/formerrors/not_null';
		}
		if ($this->aConfig['required'] == 'TRUE' && $this->mValue != NULL && !is_mysqldatetime($this->mValue)) {
			$this->sErrorLabel = '$locale/sbSystem/formerrors/no_datetime';
		}
		
		return (parent::checkInput());
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getValue() {
		if (strlen(trim($this->mValue)) == 0) {
			$this->mValue = NULL;
		}
		return ($this->mValue);
	}
	
}

?>