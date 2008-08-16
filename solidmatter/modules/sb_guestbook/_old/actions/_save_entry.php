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
class _guestbook_save_entry extends Action {

	var $iAccess		= OPEN;
	var $iType			= STANDALONE;
	var $bLoginRequired = FALSE;
	var $bSetLinkback	= FALSE;
	var $bUseLocale		= TRUE;
	
	function Initialize() {
		Action::RequirePermission('GUESTBOOK_EDIT_ENTRIES');
	}
	
	function &GenerateOutput () {
		
		global $DB;
		global $PAGETITLE;
		global $PATH_LOCALTHEME;
		
		$PAGETITLE = mask_string(gls('GUESTBOOK_TITLE').' : '.gls('GUESTBOOK_TITLE_EDITENTRY'), HTML);
		
		$fEditEntry = call_action('guestbook', 'generate_form_editentry');
		$fEditEntry->RecieveInputs();
		
		if ($fEditEntry->CheckInputs()) {
		
			$aInputs = $fEditEntry->ExportInputs(SQL);
			$DB->UPDATE('
						'.TABLE_PREFIX.'_guestbook_entries
				SET		s_name			= '.$aInputs['name'].',
						s_email			= '.$aInputs['email'].',
						s_homepage		= '.$aInputs['homepage'].',
						t_comment		= '.$aInputs['comment'].',
						dt_created		= '.$aInputs['created'].'
				WHERE	id = '.$fEditEntry->GetEditID().'
			');
			if ($aInputs['admincomment'] != NULL) {
				$DB->UPDATE('
							'.TABLE_PREFIX.'_guestbook_entries
					SET		t_admincomment	= '.$aInputs['admincomment'].',
							fk_commentor_id	= '.$_SESSION['user_id'].'
					WHERE	id = '.$fEditEntry->GetEditID().'
				');
			}
			
			$aParameters['proceedurl']	= linktools(GET_ADMINLINKBACK);
			$aParameters['infotext']	= gls('GUESTBOOK_TITLE_ENTRYSAVED');
			$tAcknowledge = call_action('system', 'generate_acknowledge', INCLUDABLE, $aParameters);
			return ($tAcknowledge);
			
		} else {
			$fEditEntry->EmbedFormStuff();
			return ($fEditEntry);
		}
	}
}

?>