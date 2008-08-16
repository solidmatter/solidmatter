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
class _guestbook_show_entries extends Action {

	var $iAccess		= OPEN;
	var $iType			= STANDALONE;
	var $bLoginRequired = TRUE;
	var $bSetLinkback	= TRUE;
	var $bUseLocale		= TRUE;
	
	function Initialize() {
		Action::LoadLibrary('functions_build');
		Action::StoreParameter('page', 	GET,	OPTIONAL, 1);
		Action::StoreParameter('view', 	GET,	OPTIONAL, 'compact');
		Action::RequirePermission('GUESTBOOK_EDIT_ENTRIES');
	}

	function &GenerateOutput () {
		
		global $DB;
		global $PAGETITLE;
		global $PATH_LOCALTHEME;
		global $PATH_GLOBALTHEME;
		
		$PAGETITLE = gls('GUESTBOOK_TITLE').' : '.gls('GUESTBOOK_PAGETITLE_ENTRIES');
		
		$tcTemplates	= new TemplateCollection("$PATH_LOCALTHEME/templates/entries.collection.html");
		
		$iNumEntries = $DB->Count('FROM '.TABLE_PREFIX.'_guestbook_entries');
		if ($iNumEntries == 0) {
			$tNoEntries	= $tcTemplates->ExtractTemplate('NOENTRIES');
			$tNoEntries->Embed('TITLE_NOENTRIES', gls('GUESTBOOK_TEXT_NOENTRIES'));
			return ($tNoEntries);
		}
		
		switch ($this->aParameters['view']) {
			
		    case 'detailed':
				$tContent		= $tcTemplates->ExtractTemplate('CONTAINER_DETAILED');
				$tEntry			= $tcTemplates->ExtractTemplate('ENTRY_DETAILED');
				$tAdmincomment	= $tcTemplates->ExtractTemplate('ADMINCOMMENT');
				$tIP			= $tcTemplates->ExtractTemplate('IP');
				$tSpacer		= $tcTemplates->ExtractTemplate('SPACER');
				
				$iEntriesPerPage = 10;
				$aPagination = build_pagination($iNumEntries, $iEntriesPerPage, $this->aParameters['page'], 'index.php?admin&module=guestbook&action=show_entries&view=detailed&page=');
				$iCurrentPage = $aPagination['page'];
				$iCurrentEntry = $iNumEntries - ($aPagination['start']);
				
				$tEntry->EmbedSteadily('TITLE_NAME',			gls('GUESTBOOK_TITLE_NAME'), HTML);
				$tEntry->EmbedSteadily('TITLE_EMAIL',			gls('GUESTBOOK_TITLE_EMAIL'), HTML);
				$tEntry->EmbedSteadily('TITLE_HOMEPAGE',		gls('GUESTBOOK_TITLE_HOMEPAGE'), HTML);
				$tEntry->EmbedSteadily('TITLE_COMMENT',			gls('GUESTBOOK_TITLE_COMMENT'), HTML);
				$tEntry->EmbedSteadily('TITLE_ADMINCOMMENT',	gls('GUESTBOOK_TITLE_ADMINCOMMENT'), HTML);
				$tEntry->EmbedSteadily('TITLE_CREATEDAT',		gls('SYSTEM_TITLE_CREATEDAT'), HTML);
				$tEntry->EmbedSteadily('TITLE_OPTIONS',			gls('SYSTEM_TITLE_OPTIONS'), HTML);
				$tIP->EmbedSteadily('TITLE_IP',					gls('SYSTEM_TITLE_IP'), HTML);
				$tAdmincomment->EmbedSteadily('TITLE_ADMINCOMMENT',			gls('GUESTBOOK_TITLE_ADMINCOMMENT'), HTML);
				$tAdmincomment->EmbedSteadily('TITLE_COMMENTORNICKNAME',	gls('GUESTBOOK_TITLE_COMMENTORNICKNAME'), HTML);
				
				$iNewPage = ceil($this->aParameters['page'] / 2);
				$sURLChangeView = linktools(SET_ARGUMENT, linktools(GET_CURRENTPAGE), 'view', 'compact');
				$sURLChangeView = linktools(SET_ARGUMENT, $sURLChangeView, 'page', $iNewPage);
				$tContent->Embed('URL_CHANGEVIEW',		$sURLChangeView, HTML);
				$tContent->Embed('TITLE_CHANGEVIEW',	gls('SYSTEM_TITLE_VIEWCOMPACT'), HTML);
				
				$rsEntries = $DB->SELECT('
								sge.id,
								sge.s_name,
								sge.s_email,
								sge.s_homepage,
								sge.s_ip,
								sge.t_comment,
								sge.t_admincomment,
								ssu.s_nickname AS s_commentornickname,
								sge.dt_created
					FROM		'.TABLE_PREFIX.'_guestbook_entries sge
						LEFT JOIN '.TABLE_PREFIX.'_system_users ssu ON ssu.id = sge.fk_commentor_id 
					ORDER BY	sge.dt_created DESC
					LIMIT		'.$aPagination['start'].', '.$iEntriesPerPage.'
				');
				
				while ($rsEntries->Next()) {
					$tEntry->Embed('TITLE_ENTRY',	gls('GUESTBOOK_TITLE_ENTRY').' '.$iCurrentEntry, HTML);
					$tEntry->Embed('EDIT',			build_icon(EDITDOC, 'index.php?admin&module=guestbook&action=edit_entry&id='.$rsEntries->Column('id')));
					$tEntry->Embed('DELETE',		build_icon(DELETEDOC, 'index.php?admin&module=guestbook&action=delete_entry&id='.$rsEntries->Column('id')));
					$tEntry->Embed('NAME',			$rsEntries->Column('s_name'), HTML);
					$tEntry->Embed('EMAIL',			$rsEntries->Column('s_email'), HTML);
					$tEntry->Embed('HOMEPAGE',		$rsEntries->Column('s_homepage'), HTML);
					$tEntry->Embed('COMMENT',		$rsEntries->Column('t_comment'), HTML|BR);
					$tEntry->Embed('DATE_CREATED',	datetime_mysql2local($rsEntries->Column('dt_created'), LONGDATETIME), HTML);
					if ($rsEntries->Column('s_ip') != NULL) {
						$tIP->Embed('IP', $rsEntries->Column('s_ip'), HTML|BR);
						$tEntry->Embed('IP', $tIP);
					} else {
						$tEntry->Strip('IP');
					}
					if ($rsEntries->Column('t_admincomment') != NULL) {
						$tAdmincomment->Embed('ADMINCOMMENT',		$rsEntries->Column('t_admincomment'), HTML|BR);
						$tAdmincomment->Embed('COMMENTORNICKNAME',	$rsEntries->Column('s_commentornickname'), HTML);
						$tEntry->Embed('ADMINCOMMENT', $tAdmincomment);
						$tAdmincomment->Reset();
					} else {
						$tEntry->Strip('ADMINCOMMENT');
					}
					$tEntry->Append();
					$iCurrentEntry--;
					if (!$rsEntries->isLast()) {
						$tEntry->Append($tSpacer);
					}
				}
				
				break;
			
		    case 'compact':
				$tContent	= $tcTemplates->ExtractTemplate('CONTAINER_COMPACT');
				$tEntry		= $tcTemplates->ExtractTemplate('ENTRY_COMPACT');
				
				$tContent->Embed('TITLE_NUMBER',		gls('SYSTEM_TITLE_NUMBERABB'), HTML);
				$tContent->Embed('TITLE_NAME',			gls('GUESTBOOK_TITLE_NAME'), HTML);
				$tContent->Embed('TITLE_CREATEDAT',		gls('SYSTEM_TITLE_CREATEDAT'), HTML);
				$tContent->Embed('TITLE_OPTIONS',		gls('SYSTEM_TITLE_OPTIONS'), HTML);
				$tContent->Embed('TITLE_EMAIL',			gls('GUESTBOOK_TITLE_EMAIL'), HTML);
				$tContent->Embed('TITLE_HOMEPAGE',		gls('GUESTBOOK_TITLE_HOMEPAGE'), HTML);
				$tContent->Embed('TITLE_COMMENT',		gls('GUESTBOOK_TITLE_COMMENT'), HTML);
				$tContent->Embed('TITLE_ADMINCOMMENT',	gls('GUESTBOOK_TITLE_ADMINCOMMENT'), HTML);
				
				$iNewPage = floor($this->aParameters['page'] * 2 - 1);
				$sURLChangeView = linktools(SET_ARGUMENT, linktools(GET_CURRENTPAGE), 'view', 'detailed');
				$sURLChangeView = linktools(SET_ARGUMENT, $sURLChangeView, 'page', $iNewPage);
				$tContent->Embed('URL_CHANGEVIEW',		$sURLChangeView, HTML);
				$tContent->Embed('TITLE_CHANGEVIEW',	gls('SYSTEM_TITLE_VIEWDETAILED'), HTML);
				
				$iEntriesPerPage = 20;
				$aPagination = build_pagination($iNumEntries, $iEntriesPerPage, $this->aParameters['page'], 'index.php?admin&module=guestbook&action=show_entries&view=compact&page=');
				$iCurrentPage = $aPagination['page'];
				$iCurrentEntry = $iNumEntries - ($aPagination['start']);
				
				$rsEntries = $DB->SELECT('
								sge.id,
								sge.s_name,
								sge.s_email,
								sge.s_homepage,
								sge.t_admincomment,
								sge.dt_created
					FROM		'.TABLE_PREFIX.'_guestbook_entries sge
						LEFT JOIN '.TABLE_PREFIX.'_system_users ssu ON ssu.id = sge.fk_commentor_id
					ORDER BY	sge.dt_created DESC
					LIMIT		'.$aPagination['start'].', '.$iEntriesPerPage.'
				');
				
				while ($rsEntries->Next()) {
					if ($rsEntries->IsOdd()) {
						$sClass = 'odd';
					} else {
						$sClass = 'even';
					}
					
					$sEmail = '';
					$sHomepage = '';
					$sAdmincomment = '';
					if ($rsEntries->Column('s_email') != NULL) {
						$sEmail = build_checkmark();
					}
					if ($rsEntries->Column('s_homepage') != NULL) {
						$sHomepage = build_checkmark();
					}
					if ($rsEntries->Column('t_admincomment') != NULL) {
						$sAdmincomment = build_checkmark();
					}
					
					$tEntry->Embed('CLASS',			$sClass);
					$tEntry->Embed('NUMBER',		$iCurrentEntry);
					$tEntry->Embed('EMAIL',			$sEmail);
					$tEntry->Embed('HOMEPAGE',		$sHomepage);
					$tEntry->Embed('ADMINCOMMENT',	$sAdmincomment);
					$tEntry->Embed('EDIT',			build_icon(EDITDOC, 'index.php?admin&module=guestbook&action=edit_entry&id='.$rsEntries->Column('id')));
					$tEntry->Embed('DELETE',		build_icon(DELETEDOC, 'index.php?admin&module=guestbook&action=delete_entry&id='.$rsEntries->Column('id')));
					$tEntry->Embed('NAME',			$rsEntries->Column('s_name'), HTML);
					$tEntry->Embed('DATE_CREATED',	datetime_mysql2local($rsEntries->Column('dt_created'), SHORTDATETIME), HTML);
					$tEntry->Append();
					$iCurrentEntry--;
				}
				break;
		}
		
		$tContent->Embed('PAGINATION', $aPagination['html']);
		$tContent->Embed('ENTRIES', $tEntry);
		
		return ($tContent);
	}
}

?>