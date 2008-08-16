<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Tools
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

function rebuild_nestedsets($iNodeID = NULL, $iCounter = 1) {
	
	global $DB;
	
	if ($iNodeID == NULL) {
		$stmtNodes = $DB->query('
			SELECT		id
			FROM		'.TABLE_PREFIX.'_system_nodes
			WHERE		fk_parent_id IS NULL
			ORDER BY	n_position
		');
	} else {
		$stmtNodes = $DB->query('
			SELECT		id
			FROM		'.TABLE_PREFIX.'_system_nodes
			WHERE		fk_parent_id = '.$iNodeID.'
			ORDER BY	n_position
		');
	}
	
	$aResultset = $stmtNodes->fetchALL(PDO::FETCH_ASSOC);
	
	foreach ($aResultset as $iRownumber => $aRow) {
		$DB->query('
			UPDATE 	'.TABLE_PREFIX.'_system_nodes
			SET		n_left = '.$iCounter.'
			WHERE	id = '.$aRow['id'].'
		');
		$iCounter++;
		rebuild_nestedsets($aRow['id'], &$iCounter);
		$DB->query('
			UPDATE 	'.TABLE_PREFIX.'_system_nodes
			SET		n_right = '.$iCounter.'
			WHERE	id = '.$aRow['id'].'
		');
		$iCounter++;
	}
	
	
}

function rebuild_positions($iNodeID = NULL) {
	
	global $DB;
	
	if ($iNodeID == NULL) {
		$stmtNodes = $DB->query('
			SELECT		id
			FROM		'.TABLE_PREFIX.'_system_nodes
			WHERE		fk_parent_id IS NULL
			ORDER BY	n_position
		');
	} else {
		$stmtNodes = $DB->query('
			SELECT		id
			FROM		'.TABLE_PREFIX.'_system_nodes
			WHERE		fk_parent_id = '.$iNodeID.'
			ORDER BY	n_position
		');
	}
	
	$aResultset = $stmtNodes->fetchALL(PDO::FETCH_ASSOC);
	
	$iCounter = 0;
	foreach ($aResultset as $iRownumber => $aRow) {
		$DB->query('
			UPDATE 	'.TABLE_PREFIX.'_system_nodes
			SET		n_position = '.$iCounter.'
			WHERE	id = '.$aRow['id'].'
		');
		$iCounter++;
		rebuild_positions($aRow['id']);
	}
	
}






?>