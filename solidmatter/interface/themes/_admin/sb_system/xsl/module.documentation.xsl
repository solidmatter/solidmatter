<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
>

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
			<xsl:apply-templates select="response/content" />
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template match="response/content">
		
		<xsl:apply-templates select="documentation" />
		
	</xsl:template>
	
	<xsl:template match="documentation">
		<table class="default">
			<tr><th colspan="4">Documentation</th></tr>
			<xsl:for-each select="files/file">
				<tr>
					<xsl:call-template name="colorize" />
					<td><a href="documentation/render/file={@name}" target="_blank"><xsl:value-of select="@name" /></a></td>
				</tr>
			</xsl:for-each>
		</table>
	</xsl:template>	
	
	
</xsl:stylesheet>