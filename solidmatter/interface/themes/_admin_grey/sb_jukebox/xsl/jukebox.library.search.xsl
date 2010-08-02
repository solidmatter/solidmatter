<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" extension-element-prefixes="dyn">

	<xsl:import href="global.default.xsl" />
	<xsl:import href="global.sbform.xsl" />
	
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
			<xsl:apply-templates select="$content/sbform[@id='searchJukebox']" />
			<xsl:call-template name="renderResult">
				<xsl:with-param name="label" select="'Artists'" />
				<xsl:with-param name="nodes" select="$content/searchresult/resultset/row[@nodetype='sbJukebox:Artist']" />
				<xsl:with-param name="type" select="'sb_artist'" />
			</xsl:call-template>
			<xsl:call-template name="renderResult">
				<xsl:with-param name="label" select="'Albums'" />
				<xsl:with-param name="nodes" select="$content/searchresult/resultset/row[@nodetype='sbJukebox:Album']" />
				<xsl:with-param name="type" select="'sb_album'" />
			</xsl:call-template>
			<xsl:call-template name="renderResult">
				<xsl:with-param name="label" select="'Tracks'" />
				<xsl:with-param name="nodes" select="$content/searchresult/resultset/row[@nodetype='sbJukebox:Track']" />
				<xsl:with-param name="type" select="'sb_track'" />
			</xsl:call-template>
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template name="renderResult">
		<xsl:param name="label" />
		<xsl:param name="nodes" />
		<xsl:param name="icon" />
		<xsl:param name="type" />
		<table class="default" width="100%">
			<thead>
				<tr>
					<th colspan="2">
						<span class="type {$type}">
							<xsl:value-of select="$locale/system/general/labels/search/results" />
							<xsl:value-of select="concat(' ', $label, ' (', count($nodes), ' Hits)')" />
						</span>
					</th>
				</tr>
			</thead>
			<tbody>
				<xsl:choose>
					<xsl:when test="$nodes">
						<xsl:for-each select="$nodes">
							<tr>
								<xsl:call-template name="colorize" />
								<td>
									<a href="/{@uuid}"><xsl:value-of select="@label" /></a>
								</td>
							</tr>
						</xsl:for-each>
					</xsl:when>
					<xsl:otherwise>
						<!--<tr><td colspan="5"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>-->
					</xsl:otherwise>
				</xsl:choose>
			</tbody>
		</table>
	</xsl:template>
	<!--<xsl:template match="searchresult">
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
	</xsl:template>-->
	
	
</xsl:stylesheet>