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
		<xsl:apply-templates select="response/metadata" />
	</head>
	<body>
		<xsl:call-template name="views" />
		<div class="workbench" style="padding-right:5px;">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</body>
	</html>
	</xsl:template>
		
	<xsl:template match="sbnode">
		
		<xsl:for-each select="children/sbnode">
			<a href="/{@uuid}" class="explorercard" title="{@name}">
				<span class="container">
					<xsl:if test="@nodetype='sb_system:image'">
						<img src="/{@uuid}/preview/outputresized" />
					</xsl:if>
				</span>
				<span class="type {@csstype}"><xsl:value-of select="@name" /></span>
			</a>
		</xsl:for-each>
		
	</xsl:template>

</xsl:stylesheet>