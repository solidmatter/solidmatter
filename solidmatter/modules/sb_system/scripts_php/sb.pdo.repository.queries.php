<?php

global $_QUERIES;

$_QUERIES['MAPPING']['{TABLE_NODETYPES}']		= '{PREFIX_REPOSITORY}_nodetypes';
$_QUERIES['MAPPING']['{TABLE_NAMESPACES}']		= '{PREFIX_REPOSITORY}_namespaces';
$_QUERIES['MAPPING']['{TABLE_NTHIERARCHY}']		= '{PREFIX_REPOSITORY}_nodetypes_inheritance';
$_QUERIES['MAPPING']['{TABLE_LIFECYCLE}']		= '{PREFIX_REPOSITORY}_nodetypes_lifecycles';
$_QUERIES['MAPPING']['{TABLE_VIEWS}']			= '{PREFIX_REPOSITORY}_nodetypes_views';
$_QUERIES['MAPPING']['{TABLE_ACTIONS}']			= '{PREFIX_REPOSITORY}_nodetypes_viewactions';
$_QUERIES['MAPPING']['{TABLE_MODES}']			= '{PREFIX_REPOSITORY}_nodetypes_modes';
$_QUERIES['MAPPING']['{TABLE_AUTHDEF}']			= '{PREFIX_REPOSITORY}_nodetypes_authorisations';
$_QUERIES['MAPPING']['{TABLE_VIEWAUTH}']		= '{PREFIX_REPOSITORY}_nodetypes_viewauthorisations';
$_QUERIES['MAPPING']['{TABLE_PROPERTYDEFS}']	= '{PREFIX_REPOSITORY}_nodetypes_properties';

$_QUERIES['MAPPING']['{TABLE_AUTHCACHE}']		= '{PREFIX_WORKSPACE}_system_cache_authorisations';
$_QUERIES['MAPPING']['{TABLE_PATHCACHE}']		= '{PREFIX_WORKSPACE}_system_cache_paths';

$_QUERIES['MAPPING']['{TABLE_NODES}']			= '{PREFIX_WORKSPACE}_system_nodes';
$_QUERIES['MAPPING']['{TABLE_HIERARCHY}']		= '{PREFIX_WORKSPACE}_system_nodes_parents';
$_QUERIES['MAPPING']['{TABLE_PROPERTIES}']		= '{PREFIX_WORKSPACE}_system_nodes_properties';
$_QUERIES['MAPPING']['{TABLE_BINPROPERTIES}']	= '{PREFIX_WORKSPACE}_system_nodes_properties_binary';
$_QUERIES['MAPPING']['{TABLE_LOCKS}']			= '{PREFIX_WORKSPACE}_system_nodes_locks';
$_QUERIES['MAPPING']['{TABLE_AUTH}']			= '{PREFIX_WORKSPACE}_system_nodes_authorisation';

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
	SELECT		v.s_view AS name,
				v.s_classfile AS classfile,
				v.s_class AS class,
				v.b_display AS visible,
				v.n_priority AS priority,
				v.n_order AS `order`
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

$_QUERIES['sbCR/repository/getNodeTypes'] = '
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
$_QUERIES['sbCR/repository/getNodeTypeHierarchy'] = '
	SELECT		fk_parentnodetype,
				fk_childnodetype
	FROM		{TABLE_NTHIERARCHY}
';

// node retrieval --------------------------------------------------------------

$_QUERIES['sbCR/getNode/root'] = '
	SELECT		n.uuid,
				n.s_uid,
				n.fk_nodetype,
				n.s_name,
				n.s_label,
				n.b_inheritrights,
				n.b_bequeathrights,
				n.b_bequeathlocalrights,
				n.s_currentlifecyclestate,
				nt.s_classfile,
				nt.s_class,
				NULL AS fk_parent,
				\'TRUE\' AS b_primary
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
				n.b_inheritrights,
				n.b_bequeathrights,
				n.b_bequeathlocalrights,
				n.s_currentlifecyclestate,
				nt.s_classfile,
				nt.s_class,
				h.fk_parent,
				h.b_primary
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_NODETYPES} nt
		ON		n.fk_nodetype = nt.s_type
	LEFT JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	WHERE		n.uuid = :id
	ORDER BY	h.b_primary ASC /* caution: this has to be ASC because the enum-field is in order! TRUE=1 FALSE=2 */
	LIMIT		0, 1
