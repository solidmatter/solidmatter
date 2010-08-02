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
		
		<xsl:apply-templates select="properties/about" />
		
		<table class="default">
			<tr>
				<th colspan="5">Modules</th>
			</tr>
			<tr class="th2">
				<th>Name</th>
				<th>Version</th>
				<th>Installed</th>
				<th>last update</th>
				<th>update available</th>
			</tr>
			<xsl:for-each select="modules/row">
				<tr>
					<td><xsl:value-of select="s_name" /></td>
					<td><xsl:value-of select="n_mainversion" />.<xsl:value-of select="n_subversion" />.<xsl:value-of select="n_bugfixversion" />
						<xsl:value-of select="s_versioninfo" />
					</td>
					<td><xsl:value-of select="dt_installed" /></td>
					<td><xsl:value-of select="dt_updated" /></td>
					<td><xsl:value-of select="'no'" /></td>
				</tr>
			</xsl:for-each>
		</table>
	</xsl:template>

	
	<xsl:template match="properties/about">
		<table class="default">
			<tr><th colspan="2">General Info</th></tr>
			<tr>
				<td>Autor:</td>
				<td><xsl:value-of select="author" /></td>
			</tr>
			<tr>
				<td>Homepage:</td>
				<td><a href="{homepage/@url}" target="_blank"><xsl:value-of select="homepage" /></a></td>
			</tr>
			<tr>
				<td>Feedback:</td>
				<td><a href="{feedback/@url}"><xsl:value-of select="feedback" /></a></td>
			</tr>
			<tr>
				<td>Support:</td>
				<td><a href="{support/@url}"><xsl:value-of select="support" /></a></td>
			</tr>
		</table>
	</xsl:template>	
	
</xsl:stylesheet>