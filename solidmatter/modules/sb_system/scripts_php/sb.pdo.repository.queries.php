<?php

global $_QUERIES;

$_QUERIES['MAPPING']['{TABLE_NODETYPES}']		= '{PREFIX_FRAMEWORK}_nodetypes';
$_QUERIES['MAPPING']['{TABLE_NAMESPACES}']		= '{PREFIX_FRAMEWORK}_namespaces';
$_QUERIES['MAPPING']['{TABLE_NTHIERARCHY}']		= '{PREFIX_FRAMEWORK}_nodetypes_inheritance';
$_QUERIES['MAPPING']['{TABLE_VIEWS}']			= '{PREFIX_FRAMEWORK}_nodetypes_views';
$_QUERIES['MAPPING']['{TABLE_ACTIONS}']			= '{PREFIX_FRAMEWORK}_nodetypes_viewactions';
$_QUERIES['MAPPING']['{TABLE_MODES}']			= '{PREFIX_FRAMEWORK}_nodetypes_modes';
$_QUERIES['MAPPING']['{TABLE_AUTHDEF}']			= '{PREFIX_FRAMEWORK}_nodetypes_authorisations';
$_QUERIES['MAPPING']['{TABLE_VIEWAUTH}']		= '{PREFIX_FRAMEWORK}_nodetypes_viewauthorisations';
$_QUERIES['MAPPING']['{TABLE_PROPERTYDEFS}']	= '{PREFIX_FRAMEWORK}_nodetypes_properties';

$_QUERIES['MAPPING']['{TABLE_AUTHCACHE}']		= '{PREFIX_WORKSPACE}_system_cache_authorisations';
$_QUERIES['MAPPING']['{TABLE_PATHCACHE}']		= '{PREFIX_WORKSPACE}_system_cache_paths';

$_QUERIES['MAPPING']['{TABLE_NODES}']			= '{PREFIX_WORKSPACE}_system_nodes';
$_QUERIES['MAPPING']['{TABLE_HIERARCHY}']		= '{PREFIX_WORKSPACE}_system_nodes_parents';
$_QUERIES['MAPPING']['{TABLE_PROPERTIES}']		= '{PREFIX_WORKSPACE}_system_nodes_properties';
$_QUERIES['MAPPING']['{TABLE_BINPROPERTIES}']	= '{PREFIX_WORKSPACE}_system_nodes_properties_binary';
$_QUERIES['MAPPING']['{TABLE_LOCKS}']			= '{PREFIX_WORKSPACE}_system_nodes_locks';
$_QUERIES['MAPPING']['{TABLE_AUTH}']			= '{PREFIX_WORKSPACE}_system_nodes_authorisation';

$_QUERIES['MAPPING']['{TABLE_HIERARCHYMEM}']	= '{PREFIX_WORKSPACE}_system_nodes_parents_mem';

//------------------------------------------------------------------------------
// sbNodeType
//------------------------------------------------------------------------------

$_QUERIES['sbCR/repository/loadAuthorisations/supported'] = '
	SELECT		ad.s_authorisation,
				ad.fk_parentauthorisation
	FROM		{TABLE_AUTHDEF} ad
	WHERE		ad.fk_nodetype = :node_type
	ORDER BY	ad.n_order
';
$_QUERIES['sbCR/repository/loadViews/supported'] = '
	SELECT		v.s_view,
				v.s_classfile,
				v.s_class,
				v.b_default,
				v.b_display
	FROM		{TABLE_VIEWS} v
	WHERE		v.fk_nodetype = :nodetype
	ORDER BY	v.n_order
';
$_QUERIES['sbCR/repository/loadViewAuthorisations'] = '
	SELECT		fk_view,
				fk_authorisation
	FROM		{TABLE_VIEWAUTH}
	WHERE		fk_nodetype = :nodetype
';

//------------------------------------------------------------------------------
// PathCache
//------------------------------------------------------------------------------

$_QUERIES['sb_system/cache/paths/store'] = '
	INSERT INTO	{TABLE_PATHCACHE}
				(
					s_path,
					fk_node
				) VALUES (
					:path,
					:node_id
				)
	ON DUPLICATE KEY UPDATE
				fk_node = :node_id
';
$_QUERIES['sb_system/cache/paths/load'] = '
	SELECT		fk_node
	FROM		{TABLE_PATHCACHE}
	WHERE		s_path = :path
';
$_QUERIES['sb_system/cache/paths/clear'] = '
	DELETE FROM	{TABLE_PATHCACHE}
	WHERE		s_path LIKE :path
';
$_QUERIES['sb_system/cache/paths/empty'] = '
	DELETE FROM {TABLE_PATHCACHE}
';

//------------------------------------------------------------------------------
// AuthorisationCache
//------------------------------------------------------------------------------

