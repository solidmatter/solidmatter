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
class _guestbook_edit_entry extends Action {

	var $iAccess		= SECURE;
	var $iType			= STANDALONE;
	var $bLoginRequired = TRUE;
	var $bSetLinkback	= FALSE;
	var $bUseLocale		= TRUE;
	
	function Initialize() {
		Action::StoreParameter('id',	GET,	REQUIRED);
		Action::RequirePermission('GUESTBOOK_EDIT_ENTRIES');
	}
	
	function &GenerateOutput () {
		
		global $DB;
		global $PAGETITLE;
		
		$PAGETITLE = mask_string(gls('GUESTBOOK_TITLE').' : '.gls('GUESTBOOK_TITLE_EDITENTRY'), HTML);
		
		$fEditEntry = call_action('guestbook', 'generate_form_editentry');
		
		$rsEntry = $DB->SELECT('
					s_name,
					s_email,
					s_homepage,
					t_comment,
					t_admincomment,
					dt_created
			FROM	'.TABLE_PREFIX.'_guestbook_entries
			WHERE	id = '.mask_string($this->aParameters['id'], SQL).'
		');
		$rsEntry->First();
		
		$fEditEntry->SetValue('name',			$rsEntry->Column('s_name'));
		$fEditEntry->SetValue('email',			$rsEntry->Column('s_email'));
		$fEditEntry->SetValue('homepage',		$rsEntry->Column('s_homepage'));
		$fEditEntry->SetValue('comment',		$rsEntry->Column('t_comment'));
		$fEditEntry->SetValue('created',		$rsEntry->Column('dt_created'));
		$fEditEntry->SetValue('admincomment',	$rsEntry->Column('t_admincomment'));
		
		$fEditEntry->EmbedFormStuff();
		$fEditEntry->StoreEditID($this->aParameters['id']);
		
		return ($fEditEntry);
	}
}

?>