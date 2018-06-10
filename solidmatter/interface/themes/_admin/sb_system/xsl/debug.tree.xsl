<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml">

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
			<xsl:apply-templates select="response/content/root" />
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template match="root">
		<ul>
			<li>ROOT</li>
			<ul>
				<xsl:apply-templates select="sbnode" />
			</ul>
		</ul>
	</xsl:template>
	
	<xsl:template match="sbnode">
		<li>
			<xsl:value-of select="@level"/>.<xsl:value-of select="@order"/>
			<span title="{@nodetype}"><xsl:value-of select="concat(' ', @name)"/></span>
			<xsl:choose>
				<xsl:when test="@primary = 'TRUE'">
					<span style="color:green; font-weight:bold;"> P</span>
				</xsl:when>
				<xsl:otherwise>
					<span style="color:blue; font-weight:bold;"> S</span>
				</xsl:otherwise>
			</xsl:choose>
			<span style="color:#888; font-size:0.8em; vertical-align:middle;">
				 - 
				<xsl:value-of select="@mpath"/>
				 - 
				<xsl:value-of select="@uuid"/>
			</span>
			<xsl:if test="sbnode">
				<ul>
					<xsl:apply-templates select="sbnode" />
				</ul>
			</xsl:if>
		</li>		
	</xsl:template>
				
				
</xsl:stylesheet>