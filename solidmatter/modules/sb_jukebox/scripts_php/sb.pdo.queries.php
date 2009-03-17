<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbJukebox]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

global $_QUERIES;

$_QUERIES['MAPPING']['{TABLE_JB_ALBUMS}']			= '{PREFIX_WORKSPACE}_jukebox_albums';
$_QUERIES['MAPPING']['{TABLE_JB_TRACKS}']			= '{PREFIX_WORKSPACE}_jukebox_tracks';
$_QUERIES['MAPPING']['{TABLE_JB_ARTISTS}']			= '{PREFIX_WORKSPACE}_jukebox_artists';
$_QUERIES['MAPPING']['{TABLE_JB_GENRES}']			= '{PREFIX_WORKSPACE}_jukebox_genres';
$_QUERIES['MAPPING']['{TABLE_JB_TRACKSGENRES}']		= '{PREFIX_WORKSPACE}_jukebox_tracks_genres';
$_QUERIES['MAPPING']['{TABLE_JB_BLACKLIST}']		= '{PREFIX_WORKSPACE}_jukebox_blacklist';
$_QUERIES['MAPPING']['{TABLE_JB_NOWPLAYING}']		= '{PREFIX_WORKSPACE}_jukebox_nowplaying';
$_QUERIES['MAPPING']['{TABLE_JB_HISTORY}']			= '{PREFIX_WORKSPACE}_jukebox_history';

//------------------------------------------------------------------------------
// nowplaying / history
//------------------------------------------------------------------------------

$_QUERIES['sbJukebox/nowPlaying/set'] = '
	INSERT INTO	{TABLE_JB_NOWPLAYING}
				(
					fk_user,
					fk_track,
					n_playtime,
					dt_played
				) VALUES (
					:user_uuid,
					:track_uuid,
					:playtime,
					NOW()
				)
	ON DUPLICATE KEY UPDATE
				fk_track = :track_uuid,
				n_playtime = :playtime,
				dt_played = NOW()
';
$_QUERIES['sbJukebox/nowPlaying/get'] = '
	SELECT		p.fk_track AS uuid,
				n.s_label AS label,
				p.fk_user AS useruuid,
				n2.s_label AS username,
				\'sbJukebox:Track\' as nodetype
	FROM		{TABLE_JB_NOWPLAYING} p
	INNER JOIN	{TABLE_NODES} n
		ON		n.uuid = p.fk_track
	INNER JOIN	{TABLE_NODES} n2
		ON		n2.uuid = p.fk_user
	ORDER BY	n.s_label
';
$_QUERIES['sbJukebox/nowPlaying/clear'] = '
	DELETE FROM	{TABLE_JB_NOWPLAYING}
	WHERE		UNIX_TIMESTAMP() - UNIX_TIMESTAMP(dt_played) > :seconds
		AND		UNIX_TIMESTAMP() - UNIX_TIMESTAMP(dt_played) > n_playtime
';

$_QUERIES['sbJukebox/history/set'] = '
	INSERT INTO	{TABLE_JB_HISTORY}
				(
					fk_user,
					fk_track,
					n_playtime,
					dt_played
				) VALUES (
					:user_uuid,
					:track_uuid,
					:playtime,
					NOW()
				)
';

//------------------------------------------------------------------------------
// charts
//------------------------------------------------------------------------------

$_QUERIES['sbJukebox/jukebox/getVoters'] = '
	SELECT		n.uuid,
				n.s_label AS label,
				n.fk_nodetype
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_VOTES} v
		ON		n.uuid = v.fk_user
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		v.fk_subject = h.fk_child
	WHERE		h.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
	ORDER BY	n.s_label
';
$_QUERIES['sbJukebox/jukebox/various/getTop'] = '
	SELECT		n.uuid,
				n.s_label AS label,
				n.s_name AS name,
				v.n_vote AS vote,
				:nodetype as nodetype
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	INNER JOIN	{TABLE_VOTES} v
		ON		n.uuid = v.fk_subject
	WHERE		h.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
		AND		n.fk_nodetype = :nodetype
		AND		v.fk_user = :user_uuid
	ORDER BY	n_vote DESC
	LIMIT		0, :limit
