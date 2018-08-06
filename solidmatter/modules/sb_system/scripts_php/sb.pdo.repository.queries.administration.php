<?php

global $_QUERIES;

// repository administration ---------------------------------------------------

$_QUERIES['sbCR/nodetype/save'] = '
	INSERT INTO	{TABLE_NODETYPES}
				(
					s_type,
					s_class,
					s_classfile,
					e_type
				) VALUES (
					:nodetype,
					:class,
					:classfile,
					:type
				)
	ON DUPLICATE KEY UPDATE
				s_type = :nodetype,
				s_class = :class,
				s_classfile = :classfile,
				e_type = :type
';
$_QUERIES['sbCR/nodetype/remove'] = '
	DELETE FROM	{TABLE_NODETYPES}
	WHERE		s_type = :nodetype
';
$_QUERIES['sbCR/property/save'] = '
	INSERT INTO	{TABLE_PROPERTYDEFS}
				(
					fk_nodetype,
					s_attributename,
					e_type,
					s_internaltype,
					b_showinproperties,
					s_labelpath,
					e_storagetype,
					s_auxname,
					n_order,
					b_protected,
					b_protectedoncreation,
					b_multiple,
					s_defaultvalues,
					s_descriptionpath
				) VALUES (
					:nodetype,
					:attributename,
					:type,
					:internaltype,
					:showinproperties,
					:labelpath,
					:storagetype,
					:auxname,
					:order,
					:protected,
					:protectedoncreation,
					:multiple,
					:defaultvalues,
					:descriptionpath
				)
	ON DUPLICATE KEY UPDATE
				e_type = :type,
				s_internaltype = :internaltype,
				b_showinproperties = :showinproperties,
				s_labelpath = :labelpath,
				e_storagetype = :storagetype,
				s_auxname = :auxname,
				n_order = :order,
				b_protected = :protected,
				b_protectedoncreation = :protectedoncreation,
				b_multiple = :multiple,
				s_defaultvalues = :defaultvalues,
				s_descriptionpath = :descriptionpath
';
$_QUERIES['sbCR/property/remove'] = '
	DELETE FROM	{TABLE_PROPERTYDEFS}
	WHERE		fk_nodetype = :nodetype
		AND		s_attributename = :attributename
';
$_QUERIES['sbCR/view/save'] = '
	INSERT INTO	{TABLE_VIEWS}
				(
					fk_nodetype,
					s_view,
					b_display,
					s_labelpath,
					s_classfile,
					s_class,
					n_order,
					n_priority
				) VALUES (
					:nodetype,
					:view,
					:display,
					:labelpath,
					:classfile,
					:class,
					:order,
					:priority
				)
	ON DUPLICATE KEY UPDATE
				b_display = :display,
				s_labelpath = :labelpath,
				s_classfile = :classfile,
				s_class = :class,
				n_order = :order,
				n_priority = :priority
';
$_QUERIES['sbCR/view/remove'] = '
	DELETE FROM	{TABLE_VIEWS}
	WHERE		fk_nodetype = :nodetype
		AND		s_view = :view
';
$_QUERIES['sbCR/action/save'] = '
	INSERT INTO	{TABLE_ACTIONS}
				(
					fk_nodetype,
					s_view,
					s_action,
					b_default,
					s_classfile,
					s_class,
					e_outputtype,
					s_stylesheet,
					s_mimetype,
					b_uselocale,
					b_isrecallable
				) VALUES (
					:nodetype,
					:view,
					:action,
					:default,
					:classfile,
					:class,
					:outputtype,
					:stylesheet,
					:mimetype,
					:uselocale,
					:isrecallable
				)
	ON DUPLICATE KEY UPDATE
				b_default = :default,
				s_classfile = :classfile,
				s_class = :class,
				e_outputtype = :outputtype,
				s_stylesheet = :stylesheet,
				s_mimetype = :mimetype,
				b_uselocale = :uselocale,
				b_isrecallable = :isrecallable
';
$_QUERIES['sbCR/action/remove'] = '
	DELETE FROM	{TABLE_ACTIONS}
	WHERE		fk_nodetype = :nodetype
		AND		s_view = :view
		AND		s_action = :action
';
// $_QUERIES['sbCR/authorisation/save'] = '
// 	INSERT INTO	{TABLE_AUTHDEF}
// 				(
// 					fk_nodetype,
// 					s_authorisation,
// 					fk_parentauthorisation,
// 					b_default,
// 					n_order,
// 					b_onlyfrontend
// 				) VALUES (
// 					:nodetype,
// 					:authorisation,
// 					:parentauthorisation,
// 					:default,
// 					:order,
// 					:onlyfrontend
// 				)
// 	ON DUPLICATE KEY UPDATE
				
