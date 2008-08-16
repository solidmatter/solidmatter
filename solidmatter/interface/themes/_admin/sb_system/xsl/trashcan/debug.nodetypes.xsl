<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml">

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
	</head>
	<body>
		<xsl:call-template name="views" />
		<div class="workbench">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="response/content/nodetypes" />
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template match="response/content/nodetypes">
		<ul>
			<xsl:for-each select="row">
				<li><xsl:value-of select="@s_type" /></li>
				<xsl:call-template name="render_views">
					<xsl:with-param name="nodetype" select="@s_type" />
				</xsl:call-template>
			</xsl:for-each>
		</ul>
	</xsl:template>
	
	<xsl:template name="render_views">
		<xsl:param name="nodetype" />
		<xsl:if test="/response/content/views/row[@fk_nodetype=$nodetype]">
			<ul>
				<xsl:for-each select="/response/content/views/row[@fk_nodetype=$nodetype]">
					<li><xsl:value-of select="@s_view" /></li>
					<xsl:call-template name="render_viewactions">
						<xsl:with-param name="nodetype" select="$nodetype" />
						<xsl:with-param name="view" select="@s_view" />
					</xsl:call-template>
				</xsl:for-each>
			</ul>		
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="render_viewactions">
		<xsl:param name="nodetype" />
		<xsl:param name="view" />
		<xsl:if test="/response/content/viewactions/row[@fk_nodetype=$nodetype and @s_view=$view]">
			<ul>
				<xsl:for-each select="/response/content/viewactions/row[@fk_nodetype=$nodetype and @s_view=$view]">
					<li><xsl:value-of select="@s_action" /></li>
				</xsl:for-each>
			</ul>		
		</xsl:if>
	</xsl:template>
	
</xsl:stylesheet>