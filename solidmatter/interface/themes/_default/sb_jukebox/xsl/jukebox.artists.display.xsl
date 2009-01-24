<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform" 
	exclude-element-prefixes="html sbform" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" extension-element-prefixes="dyn">

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
		<div class="nav">
			<xsl:call-template name="simplesearch">
				<xsl:with-param name="form" select="$content/sbform[@id='searchArtists']" />
			</xsl:call-template>
			<xsl:call-template name="render_alphanum">
				<xsl:with-param name="url" select="'/-/artists/-/?show='"/>
			</xsl:call-template>
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:choose>
				<xsl:when test="$content/@show">
					<xsl:call-template name="renderArtists">
						<xsl:with-param name="data" select="$content/random/resultset/row" />
						<xsl:with-param name="label" select="concat($locale/sbJukebox/labels/artists_beginning_with, ' ', $content/@show)" />
					</xsl:call-template>
				</xsl:when>
				<xsl:when test="$content/@action = 'search'">
					<xsl:call-template name="renderArtists">
						<xsl:with-param name="data" select="$content/searchresult/resultset/row" />
						<xsl:with-param name="label" select="$locale/system/general/labels/search/results" />
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="renderArtists">
						<xsl:with-param name="data" select="$content/random/resultset/row" />
						<xsl:with-param name="label" select="$locale/sbJukebox/labels/random_artists" />
					</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>
	
	<xsl:template name="renderArtists">
		<xsl:param name="data" />
		<xsl:param name="label" />
		<table class="default" width="100%" summary="">
			<thead>
				<tr>
					<th colspan="2"><span class="type artist"><xsl:value-of select="$label" /></span></th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="$data">
					<xsl:for-each select="$data">
						<tr>
							<xsl:call-template name="colorize" />
							<td width="80">
								<xsl:call-template name="render_stars">
									<xsl:with-param name="vote" select="@vote" />
								</xsl:call-template>
							</td>
							<td>
								<span style="float:right;"><xsl:call-template name="render_buttons" /></span>
								<a href="/{@uuid}"><xsl:value-of select="@label" /></a>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="5"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			
			</tbody>
		</table>
		
	</xsl:template>

</xsl:stylesheet>