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
class sbInput_captcha extends sbInput {
	
	protected $sType = 'captcha';
	
	protected $aConfig = array(
		'uid' => '',
		'width' => '200',
		'height' => '80',
		'required' => 'TRUE',
		'length' => 4,
		'size' => 4,
		'accept_lc' => 'TRUE'
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkInput() {
		
		$mValue = $this->mValue;
		// FIXME: sometimes empty?!
		$mSequence = sbSession::$aData['captcha'][$this->aConfig['uid']];
		if ($this->aConfig['accept_lc'] == 'TRUE') {
			$mValue = strtolower($mValue);
			$mSequence = strtolower($mSequence);
		}
		
		if ($mValue != $mSequence) {
			$this->sErrorLabel = '$locale/sbSystem/formerrors/wrong_sequence';
			$this->mValue = '-';
		}
		
		if ($this->sErrorLabel == '') {
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
	public function getSequence() {
		if (isset(sbSession::$aData['captcha'][$this->aConfig['uid']])) {
			return (sbSession::$aData['captcha'][$this->aConfig['uid']]);
		}
		return ('');
	}
	
}

?>