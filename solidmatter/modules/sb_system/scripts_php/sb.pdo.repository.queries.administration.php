<?php

global $_QUERIES;

// repository administration ---------------------------------------------------

$_QUERIES['sbCR/nodetype/save'] = '
	INSERT INTO	{TABLE_NODETYPES}
				(
					s_type,
					e_type,
					s_class,
					s_classfile,
					s_category
				) VALUES (
					:nodetype,
					:abstract,
					:type,
					:class,
					:classfile,
					:category
				)
	ON DUPLICATE KEY UPDATE
				s_type = :nodetype,
				e_type = :type,
				s_class = :class,
				s_classfile = :classfile,
				s_category = :category
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


		
?>