// ';
// $_QUERIES['sbCR/authorisation/remove'] = '
// 	DELETE FROM	{TABLE_AUTHDEF}
// 	WHERE		fk_nodetype = :nodetype
// 		AND		s_view = :view
// 		AND		s_action = :action
// ';
$_QUERIES['sbCR/viewauthorisation/save'] = '
	INSERT INTO	{TABLE_VIEWAUTH}
				(
					fk_nodetype,
					fk_view,
					fk_action,
					fk_authorisation
				) VALUES (
					:nodetype,
					:view,
					:action,
					:authorisation
				)
	ON DUPLICATE KEY UPDATE
				fk_authorisation = :authorisation
';
$_QUERIES['sbCR/viewauthorisation/remove'] = '
	DELETE FROM	{TABLE_VIEWAUTH}
	WHERE		fk_nodetype = :nodetype
		AND		fk_view = :view
		AND		fk_action = :action
';
$_QUERIES['sbCR/hierarchy/save'] = '
	INSERT IGNORE INTO {TABLE_NTHIERARCHY}
				(
					fk_parentnodetype,
					fk_childnodetype
				) VALUES (
					:parentnodetype,
					:childnodetype
				)
';
$_QUERIES['sbCR/hierarchy/remove'] = '
	DELETE FROM	{TABLE_NTHIERARCHY}
	WHERE		fk_parentnodetype = :parentnodetype
		AND		fk_childnodetype = :childnodetype
';
$_QUERIES['sbCR/mode/save'] = '
	INSERT INTO	{TABLE_MODES}
				(
					s_mode,
					fk_parentnodetype,
					fk_nodetype,
					b_display,
					b_choosable
				) VALUES (
					:mode,
					:parentnodetype,
					:childnodetype,
					:display,
					:choosable
				)
	ON DUPLICATE KEY UPDATE
				b_display = :display,
				b_choosable = :choosable
';
$_QUERIES['sbCR/mode/remove'] = '
	DELETE FROM	{TABLE_MODES}
	WHERE		s_mode = :mode
		AND		fk_parentnodetype = :parentnodetype
		AND		fk_childnodetype = :childnodetype
';
$_QUERIES['sbCR/ontology/save'] = '
	INSERT INTO	{TABLE_ONTOLOGY}
				(
					s_relation,
					fk_sourcenodetype,
					fk_targetnodetype,
					s_reverserelation
				) VALUES (
					:relation,
					:sourcenodetype,
					:targetnodetype,
					:reverserelation
				)
	ON DUPLICATE KEY UPDATE
				s_reverserelation = :reverserelation
';
$_QUERIES['sbCR/ontology/remove'] = '
	DELETE FROM	{TABLE_ONTOLOGY}
	WHERE		s_relation = :relation
		AND		fk_sourcenodetype = :sourcenodetype
		AND		fk_targetnodetype = :targetnodetype
';
$_QUERIES['sbCR/lifecycle/save'] = '
	INSERT IGNORE INTO {TABLE_LIFECYCLE}
				(
					fk_nodetype,
					s_state,
					s_statetransition
				) VALUES (
					:nodetype,
					:state,
					:statetransition
				)
';
$_QUERIES['sbCR/lifecycle/remove'] = '
	DELETE FROM	{TABLE_LIFECYCLE}
	WHERE		fk_nodetype = :nodetype
		AND		s_state = :state
		AND		s_statetransition = :statetransition
';
$_QUERIES['sbCR/registry/save'] = '
	INSERT INTO	{TABLE_REGISTRY}
				(
					s_key,
					e_type,
					s_internaltype,
					b_userspecific,
					s_defaultvalue,
					s_comment
				) VALUES (
					:key,
					:type,
					:internaltype,
					:userspecific,
					:defaultvalue,
					:comment
				)
	ON DUPLICATE KEY UPDATE
				e_type = :type,
				s_internaltype = :internaltype,
				b_userspecific = :userspecific,
				s_defaultvalue = :defaultvalue,
				s_comment = :comment
';
$_QUERIES['sbCR/registry/remove'] = '
	DELETE FROM	{TABLE_REGISTRY}
	WHERE		s_key = :key
';


$_QUERIES['sbCR/module/installed'] = '
	INSERT INTO	{TABLE_MODULES}
				(
					s_name,
					s_title,
					n_mainversion,
					n_subversion,
					n_bugfixversion,
					s_versioninfo,
					dt_installed,
					dt_updated,
					b_uninstallable,
					b_active
				) VALUES (
					:name,
					:title,
					:mainversion,
					:subversion,
					:bugfixversion,
					:versioninfo,
					NOW(),
					NOW(),
					:uninstallable,
					:active
				)
	ON DUPLICATE KEY UPDATE
				s_title = :title,
				n_mainversion = :mainversion,
				n_subversion = :subversion,
				n_bugfixversion = :bugfixversion,
				s_versioninfo = :versioninfo,
				dt_updated = NOW(),
				b_uninstallable = :uninstallable,
				b_active = :active
';
$_QUERIES['sbCR/module/uninstalled'] = '
	DELETE FROM	{TABLE_MODULES}
	WHERE		s_name = :name
';

		
?>