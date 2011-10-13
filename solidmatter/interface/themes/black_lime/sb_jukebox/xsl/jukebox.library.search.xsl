<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform php" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" 
	extension-element-prefixes="dyn"
	xmlns:php="http://php.net/xsl"
	>

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
		<xsl:call-template name="layout" />
	</xsl:template>
	
	<xsl:template name="content">
		<div class="toolbar">
			<xsl:call-template name="simplesearch">
				<xsl:with-param name="form" select="$content/sbform[@id='searchJukebox']" />
			</xsl:call-template>
		</div>
		<div class="nav">
			
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:call-template name="renderResult">
				<xsl:with-param name="label" select="'Artists'" />
				<xsl:with-param name="nodes" select="$content/searchresult/resultset/row[@nodetype='sbJukebox:Artist']" />
				<xsl:with-param name="type" select="'artist'" />
			</xsl:call-template>
			<xsl:call-template name="renderResult">
				<xsl:with-param name="label" select="'Albums'" />
				<xsl:with-param name="nodes" select="$content/searchresult/resultset/row[@nodetype='sbJukebox:Album']" />
				<xsl:with-param name="type" select="'album'" />
			</xsl:call-template>
			<xsl:call-template name="renderResult">
				<xsl:with-param name="label" select="'Tracks'" />
				<xsl:with-param name="nodes" select="$content/searchresult/resultset/row[@nodetype='sbJukebox:Track']" />
				<xsl:with-param name="type" select="'track'" />
			</xsl:call-template>
		</div>
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
							<tr class="highlight_{@uuid}">
								<xsl:call-template name="colorize" />
								<td>
									<a href="/{@uuid}"><xsl:value-of select="@label" /></a>
								</td>
								<td align="right">
									<xsl:call-template name="render_buttons" />
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
	

</xsl:stylesheet>