';
$_QUERIES['sbCR/getNode/byUID'] = '
	SELECT		n.uuid,
				n.s_uid,
				n.fk_nodetype,
				n.s_name,
				n.s_label,
				n.b_inheritrights,
				n.b_bequeathrights,
				n.b_bequeathlocalrights,
				n.s_currentlifecyclestate,
				nt.s_classfile,
				nt.s_class,
				h.fk_parent,
				h.b_primary
	FROM		{TABLE_NODES} n
	INNER JOIN	{TABLE_NODETYPES} nt
		ON		n.fk_nodetype = nt.s_type
	INNER JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	WHERE		n.s_uid = :uid
	ORDER BY	h.b_primary ASC /* caution: this has to be ASC because the enum-field is in order! TRUE=1 FALSE=2 */
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
$_QUERIES['sbCR/node/loadChildren/mode/standard/unordered'] = '
	SELECT		n.uuid,
				n.s_label,
				h.b_primary
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
		AND		h.fk_deletedby IS NULL /* prevent nodes in trash from showing up */
';
$_QUERIES['sbCR/node/loadChildren/mode/standard/byOrder'] = $_QUERIES['sbCR/node/loadChildren/mode/standard/unordered'].'
	ORDER BY	h.n_order
';
$_QUERIES['sbCR/node/loadChildren/mode/standard/byLabel'] = $_QUERIES['sbCR/node/loadChildren/mode/standard/unordered'].'
	ORDER BY	n.s_label
';
$_QUERIES['sbCR/node/loadChildren/mode/standard/byNodetype'] = $_QUERIES['sbCR/node/loadChildren/mode/standard/unordered'].'
	ORDER BY	n.fk_nodetype
';
$_QUERIES['sbCR/node/loadChildren/mode/standard/byNodetypeAndLabel'] = $_QUERIES['sbCR/node/loadChildren/mode/standard/unordered'].'
	ORDER BY	n.fk_nodetype, n.s_label
';
$_QUERIES['sbCR/node/loadChildren/mode/standard/byCreationDate/ASC'] = $_QUERIES['sbCR/node/loadChildren/mode/standard/unordered'].'
	ORDER BY	n.dt_created
';
$_QUERIES['sbCR/node/loadChildren/mode/standard/byCreationDate/DESC'] = $_QUERIES['sbCR/node/loadChildren/mode/standard/unordered'].'
	ORDER BY	n.dt_created DESC
';
$_QUERIES['sbCR/node/loadChildren/debug'] = '
	SELECT		n.uuid,
				n.fk_nodetype,
				n.s_name,
				n.s_label,
				h.b_primary
	FROM		{TABLE_NODES} n
	LEFT JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child
	WHERE		h.fk_parent = :parent_uuid
		AND		h.fk_child != :parent_uuid /* prevent root finding itself */
		AND		h.fk_deletedby IS NULL /* prevent nodes in trash from showing up */
	ORDER BY	h.n_order
';





$_QUERIES['sbCR/node/getChild/byName'] = '
	SELECT		n.uuid,
				n.fk_nodetype,
				n.s_name
	FROM		{TABLE_NODES} n
	LEFT JOIN	{TABLE_HIERARCHY} h
		ON		n.uuid = h.fk_child 
	WHERE		n.s_name = :name
		AND		h.fk_parent = :parent_uuid
		AND		h.fk_deletedby IS NULL /* prevent nodes in trash from showing up */
