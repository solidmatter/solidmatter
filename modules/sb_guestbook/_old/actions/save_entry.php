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
class guestbook_save_entry extends Action {

	var $iAccess		= OPEN;
	var $iType			= STANDALONE;
	var $bLoginRequired = FALSE;
	var $bSetLinkback	= FALSE;
	var $bUseLocale		= TRUE;
	
	function &GenerateOutput () {
		
		global $DB;
		global $PATH_LOCALTHEME;
		
		add_navigation(1, gls('GUESTBOOK_PAGETITLE_SHOWENTRIES'), 'index.php?module=guestbook&action=show_entries');
		add_navigation(2, gls('GUESTBOOK_PAGETITLE_NEWENTRY'), 'index.php?module=guestbook&action=new_entry');
		
		$fNewEntry = call_action('guestbook', 'generate_form_newentry');
		$fNewEntry->RecieveInputs();
		
		if ($fNewEntry->CheckInputs()) {
			$aInputs = $fNewEntry->ExportInputs(SQL);
			$sIP = NULL;
			if (get_config('guestbook', 'LOG_IPS') == 'TRUE') {
				$sIP = $_SERVER['REMOTE_ADDR'];
			}
			$DB->INSERT('
						INTO '.TABLE_PREFIX.'_guestbook_entries (
						s_name,
						s_email,
						s_homepage,
						t_comment,
						s_ip,
						dt_created
						) VALUES (
						'.$aInputs['name'].',
						'.$aInputs['email'].',
						'.$aInputs['homepage'].',
						'.$aInputs['comment'].',
						'.mask_string($sIP, SQL).',
						NOW()
						)
			');
			redirect('index.php?module=guestbook&action=show_entries', is_secure_connection());
		} else {
			$fNewEntry->EmbedFormStuff();
			return ($fNewEntry);
		}
		
	}
}

?>