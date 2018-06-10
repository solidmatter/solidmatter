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
		
		<xsl:apply-templates select="scripts" />
		
	</xsl:template>
	
	<xsl:template match="scripts">
		<table class="default">
			<thead>
				<tr><th colspan="4">Scripts (<xsl:value-of select="@totalfiles" /> scripts, <xsl:value-of select="@totalcodelines" /> codelines total)</th></tr>
				<tr class="th2">
					<th>Filename</th>
					<th style="text-align:right">Filesize</th>
					<th style="text-align:right">Codelines</th>
					<th style="text-align:right">Version</th>
				</tr>
			</thead>
			<tbody>
			<xsl:for-each select="files/file">
				<tr>
					<xsl:choose>
						<xsl:when test="position() mod 2 = 1">
							<xsl:attribute name="class">odd</xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
							<xsl:attribute name="class">even</xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
					<td><xsl:value-of select="@name" /></td>
					<td style="text-align:right"><xsl:value-of select="@hrsize" /></td>
					<td style="text-align:right"><xsl:value-of select="@codelines" /></td>
					<td style="text-align:right"><xsl:value-of select="@version" /></td>
				</tr>
			</xsl:for-each>
			</tbody>
			<tfoot><tr><td colspan="4"></td></tr></tfoot>
		</table>
	</xsl:template>	
	
	
</xsl:stylesheet>