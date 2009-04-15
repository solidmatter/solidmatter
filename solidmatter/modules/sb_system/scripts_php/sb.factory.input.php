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
*/
class InputFactory {
	
	private static $aKnownTypes = array(
		'string',
		'text',
		'password',
		'email',
		'url',
		'urlsafe',
		'domain',
		'ipaddress',
		//'ipaddresses',
		//'iprange',
		'integer',
		//'float',
		//'money',
		//'currency',
		'number',
		//'date',
		'datetime',
		//'radio',
		'select',
		'checkbox',
		//'checkboxes',
		'hidden',
		//'file',
		//'files',
		//'image',
		'relation',
		'autocomplete',
		'fileupload',
		'multifileupload',
		'color',
		'captcha',
		'nodeselector',
		'codeeditor',
		'users',
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getInstance($sConfig, $domForm) {
		
		$aConfig = explode(';', $sConfig);
		$sName = $aConfig[0];
		unset($aConfig[0]);
		$sType = $aConfig[1];
		unset($aConfig[1]);
		
		if (!in_array($sType, InputFactory::$aKnownTypes)) {
			throw new sbException('unknown input type: '.$sType);
		}
		
		import('sb.form.input.'.$sType);
		$sClass = 'sbInput_'.$sType;
		
		$ifGenerated = new $sClass($sName, $domForm, $aConfig);
		
		return ($ifGenerated);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function registerInputType($sType, $sModule) {
		
	}
	
}

?>