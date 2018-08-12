<?php

//------------------------------------------------------------------------------
/**
 *	@package solidMatter[sbSystem]
 *	@author	()((() [Oliver Müller]
 *	@version 1.00.00
 */
//------------------------------------------------------------------------------

global $_QUERIES;

$_QUERIES['sbSystem/sbUUID/getAllUUIDs'] = '
	SELECT		uuid,
				fk_nodetype
	FROM		{TABLE_NODES}
	ORDER BY	dt_created
';

$_QUERIES['sbSystem/sbUUID/updateRoot'] = '

	SET FOREIGN_KEY_CHECKS = 0;
	
	UPDATE		{TABLE_HIERARCHY}
	SET			fk_parent = "0000000000000000000000"
	WHERE		fk_parent = "00000000000000000000000000000000";

	UPDATE		{TABLE_HIERARCHY}
	SET			fk_child = "0000000000000000000000"
	WHERE		fk_child = "00000000000000000000000000000000";
	
	SET FOREIGN_KEY_CHECKS = 1;

';


$_QUERIES['sbSystem/sbUUID/updateUUID'] = '
	UPDATE		{TABLE_NODES}
	SET			uuid = :uuid_new
	WHERE		uuid = :uuid_old
';
$_QUERIES['sbSystem/sbUUID/updateEventlog/subject'] = '
	UPDATE		{TABLE_EVENTLOG}
	SET			fk_subject = :uuid_new
	WHERE		fk_subject = :uuid_old
';
$_QUERIES['sbSystem/sbUUID/updateEventlog/user'] = '
	UPDATE		{TABLE_EVENTLOG}
	SET			fk_user = :uuid_new
	WHERE		fk_user = :uuid_old
';
$_QUERIES['sbSystem/sbUUID/updateNodes/created'] = '
	UPDATE		{TABLE_NODES}
	SET			fk_createdby = :uuid_new
	WHERE		fk_createdby = :uuid_old
';
$_QUERIES['sbSystem/sbUUID/updateNodes/modified'] = '
	UPDATE		{TABLE_NODES}
	SET			fk_modifiedby = :uuid_new
	WHERE		fk_modifiedby = :uuid_old
';
// check this
$_QUERIES['sbSystem/sbUUID/updateProperties'] = '
	UPDATE		{TABLE_PROPERTIES}
	SET			m_content = :uuid_new
	WHERE		m_content = :uuid_old
';
$_QUERIES['sbSystem/sbUUID/updateRegistry'] = '
	UPDATE		{TABLE_REGVALUES}
	SET			fk_user = :uuid_new
	WHERE		fk_user = :uuid_old
';

// jukebox
$_QUERIES['sbSystem/sbUUID/updateAlbums/artist'] = '
	UPDATE		{TABLE_JB_ALBUMS}
	SET			fk_artist = :uuid_new
	WHERE		fk_artist = :uuid_old
';
$_QUERIES['sbSystem/sbUUID/updateAlbums/albumartist'] = '
	UPDATE		{TABLE_JB_ALBUMS}
	SET			fk_albumartist = :uuid_new
	WHERE		fk_albumartist = :uuid_old
';
$_QUERIES['sbSystem/sbUUID/updateTracks'] = '
	UPDATE		{TABLE_JB_TRACKS}
	SET			fk_album = :uuid_new
	WHERE		fk_album = :uuid_old
';
$_QUERIES['sbSystem/sbUUID/updateAlbumhistory/user'] = '
	UPDATE		{TABLE_JB_HISTORY_ALBUMS}
	SET			fk_user = :uuid_new
	WHERE		fk_user = :uuid_old
';
$_QUERIES['sbSystem/sbUUID/updateTrackhistory/user'] = '
	UPDATE		{TABLE_JB_HISTORY_TRACKS}
	SET			fk_user = :uuid_new
	WHERE		fk_user = :uuid_old
';

$_QUERIES['sbSystem/sbUUID/alterTables'] = '
	UPDATE		{TABLE_JB_HISTORY_TRACKS}
	SET			fk_user = :uuid_new
	WHERE		fk_user = :uuid_old
';



?>