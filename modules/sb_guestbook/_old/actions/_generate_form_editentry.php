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
class _guestbook_generate_form_editentry extends Action {

	var $iAccess		= SECURE;
	var $iType			= INCLUDABLE;
	var $bLoginRequired = FALSE;
	var $bSetLinkback	= FALSE;
	var $bUseLocale		= TRUE;
	
	function Initialize() {
		Action::LoadLibrary('class_form');	
	}
	
	function &GenerateOutput () {
		
		global $PATH_LOCALTHEME;
	
		$fEditEntry = new Form("$PATH_LOCALTHEME/templates/entry.form.html");
		
		$fEditEntry->Embed('TITLE_EDITENTRY',		gls('GUESTBOOK_TITLE_EDITENTRY'),		HTML);
		$fEditEntry->Embed('TITLE_NAME',			gls('GUESTBOOK_TITLE_NAME'),			HTML);
		$fEditEntry->Embed('TITLE_EMAIL',			gls('GUESTBOOK_TITLE_EMAIL'),			HTML);
		$fEditEntry->Embed('TITLE_HOMEPAGE',		gls('GUESTBOOK_TITLE_HOMEPAGE'),		HTML);
		$fEditEntry->Embed('TITLE_COMMENT',			gls('GUESTBOOK_TITLE_COMMENT'),			HTML);
		$fEditEntry->Embed('TITLE_CREATED',			gls('SYSTEM_TITLE_CREATEDAT'),			HTML);
		$fEditEntry->Embed('TITLE_ADMINCOMMENT',	gls('GUESTBOOK_TITLE_ADMINCOMMENT'),	HTML);
		
		$fEditEntry->sAction = 'index.php?admin&module=guestbook&action=save_entry';
		$fEditEntry->sMethod = POST;
		$fEditEntry->sSubmittitle = gls('SYSTEM_TITLE_SAVE');
		
		$fEditEntry->AddInputfield('name:STRING:TEXT:REQUIRED:50:50');
		$fEditEntry->AddInputfield('email:EMAIL:TEXT:OPTIONAL:100:50');
		$fEditEntry->AddInputfield('homepage:URL:TEXT:OPTIONAL:100:50');
		$fEditEntry->AddInputfield('comment:STRING:TEXTAREA:REQUIRED:2000:50:10');
		$fEditEntry->AddInputfield('created:DATETIME:TEXT:REQUIRED');
		$fEditEntry->AddInputfield('admincomment:STRING:TEXTAREA:OPTIONAL:2000:50:10');
	
		$fEditEntry->Embed('TITLE_LINKBACK',	gls('SYSTEM_TITLE_LINKBACK'), HTML);
		$fEditEntry->Embed('URL_LINKBACK',		linktools(GET_ADMINLINKBACK), HTML);
		
		return ($fEditEntry);
	}
}

?>