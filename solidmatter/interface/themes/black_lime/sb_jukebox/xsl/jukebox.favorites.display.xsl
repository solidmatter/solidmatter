<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic"
	xmlns:php="http://php.net/xsl"
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
		<xsl:call-template name="layout" />
	</xsl:template>
	
	<xsl:template name="content">
		<div class="toolbar">
			<!-- <xsl:call-template name="simplesearch">
				<xsl:with-param name="form" select="$content/sbform[@id='searchAlbums']" />
			</xsl:call-template> -->
		</div>
		<div class="nav">
			<span style="float:right;">
				<a class="type remove" href="javascript:request_confirmation('/-/favorites/removeItem/?item=all');"><xsl:value-of select="$locale/sbSystem/actions/remove_all" /></a>
			</span>
			<a class="type play" href="/-/favorites/getM3U/playlist.m3u?sid={$sessionid}"><xsl:value-of select="$locale/sbJukebox/actions/play" /></a>
			<a class="type play" href="/-/favorites/getM3U/playlist.m3u?random=true&amp;sid={$sessionid}"><xsl:value-of select="$locale/sbJukebox/actions/play_random" /></a>
		</div>
		<div class="content">
			<xsl:apply-templates select="/response/errors" />
			<xsl:call-template name="renderResult">
				<xsl:with-param name="label" select="$locale/sbJukebox/labels/favorite_artists" />
				<xsl:with-param name="nodes" select="$content/favorites/sbnode[@nodetype='sbJukebox:Artist']" />
				<xsl:with-param name="type" select="'artist'" />
			</xsl:call-template>
			<xsl:call-template name="renderResult">
				<xsl:with-param name="label" select="$locale/sbJukebox/labels/favorite_albums" />
				<xsl:with-param name="nodes" select="$content/favorites/sbnode[@nodetype='sbJukebox:Album']" />
				<xsl:with-param name="type" select="'album'" />
			</xsl:call-template>
			<xsl:call-template name="renderResult">
				<xsl:with-param name="label" select="$locale/sbJukebox/labels/favorite_tracks" />
				<xsl:with-param name="nodes" select="$content/favorites/sbnode[@nodetype='sbJukebox:Track']" />
				<xsl:with-param name="type" select="'track'" />
			</xsl:call-template>
			<xsl:call-template name="renderHistory">
				<xsl:with-param name="label" select="$locale/sbJukebox/labels/most_recently_played" />
				<xsl:with-param name="nodes" select="$content/history/row" />
				<xsl:with-param name="type" select="'ear'" />
			</xsl:call-template>
		</div>
	</xsl:template>
	
	<xsl:template name="renderResult">
		<xsl:param name="label" />
		<xsl:param name="nodes" />
		<xsl:param name="expand" />
		<xsl:param name="type" />
		<table class="default" width="100%" summary="CHANGEME">
			<thead>
				<tr>
					<th colspan="3">
						<span class="type {$type}">
							<xsl:value-of select="concat(' ', $label)" />
						</span>
					</th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="$nodes">
					<xsl:for-each select="$nodes">
						<tr id="highlight_{@uuid}">
							<xsl:call-template name="colorize" />
							<td width="{$starcolwidth}">
								<xsl:call-template name="render_stars" />
							</td>
							<td>
								<a href="/{@uuid}"><xsl:value-of select="@label" /></a>
							</td>
							<td style="text-align:right;">
								<xsl:call-template name="render_buttons">
									<xsl:with-param name="with_favorites" select="boolean(false)" />
								</xsl:call-template>
								<!--<a class="type play icononly" href="/{@uuid}/-/getM3U/playlist.m3u?sid={$sessionid}" title="{$locale/sbJukebox/actions/play}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>-->
								<a class="type remove icononly" href="/-/favorites/removeItem/?item={@uuid}" title="{$locale/sbJukebox/actions/remove}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
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
	
	<xsl:template name="renderHistory">
		<xsl:param name="label" />
		<xsl:param name="nodes" />
		<xsl:param name="type" />
		<table class="default" width="100%" summary="CHANGEME">
			<thead>
				<tr>
					<th colspan="3">
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
						<tr id="highlight_{@uuid}">
							<xsl:call-template name="colorize" />
							<td width="70%">
								<a href="/{@uuid}"><xsl:value-of select="@label" /></a>
							</td>
							<td width="15%">
								<xsl:value-of select="php:functionString('datetime_mysql2local', string(@played), string($locale/sbSystem/formats/datetime_short))" />
							</td>
							<td width="15%" align="right">
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