';
$_QUERIES['sbJukebox/history/getTop/base'] = '
	SELECT		n.uuid,
				n.s_label AS label,
				n.s_name AS name,
				COUNT(*) AS times_played,
				\'sbJukebox:Track\' as nodetype
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_JB_HISTORY} hi
		ON		n.uuid = hi.fk_track
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	WHERE		h.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
		AND		UNIX_TIMESTAMP() - UNIX_TIMESTAMP(hi.dt_played) < :timeframe
';
$_QUERIES['sbJukebox/history/getTop/allUsers'] = $_QUERIES['sbJukebox/history/getTop/base'].'
	GROUP BY	hi.fk_track
	ORDER BY	COUNT(*) DESC
	LIMIT		0, :limit
';
$_QUERIES['sbJukebox/history/getTop/byUser'] = $_QUERIES['sbJukebox/history/getTop/base'].'
		AND		hi.fk_user = :user_uuid
	GROUP BY	hi.fk_track
	ORDER BY	COUNT(*) DESC
	LIMIT		0, :limit
';

//------------------------------------------------------------------------------
// jukebox
//------------------------------------------------------------------------------

$_QUERIES['sbJukebox/jukebox/gatherInfo'] = '
	SELECT		(SELECT COUNT(*)
					FROM		{TABLE_HIERARCHY} h
					INNER JOIN	{TABLE_NODES} n
						ON		h.fk_child = n.uuid
					WHERE		n.fk_nodetype = \'sbJukebox:Album\'
						AND		h.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
				) AS n_numalbums,
				(SELECT COUNT(*)
					FROM		{TABLE_HIERARCHY} h
					INNER JOIN	{TABLE_NODES} n
						ON		h.fk_child = n.uuid
					WHERE		n.fk_nodetype = \'sbJukebox:Artist\'
						AND		h.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
				) AS n_numartists,
				(SELECT COUNT(*)
					FROM		{TABLE_HIERARCHY} h
					INNER JOIN	{TABLE_NODES} n
						ON		h.fk_child = n.uuid
					WHERE		n.fk_nodetype = \'sbJukebox:Track\'
						AND		h.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
				) AS n_numtracks,
				(SELECT COUNT(*)
					FROM		{TABLE_HIERARCHY} h
					INNER JOIN	{TABLE_NODES} n
						ON		h.fk_child = n.uuid
					WHERE		n.fk_nodetype = \'sbJukebox:Playlist\'
						AND		h.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
				) AS n_numplaylists
';
$_QUERIES['sbJukebox/jukebox/search/anything/byLabel'] = '
	SELECT		n.uuid,
				n.fk_nodetype as nodetype,
				n.s_label AS label,
				n.s_name AS name
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_NODETYPES} nt
		ON		n.fk_nodetype = nt.s_type
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	WHERE		h.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
		AND		n.s_label LIKE :searchstring
	ORDER BY	n.fk_nodetype,
				n.s_label
';
$_QUERIES['sbJukebox/jukebox/search/various/byLabel'] = '
	SELECT		n.uuid,
				n.fk_nodetype as nodetype,
				n.s_label AS label,
				n.s_name AS name,
				(SELECT 	n_vote 
					FROM	{TABLE_VOTES} v
					WHERE	v.fk_subject = n.uuid
						AND	v.fk_user = :user_uuid
				) AS vote
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	WHERE		h.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
		AND		n.fk_nodetype = :nodetype
		AND		n.s_label LIKE :searchstring
	ORDER BY	n.s_label
';
$_QUERIES['sbJukebox/jukebox/search/various/numeric'] = '
	SELECT		n.uuid,
				n.fk_nodetype as nodetype,
				n.s_label AS label,
				n.s_name AS name,
				(SELECT 	n_vote 
					FROM	{TABLE_VOTES} v
					WHERE	v.fk_subject = n.uuid
						AND	v.fk_user = :user_uuid
				) AS vote
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	WHERE		h.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
		AND		n.fk_nodetype = :nodetype
		AND		n.s_label REGEXP \'^[0-9]\'
	ORDER BY	n.s_label