';
// TODO: move this query from file
$_QUERIES['sbSystem/node/getAllowedSubtypes'] = '
	SELECT		m.fk_parentnodetype,
				m.fk_nodetype,
				REPLACE(fk_nodetype, \':\', \'_\') AS displaytype
	FROM		{TABLE_MODES} m
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
	SELECT		fk_parent,
				b_primary
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
$_QUERIES['sbCR/node/checkDescendant'] = '
	SELECT		h.fk_parent
	FROM		{TABLE_HIERARCHY} h
	WHERE		h.fk_parent = :parent_uuid
		AND		h.s_mpath LIKE (
					SELECT		s_mpath
					FROM		{TABLE_HIERARCHY}
					WHERE		fk_child = :child_uuid
						AND		b_primary = \'TRUE\'
				)
';

// node counting ---------------------------------------------------------------

$_QUERIES['sbCR/node/countChildren/debug'] = '
	SELECT		COUNT(*) as num_children
	FROM		{TABLE_HIERARCHY} h
	WHERE		h.fk_parent = :parent_uuid
		AND		h.fk_deletedby IS NULL /* prevent nodes in trash from showing up */
';
$_QUERIES['sbCR/node/countChildren/mode'] = '
	SELECT		COUNT(*) as num_children
	FROM		{TABLE_NODES} n
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
		AND		h.fk_deletedby IS NULL /* prevent nodes in trash from showing up */
';

$_QUERIES['sbCR/node/countChildrenByName/debug'] = '
	SELECT		COUNT(*) as num_children
	FROM		{TABLE_HIERARCHY} h
	INNER JOIN	{TABLE_NODES} n
		ON		n.uuid = h.fk_child
	WHERE		h.fk_parent = :parent_uuid
		AND		n.s_name = :child_name
';

// descendants
/*$_QUERIES['sbCR/node/countDescendants/all'] = '
	SELECT		COUNT(*) as num_descendants
';
$_QUERIES['sbCR/node/countDescendants/unique'] = '
	SELECT		COUNT(DISTINCT n.uuid) as num_descendants
';
$_QUERIES['sbCR/node/countDescendants/base'] = '
	FROM		{TABLE_HIERARCHY} h
	INNER JOIN	{TABLE_NODES} n
		ON		h.fk_child = n.uuid
';
$_QUERIES['sbCR/node/countDescendants/byMode'] = '
	FROM		{TABLE_HIERARCHY} h
	INNER JOIN	{TABLE_NODES} n
		ON		h.fk_child = n.uuid
	WHERE		h.s_mpath LIKE CONCAT(:current_mpath, \'%\')
';
$_QUERIES['sbCR/node/countDescendants/pathConstraint'] = '
	WHERE		h.s_mpath LIKE CONCAT(:current_mpath, \'%\')
';
$_QUERIES['sbCR/node/countDescendants/byNodetype'] = '
		AND		n.nodetype = :nodetype
';
$_QUERIES['sbCR/node/countDescendants/onlyPrimary'] = '
		AND		h.b_primary = \'TRUE\'
';
$_QUERIES['sbCR/node/countDescendants/onlySecondary'] = '
		AND		h.b_primary = \'FALSE\'
';

$_QUERIES['sbCR/node/countUniqueDescendants/byMode'] = '
	SELECT		COUNT(DISTINCT n.uuid) as num_descendants
	FROM		{TABLE_HIERARCHY} h
	INNER JOIN	{TABLE_NODES} n
		ON		h.fk_child = n.uuid
	WHERE		h.s_mpath LIKE CONCAT(:current_mpath, \'%\')
		AND		n.nodetype IN (
					SELECT		m.fk_nodetype
					FROM		{TABLE_MODES} m
					WHERE		m.fk_parentnodetype = :parent_nodetype
						AND		m.s_mode = :mode
				)
';
$_QUERIES['sbCR/node/countUniqueDescendants/byNodetype'] = '
	SELECT		COUNT(DISTINCT n.uuid) as num_descendants
	FROM		{TABLE_HIERARCHY} h
	INNER JOIN	{TABLE_NODES} n
		ON		h.fk_child = n.uuid
	WHERE		h.s_mpath LIKE CONCAT(:current_mpath, \'%\')
		AND		n.nodetype = :nodetype
';*/

// save/delete -----------------------------------------------------------------

$_QUERIES['sbCR/node/save'] = '
	INSERT INTO	{TABLE_NODES}
				(
					uuid,
					s_uid,
					fk_nodetype,
					s_label,
					s_name,
					b_inheritrights,
					b_bequeathrights,
					b_bequeathlocalrights,
					fk_createdby,
					fk_modifiedby,
					dt_created,
					dt_modified
				) VALUES (
					:uuid,
					:uid,
					:nodetype,
					:label,
					:name,
					:inheritrights,
					:bequeathrights,
					:bequeathlocalrights,
					:user_id,
					:user_id,
					NOW(),
					NOW()
				)
	ON DUPLICATE KEY UPDATE
				s_uid = :uid,
				s_label = :label,
				s_name = :name,
				b_inheritrights = :inheritrights,
				b_bequeathrights = :bequeathrights,
				b_bequeathlocalrights = :bequeathlocalrights,
				fk_modifiedby = :user_id,
				dt_modified = NOW()
';
$_QUERIES['sbCR/node/delete/forGood'] = '
	DELETE FROM	{TABLE_NODES}
	WHERE		uuid = :uuid
';

// linking ---------------------------------------------------------------------

$_QUERIES['sbCR/node/addLink/getBasicInfo'] = '
	SELECT		n_level,
				(SELECT	COUNT(*)
					FROM	{TABLE_HIERARCHY}
					WHERE	fk_parent = :current_uuid
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
						AND		h.fk_parent = :current_uuid
				) AS n_numsamenamesiblings
	FROM		{TABLE_HIERARCHY}
	WHERE		fk_child = :current_uuid
		AND		fk_parent = :parent_uuid
';
$_QUERIES['sbCR/node/addLink/insertNode'] = '
	INSERT INTO	{TABLE_HIERARCHY}
				(
					fk_child,
					fk_parent,
					b_primary,
					n_order,
					n_level,
					s_mpath
				) VALUES (
					:child_uuid,
					:parent_uuid,
					:is_primary,
					:order,
					:level,
					:mpath
				)
';
$_QUERIES['sbCR/node/hierarchy/getInfo'] = '
	SELECT		n_level,
				n_order,
				b_primary,
				fk_deletedby,
				dt_deleted
	FROM		{TABLE_HIERARCHY}
	WHERE		fk_parent = :parent_uuid
		AND		fk_child = :child_uuid
';
$_QUERIES['sbCR/node/removeDescendantLinks'] = '
	DELETE FROM	{TABLE_HIERARCHY}
	WHERE		h.s_mpath LIKE CONCAT(:mpath, \'%\')
';
$_QUERIES['sbCR/node/removeLink'] = '
	DELETE FROM	{TABLE_HIERARCHY}
	WHERE		fk_parent = :parent_uuid
		AND		fk_child = :child_uuid;
';
$_QUERIES['sbCR/node/getLinkStatus'] = '
	SELECT		h.b_primary
	FROM		{TABLE_HIERARCHY} h
	WHERE		h.fk_parent = :parent_uuid
		AND		h.fk_child = :child_uuid
';
$_QUERIES['sbCR/node/setLinkStatus'] = '
	UPDATE		{TABLE_HIERARCHY}
	SET			b_primary = :status
	WHERE		fk_parent = :parent_uuid
		AND		fk_child = :child_uuid
';
$_QUERIES['sbCR/node/setLinkStatus/allSecondary'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			b_primary = \'FALSE\'
	WHERE		h.fk_child = :child_uuid
';
// mysql doesn't support LIMIT in IN clause 
/*$_QUERIES['sbCR/node/setLinkStatus/newPrimary'] = '
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
';*/
$_QUERIES['sbCR/node/hierarchy/moveSiblings'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			h.n_order = h.n_order + :offset
	WHERE		h.fk_parent = :parent_uuid
		AND		h.n_order >= :low_position
		AND		h.n_order <= :high_position
';
$_QUERIES['sbCR/node/orderBefore/getInfo'] = '
	SELECT		h.n_order,
				n.uuid
	FROM		{TABLE_HIERARCHY} h
	INNER JOIN	{TABLE_NODES} n
		ON		h.fk_child = n.uuid
	WHERE		h.fk_parent = :parent_uuid
		AND		n.s_name = :child_name
';
$_QUERIES['sbCR/node/orderBefore/writeOrder/node'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			h.n_order = :target_position
	WHERE		h.fk_child = :child_uuid
		AND		h.fk_parent = :parent_uuid
';

// move branch -----------------------------------------------------------------

$_QUERIES['sbCR/node/moveBranch/getSourceInfo'] = '
	SELECT		n_order,
				n_level,
				b_primary,
				s_mpath,
				(SELECT		MAX(n_order)
					FROM	{TABLE_HIERARCHY}
					WHERE	fk_parent = :oldparent_uuid
				) AS n_maxorder
	FROM		{TABLE_HIERARCHY} h
	WHERE		fk_child = :subject_uuid
		AND		fk_parent = :oldparent_uuid
';
$_QUERIES['sbCR/node/moveBranch/getDestinationInfo'] = '
	SELECT		n_order,
				n_level,
				s_mpath,
				(SELECT		COUNT(*)
					FROM	{TABLE_HIERARCHY}
					WHERE	fk_parent = :newparent_uuid
				) AS n_numchildren
	FROM		{TABLE_HIERARCHY} h
	WHERE		fk_child = :newparent_uuid
		AND		b_primary = \'TRUE\'
';
$_QUERIES['sbCR/node/moveBranch/updateBranch'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			h.n_level = h.n_level + :offset_level,
				h.s_mpath = REPLACE(h.s_mpath, :old_mpath, :new_mpath)
	WHERE		h.s_mpath LIKE CONCAT(:old_mpath, \'%\')
';
$_QUERIES['sbCR/node/moveBranch/updateLink'] = '
	UPDATE		{TABLE_HIERARCHY} h
	SET			h.fk_parent = :newparent_uuid,
				h.n_order = :order,
				h.s_mpath = :mpath
	WHERE		h.fk_parent = :oldparent_uuid
		AND		h.fk_child = :subject_uuid
';

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
				n.b_inheritrights,
				n.b_bequeathrights,
				n.b_bequeathlocalrights,
				n.fk_createdby,
				n.fk_modifiedby,
				n.dt_created,
				n.dt_modified
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

// lifecycle -------------------------------------------------------------------

$_QUERIES['sbCR/node/lifecycle/getAllowedTransitions'] = '
	SELECT		s_statetransition AS transition
	FROM		{TABLE_LIFECYCLE}
	WHERE		fk_nodetype = :nodetype
		AND		s_state = :state
';
$_QUERIES['sbCR/node/lifecycle/followTransition'] = '
	UPDATE		{TABLE_NODES}
	SET			s_currentlifecyclestate = :state
	WHERE		uuid = :node_uuid
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
$_QUERIES['sb_system/node/view/security/changeInheritance'] = '
	UPDATE		{TABLE_NODES}
	SET			b_inheritrights = :inheritrights,
				b_bequeathrights = :bequeathrights,
				b_bequeathlocalrights = :bequeathlocalrights,
				fk_modifiedby = :user_id,
				dt_modified = NOW()
	WHERE		uuid = :subject_uuid
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

?>