$_QUERIES['sb_system/cache/authorisation/store'] = '
	INSERT INTO	{TABLE_AUTHCACHE}
				(
					fk_subject,
					fk_entity,
					fk_authorisation,
					e_granttype,
					e_authtype
				) VALUES (
					:subject_uuid,
					:entity_uuid,
					:authorisation,
					:granttype,
					:authtype
				)
	ON DUPLICATE KEY UPDATE
				e_granttype = :granttype
';
$_QUERIES['sb_system/cache/authorisation/load'] = '
	SELECT		fk_authorisation,
				e_granttype
	FROM		{TABLE_AUTHCACHE}
	WHERE		fk_subject = :subject_uuid
		AND		fk_entity = :entity_uuid
		AND		e_authtype = :authtype
';
$_QUERIES['sb_system/cache/authorisation/clear'] = '
	DELETE FROM	{TABLE_AUTHCACHE}
	WHERE		fk_entity = :entity_uuid
';
$_QUERIES['sb_system/cache/authorisation/empty'] = '
	DELETE FROM {TABLE_AUTHCACHE}
';

//------------------------------------------------------------------------------
// sbContentRepository
//------------------------------------------------------------------------------

$_QUERIES['sbCR/repository/getNodetypes'] = '
	SELECT		*
	FROM		{TABLE_NODETYPES} sn
	ORDER BY	sn.s_type
';
$_QUERIES['sb_system/repository/getViews'] = '
	SELECT		*
	FROM		{TABLE_VIEWS} snv
	ORDER BY	snv.fk_nodetype, snv.s_view
';
$_QUERIES['sb_system/repository/getActions'] = '
	SELECT		*
	FROM		{TABLE_ACTIONS} snva
	ORDER BY	snva.fk_nodetype, snva.s_view, snva.s_action
';
$_QUERIES['sb_system/repository/getModes'] = '
	SELECT		*
	FROM		{TABLE_MODES} snm
	ORDER BY	snm.s_mode, snm.fk_parent, snm.fk_child
';
$_QUERIES['sb_system/repository/getAuthorisations'] = '
	SELECT		*
	FROM		{TABLE_VIEWAUTH} snva
	ORDER BY	snva.fk_nodetype, snva.fk_view
';

$_QUERIES['sbCR/repository/getNodeTypes'] = '
	SELECT		*
	FROM		{TABLE_NODETYPES} sn
	ORDER BY	sn.s_type
';
$_QUERIES['sbCR/repository/getNodeTypeHierarchy'] = '
	SELECT		fk_parentnodetype,
				fk_childnodetype
	FROM		{TABLE_NTHIERARCHY}
';

// repository administration ---------------------------------------------------

$_QUERIES['sbCR/nodetype/save'] = '
	INSERT INTO	{TABLE_NODETYPES}
				(
					s_type,
					b_abstract,
					e_type,
					s_class,
					s_classfile,
					s_category,
					s_csstype,
					b_taggable
				) VALUES (
					:nodetype,
					:abstract,
					:type,
					:class,
					:classfile,
					:category,
					:displaytype,
					:taggable
				)
	ON DUPLICATE KEY UPDATE
				s_type = :nodetype,
				b_abstract = :abstract,
				e_type = :type,
				s_class = :class,
				s_classfile = :classfile,
				s_category = :category,
				s_csstype = :displaytype,
				b_taggable = :taggable
';
$_QUERIES['sbCR/nodetype/remove'] = '
	DELETE FROM	{TABLE_NODETYPES}
	WHERE		s_type = :nodetype
';
$_QUERIES['sbCR/view/save'] = '
	INSERT INTO	{TABLE_VIEWS}
				(
				
				) VALUES (
					
				)
	ON DUPLICATE KEY UPDATE
';
$_QUERIES['sbCR/action/save'] = '
	INSERT INTO	{TABLE_ACTIONS}
				(
					
				) VALUES (
					
				)
	ON DUPLICATE KEY UPDATE
';


// node retrieval --------------------------------------------------------------

$_QUERIES['sbCR/getNode/root'] = '
	SELECT		n.uuid,
				n.s_uid,
				n.fk_nodetype,
				n.s_name,
				n.s_label,
				n.s_customcsstype,
				n.b_inheritrights,
				n.b_bequeathrights,
				nt.s_classfile,
				nt.s_class,
				nt.s_csstype,
				\'FALSE\' AS b_taggable,
				NULL AS fk_parent
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_NODETYPES} nt
		ON		n.fk_nodetype = nt.s_type
	WHERE		n.fk_nodetype = \'sbSystem:Root\'
';
$_QUERIES['sbCR/getNode/byUUID'] = '
	SELECT		n.uuid,
				n.s_uid,
				n.fk_nodetype,
				n.s_name,
				n.s_label,
				n.s_customcsstype,
				n.b_inheritrights,
				n.b_bequeathrights,
				nt.s_classfile,
				nt.s_class,
				nt.s_csstype,
				nt.b_taggable,
				h.fk_parent
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_NODETYPES} nt
		ON		n.fk_nodetype = nt.s_type
	LEFT JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	WHERE		n.uuid = :id
		AND		h.b_primary = \'TRUE\'
