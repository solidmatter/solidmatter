<?php

//------------------------------------------------------------------------------
/**
*	@package solidBrickz[Guestbook]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* 
*/
class _guestbook_acknowledge_changes extends Action {

	var $iAccess		= SECURE;
	var $iType			= STANDALONE;
	var $bLoginRequired = TRUE;
	var $bSetLinkback	= FALSE;
	var $bUseLocale		= TRUE;
	
	function &GenerateOutput () {
		
		$aParameters['infotext']	= gls('SYSTEM_TITLE_ACKNOWLEDGECHANGES');
		$aParameters['proceedurl']	= linktools(GET_ADMINLINKBACK);
		$tAcknowledge = call_action('system', 'generate_acknowledge', INCLUDABLE, $aParameters);
		
		return ($tAcknowledge);
	}
}

?>