';
$_QUERIES['sbJukebox/jukebox/search/albums/byLabel'] = '
	SELECT		n.uuid,
				n.fk_nodetype as nodetype,
				n.s_label AS label,
				n.s_name AS name,
				/*a.b_coverexists AS coverexists,*/
				a.n_published AS published,
				(SELECT 	n_vote 
					FROM	{TABLE_VOTES} v
					WHERE	v.fk_subject = n.uuid
						AND	v.fk_user = :user_uuid
				) AS vote
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_JB_ALBUMS} a
		ON		n.uuid = a.uuid
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	WHERE		h.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
		AND		n.s_label LIKE :searchstring
	ORDER BY	n.s_label
';
$_QUERIES['sbJukebox/jukebox/search/albums/numeric'] = '
	SELECT		n.uuid,
				n.fk_nodetype as nodetype,
				n.s_label AS label,
				n.s_name AS name,
				/*a.b_coverexists AS coverexists,*/
				a.n_published AS published,
				(SELECT 	n_vote 
					FROM	{TABLE_VOTES} v
					WHERE	v.fk_subject = n.uuid
						AND	v.fk_user = :user_uuid
				) AS vote
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_JB_ALBUMS} a
		ON		n.uuid = a.uuid
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	WHERE		h.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
		AND		n.s_label REGEXP \'^[0-9]\'
	ORDER BY	n.s_label
';
// NOTE: optimized via RAND() on well-indexed column (see example below or http://www.paperplanes.de/archives/2008/4/24/mysql_nonos_order_by_rand/)
//SELECT USERS.* FROM (SELECT ID FROM USERS WHERE IS_ACTIVE = 1
//ORDER BY RAND() LIMIT 20)  
//AS RANDOM_USERS JOIN USERS ON USERS.ID = RANDOM_USERS.ID
$_QUERIES['sbJukebox/jukebox/albums/getRandom'] = '
	SELECT		n.uuid,
				n.fk_nodetype as nodetype,
				n.s_label AS label,
				n.s_name AS name,
				a.n_published AS published,
				(SELECT 	n_vote 
					FROM	{TABLE_VOTES} v
					WHERE	v.fk_subject = n.uuid
						AND	v.fk_user = :user_uuid
				) AS vote
	FROM		(SELECT			uuid
					FROM		{TABLE_JB_ALBUMS} a2
					INNER JOIN	{TABLE_HIERARCHY} h
						ON		a2.uuid = h.fk_child
					WHERE		h.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
					ORDER BY 	RAND() 
					LIMIT :limit
				) as rand
	INNER JOIN	{TABLE_NODES} n
		ON		rand.uuid = n.uuid
	INNER JOIN	{TABLE_JB_ALBUMS} a
		ON		a.uuid = n.uuid
';
$_QUERIES['sbJukebox/jukebox/albums/getLatest'] = '
	SELECT		n.uuid,
				n.fk_nodetype as nodetype,
				n.s_label AS label,
				n.s_name AS name,
				a.b_coverexists,
				(SELECT 	n_vote 
					FROM	{TABLE_VOTES} v
					WHERE	v.fk_subject = n.uuid
						AND	v.fk_user = :user_uuid
				) AS vote
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_JB_ALBUMS} a
		ON		n.uuid = a.uuid
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		a.uuid = h.fk_child
	WHERE		h.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
		AND		n.fk_nodetype = :nodetype
	ORDER BY	n.dt_created DESC
	LIMIT		0, :limit
';
$_QUERIES['sbJukebox/jukebox/albums/getAll'] = '
	SELECT		n.uuid,
				n.s_label AS label,
				n.s_name AS name,
				a.b_coverexists
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_JB_ALBUMS} a
		ON		n.uuid = a.uuid
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		a.uuid = h.fk_child
	WHERE		h.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
	ORDER BY	n.s_label ASC
';
$_QUERIES['sbJukebox/jukebox/artists/getAll'] = '
	SELECT		n.uuid,
				n.s_label AS label
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	WHERE		h.fk_parent = :jukebox_uuid
		AND		n.fk_nodetype = \'sbJukebox:Artist\'
	ORDER BY	n.s_label ASC
