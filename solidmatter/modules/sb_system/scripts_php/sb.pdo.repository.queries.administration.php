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
$_QUERIES['sbCR/property/add'] = '
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
';
$_QUERIES['sbCR/property/remove'] = '
	DELETE FROM	{TABLE_PROPERTYDEFS}
	WHERE		fk_nodetype = :nodetype
		AND		s_attributename = :attributename
';
$_QUERIES['sbCR/view/add'] = '
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
';
$_QUERIES['sbCR/view/remove'] = '
	DELETE FROM	{TABLE_VIEWS}
	WHERE		fk_nodetype = :nodetype
		AND		s_view = :view
';
$_QUERIES['sbCR/action/add'] = '
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
';
$_QUERIES['sbCR/action/remove'] = '
	DELETE FROM	{TABLE_ACTIONS}
	WHERE		fk_nodetype = :nodetype
		AND		s_view = :view
		AND		s_action = :action
';
$_QUERIES['sbCR/authorisation/add'] = '
	INSERT INTO	{TABLE_AUTHDEF}
				(
					fk_nodetype,
					s_authorisation,
					fk_parentauthorisation,
					b_default,
					n_order,
					b_onlyfrontend
				) VALUES (
					:nodetype,
					:authorisation,
					:parentauthorisation,
					:default,
					:order,
					:onlyfrontend
				)
';
$_QUERIES['sbCR/authorisation/remove'] = '
	DELETE FROM	{TABLE_AUTHDEF}
	WHERE		fk_nodetype = :nodetype
		AND		s_view = :view
		AND		s_action = :action
';
$_QUERIES['sbCR/viewauthorisation/add'] = '
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
';
$_QUERIES['sbCR/viewauthorisation/remove'] = '
	DELETE FROM	{TABLE_VIEWAUTH}
	WHERE		fk_nodetype = :nodetype
		AND		fk_view = :view
		AND		fk_action = :action
';

?>


		
?>