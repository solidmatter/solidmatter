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
class guestbook_new_entry extends Action {

	var $iAccess		= OPEN;
	var $iType			= STANDALONE;
	var $bLoginRequired = FALSE;
	var $bSetLinkback	= FALSE;
	var $bUseLocale		= TRUE;
	
	function &GenerateOutput () {
		
		add_navigation(1, gls('GUESTBOOK_PAGETITLE_SHOWENTRIES'), 'index.php?module=guestbook&action=show_entries');
		add_navigation(2, gls('GUESTBOOK_PAGETITLE_NEWENTRY'), $_SERVER['REQUEST_URI']);
		
		$fNewEntry = call_action('guestbook', 'generate_form_newentry');
		$fNewEntry->EmbedFormStuff();
		
		return ($fNewEntry);
	}
}

?>