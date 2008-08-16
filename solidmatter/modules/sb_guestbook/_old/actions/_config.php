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
class _guestbook_config extends ConfigAction {

	function CInitialize() {
		
		if (!Action::RequirePermission('GUESTBOOK_EDIT_CONFIG')) {
			return (FALSE);
		}
		
		$this->aConfigFields = array(
			'config' => array(
				'label' => gls('SYSTEM_TITLE_CONFIG'),
				'fields' => array(
					'entriesperpage' => array(
						'label' => gls('GUESTBOOK_TITLE_ENTRIESPERPAGE'),
						'config' => 'INTEGER:REQUIRED:5:50',
						'db' => 'ENTRIES_PER_PAGE'
					),
					'sortdirection' => array(
						'label' => gls('GUESTBOOK_TITLE_SORTDIRECTION'),
						'config' => 'SELECT:REQUIRED',
						'db' => 'SORTDIRECTION',
						'options' => array(
							gls('SYSTEM_OPTION_ASCENDING') => 'ASC',
							gls('SYSTEM_OPTION_DESCENDING') => 'DESC'
						)
					),
					'logips' => array(
						'label' => gls('SYSTEM_TITLE_LOGIPS'),
						'config' => 'CHECKBOX:REQUIRED',
						'db' => 'LOG_IPS'
					),
					'allowsmilies' => array(
						'label' => gls('GUESTBOOK_TITLE_ALLOWSMILIES'),
						'config' => 'CHECKBOX:REQUIRED',
						'db' => 'ALLOW_SMILIES'
					),
					'allowimages' => array(
						'label' => gls('GUESTBOOK_TITLE_ALLOWIMAGES'),
						'config' => 'CHECKBOX:REQUIRED',
						'db' => 'ALLOW_IMAGES'
					),
					'allowlinks' => array(
						'label' => gls('GUESTBOOK_TITLE_ALLOWLINKS'),
						'config' => 'CHECKBOX:REQUIRED',
						'db' => 'ALLOW_LINKS'
					),
					'allowicons' => array(
						'label' => gls('GUESTBOOK_TITLE_ALLOWICONS'),
						'config' => 'CHECKBOX:REQUIRED',
						'db' => 'ALLOW_ICONS'
					),
					'embedcutoff' => array(
						'label' => gls('GUESTBOOK_TITLE_EMBEDCUTOFF'),
						'config' => 'INTEGER:REQUIRED:30:500',
						'db' => 'EMBED_CUTOFF'
					)
				)
			)
		);
		
	}

}

?>