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
class _guestbook_generate_permissions extends Action {
	
	var $iAccess		= SECURED;
	var $iType			= INCLUDABLE;
	var $bLoginRequired = TRUE;
	var $bSetLinkback	= FALSE;
	var $bUseLocale		= TRUE;
	
	function &GenerateOutput () {
		
		global $PATH_LOCALTHEME;
		
		$aPermissions = NULL;
		
		$aPermissions[] = array('GUESTBOOK_VIEW_INFO',		gls('GUESTBOOK_VIEW_INFO'),		0, '', FALSE, TRUE);
		$aPermissions[] = array('GUESTBOOK_EDIT_CONFIG',	gls('GUESTBOOK_EDIT_CONFIG'),	0, '', FALSE, TRUE);
		$aPermissions[] = array('GUESTBOOK_EDIT_ENTRIES',	gls('GUESTBOOK_EDIT_ENTRIES'), 	0, '', FALSE, TRUE);
		//$aPermissions[] = array('GUESTBOOK_ADD_COMMENTS',	$LOCALE['GUESTBOOK_ADD_COMMENTS'],	0, $LOCALE['SYSTEM_TITLE_NA']);
		
		return ($aPermissions);
	}
}


?>