<?php

//------------------------------------------------------------------------------
/**
*	@package solidBrickz
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* 
*/
class guestbook_embed_entries extends Action {

	var $iAccess		= OPEN;
	var $iType			= EMBEDABLE;
	var $bLoginRequired = FALSE;
	var $bSetLinkback	= FALSE;
	var $bUseLocale		= TRUE;
	
	function Initialize() {
		Action::StoreParameter('mode', 		SET,		OPTIONAL,	'random');
		Action::StoreParameter('limit', 	SET,		OPTIONAL,	1);
	}

	function &GenerateOutput () {
		
		global $DB;
		global $PATH_LOCALTHEME;
		
		$tcEntry	= new TemplateCollection("$PATH_LOCALTHEME/templates/embedentry.collection.html");
		$tContainer	= $tcEntry->ExtractTemplate('CONTAINER');
		$tEntry		= $tcEntry->ExtractTemplate('ENTRY');
		
		switch ($this->aParameters['mode']) {
			
			case 'latest':
				
				if ($this->aParameters['limit'] == 1) {
					$tContainer->Embed('TITLE', gls('GUESTBOOK_TITLE_LATESTENTRY'));
				} else {
					$tContainer->Embed('TITLE', gls('GUESTBOOK_TITLE_LATESTENTRIES'));
				}
				
				$rsEntries = $DB->SELECT('
								s_name,
								t_comment,
								dt_created
					FROM		'.TABLE_PREFIX.'_guestbook_entries
					ORDER BY	dt_created DESC
					LIMIT		1, '.number_format($this->aParameters['limit'], 0).'
				');
				
				break;
			
		    case 'random':
				
				if ($this->aParameters['limit'] == 1) {
					$tContainer->Embed('TITLE', gls('GUESTBOOK_TITLE_RANDOMENTRY'));
				} else {
					$tContainer->Embed('TITLE', gls('GUESTBOOK_TITLE_RANDOMENTRIES'));
				}
				
				$rsEntries = $DB->SELECT('
								s_name,
								t_comment,
								dt_created
					FROM		'.TABLE_PREFIX.'_guestbook_entries
					ORDER BY	RAND()
					LIMIT		1, '.number_format($this->aParameters['limit'], 0).'
				');
				
				break;
		}
		
		$iMask = 0;
		if (get_config('guestbook', 'ALLOW_SMILIES') == 'TRUE') {
			$iMask = $iMask | SMILIES;
		}
		if (get_config('guestbook', 'ALLOW_ICONS') == 'TRUE') {
			$iMask = $iMask | ICONS;
		}
		if (get_config('guestbook', 'ALLOW_LINKS') == 'TRUE') {
			$iMask = $iMask | URLS;
		}
		
		while ($rsEntries->Next()) {
			$sComment = $rsEntries->Column('t_comment');
			if (mb_strlen($sComment) > get_config('guestbook', 'EMBED_CUTOFF')) {
				$sComment = mb_strimwidth($sComment, 0, get_config('guestbook', 'EMBED_CUTOFF'), '...');
			}
			$sComment = str_replace(array("\r\n", "\r", "\n", "\t"), array(' ', ' ', ' ', ' '), $sComment);
			$sComment = mb_wordcut($sComment, 20);
			$tEntry->Embed('ENTRY_NAME', $rsEntries->Column('s_name'), HTML);
			$tEntry->Embed('ENTRY_COMMENT', $sComment, HTML|$iMask);
			$tEntry->Append();
		}
		
		$tContainer->Embed('ENTRY_MORE', gls('GUESTBOOK_TITLE_MORE'), HTML);
		$tContainer->Embed('URL_MORE', 'index.php?module=guestbook&action=show_entries', HTML);
		$tContainer->Embed('ENTRIES', $tEntry);
		
		return ($tContainer);
	}
}

?>