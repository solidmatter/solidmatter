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
class _guestbook_test_dataaccess extends DataAccessAction {

	var $iAccess		= OPEN;
	var $iType			= STANDALONE;
	var $bLoginRequired = TRUE;
	var $bSetLinkback	= FALSE;
	var $bUseLocale		= TRUE;
	
	function DAInitialize() {
		
		// general
		
		$this->DAUID		= 'guestbook_entries';
		$this->sListTitle	= gls('GUESTBOOK_TITLE_ENTRIES');
		$this->sPageTitle	= gls('guestbook').' : '.gls('GUESTBOOK_TITLE_ENTRIES');
		$this->sDBTable		= TABLE_PREFIX.'_guestbook_entries';
		$this->sPrimaryKey	= 'id';
		$this->iOptions		= DA_OPTION_EDIT|DA_OPTION_DELETE;
		$this->bUseHistory	= TRUE;
		
		switch ($this->aParameters['intaction']) {
		
			case 'listview': 
		
				$this->sCountQuery = 'FROM '.TABLE_PREFIX.'_guestbook_entries';
				$this->iItemsPerPage = 20;
				$this->sDefaultOrderBy = 's_name';
				$this->sListviewQuery = '
								id,
								s_name,
								s_homepage,
								s_email
					FROM		'.TABLE_PREFIX.'_guestbook_entries
				';
				
				$this->sSearchClause = 's_name LIKE \'%<<SEARCH>>%\'';
				$this->sNoItems = gls('GUESTBOOK_TEXT_NOENTRIES');
				$this->aColumns = array(
					's_name' => array(
						'title' => gls('GUESTBOOK_TITLE_NAME'),
						'width' => 40
					),
					's_homepage' => array(
						'title' => gls('GUESTBOOK_TITLE_HOMEPAGE'),
						'width' => 40
					),
					's_email' => array(
						'title' => gls('GUESTBOOK_TITLE_EMAIL'),
						'width' => 20
					)
				);
				break;
		
			default:		
				
				$this->sEditQuery = '
								id,
								s_name,
								s_homepage,
								s_email,
								s_ip,
								t_comment,
								t_admincomment
					FROM		'.TABLE_PREFIX.'_guestbook_entries
				';
				$this->sFormTitle = gls('GUESTBOOK_TITLE_EDITENTRY');
				$this->aFields = array(
					'id' => array(
						'label' => gls('SYSTEM_TITLE_ID'),
						'readonly' => TRUE
					),
					's_name' => array(
						'label' => gls('GUESTBOOK_TITLE_NAME'),
						'config' => 'STRING:REQUIRED:50',
					),
					's_email' => array(
						'label' => gls('GUESTBOOK_TITLE_EMAIL'),
						'config' => 'EMAIL:OPTIONAL:50',
					),
					's_ip' => array(
						'label' => gls('SYSTEM_TITLE_IP'),
						'config' => 'STRING:OPTIONAL:20',
						'readonly' => TRUE,
						/*'options' => array('YES' => 1, 'NO' => 2)
						'query' => '
									s_name AS title,
									id AS value
							FROM	'.TABLE_PREFIX.'_shop_types
						'*/
					),
					't_comment' => array(
						'label' => gls('GUESTBOOK_TITLE_COMMENT'),
						'config' => 'TEXT:REQUIRED:2000:50:10'
					),
					't_admincomment' => array(
						'label' => gls('GUESTBOOK_TITLE_ADMINCOMMENT'),
						'config' => 'TEXT:OPTIONAL:2000:50:10'
					)
				);
				$this->bUseLocking = TRUE;
				$this->bUseHistory = FALSE;
				$this->bConfirmSave = FALSE;
				$this->sBackupQuery = '
					INTO		'.$this->sDBTable.'_history
								(
									id,
									s_name,
									s_email,
									s_homepage,
									s_ip,
									t_comment,
									t_admincomment,
									fk_commentor_id,
									dt_created,
									dt_recorded
								)
					SELECT		(
									id,
									s_name,
									s_email,
									s_homepage,
									s_ip,
									t_comment,
									t_admincomment,
									fk_commentor_id,
									dt_created,
									NOW()
								)
					FROM	'.$this->sDBTable.'
					WHERE	'.$this->sPrimaryKey.' = '.mask_string($this->aParameters['item_id'], SQL);
				break;
	
		}
	}	
}

?>