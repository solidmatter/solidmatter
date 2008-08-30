<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:dyn="http://exslt.org/dynamic" extension-element-prefixes="dyn">

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
		<div class="workbench" style="padding:0;">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="response/content" />
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:variable name="subjecttype" select="response/content/sbnode/@subjecttype" />
	<xsl:variable name="subjectid" select="response/content/sbnode/@subjectid" />
	
	<xsl:template match="response/content">
		<!--<xsl:variable name="subjecttype" select="sbnode/@subjecttype" />
		<xsl:variable name="subjectid" select="sbnode/@subjectid" />-->
		<div class="eyecandy"><div class="left"><div class="right">
			<h1>Berechtigungen von XYZ</h1>
			<table>
				<tr>
					<th>Berechtigung</th>
					<th>Erl.</th>
					<th>Ver.</th>
				</tr>
				<xsl:apply-templates select="sbnode/supported_authorisations/authorisation[1]" />
			</table>
		</div></div></div>
	</xsl:template>
	
	<xsl:template match="sbnode/supported_authorisations/authorisation[1]">
		<xsl:call-template name="build_authorisation">
			<xsl:with-param name="parent" select="''" />
			<xsl:with-param name="prefix" select="''" />
		</xsl:call-template>
	</xsl:template>
	
	<xsl:template name="build_authorisation">
		<xsl:param name="parent" />
		<xsl:param name="prefix" />
		<xsl:for-each select="../authorisation[@parent=$parent]">
			<xsl:variable name="name" select="@name" />
			<xsl:variable name="inherited" select="$content/sbnode/inherited_authorisations/authorisation[@name=$name and @uuid=$subjectid]" />
			<xsl:variable name="local" select="$content/sbnode/local_authorisations/authorisation[@name=$name and @uuid=$subjectid]" />
			<tr>
				<td><xsl:value-of select="$prefix" /><xsl:value-of select="$locale/*/authorisations/*[name()=$name]" /></td>
				<td>
					<input type="checkbox" name="{$name}|allow">
						<xsl:if test="$inherited">
							<xsl:attribute name="disabled">disabled</xsl:attribute>
						</xsl:if>
						<xsl:if test="$inherited[@grant_type='ALLOW'] or $local[@grant_type='ALLOW']">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
				</td>
				<td>
					<input type="checkbox" name="{$name}|deny">
						<xsl:if test="$inherited[@grant_type='DENY'] or $local[@grant_type='DENY']">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
				</td>
			</tr>
			<xsl:call-template name="build_authorisation">
				<xsl:with-param name="parent" select="@name" />
				<xsl:with-param name="prefix" select="concat($prefix, '&#160;&#160;')" />
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
	
	<!--<xsl:template name="build_authorisation">
		<xsl:param name="parent" />
		<xsl:param name="prefix" />
		<xsl:for-each select="../authorisation[@parent=$parent]">
			<xsl:variable name="name" select="@name" />
			<xsl:variable name="inherited" select="$content/sbnode/inherited_authorisations/authorisation[@name=$name and @uuid=$subjectid]" />
			<xsl:variable name="local" select="$content/sbnode/local_authorisations/authorisation[@name=$name and @uuid=$subjectid]" />
			<tr>
				<td><xsl:value-of select="$prefix" /><xsl:value-of select="$locale/*/authorisations/*[name()=$name]" /></td>
				<td>
					<input type="checkbox" name="{$name}|allow">
						<xsl:if test="$inherited">
							<xsl:attribute name="disabled">disabled</xsl:attribute>
						</xsl:if>
						<xsl:if test="$inherited[@grant_type='ALLOW'] or $local[@grant_type='ALLOW']">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
				</td>
				<td>
					<input type="checkbox" name="{$name}|deny">
						<xsl:if test="$inherited[@grant_type='DENY'] or $local[@grant_type='DENY']">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
				</td>
			</tr>
			<xsl:call-template name="build_authorisation">
				<xsl:with-param name="parent" select="@name" />
				<xsl:with-param name="prefix" select="concat($prefix, '&#160;&#160;')" />
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>-->

</xsl:stylesheet>