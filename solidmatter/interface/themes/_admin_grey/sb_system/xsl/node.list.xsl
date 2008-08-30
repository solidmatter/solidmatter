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
		<div class="workbench">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</body>
	</html>
	</xsl:template>
		
	<xsl:template match="sbnode">
		
		<table class="default" width="100%">
			<thead>
				<tr>
					<th></th>
					<th><xsl:value-of select="$locale/system/general/labels/name" /></th>
					<th><xsl:value-of select="$locale/system/general/labels/type" /></th>
					<th><xsl:value-of select="$locale/system/general/labels/created_at" /></th>
					<th><xsl:value-of select="$locale/system/general/labels/modified_at" /></th>
					<th><xsl:value-of select="$locale/system/general/labels/options" /></th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="children[@mode='list']/sbnode">
					<xsl:for-each select="children[@mode='list']/sbnode">
						<tr>
							<xsl:call-template name="colorize" />
							<td width="1"><input type="checkbox" /></td>
							<td>
								<a href="/{@uuid}"><span class="type {@csstype}"><xsl:value-of select="@label" /></span></a>
							</td>
							<td>
								<xsl:variable name="type" select="@nodetype" />
								<xsl:value-of select="$locale//nodetypes/type[@id=$type]" />
							</td>
							<td>
								<xsl:value-of select="@createdat" />
							</td>
							<td>
								<xsl:value-of select="@modifiedat" />
							</td>
							<td>
								<xsl:if test="position() != 1">
									<a href="/-/structure/orderBefore/subject={$subjectid}&amp;source={@name}&amp;destination={preceding-sibling::*[1]/@name}" class="option"><img src="/theme/sb_system/icons/move_up.gif" /></a>
								</xsl:if>
								<xsl:if test="position() != last()">
									<a href="/-/structure/orderBefore/subject={$subjectid}&amp;source={following-sibling::*[1]/@name}&amp;destination={@name}" class="option"><img src="/theme/sb_system/icons/move_down.gif" /></a>
								</xsl:if>
							</td>
						</tr>
					</xsl:for-each>
					<tr class="lastline"><td colspan="6">
						<input type="checkbox" /> alle markieren | markierte
						<input type="button" value="ausschneiden" />
						<input type="button" value="löschen" />
						<input type="button" value="zu Favoriten" />
					</td></tr>
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="6"><xsl:value-of select="$locale/system/general/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			</tbody>
		</table>	
		
	</xsl:template>

</xsl:stylesheet>