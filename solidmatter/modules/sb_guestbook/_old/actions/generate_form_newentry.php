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
class guestbook_generate_form_newentry extends Action {

	var $iAccess		= SECURE;
	var $iType			= INCLUDABLE;
	var $bLoginRequired = FALSE;
	var $bSetLinkback	= FALSE;
	var $bUseLocale		= TRUE;
	
	function Initialize() {
		Action::LoadLibrary('class_quickform');
	}
	
	function &GenerateOutput () {
		
		global $PATH_LOCALTHEME;
		
		$fNewEntry	= new Quickform("$PATH_LOCALTHEME/templates/entry.form.html");
		
		$aMaskOptions = array();
		if (get_config('guestbook', 'ALLOW_SMILIES') == 'TRUE') {
			$aMaskOptions[] = 'SMILIES';
		}
		if (get_config('guestbook', 'ALLOW_ICONS') == 'TRUE') {
			$aMaskOptions[] = 'ICONS';
		}
		if (get_config('guestbook', 'ALLOW_LINKS') == 'TRUE') {
			$aMaskOptions[] = 'URLS';
		}
		if (get_config('guestbook', 'ALLOW_IMAGES') == 'TRUE') {
			$aMaskOptions[] = 'IMAGES';
		}
		
		$fNewEntry->Embed('TITLE_NEWENTRY',	gls('GUESTBOOK_PAGETITLE_NEWENTRY'), HTML);
		$fNewEntry->Embed('TITLE_NAME',		gls('GUESTBOOK_TITLE_NAME'), HTML);
		$fNewEntry->Embed('TITLE_EMAIL',	gls('GUESTBOOK_TITLE_EMAIL'), HTML);
		$fNewEntry->Embed('TITLE_HOMEPAGE',	gls('GUESTBOOK_TITLE_HOMEPAGE'), HTML);
		$fNewEntry->Embed('TITLE_COMMENT',	gls('GUESTBOOK_TITLE_COMMENT'), HTML);
		
		$fNewEntry->sAction = 'index.php?module=guestbook&action=save_entry';
		$fNewEntry->iMethod = POST;
		$fNewEntry->sSubmittitle = gls('SYSTEM_TITLE_SAVE');
		$fNewEntry->AddInputfield('name:STRING:REQUIRED:50:50');
		$fNewEntry->AddInputfield('email:EMAIL:OPTIONAL:100:50');
		$fNewEntry->AddInputfield('homepage:URL:OPTIONAL:100:50');
		$fNewEntry->AddInputfield('comment:TEXT:REQUIRED:2000:50:10:'.implode('|', $aMaskOptions));
		
		$fNewEntry->Embed('TITLE_LINKBACK',	gls('SYSTEM_TITLE_LINKBACK'), HTML);
		$fNewEntry->Embed('URL_LINKBACK',	linktools(GET_LINKBACK), HTML);
		
		return ($fNewEntry);
	}
}

?>