';
$_QUERIES['sbJukebox/jukebox/artists/getRandom'] = '
	SELECT		n.uuid,
				n.fk_nodetype as nodetype,
				n.s_label AS label,
				n.s_name AS name,
				(SELECT			v.n_vote
					FROM		{TABLE_VOTES} v
					WHERE		v.fk_user = :user_uuid
						AND		v.fk_subject = n.uuid
				) AS vote
	FROM		(SELECT 		n1.uuid
					FROM		{TABLE_NODES} n1
					INNER JOIN	{TABLE_HIERARCHY} h1
						ON		n1.uuid = h1.fk_child
					WHERE		n1.fk_nodetype = :nodetype
						AND		h1.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
					ORDER BY	RAND() LIMIT :limit
				) as rand
	INNER JOIN	{TABLE_NODES} n
		ON		rand.uuid = n.uuid
';
/*$_QUERIES['sbJukebox/jukebox/artists/getRandom'] = '
	SELECT		n.uuid,
				n.fk_nodetype as nodetype,
				n.s_label AS label,
				n.s_name AS name,
				v.n_vote AS vote
				
	FROM		(SELECT 		n1.uuid
					FROM		{TABLE_NODES} n1
					INNER JOIN	{TABLE_HIERARCHY} h1
						ON		n1.uuid = h1.fk_child
					WHERE		n1.fk_nodetype = :nodetype
						AND		h1.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
					ORDER BY	RAND() LIMIT :limit
				) as rand
	INNER JOIN	{TABLE_NODES} n
		ON		rand.uuid = n.uuid
	LEFT JOIN	{TABLE_VOTES} v
		ON		n.uuid = v.fk_subject
		AND		v.fk_user = :user_uuid
	
';
/*$_QUERIES['sbJukebox/jukebox/artists/getRandom'] = '
	SELECT		n.uuid,
				n.fk_nodetype as nodetype,
				n.s_label AS label,
				n.s_name AS name,
				(SELECT			v.n_vote
					FROM		{TABLE_VOTES} v
					WHERE		v.fk_user = :user_uuid
						AND		v.fk_subject = n.uuid
				) AS vote
	FROM		(
					SELECT 		fk_child as uuid
					FROM		(
									SELECT 		n1.uuid
									FROM 		{TABLE_NODES} n1
									WHERE		n1.fk_nodetype = :nodetype
								) as filtered
					INNER JOIN	{TABLE_HIERARCHY} h
						ON		filtered.uuid = h.fk_child
					WHERE		s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
					ORDER BY	RAND() LIMIT :limit
				) as rand
	INNER JOIN	{TABLE_NODES} n
		ON		rand.uuid = n.uuid
';*/
$_QUERIES['sbJukebox/jukebox/comments/getLatest'] = '
	SELECT		nc.uuid,
				nc.dt_created AS created,
				nu.s_label AS username,
				ni.s_label AS item_label,
				ni.uuid AS item_uuid
	FROM		{TABLE_NODES} nc
	INNER JOIN	{TABLE_NODES} nu
		ON		nc.fk_createdby = nu.uuid
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		nc.uuid = h.fk_child
	INNER JOIN	{TABLE_NODES} ni
		ON		h.fk_parent = ni.uuid
	WHERE		h.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
		AND		nc.fk_nodetype = \'sbSystem:Comment\'
	ORDER BY	nc.dt_created DESC
	LIMIT		0, :limit
';

//------------------------------------------------------------------------------
// artist
//------------------------------------------------------------------------------

