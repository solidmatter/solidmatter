<?xml version="1.0" encoding="UTF-8" ?>
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
			<xsl:value-of select="concat(' ', @name)"/>
			(<xsl:value-of select="@left"/>, <xsl:value-of select="@right"/>)
			<xsl:if test="sbnode">
				<ul>
					<xsl:apply-templates select="sbnode" />
				</ul>
			</xsl:if>
		</li>		
	</xsl:template>
				
				
</xsl:stylesheet>