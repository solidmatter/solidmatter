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
class guestbook_show_entries2 extends Action {

	var $iAccess		= OPEN;
	var $iType			= INCLUDABLE;
	var $bLoginRequired = FALSE;
	var $bSetLinkback	= TRUE;
	var $bUseLocale		= TRUE;
	
	function Initialize() {
		Action::LoadLibrary('functions_build');
		Action::StoreParameter('page', 	GET,	OPTIONAL, 1);
	}

	function &GenerateOutput () {
		
		global $sbDB;
		//global $PATH_MODULE;
		//global $PATH_LOCALTHEME;
		//global $PATH_GLOBALTHEME;
		
		add_navigation(1, gls('GUESTBOOK_PAGETITLE_SHOWENTRIES'), 'index.php?module=guestbook&action=show_entries');
		
		$stmtNumEntries = $sbDB->query('SELECT COUNT(*) AS num FROM '.TABLE_PREFIX.'_guestbook_entries');
		$aNumEntries = $stmtNumEntries->fetch(PDO::FETCH_ASSOC);
		$iNumEntries = $aNumEntries['num'];
		$stmtNumEntries->closeCursor();
		$iEntriesPerPage = get_config('guestbook', 'ENTRIES_PER_PAGE');
		$aPagination = build_pagination($iNumEntries, $iEntriesPerPage, $this->aParameters['page'], 'index.php?module=guestbook&action=show_entries&page=');
		$iCurrentPage = $aPagination['page'];
		
		
		$stmtEntries = $sbDB->prepare('
				SELECT		sge.id,
							sge.s_name,
							sge.s_email,
							sge.s_homepage,
							sge.t_comment,
							sge.t_admincomment,
							ssu.s_nickname AS s_commentornickname,
							sge.dt_created
				FROM		'.TABLE_PREFIX.'_guestbook_entries sge
					LEFT JOIN '.TABLE_PREFIX.'_system_users ssu ON ssu.id = sge.fk_commentor_id 
				ORDER BY	sge.dt_created '.get_config('guestbook', 'SORTDIRECTION').'
				LIMIT		'.$aPagination['start'].', '.get_config('guestbook', 'ENTRIES_PER_PAGE').'
		');
		
		//$aParams[':sortdirection'] = get_config('guestbook', 'SORTDIRECTION');
		//$aParams[':start'] = 0;//(integer) $aPagination['start'];
		//$aParams[':numentries'] = 15;//(integer) get_config('guestbook', 'ENTRIES_PER_PAGE');
		
		//$stmtEntries->bindParam(':calories', $calories, PDO::PARAM_INT);
		//$stmtEntries->bindParam(':colour', $colour, PDO::PARAM_STR, 12);
		
		
		$stmtEntries->execute();
		
		
		$iMask = NULL;
		if (get_config('guestbook', 'ALLOW_SMILIES') == 'TRUE') {
			$iMask = $iMask | SMILIES;
		}
		if (get_config('guestbook', 'ALLOW_ICONS') == 'TRUE') {
			$iMask = $iMask | ICONS;
		}
		if (get_config('guestbook', 'ALLOW_LINKS') == 'TRUE') {
			$iMask = $iMask | URLS;
		}
		if (get_config('guestbook', 'ALLOW_IMAGES') == 'TRUE') {
			$iMask = $iMask | IMAGES;
		}
		
		/*
		$tcTemplates	= new TemplateCollection("$PATH_LOCALTHEME/templates/entries.collection.html");
		$tContent		= $tcTemplates->ExtractTemplate('CONTAINER');
		$tSpacer		= $tcTemplates->ExtractTemplate('SPACER');
		
		$iNumEntries = $DB->Count('FROM '.TABLE_PREFIX.'_guestbook_entries');
		$iEntriesPerPage = get_config('guestbook', 'ENTRIES_PER_PAGE');
		$sSortdirection = get_config('guestbook', 'SORTDIRECTION');
		$aPagination = build_pagination($iNumEntries, $iEntriesPerPage, $this->aParameters['page'], 'index.php?module=guestbook&action=show_entries&page=');
		$iCurrentPage = $aPagination['page'];
		if ($sSortdirection == 'DESC') {
			$iCurrentEntry = $iNumEntries - ($aPagination['start']);
		} else {
			$iCurrentEntry = $aPagination['start'] + 1;
		}
		
		if ($iNumEntries == 0) {
			$tEntry	= $tcTemplates->ExtractTemplate('NOENTRIES');
			$tEntry->Embed('TEXT_NOENTRIES', gls('GUESTBOOK_TEXT_NOENTRIES'));
		} else {
			$tEntry			= $tcTemplates->ExtractTemplate('ENTRY');
			$tAdmincomment	= $tcTemplates->ExtractTemplate('ADMINCOMMENT');
			$tEntry->EmbedSteadily('TITLE_NAME',						gls('GUESTBOOK_TITLE_NAME'), HTML);
			$tEntry->EmbedSteadily('TITLE_EMAIL',						gls('GUESTBOOK_TITLE_EMAIL'), HTML);
			$tEntry->EmbedSteadily('TITLE_HOMEPAGE',					gls('GUESTBOOK_TITLE_HOMEPAGE'), HTML);
			$tEntry->EmbedSteadily('TITLE_COMMENT',						gls('GUESTBOOK_TITLE_COMMENT'), HTML);
			$tEntry->EmbedSteadily('TITLE_CREATEDAT',					gls('SYSTEM_TITLE_CREATEDAT'), HTML);
			$tAdmincomment->EmbedSteadily('TITLE_COMMENTORNICKNAME',	gls('GUESTBOOK_TITLE_COMMENTORNICKNAME'), HTML);
			$tAdmincomment->EmbedSteadily('TITLE_ADMINCOMMENT',			gls('GUESTBOOK_TITLE_ADMINCOMMENT'), HTML);
			
			$rsEntries = $DB->SELECT('
							sge.id,
							sge.s_name,
							sge.s_email,
							sge.s_homepage,
							sge.t_comment,
							sge.t_admincomment,
							ssu.s_nickname AS s_commentornickname,
							sge.dt_created
				FROM		'.TABLE_PREFIX.'_guestbook_entries sge
					LEFT JOIN '.TABLE_PREFIX.'_system_users ssu ON ssu.id = sge.fk_commentor_id 
				ORDER BY	sge.dt_created '.$sSortdirection.'
				LIMIT		'.$aPagination['start'].', '.$iEntriesPerPage.'
			');
			
			$iMask = NULL;
			if (get_config('guestbook', 'ALLOW_SMILIES') == 'TRUE') {
				$iMask = $iMask | SMILIES;
			}
			if (get_config('guestbook', 'ALLOW_ICONS') == 'TRUE') {
				$iMask = $iMask | ICONS;
			}
			if (get_config('guestbook', 'ALLOW_LINKS') == 'TRUE') {
				$iMask = $iMask | URLS;
			}
			if (get_config('guestbook', 'ALLOW_IMAGES') == 'TRUE') {
				$iMask = $iMask | IMAGES;
			}
			
			while ($rsEntries->Next()) {
				$tEntry->Embed('TITLE_ENTRY',	gls('GUESTBOOK_TITLE_ENTRY').' '.$iCurrentEntry, HTML);
				$tEntry->Embed('NAME',			$rsEntries->Column('s_name'), HTML);
				$tEntry->Embed('EMAIL',			$rsEntries->Column('s_email'), HTML|EMAIL);
				$tEntry->Embed('HOMEPAGE',		$rsEntries->Column('s_homepage'), HTML|URL);
				$tEntry->Embed('COMMENT',		$rsEntries->Column('t_comment'), HTML|BR|$iMask, "$PATH_GLOBALTHEME/images_smilies/");
				$tEntry->Embed('DATE_CREATED',	datetime_mysql2local($rsEntries->Column('dt_created'), LONGDATETIME), HTML);
				if ($rsEntries->Column('t_admincomment') != NULL) {
					$tAdmincomment->Embed('COMMENTORNICKNAME',	$rsEntries->Column('s_commentornickname'), HTML);
					$tAdmincomment->Embed('ADMINCOMMENT',		$rsEntries->Column('t_admincomment'), HTML|BR|$iMask);
					$tEntry->Embed('ADMINCOMMENT',	$tAdmincomment);
					$tAdmincomment->Reset();
				} else {
					$tEntry->Strip('ADMINCOMMENT');
				}
				$tEntry->Append();
				if (!$rsEntries->IsLast()) {
					$tEntry->Append($tSpacer);
				}
				if ($sSortdirection == 'DESC') {
					$iCurrentEntry--;
				} else {
					$iCurrentEntry++;
				}
			}
		}
		
		$tContent->Embed('TITLE_NEWENTRY', gls('GUESTBOOK_TITLE_NEWENTRY'), HTML);
		$tContent->Embed('URL_NEWENTRY', 'index.php?module=guestbook&action=new_entry', HTML);
		$tContent->Embed('PAGINATION', $aPagination['html']);
		$tContent->Embed('ENTRIES', &$tEntry);
		*/
		
		/*
		$domEntries = new DOMDocument();
		$domEntries->appendChild($domEntries->importNode($stmtEntries->fetchDOM()));
		
		$tContent = new Template();
		$tContent->sResult = $domEntries->saveXML();
		*/
		
		$elemReturn = $stmtEntries->fetchDOM('guestbook');
		return ($elemReturn);
	}
}

?>