$_QUERIES['sbJukebox/artist/getTracks/differentAlbums'] = '
	SELECT		n.uuid,
				n.s_label AS label,
				t.n_playtime AS playtime,
				n2.uuid AS albumuuid,
				n2.s_label as albumlabel
	FROM		{TABLE_JB_TRACKS} jt
	INNER JOIN	{TABLE_NODES} n
		ON		n.uuid = jt.uuid
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	INNER JOIN	{TABLE_NODES} n2
		ON		n2.uuid = h.fk_parent
	INNER JOIN	{TABLE_JB_TRACKS} t
		ON		n.uuid = t.uuid
	WHERE		h.s_mpath LIKE CONCAT(:jukebox_mpath, \'%\')
		AND		jt.fk_artist = :artist_uuid
		AND		n2.fk_nodetype = \'sbJukebox:Album\'
		AND		h.fk_parent NOT IN (
					SELECT	fk_child
					FROM	{TABLE_HIERARCHY}
					WHERE	fk_parent = :artist_uuid
				)
	ORDER BY	n2.s_label, n.s_label
	LIMIT		0, :limit
';

//------------------------------------------------------------------------------
// album
//------------------------------------------------------------------------------

$_QUERIES['sbJukebox/album/properties/load/auxiliary'] = '
	SELECT		fk_artist,
				s_title,
				n_published,
				b_coverexists,
				s_coverfilename,
				n_coverlightness,
				n_coverhue,
				n_coversaturation,
				s_relpath,
				s_abspath
	FROM		{TABLE_JB_ALBUMS}
	WHERE		uuid = :node_id
';
$_QUERIES['sbJukebox/album/properties/save/auxiliary'] = '
	INSERT INTO {TABLE_JB_ALBUMS}
				(
					uuid,
					fk_artist,
					s_title,
					n_published,
					b_coverexists,
					s_coverfilename,
					n_coverhue,
					n_coversaturation,
					n_coverlightness,
					s_relpath,
					s_abspath
				) VALUES (
					:node_id,
					:info_artist,
					:info_title,
					:info_published,
					:info_coverexists,
					:info_coverfilename,
					:ext_coverhue,
					:ext_coversaturation,
					:ext_coverlightness,
					:info_relpath,
					:info_abspath
					)
	ON DUPLICATE KEY UPDATE
				fk_artist = :info_artist,
				s_title = :info_title,
				n_published = :info_published,
				b_coverexists = :info_coverexists,
				s_coverfilename = :info_coverfilename,
				n_coverhue = :ext_coverhue,
				n_coversaturation = :ext_coversaturation,
				n_coverlightness = :ext_coverlightness,
				s_relpath = :info_relpath,
				s_abspath = :info_abspath
';
$_QUERIES['sbJukebox/album/quilt/findCover'] = '
	SELECT		n.uuid,
				n.s_label AS label,
				n.s_name AS name
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_JB_ALBUMS} a
		ON		n.uuid = a.uuid
	WHERE		h.s_mpath LIKE :jukebox_mpath
	ORDER BY 	ROUND(ABS(a.n_coverlightness - :lightness) / 4) + 
				ROUND(ABS(a.n_coverhue - :hue) / 8) + 
				ROUND(ABS(a.n_coversaturation - :saturation) / 12),
				ABS(a.n_coverlightness - :lightness),
				ABS(a.n_coverhue - :hue),
				ABS(a.n_coversaturation - :saturation)
	LIMIT		0, 1
';

//------------------------------------------------------------------------------
// track
//------------------------------------------------------------------------------

$_QUERIES['sbJukebox/track/properties/load/auxiliary'] = '
	SELECT		jt.s_title,
				jt.s_filename,
				jt.fk_artist,
				jt.n_index,
				jt.n_published,
				jt.s_playtime,
				jt.n_playtime,
				jt.s_mode,
				jt.n_bitrate,
				/*CONCAT(
					(SELECT s_relpath 
						FROM {PREFIX_WORKSPACE}_jukebox_albums
						WHERE uuid = (SELECT fk_parent 
							FROM	{PREFIX_WORKSPACE}_system_nodes_parents
							WHERE	fk_child = :node_id
								AND	b_primary = \'TRUE\'
						)
					),
					jt.s_filename
				) AS s_fullpath*/
				\'DEACTIVATED\' AS s_fullpath
	FROM		{TABLE_JB_TRACKS} jt
	WHERE		jt.uuid = :node_id
';
$_QUERIES['sbJukebox/track/properties/save/auxiliary'] = '
	INSERT INTO {TABLE_JB_TRACKS}
				(
					uuid,
					s_filename,
					fk_artist,
					s_title,
					n_index,
					n_published,
					s_playtime,
					n_playtime,
					s_mode,
					n_bitrate
				) VALUES (
					:node_id,
					:info_filename,
					:info_artist,
					:info_title,
					:info_index,
					:info_published,
					:info_playtime,
					:enc_playtime,
					:enc_mode,
					:enc_bitrate
				)
	ON DUPLICATE KEY UPDATE
				s_filename = :info_filename,
				fk_artist = :info_artist,
				n_index = :info_index,
				n_published = :info_published,
				s_playtime = :info_playtime,
				n_playtime = :enc_playtime,
				s_mode = :enc_mode,
				n_bitrate = :enc_bitrate
';

?>