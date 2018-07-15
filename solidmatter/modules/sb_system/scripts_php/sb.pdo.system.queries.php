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

$_QUERIES['MAPPING']['{TABLE_IMAGECACHE}']	= '{PREFIX_WORKSPACE}_system_cache_images';
$_QUERIES['MAPPING']['{TABLE_PROGRESS}']	= '{PREFIX_WORKSPACE}_system_progress';
$_QUERIES['MAPPING']['{TABLE_EVENTLOG}']	= '{PREFIX_WORKSPACE}_system_eventlog';
$_QUERIES['MAPPING']['{TABLE_MIMETYPES}']	= '{PREFIX_REPOSITORY}_nodetypes_mimetypemapping';
$_QUERIES['MAPPING']['{TABLE_MODULES}']		= '{PREFIX_REPOSITORY}_modules';

$_QUERIES['MAPPING']['{TABLE_USERS}']		= '{PREFIX_WORKSPACE}_system_useraccounts';
$_QUERIES['MAPPING']['{TABLE_REGISTRY}']	= '{PREFIX_REPOSITORY}_registry';
$_QUERIES['MAPPING']['{TABLE_REGVALUES}']	= '{PREFIX_WORKSPACE}_system_registry_values';

$_QUERIES['MAPPING']['{TABLE_VOTES}']		= '{PREFIX_WORKSPACE}_system_nodes_votes';
$_QUERIES['MAPPING']['{TABLE_NODETAGS}']	= '{PREFIX_WORKSPACE}_system_nodes_tags';
$_QUERIES['MAPPING']['{TABLE_TAGS}']		= '{PREFIX_WORKSPACE}_system_tags';

$_QUERIES['MAPPING']['{TABLE_ONTOLOGY}']	= '{PREFIX_REPOSITORY}_nodetypes_ontology';
$_QUERIES['MAPPING']['{TABLE_RELATIONS}']	= '{PREFIX_WORKSPACE}_system_nodes_relations';

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
// ImageCache
//------------------------------------------------------------------------------

$_QUERIES['sb_system/cache/images/store'] = '
	INSERT INTO	{TABLE_IMAGECACHE}
				(
					fk_image,
					fk_filterstack,
					e_mode,
					m_content
				) VALUES (
					:image,
					:filterstack,
					:mode,
					:content
				)
';
$_QUERIES['sb_system/cache/images/load'] = '
	SELECT		m_content
	FROM		{TABLE_IMAGECACHE}
	WHERE		fk_image = :image
		AND		fk_filterstack = :filterstack
		AND		e_mode = :mode
';
$_QUERIES['sb_system/cache/images/clear/byImage'] = '
	DELETE FROM	{TABLE_IMAGECACHE}
	WHERE		fk_image = :filterstack
';
$_QUERIES['sb_system/cache/images/clear/byFilterstack'] = '
	DELETE FROM	{TABLE_IMAGECACHE}
	WHERE		fk_filterstack = :filterstack
';
$_QUERIES['sb_system/cache/images/empty'] = '
	DELETE FROM {TABLE_IMAGECACHE}
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

//------------------------------------------------------------------------------
// Progress
//------------------------------------------------------------------------------

$_QUERIES['sb_system/progress/update'] = '
	INSERT INTO	{TABLE_PROGRESS}
				(
					fk_user,
					fk_subject,
					s_uid,
					s_status,
					n_percentage
				) VALUES (
					:user_uuid,
					:subject_uuid,
					:uid,
					:status,
					:percentage
				)
	ON DUPLICATE KEY UPDATE
				s_status = :status,
				n_percentage = :percentage
';
$_QUERIES['sb_system/progress/getStatus'] = '
	SELECT		s_status,
				n_percentage
	FROM		{TABLE_PROGRESS}
	WHERE		fk_user = :user_uuid
		AND		fk_subject = :subject_uuid
		AND		s_uid = :uid
';
$_QUERIES['sb_system/progress/remove'] = '
	DELETE FROM	{TABLE_PROGRESS}
	WHERE		fk_user = :user_uuid
		AND		fk_subject = :subject_uuid
		AND		s_uid = :uid
';

//------------------------------------------------------------------------------
// Event log
//------------------------------------------------------------------------------

$_QUERIES['sbSystem/eventLog/LogEntry'] = '
	INSERT INTO	{TABLE_EVENTLOG}
				(
					fk_module,
					s_loguid,
					t_log,
					fk_subject,
					fk_user,
					e_type,
					dt_created
				) VALUES (
					:module,
					:loguid,
					:logtext,
					:subject,
					:user,
					:type,
					NOW()
				)
