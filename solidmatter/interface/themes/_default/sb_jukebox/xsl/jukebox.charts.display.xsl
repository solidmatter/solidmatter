<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform" 
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
			<form action="{$content/sbform[@id='changePivot']/@action}" method="post" class="simplesearch">
				<xsl:value-of select="$locale/sbJukebox/labels/pivot" />:
				<xsl:apply-templates select="$content/sbform[@id='changePivot']/sbinput[@type='select']" mode="inputonly" />
				<xsl:value-of select="' '" />
				<xsl:apply-templates select="$content/sbform[@id='changePivot']/submit" mode="inputonly" />
			</form>
		</div>
		<div class="content">
			<xsl:apply-templates select="/response/errors" />
			<xsl:if test="$content/topArtists">
				<xsl:call-template name="renderResult">
					<xsl:with-param name="label" select="$locale/sbJukebox/labels/top_artists" />
					<xsl:with-param name="nodes" select="$content/topArtists/resultset/row" />
					<xsl:with-param name="type" select="'artist'" />
					<xsl:with-param name="expand" select="'artists'" />
				</xsl:call-template>
			</xsl:if>
			<xsl:if test="$content/topAlbums">
				<xsl:call-template name="renderResult">
					<xsl:with-param name="label" select="$locale/sbJukebox/labels/top_albums" />
					<xsl:with-param name="nodes" select="$content/topAlbums/resultset/row" />
					<xsl:with-param name="type" select="'album'" />
					<xsl:with-param name="expand" select="'albums'" />
				</xsl:call-template>
			</xsl:if>
			<xsl:if test="$content/topTracks">
				<xsl:call-template name="renderResult">
					<xsl:with-param name="label" select="$locale/sbJukebox/labels/top_tracks" />
					<xsl:with-param name="nodes" select="$content/topTracks/resultset/row" />
					<xsl:with-param name="type" select="'track'" />
					<xsl:with-param name="expand" select="'tracks'" />
				</xsl:call-template>
			</xsl:if>
			<xsl:if test="$content/mostPlayed">
				<xsl:call-template name="renderResult">
					<xsl:with-param name="label" select="$locale/sbJukebox/labels/most_played" />
					<xsl:with-param name="nodes" select="$content/mostPlayed/resultset/row" />
					<xsl:with-param name="type" select="'ear'" />
					<xsl:with-param name="expand" select="'mostPlayed'" />
				</xsl:call-template>
			</xsl:if>
		</div>
	</xsl:template>
	
	<xsl:template name="renderResult">
		<xsl:param name="label" />
		<xsl:param name="nodes" />
		<xsl:param name="icon" />
		<xsl:param name="expand" />
		<xsl:param name="type" />
		<table class="default" width="100%" summary="CHANGEME">
			<thead>
				<tr>
					<th colspan="4">
						<span style="float: right;">
							<xsl:choose>
								<xsl:when test="$content/@expand = $expand">
									<a class="type collapse" href="/-/charts"><xsl:value-of select="$locale/sbJukebox/actions/collapse" /></a>
								</xsl:when>
								<xsl:otherwise>
									<xsl:if test="1 or count($nodes) > 9">
										<a class="type expand" href="/-/charts/-/?expand={$expand}"><xsl:value-of select="$locale/sbJukebox/actions/expand" /></a>
									</xsl:if>
								</xsl:otherwise>
							</xsl:choose>
						</span>
						<span class="type {$type}">
							<xsl:value-of select="$label" />
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
							<td width="80">
								<xsl:choose>
									<xsl:when test="$expand = 'mostPlayed'">
										<!--<div style="text-align: right;">
											<xsl:value-of select="@times_played" />
										</div>-->
										<xsl:call-template name="render_timesplayed" />
									</xsl:when>
									<xsl:otherwise>
										<xsl:call-template name="render_stars" />
									</xsl:otherwise>
								</xsl:choose>
							</td>
							<td width="10" style="text-align: right;">
								<xsl:value-of select="position()" />.
							</td>
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
					<tr><td colspan="5"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			</tbody>
		</table>
	</xsl:template>

</xsl:stylesheet>