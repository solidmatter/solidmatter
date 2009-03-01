<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
>

	<xsl:import href="global.views.xsl" />
	<xsl:import href="global.default.xsl" />
	
	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="no"
	/>
	
	<xsl:template match="/">
	<html>
	<head>
		<xsl:apply-templates select="/response/metadata" />
		<script type="text/javascript">
			function toggle (sID) {
				var nodeIcon = document.getElementById('icon:' + sID);
				//alert(nodeIcon);
				var nodeContent = document.getElementById('content:' + sID);
				//alert(nodeContent);
				if (nodeContent.style.display == 'none') {
					nodeIcon.setAttribute('src', '/theme/sb_system/icons/tree_close.gif');
					nodeContent.style.display = 'block';
				} else {
					nodeIcon.setAttribute('src', '/theme/sb_system/icons/tree_open.gif');
					nodeContent.style.display = 'none';
				}
			}
		</script>
	</head>
	<body>
		<xsl:call-template name="views" />
		<div class="workbench">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="response/content" />
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template match="response/content">
		<ul class="tree"><li>
			<ul>
			<xsl:for-each select="repository/nodetypes/nodetype">
				<xsl:call-template name="render_nodetype">
					<xsl:with-param name="path" select="translate(@s_type, ':', '_')" />
				</xsl:call-template>
			</xsl:for-each>
			</ul>
		</li></ul>
	</xsl:template>
	
	<xsl:template name="render_nodetype">
		<xsl:param name="path" />
		<li>
			<xsl:choose>
			<xsl:when test="not(views/view)">
				<img src="/theme/sb_system/icons/tree_nothing.png" alt="" />
			</xsl:when>
			<xsl:otherwise>
				<a href="javascript:toggle('{$path}');">
				<img id="icon:{$path}" src="/theme/sb_system/icons/tree_open.gif" name="open" />
				</a>
			</xsl:otherwise>
			</xsl:choose>
			<span class="type {@s_displaytype}"><xsl:value-of select="@s_type" /></span>
			<xsl:if test="views/view">
				<ul id="content:{$path}" style="display:none;">
				<xsl:for-each select="views/view">
					<xsl:call-template name="render_view">
						<xsl:with-param name="path" select="concat($path, ':', @s_view)" />
					</xsl:call-template>
				</xsl:for-each>
				</ul>
			</xsl:if>
		</li>
	</xsl:template>
	
	<xsl:template name="render_view">
		<xsl:param name="path" />
		<li>
			<xsl:choose>
			<xsl:when test="not(actions/action)">
				<img src="/theme/sb_system/icons/tree_nothing.png" alt="" />
			</xsl:when>
			<xsl:otherwise>
				<a href="javascript:toggle('{$path}');">
					<img id="icon:{$path}" src="/theme/sb_system/icons/tree_open.gif" name="open" />
				</a>
			</xsl:otherwise>
			</xsl:choose>
			<span class="type sb_view"><xsl:value-of select="@s_view" /></span>
			<xsl:if test="actions/action">
				<ul id="content:{$path}" style="display:none;">
				<xsl:for-each select="actions/action">
					<xsl:call-template name="render_action">
						<xsl:with-param name="path" select="concat($path, ':', @s_action)" />
					</xsl:call-template>
				</xsl:for-each>
				</ul>
			</xsl:if>
		</li>
	</xsl:template>
	
	<xsl:template name="render_action">
		<xsl:param name="path" />
		<li>
			<img src="/theme/sb_system/icons/tree_nothing.png" alt="" />
			<span class="type sb_action"><xsl:value-of select="@s_action" /></span>
		</li>
	</xsl:template>
	
</xsl:stylesheet>