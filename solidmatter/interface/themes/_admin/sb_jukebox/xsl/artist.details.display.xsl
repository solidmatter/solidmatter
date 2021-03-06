<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" 
	extension-element-prefixes="dyn">

	<xsl:import href="global.default.xsl" />

	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="yes"
		doctype-system="http://www.w3.org/TR/html4/loose.dtd" 
		doctype-public="-//W3C//DTD HTML 4.01 Transitional//EN"
	/>
	
	<xsl:template match="/">
	<html>
	<head>
		<xsl:apply-templates select="response/metadata" />
	</head>
	<body>
		<xsl:call-template name="views" />
		<div class="workbench">
			<xsl:apply-templates select="$errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template match="sbnode">
		
		<table class="default" width="100%">
			<thead>
				<tr>
					<th><xsl:value-of select="$locale/sbSystem/labels/name" /></th>
					<th><xsl:value-of select="$locale/sbSystem/labels/type" /></th>
					<th><xsl:value-of select="$locale/sbSystem/labels/created_at" /></th>
					<th><xsl:value-of select="$locale/sbSystem/labels/modified_at" /></th>
					<th><xsl:value-of select="$locale/sbSystem/labels/options" /></th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="children[@mode='albums']/sbnode">
					<xsl:for-each select="children[@mode='albums']/sbnode">
						<tr>
							<xsl:call-template name="colorize" />
							<td>
								<a href="/{@uuid}"><span class="type {@displaytype}"><xsl:value-of select="@label" /></span></a>
								<!--<a href="/{@uuid}/song/play/sessionid={$system/sessionid}" class="type sb_action_play"> </a>-->
							</td>
							<td>
								<xsl:variable name="type" select="@nodetype" />
								<xsl:value-of select="$locale//nodetypes/type[@id=$type]" />
							</td>
							<td>
								<xsl:value-of select="@created" />
							</td>
							<td>
								<xsl:value-of select="@modified" />
							</td>
							<td>
								<xsl:if test="position() != 1">
									<a href="/-/structure/orderBefore/subject={$subjectid}&amp;redirectview=titles&amp;source={@name}&amp;destination={preceding-sibling::*[1]/@name}" class="option"><img src="/theme/sb_system/icons/move_up.gif" /></a>
								</xsl:if>
								<xsl:if test="position() != last()">
									<a href="/-/structure/orderBefore/subject={$subjectid}&amp;redirectview=titles&amp;source={following-sibling::*[1]/@name}&amp;destination={@name}" class="option"><img src="/theme/sb_system/icons/move_down.gif" /></a>
								</xsl:if>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="5"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			
			</tbody>
			<tfoot><tr><td colspan="5"></td></tr></tfoot>
		</table>
		
	</xsl:template>

</xsl:stylesheet>