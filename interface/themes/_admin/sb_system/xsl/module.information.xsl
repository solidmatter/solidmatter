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
		<xsl:apply-templates select="properties/interfaces" />
		
	</xsl:template>

	
	<xsl:template match="properties/about">
		<table class="default">
			<tr><th colspan="2">General Info</th></tr>
			<tr>
				<td>Autor(en):</td>
				<td>
					<xsl:for-each select="author">
						<xsl:value-of select="." />
						<xsl:if test="@role">
							(<xsl:value-of select="@role" />)
						</xsl:if>
						<br />
					</xsl:for-each>
				</td>
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
	
	<xsl:template match="properties/interfaces">
		<table class="default">
			<tr><th colspan="2">Interfaces</th></tr>
			<tr class="th2"><th colspan="2">Self</th></tr>
			<tr>
				<td>API</td>
				<td><xsl:value-of select="self/api/@version" /></td>
			</tr>
			<tr>
				<td>Locales</td>
				<td><xsl:value-of select="self/locales/@version" /></td>
			</tr>
			<tr>
				<td>Themes</td>
				<td><xsl:value-of select="self/themes/@version" /></td>
			</tr>
			<tr class="th2"><th colspan="2">Requires</th></tr>
			<xsl:if test="not(requires/*)">
				<tr><td colspan="2">no reqirements</td></tr>
			</xsl:if>
			<xsl:for-each select="requires/*">
				<tr>
					<td><xsl:value-of select="name()" /></td>
					<td><xsl:value-of select="@version" /></td>
				</tr>
			</xsl:for-each>
			<tr class="th2"><th colspan="2">Conflicts</th></tr>
			<xsl:if test="not(conflicts/*)">
				<tr><td colspan="2">no conflicts</td></tr>
			</xsl:if>
			<xsl:for-each select="conflicts/*">
				<tr>
					<td><xsl:value-of select="name()" /></td>
					<td><xsl:value-of select="@version" /></td>
				</tr>
			</xsl:for-each>
		</table>
	</xsl:template>
	
</xsl:stylesheet>