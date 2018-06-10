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
			<h1><xsl:value-of select="$locale/sbSystem/labels/security/authorisations_of" /> <xsl:call-template name="localize"><xsl:with-param name="label" select="$content/userentity/sbnode/@label" /></xsl:call-template></h1>
			<form class="default" action="/{$master/@uuid}/security/saveAuthorisations" method="post">
				<input type="hidden" name="userentity" value="{$content/@userentity}" />
				<table>
					<tr>
						<th><xsl:value-of select="$locale/sbSystem/labels/security/authorisation" /></th>
						<th colspan="2" style="text-align:center;"><xsl:value-of select="$locale/sbSystem/labels/security/allow" /></th>
						<th colspan="2" style="text-align:center;"><xsl:value-of select="$locale/sbSystem/labels/security/deny" /></th>
					</tr>
					<xsl:apply-templates select="sbnode/supported_authorisations/authorisation[1]" />
					<tr>
						<td></td>
						<td colspan="2"><input type="submit" value="SAVE" /></td>
					</tr>
				</table>
			</form>
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
		<xsl:param name="parent_allow" />
		<xsl:param name="parent_deny" />
		<xsl:param name="parent_inherited_allow" />
		<xsl:param name="parent_inherited_deny" />
		<xsl:param name="prefix" />
		<xsl:for-each select="../authorisation[@parent=$parent]">
			<xsl:variable name="name" select="@name" />
			<xsl:variable name="inherited" select="$content/sbnode/inherited_authorisations/authorisation[@name=$name and @uuid=$subjectid]" />
			<xsl:variable name="local" select="$content/sbnode/local_authorisations/authorisation[@name=$name and @uuid=$subjectid]" />
			<xsl:variable name="allow_inherited" select="$inherited[@grant_type='ALLOW'] or $parent_allow" />
			<xsl:variable name="deny_inherited" select="$inherited[@grant_type='DENY'] or $parent_deny" />
			<xsl:variable name="allow_local" select="$local[@grant_type='ALLOW']" />
			<xsl:variable name="deny_local" select="$local[@grant_type='DENY']" />
			<xsl:variable name="inherited_allow" select="$inherited[@grant_type='ALLOW']" />
			<xsl:variable name="inherited_deny" select="$inherited[@grant_type='DENY']" />
			<tr>
				<td><xsl:value-of select="$prefix" /><xsl:value-of select="$locale/*/authorisations/*[name()=$name]" /></td>
				<td>
					<input type="checkbox" disabled="disabled">
						<xsl:if test="($allow_inherited or $allow_local) and not($deny_inherited or $deny_local)">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
				</td>
				<td>
					<input type="checkbox" name="{$name}_allow">
						<xsl:if test="$allow_local">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
				</td>
				<td>
					<input type="checkbox" disabled="disabled">
						<xsl:if test="$deny_inherited or $deny_local">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
				</td>
				<td>
					<input type="checkbox" name="{$name}_deny">
						<xsl:if test="$deny_local">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:if>
					</input>
				</td>
			</tr>
			<xsl:call-template name="build_authorisation">
				<xsl:with-param name="parent" select="@name" />
				<xsl:with-param name="parent_allow" select="$allow_inherited or $allow_local" />
				<xsl:with-param name="parent_deny" select="$deny_inherited or $deny_local" />
				<xsl:with-param name="parent_inherited_allow" select="$inherited_allow or $parent_inherited_allow" />
				<xsl:with-param name="parent_inherited_deny" select="$inherited_deny or $parent_inherited_deny" />
				<xsl:with-param name="prefix" select="concat($prefix, '&#160;&#160;')" />
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
	
</xsl:stylesheet>