';
$_QUERIES['sbCR/getNode/byUID'] = '
	SELECT		n.uuid,
				n.s_uid,
				n.fk_nodetype,
				n.s_name,
				n.s_label,
				n.s_customcsstype,
				n.b_inheritrights,
				n.b_bequeathrights,
				nt.s_classfile,
				nt.s_class,
				nt.s_csstype,
				nt.b_taggable,
				h.fk_parent
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_NODETYPES} nt
		ON		n.fk_nodetype = nt.s_type
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	WHERE		n.s_uid = :uid
		AND		h.b_primary = \'TRUE\'
';

// maintenance -----------------------------------------------------------------
$_QUERIES['sbCR/maintenance/getNodes/unconnected'] = '
	SELECT		uuid 
	FROM		{TABLE_NODES}
	WHERE		uuid NOT IN (
					SELECT fk_child
					FROM {TABLE_HIERARCHY}
				) 
		AND 	uuid NOT IN (
					SELECT fk_parent
					FROM {TABLE_HIERARCHY}
				)
';
$_QUERIES['sbCR/maintenance/getNodes/notParent'] = '
	SELECT		uuid 
	FROM		{TABLE_NODES}
	WHERE		uuid NOT IN (
					SELECT fk_parent
					FROM {TABLE_HIERARCHY}
				)
';
$_QUERIES['sbCR/maintenance/getNodes/notChild'] = '
	SELECT		uuid 
	FROM		{TABLE_NODES}
	WHERE		uuid NOT IN (
					SELECT fk_child
					FROM {TABLE_HIERARCHY}
				)
';

//------------------------------------------------------------------------------
// NamespaceRegistry
//------------------------------------------------------------------------------

$_QUERIES['sbCR/NamespaceRegistry/loadNamespaces'] = '
	SELECT		s_prefix,
				s_uri
	FROM		{TABLE_NAMESPACES}
';

//------------------------------------------------------------------------------
// node:default
//------------------------------------------------------------------------------

// hierarchy -------------------------------------------------------------------
$_QUERIES['sbCR/node/loadChildren/mode/standard/unordered'] = '
	SELECT		n.uuid,
				n.s_label
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_NODETYPES} nt
		ON		n.fk_nodetype = nt.s_type
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	INNER JOIN	{TABLE_NODES} n2
		ON		h.fk_parent = n2.uuid
	WHERE		h.fk_parent = :parent_uuid
		AND		n.fk_nodetype IN (
					SELECT		fk_nodetype
					FROM		{TABLE_MODES}
					WHERE		s_mode = :mode
						AND		fk_parentnodetype = n2.fk_nodetype
				)
		AND		h.fk_child != :parent_uuid /* prevent root finding itself */
';
$_QUERIES['sbCR/node/countChildren/mode'] = '
	SELECT		COUNT(*) as num_children
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_NODETYPES} nt
		ON		n.fk_nodetype = nt.s_type
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	INNER JOIN	{TABLE_NODES} n2
		ON		h.fk_parent = n2.uuid
	WHERE		h.fk_parent = :parent_uuid
		AND		n.fk_nodetype IN (
					SELECT		fk_nodetype
					FROM		{TABLE_MODES}
					WHERE		s_mode = :mode
						AND		fk_parentnodetype = n2.fk_nodetype
				)
		AND		h.fk_child != :parent_uuid /* prevent root finding itself */
';
/*$_QUERIES['sb_system/node/countDescendentsWithRight/mode'] = '
	SELECT		COUNT(*) as num_children
	FROM		{PREFIX_WORKSPACE}_system_nodes sn
	INNER JOIN	{PREFIX_FRAMEWORK}_system_nodetypes snt
		ON		sn.fk_nodetype = snt.s_type
	INNER JOIN	{PREFIX_WORKSPACE}_system_nodes_parents snp
		ON		sn.uuid = snp.fk_child
	INNER JOIN	{PREFIX_WORKSPACE}_system_nodes snpn
		ON		snp.fk_parent = snpn.uuid
	WHERE		snp.fk_parent = :parent_uuid
		AND		sn.fk_nodetype IN (
					SELECT		fk_nodetype
					FROM		{PREFIX_FRAMEWORK}_system_nodetypes_modes
					WHERE		s_mode = :mode
					AND			fk_parentnodetype = snpn.fk_nodetype
				)
		AND		(
					SELECT		count(*)
					FROM		{PREFIX_WORKSPACE}_system_nodes_authorisation sna_check
					INNER JOIN	{PREFIX_WORKSPACE}_system_nodes_parents snp_check
						ON		sna_check.fk_subject = snp_check.fk_child
					INNER JOIN	{PREFIX_WORKSPACE}_system_nodes sn_check
						ON		sna_check.fk_subject = sn_check.uuid
					WHERE		snp_check.n_left >= snp.n_left
						AND		snp_check.n_right <= snp.n_right
						AND		sna_check.e_granttype = \'ALLOW\'
				) > 0
';*/
$_QUERIES['sbCR/node/loadChildren/mode/standard/byOrder'] = 
$_QUERIES['sbCR/node/loadChildren/mode/standard/unordered'].'
	ORDER BY	h.n_order
