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
class _guestbook_delete_entry extends Action {

	var $iAccess		= SECURE;
	var $iType			= STANDALONE;
	var $bLoginRequired = TRUE;
	var $bSetLinkback	= FALSE;
	var $bUseLocale		= TRUE;
	
	function Initialize() {
		Action::LoadLibrary('class_form');
		Action::StoreParameter('id',		GET,	REQUIRED);
		Action::StoreParameter('confirm',	GET,	OPTIONAL);
		Action::RequirePermission('GUESTBOOK_EDIT_ENTRIES');
	}
	
	function &GenerateOutput () {
		
		global $DB;
		global $PAGETITLE;
		
		$PAGETITLE = mask_string(gls('GUESTBOOK_TITLE').' : '.gls('GUESTBOOK_TITLE_DELETEENTRY'), HTML);
		
		if ($this->aParameters['confirm'] == 'true') {
		
			$DB->DELETE('
				FROM	'.TABLE_PREFIX.'_guestbook_entries
				WHERE	id = '.mask_string($this->aParameters['id'], SQL).'
			');
			
			$aParameters['proceedurl']	= linktools(GET_ADMINLINKBACK);
			$aParameters['infotext']	= gls('GUESTBOOK_TITLE_ENTRYDELETED');
			$tConfirmation = call_action('system', 'generate_acknowledge', INCLUDABLE, $aParameters);
			
		} else {
			$aParameters['actionurl']	= linktools(GET_CURRENTPAGE);
			$aParameters['infotext']	= gls('GUESTBOOK_TEXT_ENTRYWILLBEDELETED');
			$tConfirmation = call_action('system', 'generate_confirmation', INCLUDABLE, $aParameters);
		}
		
		return ($tConfirmation);
	}
}

?>