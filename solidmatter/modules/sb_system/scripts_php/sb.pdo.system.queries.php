<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

global $_QUERIES;

$_QUERIES['MAPPING']['{TABLE_SESSIONS}']	= '{PREFIX_SYSTEM}_system_sessions';
$_QUERIES['MAPPING']['{TABLE_FLATCACHE}']	= '{PREFIX_SYSTEM}_system_cache_flat';

//------------------------------------------------------------------------------
// session
//------------------------------------------------------------------------------

$_QUERIES['sbSystem/session/load'] = '
	SELECT		s_data AS data,
				n_lifespan AS lifespan,
				(UNIX_TIMESTAMP() - UNIX_TIMESTAMP(ts_created)) AS lifetime
	FROM		{TABLE_SESSIONS}
	WHERE		s_sessionid = :session_id
';
$_QUERIES['sbSystem/session/store'] = '
	INSERT INTO {TABLE_SESSIONS}
				(
					s_sessionid,
					s_data,
					ts_created,
					n_lifespan
				) VALUES (
					:session_id,
					:data,
					NOW(),
					:lifespan
				)
	ON DUPLICATE KEY UPDATE
				s_data = :data,
				ts_created = NOW()
';
$_QUERIES['sbSystem/session/destroy'] = '
	DELETE FROM	{TABLE_SESSIONS}
	WHERE		s_sessionid = :session_id
';
$_QUERIES['sbSystem/session/clear'] = '
	DELETE FROM	{TABLE_SESSIONS}
	WHERE		UNIX_TIMESTAMP() - UNIX_TIMESTAMP(ts_created) > n_lifespan
';

//------------------------------------------------------------------------------
// Flat Cache
//------------------------------------------------------------------------------

$_QUERIES['sbSystem/cache/flat/store'] = '
	INSERT INTO	{TABLE_FLATCACHE}
				(
					s_key,
					t_value
				) VALUES (
					:key,
					:data
				)
	ON DUPLICATE KEY UPDATE
				t_value = :data
';
$_QUERIES['sbSystem/cache/flat/load'] = '
	SELECT		t_value
	FROM		{TABLE_FLATCACHE}
	WHERE		s_key = :key
';
$_QUERIES['sbSystem/cache/flat/check'] = '
	SELECT		s_key
	FROM		{TABLE_FLATCACHE}
	WHERE		s_key = :key
';
$_QUERIES['sbSystem/cache/flat/clear'] = '
	DELETE FROM	{TABLE_FLATCACHE}
	WHERE		s_key LIKE :key
';
/*$_QUERIES['sbSystem/cache/flat/empty'] = '
	TRUNCATE TABLE {TABLE_FLATCACHE}
';*/

?>