';
$_QUERIES['sbCR/node/loadChildren/mode/standard/byLabel'] = 
$_QUERIES['sbCR/node/loadChildren/mode/standard/unordered'].'
	ORDER BY	n.s_label
';
$_QUERIES['sbCR/node/loadChildren/mode/standard/byNodetype'] = 
$_QUERIES['sbCR/node/loadChildren/mode/standard/unordered'].'
	ORDER BY	n.fk_nodetype
';
$_QUERIES['sbCR/node/loadChildren/mode/standard/byNodetypeAndLabel'] = 
$_QUERIES['sbCR/node/loadChildren/mode/standard/unordered'].'
	ORDER BY	n.fk_nodetype, n.label
';
$_QUERIES['sbCR/node/loadChildren/mode/standard/byCreationDate/ASC'] = 
$_QUERIES['sbCR/node/loadChildren/mode/standard/unordered'].'
	ORDER BY	n.dt_created
';
$_QUERIES['sbCR/node/loadChildren/mode/standard/byCreationDate/DESC'] = 
$_QUERIES['sbCR/node/loadChildren/mode/standard/unordered'].'
	ORDER BY	n.dt_created DESC
';
$_QUERIES['sbCR/node/loadChildren/debug'] = '
	SELECT		n.uuid,
				n.fk_nodetype,
				n.s_name,
				n.s_label,
				n.s_customcsstype,
				nt.s_csstype,
				h.b_primary
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_NODETYPES} nt
		ON		n.fk_nodetype = nt.s_type
	LEFT JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	WHERE		h.fk_parent = :parent_uuid
	AND			h.fk_child != :parent_uuid /* prevent root finding itself */
	ORDER BY	h.n_order
';
$_QUERIES['sbCR/node/countChildren/debug'] = '
	SELECT		COUNT(*) as num_children
	FROM		{TABLE_HIERARCHY} h
	WHERE		h.fk_parent = :parent_uuid
';
$_QUERIES['sbCR/node/countChildrenByName/debug'] = '
	SELECT		COUNT(*) as num_children
	FROM		{TABLE_HIERARCHY} h
	INNER JOIN	{TABLE_NODES} n
		ON		n.uuid = h.fk_child
	WHERE		h.fk_parent = :parent_uuid
		AND		n.s_name = :child_name
';
$_QUERIES['sbCR/node/getChild/byName'] = '
	SELECT		n.uuid,
				n.fk_nodetype,
				n.s_name,
				nt.s_csstype
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_NODETYPES} nt
		ON		n.fk_nodetype = nt.s_type
	LEFT JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child 
	WHERE		n.s_name = :name
		AND		h.fk_parent = :parent_uuid
';
// TODO: move this query from file
$_QUERIES['sbSystem/node/getAllowedSubtypes'] = '
	SELECT		m.fk_parentnodetype,
				m.fk_nodetype,
				nt.s_csstype
	FROM		{TABLE_MODES} m
	INNER JOIN	{TABLE_NODETYPES} nt
		ON		m.fk_nodetype = nt.s_type
	WHERE		m.s_mode = :mode
		AND		m.fk_parentnodetype = (
					SELECT	fk_nodetype
					FROM	{TABLE_NODES}
					WHERE	uuid = :parent_uuid
				)
		AND		m.b_display = \'TRUE\'
		
';
$_QUERIES['sbCR/node/getSharedSet'] = '
	SELECT		fk_parent,
				fk_child,
				b_primary
	FROM		{TABLE_HIERARCHY} h
		WHERE	fk_child = :child_uuid
';
$_QUERIES['sbCR/node/getPrimaryParent'] = '
	SELECT		fk_parent
	FROM		{TABLE_HIERARCHY}
	WHERE		fk_child = :child_uuid
		AND		b_primary = \'TRUE\'
';
$_QUERIES['sbCR/node/getParents/all'] = '
	SELECT		fk_parent
	FROM		{TABLE_HIERARCHY}
	WHERE		fk_child = :child_uuid
';
$_QUERIES['sbCR/node/getParents/byNodetype'] = '
	SELECT		h.fk_parent
	FROM		{TABLE_HIERARCHY} h
	INNER JOIN	{TABLE_NODES} n
		ON		h.fk_parent = n.uuid
	WHERE		h.fk_child = :child_uuid
		AND		n.fk_nodetype = :nodetype
