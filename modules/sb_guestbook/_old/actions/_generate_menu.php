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
class _guestbook_generate_menu extends Action {
	
	var $iAccess		= SECURED;
	var $iType			= INCLUDABLE;
	var $bLoginRequired = TRUE;
	var $bSetLinkback	= FALSE;
	var $bUseLocale		= TRUE;
	
	function &GenerateOutput () {
		
		$menuGuestbook = new Menu();
		
		$menuGuestbook->Add('index.php?admin&module=guestbook&action=about', gls('SYSTEM_MENU_ABOUT'), ICON_ABOUT);
		// info & config
		$menuGuestbook->StartGroup();
		$menuGuestbook->Add('index.php?admin&module=guestbook&action=info',			gls('SYSTEM_MENU_INFO'),		ICON_INFO,		'GUESTBOOK_VIEW_INFO');
		$menuGuestbook->Add('index.php?admin&module=guestbook&action=config',		gls('SYSTEM_MENU_CONFIG'),		ICON_CONFIG,	'GUESTBOOK_EDIT_CONFIG');
		$menuGuestbook->EndGroup();
		// entries
		$menuGuestbook->StartGroup();
		$menuGuestbook->Add('index.php?admin&module=guestbook&action=show_entries',	gls('GUESTBOOK_MENU_ENTRIES'),	'entries.png',	'GUESTBOOK_EDIT_ENTRIES');
		$menuGuestbook->EndGroup();
		
		return ($menuGuestbook);
		

	}
}

?>