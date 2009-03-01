<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" extension-element-prefixes="dyn">

	<xsl:import href="../../sb_system/stylesheets_views/global.views.xsl" />
	<xsl:import href="../../sb_system/stylesheets_views/global.default.xsl" />
	<xsl:import href="../../sb_system/stylesheets_views/global.sbform.xsl" />
	
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
			<xsl:apply-templates select="$content/sbform[@id='search_jukebox']" />
			<xsl:apply-templates select="$content/searchresult" />
		</div>
	</body>
	</html>
	</xsl:template>
			
	<xsl:template match="searchresult">
		<table class="default" width="100%">
			<thead>
				<tr>
					<th colspan="2"><xsl:value-of select="$locale/system/general/labels/search/results" /></th>
				</tr>
				<tr>
					<th class="th2"><xsl:value-of select="$locale/system/general/labels/name" /></th>
					<th class="th2"><xsl:value-of select="$locale/system/general/labels/type" /></th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="resultset/row">
					<xsl:for-each select="resultset/row">
						<tr>
							<xsl:choose>
								<xsl:when test="position() mod 2 = 1">
									<xsl:attribute name="class">odd</xsl:attribute>
								</xsl:when>
								<xsl:otherwise>
									<xsl:attribute name="class">even</xsl:attribute>
								</xsl:otherwise>
							</xsl:choose>
							<td>
								<a href="/{@uuid}"><span class="type {@displaytype}"><xsl:value-of select="@label" /></span></a>
								<!--| <a href="/{@uuid}/song/play/sessionid={$system/sessionid}">play</a>-->
							</td>
							<td>
								<xsl:variable name="type" select="@nodetype" />
								<xsl:value-of select="$locale//nodetypes/type[@id=$type]" />
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="5"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			
			</tbody>
			<tfoot></tfoot>
		</table>
	</xsl:template>
	
	
</xsl:stylesheet>