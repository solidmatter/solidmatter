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
		
		<table class="default">
			<tr>
				<th colspan="13">Groups</th>
			</tr>
			<tr class="th2">
				<th>Group</th>
				<th>Option</th>
			</tr>
			<xsl:for-each select="groups/group">
				<tr>
					<xsl:choose>
						<xsl:when test="position() mod 2 = 1">
							<xsl:attribute name="class">odd</xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
							<xsl:attribute name="class">even</xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
					<td><a href="/{@uuid}/security" class="type {@displaytype}"><xsl:value-of select="@label" /></a></td>
					<td>
						<xsl:choose>
							<xsl:when test="@member = 'TRUE'">
								<a href="/{$master/@uuid}/groups/remove/group={@uuid}" class="option"><img src="/theme/sb_system/icons/remove.gif" /></a>
							</xsl:when>
							<xsl:otherwise>
								<a href="/{$master/@uuid}/groups/add/group={@uuid}" class="option"><img src="/theme/sb_system/icons/add.gif" /></a>
							</xsl:otherwise>
						</xsl:choose>
					</td>
				</tr>
			</xsl:for-each>
		</table>
	</xsl:template>
	
</xsl:stylesheet>