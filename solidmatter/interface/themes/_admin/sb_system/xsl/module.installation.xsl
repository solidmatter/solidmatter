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
		
		<xsl:apply-templates select="structure" />
		
	</xsl:template>

	
	<xsl:template match="structure">
		<xsl:if test="$master/@installed = 'TRUE'">
		<table class="default">
			<thead>
				<tr><th>Currently Installed</th></tr>
			</thead>
			<tbody>
				<tr>
					<td>Version
						<xsl:value-of select="$master/@version_main" />.<xsl:value-of select="$master/@version_sub" />.<xsl:value-of select="$master/@version_bugfix" />
						<xsl:value-of select="$master/@version_suffix" />
					</td>
				</tr>
			</tbody>
			<tfoot><tr><td colspan="2"></td></tr></tfoot>
		</table>
		</xsl:if>
		<table class="default">
			<thead>
				<tr><th>Available Options</th></tr>
			</thead>
			<tbody>
				<xsl:for-each select="option">
					<xsl:choose>
					<xsl:when test="@type = 'install' and $master/@installed != 'TRUE'">
						<tr><td><a href="/sbSystem:Modules::{$master/@name}/installation/install?version={@version}" class="type install highlighted">Install Version <xsl:value-of select="@version" /></a></td></tr>
					</xsl:when>
					<xsl:when test="@type = 'install' and $master/@installed = 'TRUE'">
						<tr><td><a href="/sbSystem:Modules::{$master/@name}/installation/install?version={@version}" class="type install highlighted">Reinstall Version <xsl:value-of select="@version" /> &lt;- only for debugging purposes, option will be removed</a></td></tr>
					</xsl:when>
					<xsl:when test="@type = 'update' and @from = $master/@version">
						<tr><td><a href="/sbSystem:Modules::{$master/@name}/installation/update?to={@to}" class="type update highlighted">Update from Version <xsl:value-of select="@from" /> to Version <xsl:value-of select="@to" /></a></td></tr>
					</xsl:when>
					<xsl:when test="@type = 'uninstall' and @version = $master/@version">
						<tr><td><a href="/sbSystem:Modules::{$master/@name}/installation/uninstall?version={@version}" class="type uninstall warning">Uninstall Version <xsl:value-of select="@version" /></a></td></tr>
					</xsl:when>
					</xsl:choose>
				</xsl:for-each>
			</tbody>
			<tfoot><tr><td colspan="2"></td></tr></tfoot>
		</table>
	</xsl:template>
	
	<xsl:template match="properties/interfaces">
		<table class="default">
			<thead>
				<tr><th colspan="2">Interfaces</th></tr>
				<tr class="th2"><th colspan="2">Self</th></tr>
			</thead>
			<tbody>
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
			</tbody>
			<tfoot><tr><td colspan="2"></td></tr></tfoot>
		</table>
	</xsl:template>
	
</xsl:stylesheet>