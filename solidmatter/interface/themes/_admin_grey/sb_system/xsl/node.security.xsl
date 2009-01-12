<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:dyn="http://exslt.org/dynamic" extension-element-prefixes="dyn">

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
		
		<table class="invisible">
			<tr>
				<td width="50%">
					<div class="eyecandy"><div class="left"><div class="right">
						<h1><xsl:value-of select="$locale/sbSystem/labels/security/current_rights" /></h1>
						<ul class="invisible">
							<xsl:for-each select="groups/row">
								<xsl:variable name="id" select="uuid" />
								<xsl:if test="$content/sbnode/inherited_authorisations/authorisation[@uuid=$id] or $content/sbnode/local_authorisations/authorisation[@uuid=$id]">
								<li style="margin-bottom:3px; display:block; position:relative;">
									<a href="/{$content/sbnode/@uuid}/security/editAuthorisations/userentity={$id}" class="type sb_usergroup" target="details">
										<xsl:value-of select="s_label" />
									</a>
									<xsl:if test="$content/sbnode/local_authorisations/authorisation[@uuid=$id]">
										<a href="/{$content/sbnode/@uuid}/security/removeUser/userentity={$id}" class="type delete" style="position:absolute; right:0;">remove</a>
									</xsl:if>
								</li>
								</xsl:if>
							</xsl:for-each>
							<xsl:for-each select="users/row">
								<xsl:variable name="id" select="uuid" />
								<xsl:if test="$content/sbnode/inherited_authorisations/authorisation[@uuid=$id] or $content/sbnode/local_authorisations/authorisation[@uuid=$id]">
								<li style="margin-bottom:3px; display:block; position:relative;">
									<a href="/{$content/sbnode/@uuid}/security/editAuthorisations/userentity={$id}" class="type sb_user" target="details">
										<xsl:value-of select="s_label" />
									</a>
									<xsl:if test="$content/sbnode/local_authorisations/authorisation[@uuid=$id]">
										<a href="/{$content/sbnode/@uuid}/security/removeUser/userentity={$id}" class="type delete" style="position:absolute; right:0;">remove</a>
									</xsl:if>
								</li>
								</xsl:if>
							</xsl:for-each>
						</ul>
					</div></div></div>
					<div class="eyecandy"><div class="left"><div class="right">
						<h1><xsl:value-of select="$locale/sbSystem/labels/security/add_entities" /></h1>
						<form action="/{$content/sbnode/@uuid}/security/addUser" method="post" accept-charset="UTF-8">
							<select name="entity_uuid">
								<optgroup label="{$locale/sbSystem/labels/groups}">
									<xsl:for-each select="$content/groups/row">
										<xsl:variable name="uuid" select="uuid" />
										<xsl:if test="not($content/sbnode/local_authorisations/authorisation[@uuid=$uuid])">
											<option value="{$uuid}"><xsl:value-of select="s_label" /></option>
										</xsl:if>
									</xsl:for-each>
								</optgroup>
								<optgroup label="{$locale/sbSystem/labels/users}">
									<xsl:for-each select="$content/users/row">
										<xsl:variable name="uuid" select="uuid" />
										<xsl:if test="not($content/sbnode/local_authorisations/authorisation[@uuid=$uuid])">
											<option value="{$uuid}"><xsl:value-of select="s_label" /></option>
										</xsl:if>
									</xsl:for-each>
								</optgroup>
							</select><br/>
							<input type="submit" value="{$locale/sbSystem/actions/add}" />
						</form>
					</div></div></div>
					<div class="eyecandy"><div class="left"><div class="right">
						<h1><xsl:value-of select="$locale/sbSystem/labels/security/inheritance" /></h1>
						<form action="/{$content/sbnode/@uuid}/security/changeInheritance" method="post" accept-charset="UTF-8">
							<input type="checkbox" name="inheritrights">
								<xsl:if test="/response/content/sbnode[@inheritrights = 'TRUE']">
									<xsl:attribute name="checked">checked</xsl:attribute>
								</xsl:if>
							</input> <xsl:value-of select="$locale/sbSystem/labels/security/inherit_rights" /><br/>
							<input type="checkbox" name="bequeathrights">
								<xsl:if test="/response/content/sbnode[@bequeathrights = 'TRUE']">
									<xsl:attribute name="checked">checked</xsl:attribute>
								</xsl:if>
							</input> <xsl:value-of select="$locale/sbSystem/labels/security/bequeath_rights" /><br/>
							<input type="submit" value="{$locale/sbSystem/actions/save}" />
						</form>
					</div></div></div>
				</td>
				<td><div class="spacer"></div></td>
				<td width="50%">
					<iframe name="details" src="" width="100%" height="600" style="border:0;" />
				</td>
			</tr>
		</table>
		
	</xsl:template>

</xsl:stylesheet>