';
$_QUERIES['sbCR/node/getAncestor'] = '
	SELECT		h.fk_parent
	FROM		{TABLE_HIERARCHY} h
	WHERE		n_left < (
					SELECT		n_left
					FROM		{TABLE_HIERARCHY}
					WHERE		fk_child = :child_uuid
						AND		b_primary = \'TRUE\'
				)
		AND		n_right > (
					SELECT		n_right
					FROM		{TABLE_HIERARCHY}
					WHERE		fk_child = :child_uuid
						AND		b_primary = \'TRUE\'
				)
		AND		h.n_level = :depth + 1
';
$_QUERIES['sbCR/node/getAncestors'] = '
	SELECT		h.fk_parent
	FROM		{TABLE_HIERARCHY} h
	WHERE		n_left < (
					SELECT		n_left
					FROM		{TABLE_HIERARCHY}
					WHERE		fk_child = :child_uuid
						AND		b_primary = \'TRUE\'
				)
		AND		n_right > (
					SELECT		n_right
					FROM		{TABLE_HIERARCHY}
					WHERE		fk_child = :child_uuid
						AND		b_primary = \'TRUE\'
				)
	ORDER BY	h.n_left DESC
';
$_QUERIES['sbCR/node/getAncestor/byUUID'] = '
	SELECT		h.fk_parent
	FROM		{TABLE_HIERARCHY} h
	WHERE		h.n_left <= (
					SELECT		n_left
					FROM		{TABLE_HIERARCHY}
					WHERE		fk_child = :child_uuid
						AND		b_primary = \'TRUE\'
				)
		AND		h.n_right >= (
					SELECT		n_right
					FROM		{TABLE_HIERARCHY}
					WHERE		fk_child = :child_uuid
						AND		b_primary = \'TRUE\'
				)
		AND		h.fk_parent = :parent_uuid
';
// save/delete -----------------------------------------------------------------
$_QUERIES['sbCR/node/save/new'] = '
	INSERT INTO	{TABLE_NODES}
				(
					uuid,
					s_uid,
					fk_nodetype,
					s_label,
					s_name,
					s_customcsstype,
					b_inheritrights,
					b_bequeathrights,
					fk_createdby,
					fk_modifiedby,
					fk_deletedby,
					dt_createdat,
					dt_modifiedat,
					dt_deletedat
				) VALUES (
					:uuid,
					:uid,
					:nodetype,
					:label,
					:name,
					:customcsstype,
					:inheritrights,
					:bequeathrights,
					:user_id,
					:user_id,
					NULL,
					NOW(),
					NOW(),
					NULL
				)
';
$_QUERIES['sbCR/node/save/existing'] = '
	UPDATE		{TABLE_NODES}
	SET			s_uid = :uid,
				s_label = :label,
				s_name = :name,
				s_customcsstype = :customcsstype,
				b_inheritrights = :inheritrights,
				b_bequeathrights = :bequeathrights,
				fk_modifiedby = :user_id,
				dt_modifiedat = NOW()
	WHERE		uuid = :uuid
';
$_QUERIES['sbCR/node/delete/forGood'] = '
	DELETE FROM	{TABLE_NODES}
	WHERE		uuid = :uuid
';
// linking ---------------------------------------------------------------------
$_QUERIES['sbCR/node/addLink/getBasicInfo'] = '
	SELECT		n_right,
				n_level,
				(SELECT	COUNT(*)
					FROM	{TABLE_HIERARCHY}
					WHERE	fk_parent = :parent_uuid
				) AS n_position,
				(SELECT COUNT(*)
					FROM	{TABLE_HIERARCHY}
					WHERE	fk_child = :child_uuid
				) AS n_numparents,
				(SELECT COUNT(*)
					FROM		{TABLE_HIERARCHY} h
					INNER JOIN	{TABLE_NODES} n
						ON		h.fk_child = n.uuid
					WHERE		n.s_name = :child_name
						AND		h.fk_parent = :parent_uuid
				) AS n_numsamenamesiblings
	FROM		{TABLE_HIERARCHY}
	WHERE		fk_child = :parent_uuid
		AND		b_primary = \'TRUE\'
'; // uses getParent
$_QUERIES['sbCR/node/addLink/updateRight'] = '
	UPDATE		{TABLE_HIERARCHY}
	SET			n_right = n_right + 2
	WHERE		n_right >= :right
'; // uses temp_right
$_QUERIES['sbCR/node/addLink/updateLeft'] = '
	UPDATE		{TABLE_HIERARCHY}
	SET			n_left = n_left + 2
	WHERE		n_left > :right
'; // uses getAncestor ?
$_QUERIES['sbCR/node/addLink/insertNode'] = '
	INSERT INTO	{TABLE_HIERARCHY}
				(
					fk_child,
					fk_parent,
					b_primary,
					n_order,
					n_left,
					n_right,
					n_level
				) VALUES (
					:child_uuid,
					:parent_uuid,
					:is_primary,
					:order,
					:right,
					:right+1,
					:level+1
				)