';
$_QUERIES['sbSystem/eventLog/getEntries/filtered'] = '
	SELECT 		el.id,
				el.fk_module,
				el.s_loguid,
				el.t_log,
				el.fk_subject,
				REPLACE(ns.fk_nodetype, \':\', \'_\') AS s_subjectdisplaytype,
				el.fk_user,
				el.e_type,
				el.dt_created,
				nu.s_label AS username,
				ns.s_label AS subjectlabel
	FROM		{TABLE_EVENTLOG} el
	LEFT JOIN	{TABLE_NODES} nu
		ON		el.fk_user = nu.uuid
	LEFT JOIN	{TABLE_NODES} ns
		ON		el.fk_subject = ns.uuid
	WHERE		el.fk_module LIKE :module
		AND		el.e_type LIKE :type
		AND		el.s_loguid LIKE :loguid
	ORDER BY	el.dt_created DESC
	LIMIT		0, 1000
';

//------------------------------------------------------------------------------
// Voting
//------------------------------------------------------------------------------

$_QUERIES['sbSystem/voting/placeVote'] = '
	INSERT INTO	{TABLE_VOTES}
				(
					fk_subject,
					fk_user,
					n_vote
				) VALUES (
					:subject_uuid,
					:user_uuid,
					:vote
				)
	ON DUPLICATE KEY UPDATE
				n_vote = :vote
';
$_QUERIES['sbSystem/voting/removeVote'] = '
	DELETE FROM	{TABLE_VOTES}
	WHERE		fk_subject = :subject_uuid
		AND		fk_user = :user_uuid
';
$_QUERIES['sbSystem/voting/removeAllVotes'] = '
	DELETE FROM	{TABLE_VOTES}
	WHERE		fk_subject = :subject_uuid
';
$_QUERIES['sbSystem/voting/getVote/byUser'] = '
	SELECT		n_vote
	FROM		{TABLE_VOTES}
	WHERE		fk_subject = :subject_uuid
		AND		fk_user = :user_uuid
';
$_QUERIES['sbSystem/voting/getVote/average'] = '
	SELECT		AVG(n_vote) AS n_average
	FROM		{TABLE_VOTES}
	WHERE		fk_subject = :subject_uuid
		AND		fk_user <> :ignore_uuid
	GROUP BY	fk_subject
';
$_QUERIES['sbSystem/voting/getVotes'] = '
	SELECT		fk_user AS user_uuid,
				n_vote AS vote
	FROM		{TABLE_VOTES}
	WHERE		fk_subject = :subject_uuid
';
$_QUERIES['sbSystem/voting/getUserVotes'] = '
	SELECT		v.fk_user AS user_uuid,
				n.s_label AS user_label,
				v.n_vote AS vote
	FROM		{TABLE_VOTES} v
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		v.fk_user = h.fk_child
	INNER JOIN	{TABLE_NODES} n
		ON		h.fk_child = n.uuid
	WHERE		v.fk_subject = :subject_uuid
		AND		h.b_primary = \'TRUE\'
';

//------------------------------------------------------------------------------
// Tagging
//------------------------------------------------------------------------------

// FIXME: shouldn't need to update to same value --> bug in hasTag()
$_QUERIES['sbSystem/tagging/node/addTag'] = '
	INSERT INTO	{TABLE_NODETAGS}
				(
					fk_subject,
					fk_tag
				) VALUES (
					:subject_uuid,
					:tag_id
				)
	ON DUPLICATE KEY UPDATE
				fk_tag = :tag_id
';
$_QUERIES['sbSystem/tagging/node/removeTag'] = '
	DELETE FROM	{TABLE_NODETAGS}
	WHERE		fk_subject = :subject_uuid
		AND		fk_tag = :tag_id
';
$_QUERIES['sbSystem/tagging/node/getTags'] = '
	SELECT		t.id,
				t.s_tag
	FROM		{TABLE_TAGS} t
	INNER JOIN	{TABLE_NODETAGS} nt
		ON		t.id = nt.fk_tag
	WHERE		nt.fk_subject = :subject_uuid
	ORDER BY 	t.s_tag
';
$_QUERIES['sbSystem/tagging/node/getBranchTags'] = '
	SELECT		t.id,
				t.s_tag,
				t.n_popularity,
				t.n_customweight,
				COUNT(*) AS n_numitemstagged
	FROM		{TABLE_TAGS} t
	INNER JOIN	{TABLE_NODETAGS} nt
		ON		t.id = nt.fk_tag
	WHERE		t.id IN (
					SELECT		fk_tag
					FROM		{TABLE_NODETAGS} nt2
					INNER JOIN	{TABLE_NODES} n
						ON		n.uuid = nt2.fk_subject
					INNER JOIN	{TABLE_HIERARCHY} h
						ON		n.uuid = h.fk_child
					WHERE		h.s_mpath LIKE :root_mpath
				)
		AND		t.e_visibility <> \'HIDDEN\'
	GROUP BY	t.id
	ORDER BY	t.s_tag
';
$_QUERIES['sbSystem/tagging/tags/getMatchingTags'] = '
	SELECT		t.s_tag AS tag
	FROM		{TABLE_TAGS} t
	WHERE		t.s_tag LIKE :substring
		AND		t.e_visibility <> \'HIDDEN\'
	ORDER BY	t.s_tag
';
$_QUERIES['sbSystem/tagging/tags/getID'] = '
	SELECT		id
	FROM		{TABLE_TAGS}
	WHERE		LOWER(s_tag) = LOWER(:tag)
';
$_QUERIES['sbSystem/tagging/tags/getTag'] = '
	SELECT		s_tag
	FROM		{TABLE_TAGS}
	WHERE		id = :tag_id
';
$_QUERIES['sbSystem/tagging/tags/addTag'] = '
	INSERT INTO	{TABLE_TAGS}
				(
					s_tag,
					n_popularity,
					n_customweight,
					e_visibility
				) VALUES (
					:tag,
					0,
					0,
					\'VISIBLE\'
				)
';
$_QUERIES['sbSystem/tagging/tags/updateTag'] = '
	UPDATE		{TABLE_TAGS}
	SET			s_tag = :tag,
				n_popularity = :popularity,
				n_customweight = :customweight,
				e_visibility = :visibility
		WHERE	id = :tag_id
';
$_QUERIES['sbSystem/tagging/tags/increasePopularity'] = '
	UPDATE		{TABLE_TAGS}
	SET			n_popularity = n_popularity + 1
		WHERE	id = :tag_id
';
$_QUERIES['sbSystem/tagging/tags/getAllTags'] = '
	SELECT		t.id,
				t.s_tag,
				t.n_popularity,
				t.n_customweight,
				t.e_visibility,
				(SELECT 	COUNT(*) 
					FROM	{TABLE_NODETAGS} nt
					WHERE	nt.fk_tag = t.id
				) AS n_numitemstagged
	FROM		{TABLE_TAGS} t
';
$_QUERIES['sbSystem/tagging/tags/getAllTags/orderByTag'] = $_QUERIES['sbSystem/tagging/tags/getAllTags'].'
	ORDER BY	t.s_tag
';
$_QUERIES['sbSystem/tagging/tags/getTagData'] = $_QUERIES['sbSystem/tagging/tags/getAllTags'].'
	WHERE		t.id = :tag_id
';
$_QUERIES['sbSystem/tagging/getItems/byTagID'] = '
	SELECT		n.uuid,
				n.s_label AS label,
				n.fk_nodetype AS nodetype,
				h.fk_parent AS parent_uuid
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_NODETAGS} nt
		ON		n.uuid = nt.fk_subject
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	WHERE		nt.fk_tag = :tag_id
		AND		h.s_mpath LIKE CONCAT(:root_mpath, \'%\')
';
$_QUERIES['sbSystem/tagging/getItems/byTagID/all'] = $_QUERIES['sbSystem/tagging/getItems/byTagID'].'
	ORDER BY	n.s_label
	LIMIT		0, :limit
';
$_QUERIES['sbSystem/tagging/getItems/byTagID/byNodetype'] = $_QUERIES['sbSystem/tagging/getItems/byTagID'].'
		AND		n.fk_nodetype = :nodetype
	ORDER BY	n.s_label
	LIMIT		0, :limit
';
$_QUERIES['sbSystem/tagging/getItems/byTagID/byNodetype/random'] = $_QUERIES['sbSystem/tagging/getItems/byTagID'].'
		AND		n.fk_nodetype = :nodetype
	ORDER BY	RAND()
	LIMIT		0, :limit
';
$_QUERIES['sbSystem/tagging/clearUnusedTags'] = '
	DELETE FROM {TABLE_TAGS}
	WHERE		id NOT IN (
					SELECT	fk_tag
					FROM	{TABLE_NODETAGS}
				)
';

//------------------------------------------------------------------------------
// relations
//------------------------------------------------------------------------------

$_QUERIES['sbSystem/relations/getRelations'] = '
	SELECT		r.s_relation AS relation,
				r.fk_entity2 AS target_uuid,
				n.s_label AS target_label,
				n.fk_nodetype AS target_nodetype
	FROM		{TABLE_RELATIONS} r
	INNER JOIN	{TABLE_NODES} n
		ON		n.uuid = r.fk_entity2
	WHERE		r.fk_entity1 = :source_uuid
';
$_QUERIES['sbSystem/relations/getSupportedRelations'] = '
	SELECT		s_relation AS relation,
				fk_targetnodetype AS targetnodetype,
				s_reverserelation AS reverserelation
	FROM		{TABLE_ONTOLOGY}
	WHERE		fk_sourcenodetype = :nodetype
	UNION
	SELECT		s_reverserelation AS relation,
				fk_sourcenodetype AS targetnodetype,
				s_relation AS reverserelation
	FROM		{TABLE_ONTOLOGY}
	WHERE		fk_targetnodetype = :nodetype
		AND		s_reverserelation IS NOT NULL
';
$_QUERIES['sbSystem/relations/getPossibleTargets'] = '
	SELECT		n.s_label AS label,
				n.uuid,
				n.fk_nodetype AS nodetype
	FROM		{TABLE_NODES} n
	WHERE		s_label LIKE :substring
		AND		n.fk_nodetype IN
				(
					SELECT		fk_targetnodetype AS targetnodetype
					FROM		{TABLE_ONTOLOGY}
					WHERE		fk_sourcenodetype = :sourcenodetype
						AND		s_relation = :relation
					UNION
					SELECT		fk_sourcenodetype AS targetnodetype
					FROM		{TABLE_ONTOLOGY}
					WHERE		fk_targetnodetype = :sourcenodetype
						AND		s_reverserelation = :relation
				)
	ORDER BY	CHAR_LENGTH(s_label), n.s_label
	LIMIT		0, 10
';
$_QUERIES['sbSystem/relations/addRelation'] = '
	INSERT INTO {TABLE_RELATIONS}
				(
					s_relation,
					fk_entity1,
					fk_entity2
				) VALUES (
					:relation,
					:source_uuid,
					:target_uuid
				)
';
$_QUERIES['sbSystem/relations/removeRelation'] = '
	DELETE FROM {TABLE_RELATIONS}
	WHERE		s_relation = :relation
		AND		fk_entity1 = :source_uuid
		AND		fk_entity2 = :target_uuid
';

//------------------------------------------------------------------------------
// action- & view-related
//------------------------------------------------------------------------------

$_QUERIES['sbSystem/node/loadActionDetails/given'] = '
	SELECT		*
	FROM		{TABLE_ACTIONS} a
	WHERE		a.fk_nodetype = :nodetype
		AND		a.s_view = :view
		AND		a.s_action = :action
';
$_QUERIES['sbSystem/node/loadActionDetails/default'] = '
	SELECT		*
	FROM		{TABLE_ACTIONS} a
	WHERE		a.fk_nodetype = :nodetype
		AND		a.s_view = :view
		AND		a.b_default = \'TRUE\'
';

//------------------------------------------------------------------------------
// authorisations
//------------------------------------------------------------------------------

$_QUERIES['sbSystem/node/loadAuthorisations/local'] = '
	SELECT		a.fk_authorisation,
				a.fk_userentity,
				a.e_granttype,
				n.fk_nodetype AS fk_userentitytype
	FROM		{TABLE_AUTH} a
	INNER JOIN	{TABLE_NODES} n
		ON		a.fk_userentity = n.uuid
	WHERE		a.fk_subject = :uuid
';
$_QUERIES['sbSystem/node/loadAuthorisations/local/byEntity'] = '
	SELECT		a.fk_authorisation,
				a.e_granttype
	FROM		{TABLE_AUTH} a
	WHERE		a.fk_subject = :node_uuid
		AND		a.fk_userentity = :entity_uuid
';
$_QUERIES['sbSystem/node/setAuthorisation'] = '
	INSERT INTO {TABLE_AUTH}
				(
					fk_authorisation,
					fk_userentity,
					fk_subject,
					e_granttype
				) VALUES (
					:authorisation,
					:entity_uuid,
					:subject_uuid,
					:granttype
				)
	ON DUPLICATE KEY UPDATE
				e_granttype = :granttype
';
$_QUERIES['sbSystem/node/removeAuthorisation'] = '
	DELETE FROM	{TABLE_AUTH}
	WHERE		fk_subject = :subject_uuid
		AND		fk_userentity = :entity_uuid
		AND		fk_authorisation = :authorisation
';

//------------------------------------------------------------------------------
// special actions
//------------------------------------------------------------------------------

$_QUERIES['sbSystem/node/moveToTrash/updateNode'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			h.fk_deletedby = :user_uuid,
				h.dt_deleted = NOW(),
				h.s_mpath = CONCAT(\'DELETED_\', h.s_mpath)
	WHERE		h.fk_parent = :parent_uuid
		AND		h.fk_child = :subject_uuid
';
$_QUERIES['sbSystem/node/moveToTrash/updateChildren'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			h.s_mpath = CONCAT(\'DELETED_\', h.s_mpath)
	WHERE		h.s_mpath LIKE CONCAT(:mpath, \'%\')
';
$_QUERIES['sbSystem/node/recoverFromTrash/getInfo'] = '
	SELECT		h.s_mpath AS mpath
	FROM		{TABLE_HIERARCHY} h
	WHERE		h.fk_parent = :parent_uuid
		AND		h.fk_child = :subject_uuid
';
$_QUERIES['sbSystem/node/recoverFromTrash'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			fk_deletedby = NULL,
				dt_deleted = NULL,
				h.s_mpath = REPLACE(h.s_mpath, \'DELETED_\', \'\') 
	WHERE		h.s_mpath LIKE CONCAT(:mpath, \'%\')
		OR		(h.fk_parent = :parent_uuid
			AND		h.fk_child = :subject_uuid)
';

//------------------------------------------------------------------------------
// node:module
//------------------------------------------------------------------------------

$_QUERIES['sbSystem/module/loadProperties/auxiliary'] = '
	SELECT		n_mainversion,
				n_subversion,
				n_bugfixversion,
				s_versioninfo,
				dt_installed,
				dt_updated,
				b_uninstallable,
				b_active
	FROM		{TABLE_MODULES}
	WHERE		uuid = :node_id

';
$_QUERIES['sbSystem/module/saveProperties/auxiliary'] = '
	INSERT INTO	{TABLE_MODULES}
				(
					n_mainversion,
					n_subversion,
					n_bugfixversion,
					s_versioninfo,
					dt_installed,
					dt_updated,
					b_uninstallable,
					b_active,
					uuid
				) VALUES (
					:version_main,
					:version_sub,
					:version_bugfix,
					:version_suffix,
					:info_installedon,
					:info_lastupdate,
					:info_uninstallable,
					:config_active,
					:node_id
				)
	ON DUPLICATE KEY UPDATE
				n_mainversion = :version_main,
				n_subversion = :version_sub,
				n_bugfixversion = :version_bugfix,
				s_versioninfo = :version_suffix,
				dt_installed = :info_installedon,
				dt_updated = :info_lastupdate,
				b_uninstallable = :info_uninstallable,
				b_active = :config_active
';

//------------------------------------------------------------------------------
// node:user 
//------------------------------------------------------------------------------
// view:edit -------------------------------------------------------------------
$_QUERIES['sbSystem/user/loadProperties/auxiliary'] = '
	SELECT		s_password,
				s_email,
				t_comment,
				b_activated,
				b_stayloggedin,
				b_locked,
				b_emailsent,
				s_activationkey,
				dt_activatedat,
				dt_currentlogin,
				dt_lastlogin,
				b_hidestatus,
				n_failedlogins,
				n_successfullogins,
				n_silentlogins,
				n_totalfailedlogins,
				b_backendaccess,
				dt_expires
	FROM		{TABLE_USERS}
	WHERE		uuid = :node_id
';
$_QUERIES['sbSystem/user/saveProperties/auxiliary'] = '
	INSERT INTO	{TABLE_USERS}
				(
					s_password,
					s_email,
					t_comment,
					b_activated,
					b_stayloggedin,
					b_locked,
					b_emailsent,
					s_activationkey,
					dt_activatedat,
					dt_currentlogin,
					dt_lastlogin,
					b_hidestatus,
					n_failedlogins,
					n_successfullogins,
					n_silentlogins,
					n_totalfailedlogins,
					b_backendaccess,
					dt_expires,
					uuid
				) VALUES (
					:security_password,
					:properties_email,
					:properties_comment,
					:security_activated,
					:security_stayloggedin,
					:security_locked,
					:info_emailsent,
					:security_activationkey,
					:info_activatedat,
					:info_currentlogin,
					:info_lastlogin,
					:config_hidestatus,
					:security_failedlogins,
					:info_successfullogins,
					:info_silentlogins,
					:info_totalfailedlogins,
					:security_backendaccess,
					:security_expires,
					:node_id
				)
	ON DUPLICATE KEY UPDATE
				s_password = :security_password,
				s_email = :properties_email,
				t_comment = :properties_comment,
				b_activated = :security_activated,
				b_stayloggedin = :security_stayloggedin,
				b_locked = :security_locked,
				b_emailsent = :info_emailsent,
				s_activationkey = :security_activationkey,
				dt_activatedat = :info_activatedat,
				dt_currentlogin = :info_currentlogin,
				dt_lastlogin = :info_lastlogin,
				b_hidestatus = :config_hidestatus,
				n_failedlogins = :security_failedlogins,
				n_successfullogins = :info_successfullogins,
				n_silentlogins = :info_silentlogins,
				n_totalfailedlogins = :info_totalfailedlogins,
				b_backendaccess = :security_backendaccess,
				dt_expires = :security_expires
';

//------------------------------------------------------------------------------
// node:userentity 
//------------------------------------------------------------------------------
// view:edit -------------------------------------------------------------------
$_QUERIES['sb_system/userentity/getAuthorisations'] = '
	SELECT		a.fk_subject,
				a.fk_authorisation,
				a.e_granttype
	FROM		{TABLE_AUTH} a
	WHERE		a.fk_userentity = :uuid
';

//------------------------------------------------------------------------------
// node:maintenance
//------------------------------------------------------------------------------
// view:repair -----------------------------------------------------------------
$_QUERIES['sbSystem/maintenance/view/repair/loadRoot'] = '
	SELECT		uuid
	FROM		{TABLE_NODES}
	WHERE		s_uid = \'sbSystem:Root\'
';
$_QUERIES['sbSystem/maintenance/view/repair/loadChildren'] = '
	SELECT		fk_child AS uuid,
				b_primary
	FROM		{TABLE_HIERARCHY}
	WHERE		fk_parent = :fk_parent
		AND		fk_child <> \'00000000000000000000000000000000\'
		AND		fk_deletedby IS NULL
	ORDER BY	n_order
';
$_QUERIES['sbSystem/maintenance/view/repair/setCoordinates/nestedSets'] = '
	UPDATE 		{TABLE_HIERARCHY}
	SET			n_left = :left,
				n_right = :right,
				n_level = :level,
				n_order = :order
	WHERE		fk_child = :fk_child
		AND		fk_parent = :fk_parent
';
$_QUERIES['sbSystem/maintenance/view/repair/setCoordinates/MPath'] = '
	UPDATE 		{TABLE_HIERARCHY}
	SET			s_mpath = :mpath,
				n_level = :level,
				n_order = :order
	WHERE		fk_child = :fk_child
		AND		fk_parent = :fk_parent
';
$_QUERIES['sbSystem/maintenance/view/repair/checkAbandonedProperties'] = '
	SELECT 		*
	FROM		{TABLE_PROPERTIES} p
	LEFT JOIN	{TABLE_NODES} n
		ON		p.fk_node = n.uuid
	WHERE		n.uuid IS NULL
	UNION 
	SELECT 		*
	FROM		{TABLE_BINPROPERTIES} pb
	LEFT JOIN	{TABLE_NODES} n
		ON		pb.fk_node = n.uuid
	WHERE		n.uuid IS NULL
';
$_QUERIES['sbSystem/maintenance/view/repair/removeAbandonedProperties/normal'] = '
	DELETE FROM	{TABLE_PROPERTIES}
	WHERE		fk_node NOT IN (
					SELECT	uuid
					FROM	{TABLE_NODES}
				)
';
$_QUERIES['sbSystem/maintenance/view/repair/removeAbandonedProperties/binary'] = '
	DELETE FROM	{TABLE_BINPROPERTIES}
	WHERE		fk_node NOT IN (
					SELECT	uuid
					FROM	{TABLE_NODES}
				)
';
$_QUERIES['sbSystem/maintenance/view/repair/removeAbandonedNodes/normal'] = '
	DELETE FROM	{PREFIX_WORKSPACE}_system_nodes
	WHERE		uuid NOT IN (
					SELECT	fk_child
					FROM	{TABLE_HIERARCHY}
				)
		/* deactivated due to problem with orphan branches
		AND		uuid NOT IN (
					SELECT	fk_parent
					FROM	{TABLE_HIERARCHY}
				)*/
';
/*$_QUERIES['sbSystem/maintenance/view/repair/gatherAbandonedNodesInTrashcan'] = '
	INSERT FROM	{TABLE_HIERARCHY}
				(
					fk_parent,
					fk_child,
					b_primary,
					n_order,
					n_level,
					fk_deletedby,
					dt_deleted
				) VALUES (
					:,
					:version_sub,
					:version_bugfix,
					:version_suffix,
					:info_installedon,
					:info_lastupdate,
					:info_uninstallable,
					:config_active,
					:node_id
				)
';*/
$_QUERIES['sbSystem/maintenance/view/repair/getAllUUIDs'] = '
	SELECT		uuid
	FROM		{TABLE_NODES}
';
$_QUERIES['sbSystem/maintenance/view/repair/updateUUID'] = '
	UPDATE		{TABLE_NODES}
	SET			uuid = :uuid_new
	WHERE		uuid = :uuid_old
';


//------------------------------------------------------------------------------
// node:folder
//------------------------------------------------------------------------------
// view:upload -----------------------------------------------------------------
$_QUERIES['sb_system/folder/view/upload/getMimetypeMapping'] = '
	SELECT		s_mimetype,
				fk_nodetype
	FROM		{TABLE_MIMETYPES}
';

//------------------------------------------------------------------------------
// node:preferences
//------------------------------------------------------------------------------
// view:moduleinfo -------------------------------------------------------------
$_QUERIES['sbSystem/modules/getInfo'] = '
	SELECT		*
	FROM		{TABLE_MODULES}
	ORDER BY	s_name
';

//------------------------------------------------------------------------------
// node:reports_structure
//------------------------------------------------------------------------------
// view:nodetypes --------------------------------------------------------------
$_QUERIES['sbSystem/reports_structure/nodetypes/overview'] = '
	SELECT		nt.s_type,
				REPLACE(nt.s_type, \':\', \'_\') AS s_displaytype,
				(SELECT
					COUNT(*) 
					FROM	{TABLE_NODES}
					WHERE	fk_nodetype = nt.s_type
				) AS num_nodes,
				(SELECT
					COUNT(*) 
					FROM		{TABLE_NODES} n
					LEFT JOIN	{TABLE_HIERARCHY} h
						ON		n.uuid = h.fk_child
					WHERE		fk_nodetype = nt.s_type
						AND		h.fk_child IS NULL
				) AS num_lostnodes
	FROM		{TABLE_NODETYPES} nt
';

//------------------------------------------------------------------------------
// node:reports_db
//------------------------------------------------------------------------------
// view:tables -----------------------------------------------------------------
$_QUERIES['sbSystem/reports_db/tables/overview'] = '
	SHOW TABLE STATUS
';
/*
$_QUERIES['sb_system/reports_db/tables/details'] = '
	SHOW TABLE STATUS LIKE :table_name
';*/

// view:status -----------------------------------------------------------------

$_QUERIES['sbSystem/reports_db/status/variables'] = '
	SHOW VARIABLES
';
$_QUERIES['sbSystem/reports_db/status/status'] = '
	SHOW STATUS
';

//------------------------------------------------------------------------------
// node:trashcan
//------------------------------------------------------------------------------

$_QUERIES['sbSystem/node/trashcan/getAbandonedNodes'] = '
	SELECT		n.uuid,
				n.fk_nodetype,
				n.s_name,
				n.s_label
	FROM		{TABLE_NODES} n
	WHERE		(
					SELECT 	COUNT(*)
					FROM	{TABLE_HIERARCHY} h
					WHERE	fk_child = n.uuid
				) = 0
	AND			n.fk_nodetype != \'sbSystem:Root\'
';
$_QUERIES['sbSystem/node/trashcan/getTrash'] = '
	SELECT		h.fk_parent AS parent_uuid,
				h.fk_child AS child_uuid,
				u.s_label AS user_label,
				n.s_label AS parent_label,
				n.fk_nodetype AS parent_nodetype
	FROM		{TABLE_HIERARCHY} h
	LEFT JOIN	{TABLE_NODES} n
		ON		h.fk_parent = n.uuid
	LEFT JOIN	{TABLE_NODES} u
		ON		h.fk_deletedby = u.uuid
';
$_QUERIES['sbSystem/node/trashcan/getTrash/all'] = $_QUERIES['sbSystem/node/trashcan/getTrash'].'
	WHERE		h.fk_deletedby IS NOT NULL
';
$_QUERIES['sbSystem/node/trashcan/getTrash/userspecific'] = $_QUERIES['sbSystem/node/trashcan/getTrash'].'
	WHERE		h.fk_deletedby = :user_uuid
';
// TODO: first check for nodes that have to be removed because they are last in a shared set
$_QUERIES['sbSystem/node/trashcan/purge'] = '
	DELETE FROM	{TABLE_HIERARCHY}
	WHERE		fk_deletedby IS NOT NULL
		OR		SUBSTRING(s_mpath, 1, 8) = \'DELETED_\'
';


//------------------------------------------------------------------------------
// node:registry
//------------------------------------------------------------------------------

// view:edit -------------------------------------------------------------------

$_QUERIES['sbSystem/registry/getAllEntries'] = '
	SELECT		*,
				(
					SELECT s_value
					FROM		{TABLE_REGVALUES} rv
					WHERE		r.s_key = rv.s_key
						AND		rv.fk_user = :user_uuid
				) as s_value
	FROM		{TABLE_REGISTRY} r
	ORDER BY	r.s_key
';
$_QUERIES['sbSystem/registry/getEntry'] = '
	SELECT		*
	FROM		{TABLE_REGISTRY} r
	WHERE		r.s_key = :key
';
$_QUERIES['sbSystem/registry/getValue'] = '
	SELECT		r.s_key,
				r.e_type,
				r.s_defaultvalue,
				rvu.fk_user,
				rvu.s_value as s_uservalue,
				rvs.s_value as s_systemvalue
	FROM		{TABLE_REGISTRY} r
	LEFT JOIN	{TABLE_REGVALUES} rvu
		ON		r.s_key = rvu.s_key
		AND		rvu.fk_user = :user_uuid
	LEFT JOIN	{TABLE_REGVALUES} rvs
		ON		r.s_key = rvs.s_key
		AND		rvs.fk_user = \'00000000000000000000000000000000\'
	WHERE		r.s_key = :key
';
$_QUERIES['sbSystem/registry/setValue'] = '
	INSERT INTO {TABLE_REGVALUES}
				(
					s_key,
					fk_user,
					s_value
				) VALUES (
					:key,
					:user_uuid,
					:value
				)
	ON DUPLICATE KEY UPDATE
				s_value = :value
';
$_QUERIES['sbSystem/registry/removeValue'] = '
	DELETE FROM	{TABLE_REGVALUES}
	WHERE		s_key = :key
		AND		fk_user = :user_uuid
';

//------------------------------------------------------------------------------
// node:root 
//------------------------------------------------------------------------------

// view:welcome ----------------------------------------------------------------

$_QUERIES['sb_system/root/view/welcome/loadUserdata'] = '
	SELECT		n.s_label AS nickname,
				u.b_activated AS activated_at,
				u.dt_lastlogin AS last_login,
				u.dt_currentlogin AS current_login,
				u.n_totalfailedlogins AS total_failed_logins,
				u.n_successfullogins AS successful_logins
	FROM		{TABLE_USERS} u
	INNER JOIN	{TABLE_NODES} n
		ON		n.uuid = u.uuid
	WHERE		n.uuid = :user_id
';

// view:login ------------------------------------------------------------------

$_QUERIES['sb_system/root/view/login/loadUserdata'] = '
	SELECT		u.uuid,
				n.s_label AS s_nickname,
				u.s_password,
				u.b_activated,
				u.b_locked,
				u.dt_currentlogin,
				u.n_failedlogins,
				u.dt_failedlogin,
				u.dt_expires
	FROM		{TABLE_USERS} u
	INNER JOIN	{TABLE_NODES} n
		ON		n.uuid = u.uuid
	WHERE		n.s_name= :login
';
$_QUERIES['sb_system/root/view/login/increaseFailedLogins'] = '
	UPDATE		{TABLE_USERS}
	SET			n_failedlogins = n_failedlogins + 1,
				n_totalfailedlogins = n_totalfailedlogins + 1,
				dt_failedlogin = NOW()
	WHERE		uuid = :user_id
';
$_QUERIES['sb_system/root/view/login/resetFailedLogins'] = '
	UPDATE		{TABLE_USERS}
	SET			n_failedlogins = 0,
				dt_failedlogin = NULL
	WHERE		uuid = :user_id
';
$_QUERIES['sb_system/root/view/login/successfulLogin'] = '
	UPDATE		{TABLE_USERS}
	SET			n_failedlogins		= 0,
				dt_lastlogin		= dt_currentlogin,
				dt_currentlogin		= NOW(),
				n_successfullogins	= n_successfullogins + 1
	WHERE		uuid = :user_id
';

//------------------------------------------------------------------------------
// node:debug 
//------------------------------------------------------------------------------

$_QUERIES['sbSystem/debug/gatherTree'] = '
	SELECT		n.uuid,
				n.s_name,
				h.n_level,
				h.n_order,
				h.s_mpath,
				h.b_primary,
				nt.s_type,
				(SELECT COUNT(*)
					FROM	{TABLE_HIERARCHY}
					WHERE	fk_parent = n.uuid
				) as n_numchildren
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_NODETYPES} nt
		ON		n.fk_nodetype = nt.s_type
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	/*INNER JOIN	{TABLE_NODES} n2
		ON		h.fk_parent = n2.uuid*/
	WHERE		h.fk_parent = :parent_uuid
		AND		h.fk_child <> \'00000000000000000000000000000000\'
	ORDER BY	h.n_order ASC
';

?>