<?php

//------------------------------------------------------------------------------
/**
*	@package solidBrickz[Forum]
*	@author	()((() [Oliver Müller]
*	@version 0.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* 
*/
class _guestbook_info extends InfoAction {
	
	function IInitialize() {
		Action::RequirePermission('GUESTBOOK_VIEW_INFO');
	}
	
	function &GenerateCustomInfo() {
		
		global $DB;
		global $PATH_LOCALTHEME;
		
		$tcTemplates = new TemplateCollection("$PATH_LOCALTHEME/templates/info.collection.html");
		$tInfo = $tcTemplates->ExtractTemplate('INFO');
		
		$tInfo->Embed('TITLE_GUESTBOOKINFOS',	gls('GUESTBOOK_TITLE_GUESTBOOKINFOS'));
		$tInfo->Embed('TITLE_NUMENTRIES',		gls('GUESTBOOK_TITLE_NUMENTRIES'));
		$tInfo->Embed('TITLE_LATESTENTRY',		gls('GUESTBOOK_TITLE_LATESTENTRY'));
		$tInfo->Embed('TITLE_OLDESTENTRY',		gls('GUESTBOOK_TITLE_OLDESTENTRY'));
		
		$rsInfo = $DB->SELECT('
						(SELECT COUNT(*)
						FROM '.TABLE_PREFIX.'_guestbook_entries
						) AS n_numentries,
						MIN(dt_created) AS dt_oldest,
						MAX(dt_created) AS dt_newest
			FROM		'.TABLE_PREFIX.'_guestbook_entries
		');
		$rsInfo->First();
		
		$tInfo->Embed('NUMENTRIES',		$rsInfo->Column('n_numentries'));
		$tInfo->Embed('LATESTENTRY',	datetime_mysql2local($rsInfo->Column('dt_newest'), LONGDATETIME));
		$tInfo->Embed('OLDESTENTRY',	datetime_mysql2local($rsInfo->Column('dt_oldest'), LONGDATETIME));
		
		return ($tInfo);
	}
}


?>