';
$_QUERIES['sbCR/node/hierarchy/getInfo'] = '
	SELECT		n_left,
				n_right,
				n_level,
				n_order,
				b_primary
	FROM		{TABLE_HIERARCHY}
	WHERE		fk_parent = :parent_uuid
		AND		fk_child = :child_uuid
';
/*$_QUERIES['sbCR/node/hierarchy/updateLeft'] = '
	UPDATE		{TABLE_HIERARCHY}
	SET			n_left = n_left + :offset
	WHERE		n_left > :boundary
';*/
$_QUERIES['sbCR/node/removeDescendantLinks'] = '
	DELETE FROM	{TABLE_HIERARCHY}
	WHERE		n_left > :left
		AND		n_right < :right;
';
$_QUERIES['sbCR/node/removeLink'] = '
	DELETE FROM	{TABLE_HIERARCHY}
	WHERE		fk_parent = :parent_uuid
		AND		fk_child = :child_uuid;
';
$_QUERIES['sbCR/node/removeLink/shiftLeft'] = '
	UPDATE		{TABLE_HIERARCHY}
	SET			n_left = n_left - :distance,
				n_right = n_right - :distance
		WHERE	n_left > :left
';
$_QUERIES['sbCR/node/getLinkStatus'] = '
	SELECT		h.b_primary
	FROM		{TABLE_HIERARCHY} h
	WHERE		h.fk_parent = :parent_uuid
		AND		h.fk_child = :child_uuid
';
$_QUERIES['sbCR/node/setLinkStatus'] = '
	SELECT		h.b_primary
	FROM		{TABLE_HIERARCHY} h
	WHERE		h.fk_parent = :parent_uuid
		AND		h.fk_child = :child_uuid
';
$_QUERIES['sbCR/node/setLinkStatus/newPrimary'] = '
	UPDATE		{TABLE_HIERARCHY}
	SET			b_primary = \'TRUE\'
	WHERE		fk_child = :child_uuid
		AND		fk_parent IN (
					SELECT		fk_parent
					FROM		{TABLE_HIERARCHY}
					WHERE		fk_child = :child_uuid
					ORDER BY	n_order
					LIMIT		0, 1
				)
';
$_QUERIES['sbCR/node/orderBefore/getInfo'] = '
	SELECT		h.n_left,
				h.n_right,
				h.n_order,
				h.n_level
	FROM		{TABLE_HIERARCHY} h
	INNER JOIN	{TABLE_NODES} n
		ON		h.fk_child = n.uuid
	WHERE		n.s_name = :child_name
		AND		h.fk_parent = :parent_uuid
';
$_QUERIES['sbCR/node/orderBefore/setLock'] = '
	UPDATE		{TABLE_HIERARCHY}
	SET			b_positionlocked = :state
	WHERE		n_left >= :left
		AND		n_right <= :right
';
$_QUERIES['sbCR/node/orderBefore/writeNestedSet'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			h.n_left = h.n_left + :offset_nestedset,
				h.n_right = h.n_right + :offset_nestedset
	WHERE		h.n_left >= :left
		AND		h.n_right <= :right
		AND		h.b_positionlocked = :state
';
$_QUERIES['sbCR/node/orderBefore/writeOrder'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			h.n_order = h.n_order + :offset_order
	WHERE		h.n_left >= :left
		AND		h.n_right <= :right
		AND		h.b_positionlocked = :state
		AND		h.fk_parent = :parent_uuid
';
// move branch
$_QUERIES['sbCR/node/moveBranch/getSourceInfo'] = '
	SELECT		n_left,
				n_right,
				n_order,
				n_level,
				b_primary,
				(SELECT		n_left
					FROM	{TABLE_HIERARCHY}
					WHERE	fk_child = :oldparent_uuid
						AND	b_primary = \'TRUE\'
				) as n_parentleft,
				(SELECT		n_right
					FROM	{TABLE_HIERARCHY}
					WHERE	fk_child = :oldparent_uuid
						AND	b_primary = \'TRUE\'
				) as n_parentright
	FROM		{TABLE_HIERARCHY} h
	WHERE		fk_child = :subject_uuid
		AND		fk_parent = :oldparent_uuid
';
$_QUERIES['sbCR/node/moveBranch/getDestinationInfo'] = '
	SELECT		n_left,
				n_right,
				n_order,
				n_level,
				(SELECT		COUNT(*)
					FROM	{TABLE_HIERARCHY}
					WHERE	fk_parent = :newparent_uuid
				) AS n_numchildren
	FROM		{TABLE_HIERARCHY} h
	WHERE		fk_child = :newparent_uuid
		AND		b_primary = \'TRUE\'
';
$_QUERIES['sbCR/node/moveBranch/setLock'] = '
	UPDATE		{TABLE_HIERARCHY}
	SET			b_positionlocked = \'TRUE\'
	WHERE		n_left >= :left
		AND		n_right <= :right
';
$_QUERIES['sbCR/node/moveBranch/removeLock'] = '
	UPDATE		{TABLE_HIERARCHY}
	SET			b_positionlocked = \'FALSE\'
	WHERE		b_positionlocked = \'TRUE\'
';
$_QUERIES['sbCR/node/moveBranch/updateBranch'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			h.n_left = h.n_left + :offset_nestedset,
				h.n_right = h.n_right + :offset_nestedset,
				h.n_level = h.n_level + :offset_level
	WHERE		h.b_positionlocked = \'TRUE\'
';
$_QUERIES['sbCR/node/moveBranch/updateLink'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			h.fk_parent = :newparent_uuid,
				h.n_order = :order
	WHERE		h.fk_parent = :oldparent_uuid
		AND		h.fk_child = :subject_uuid
';
$_QUERIES['sbCR/node/moveBranch/shiftOrder'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			n_order = n_order + :offset
	WHERE		fk_parent = :parent_uuid
		AND		n_order >= :boundary
		AND		b_positionlocked = \'FALSE\'
';
$_QUERIES['sbCR/node/moveBranch/updateLeft'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			n_left = n_left + :offset
	WHERE		n_left > :boundary_left
		AND		n_left < :boundary_right
		AND		b_positionlocked = \'FALSE\'
';
$_QUERIES['sbCR/node/moveBranch/updateRight'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			n_right = n_right + :offset
	WHERE		n_right > :boundary_left
		AND		n_right < :boundary_right
		AND		b_positionlocked = \'FALSE\'
';
/*$_QUERIES['sbCR/node/moveBranch/shiftBoth/leftBoundary'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			n_right = n_right + :offset,
				n_left = n_left + :offset
	WHERE		n_left >= :boundary
		AND		b_positionlocked = \'FALSE\'
';
$_QUERIES['sbCR/node/moveBranch/shiftBoth/rightBoundary'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			n_right = n_right + :offset,
				n_left = n_left + :offset
	WHERE		n_left <= :boundary
		AND		b_positionlocked = \'FALSE\'
';
$_QUERIES['sbCR/node/moveBranch/shiftBoth/bothBoundaries'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			n_right = n_right + :offset,
				n_left = n_left + :offset
	WHERE		n_left > :boundaryleft
		AND		n_left < :boundaryright
		AND		b_positionlocked = \'FALSE\'
';
$_QUERIES['sbCR/node/moveBranch/updateNewParent'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			n_right = n_right + :offsetleft,
				n_left = n_left + :offsetright
	WHERE		fk_child = :newparent_uuid
		AND		b_primary = \'TRUE\'
';*/

// references & softlinks ------------------------------------------------------
$_QUERIES['sbCR/node/getReferences'] = '
	SELECT		DISTINCT p.fk_node
	FROM		{TABLE_PROPERTIES} p
	INNER JOIN	{TABLE_NODES} n
		ON		p.fk_node = n.uuid
	INNER JOIN	{TABLE_NODETYPES} nt
		ON		n.fk_nodetype = nt.s_type
	INNER JOIN	{TABLE_PROPERTYDEFS} pd
		ON		pd.fk_nodetype = nt.s_type
	WHERE		pd.e_type = \'REFERENCE\'
		AND		p.m_content = :uuid
';
$_QUERIES['sbCR/node/getSoftlinks'] = '
	SELECT		DISTINCT p.fk_node
	FROM		{TABLE_PROPERTIES} p
	INNER JOIN	{TABLE_NODES} n
		ON		p.fk_node = n.uuid
	INNER JOIN	{TABLE_NODETYPES} nt
		ON		n.fk_nodetype = nt.s_type
	INNER JOIN	{TABLE_PROPERTYDEFS} pd
		ON		pd.fk_nodetype = nt.s_type
	WHERE		pd.e_type = \'WEAKREFERENCE\'
		AND		p.m_content = :uuid
';
// property-related ------------------------------------------------------------
$_QUERIES['sbCR/node/getPropertyDefinitions'] = '
	SELECT		pd.s_attributename,
				pd.e_type,
				pd.s_internaltype,
				pd.s_labelpath,
				pd.b_showinproperties,
				pd.e_storagetype,
				pd.s_auxname,
				pd.b_protected,
				pd.b_protectedoncreation,
				pd.s_defaultvalues,
				pd.s_labelpath
	FROM		{TABLE_PROPERTYDEFS} pd
	WHERE		pd.fk_nodetype = :nodetype
		AND		pd.e_type != \'BINARY\'
	ORDER BY	pd.n_order
';

$_QUERIES['sbCR/node/loadProperties/extended'] = '
	SELECT		n.s_name,
				n.s_uid,
				n.s_label,
				n.s_customcsstype,
				n.b_inheritrights,
				n.b_bequeathrights,
				n.fk_createdby,
				n.fk_modifiedby,
				n.fk_deletedby,
				n.dt_createdat,
				n.dt_modifiedat,
				n.dt_deletedat
	FROM		{TABLE_NODES} n
	WHERE		n.uuid = :node_id
';
$_QUERIES['sbCR/node/loadProperties/external'] = '
	SELECT		p.fk_attributename AS s_attributename,
				p.m_content AS s_value
	FROM		{TABLE_PROPERTIES} p
	WHERE		p.fk_node = :node_id
';
$_QUERIES['sbCR/node/saveProperty/external'] = '
	INSERT INTO	{TABLE_PROPERTIES}
				(
					fk_node,
					fk_attributename,
					m_content
				) VALUES (
					:node_id,
					:attributename,
					:content
				)
	ON DUPLICATE KEY UPDATE
				m_content = :content
';
$_QUERIES['sbCR/node/saveBinaryProperty'] = '
	INSERT INTO	{TABLE_BINPROPERTIES}
				(
					fk_node,
					fk_attributename,
					m_content
				) VALUES (
					:node_id,
					:attributename,
					:content
				)
	ON DUPLICATE KEY UPDATE
				m_content = :content
';
$_QUERIES['sbCR/node/loadBinaryProperty'] = '
	SELECT		m_content
	FROM		{TABLE_BINPROPERTIES}
	WHERE		fk_node = :node_id
		AND		fk_attributename = :property
';
// locking ---------------------------------------------------------------------
$_QUERIES['sbCR/node/locking/clearLocks'] = '
	DELETE FROM	{TABLE_LOCKS} l
	WHERE		UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(l.ts_placed) > l.n_timetolive
';
$_QUERIES['sbCR/node/locking/checkLock/onNode'] = '
	SELECT		fk_user
	FROM		{TABLE_LOCKS} l
	WHERE		fk_lockednode = :node_uuid
';
$_QUERIES['sbCR/node/locking/checkLock/descendents'] = '
	SELECT		snl.fk_user,
				snl.fk_lockednode
	FROM		{TABLE_LOCKS} l
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		h.fk_child = l.fk_lockednode
	WHERE		h.n_left < :left
		AND		h.n_right > :right
';
$_QUERIES['sbCR/node/locking/checkLock/ancestors'] = '
	SELECT		l.fk_user,
				l.fk_lockednode
	FROM		{TABLE_LOCKS} l
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		h.fk_child = l.fk_lockednode
	WHERE		h.n_left > :left
		AND		h.n_right < :right
';
$_QUERIES['sbCR/node/locking/refreshLock'] = '
	UPDATE		{TABLE_LOCKS}
	SET			dt_placed = NOW()
	WHERE		fk_lockednode = :node_uuid
';
$_QUERIES['sbCR/node/locking/placeLock'] = '
	INSERT INTO	{TABLE_LOCKS}
				(
					fk_lockednode,
					fk_user,
					s_sessionid,
					b_deep,
					dt_placed,
					n_timetolive
				) VALUES (
					:node_uuid,
					:user_uuid,
					:session_uuid,
					:deep,
					NOW(),
					:timetolive
				)
';
$_QUERIES['sbCR/node/locking/removeLock/byNode'] = '
	DELETE FROM	{TABLE_LOCKS}
	WHERE		fk_lockednode = :node_uuid
';
$_QUERIES['sbCR/node/locking/removeLock/byUser'] = '
	DELETE FROM	{TABLE_LOCKS}
	WHERE		fk_user = :user_uuid
';

//------------------------------------------------------------------------------
// node:default
//------------------------------------------------------------------------------
// view:security ---------------------------------------------------------------
$_QUERIES['sb_system/node/view/security/addAuthorisation'] = '
	INSERT INTO	{TABLE_AUTH}
				(
					fk_subject,
					fk_userentity,
					fk_authorisation,
					e_granttype
				) VALUES (
					:subject_uuid,
					:entity_uuid,
					:authorisation,
					:granttype
				)
';
$_QUERIES['sb_system/node/view/security/removeAuthorisations'] = '
	DELETE FROM	{TABLE_AUTH}
	WHERE		fk_subject = :subject_uuid
		AND		fk_userentity = :entity_uuid
';
$_QUERIES['sbCR/test/loadRoot'] = '
	SELECT		uuid
	FROM		{TABLE_NODES}
	WHERE		s_uid = \'sbSystem:Root\'
';
$_QUERIES['sbCR/test/loadChildren'] = '
	SELECT		fk_child as uuid
	FROM		{TABLE_HIERARCHY}
	WHERE		fk_parent = :fk_parent
	ORDER BY	n_order
';
$_QUERIES['sbCR/test/setCoordinates'] = '
	INSERT INTO	{TABLE_HIERARCHYMEM}
				(
					n_left,
					n_right,
					n_level,
					n_order,
					fk_child,
					fk_parent
				) VALUES (
					:left,
					:right,
					:level,
					:order,
					:fk_child,
					:fk_parent
				)
	ON DUPLICATE KEY UPDATE
				n_left = :left,
				n_right = :right,
				n_level = :level,
				n_order = :order
';










		
?>