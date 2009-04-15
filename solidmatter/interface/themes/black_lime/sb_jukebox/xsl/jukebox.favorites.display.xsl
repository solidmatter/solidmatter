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
		<div class="toolbar">
			<!-- <xsl:call-template name="simplesearch">
				<xsl:with-param name="form" select="$content/sbform[@id='searchAlbums']" />
			</xsl:call-template> -->
		</div>
		<div class="nav">
			<a class="type play" href="/-/favorites/getM3U/playlist.m3u?sid={$sessionid}"><xsl:value-of select="$locale/sbJukebox/actions/play" /></a>
			<a class="type play" href="/-/favorites/getM3U/playlist.m3u?random&amp;sid={$sessionid}"><xsl:value-of select="$locale/sbJukebox/actions/play_random" /></a>
			<a class="type remove" href="/-/favorites/removeItem/item=all"><xsl:value-of select="$locale/sbSystem/actions/remove_all" /></a>
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
					<th colspan="3">
						<!--<span style="float: right;">
							<xsl:choose>
								<xsl:when test="$content/@expand = $expand">
									<a class="type collapse" href="/-/tags/listItems/tagid={$content/@tagid}">Collapse</a>
								</xsl:when>
								<xsl:otherwise>
									<a class="type expand" href="/-/tags/listItems/tagid={$content/@tagid}&amp;expand={$expand}">Expand</a>
								</xsl:otherwise>
							</xsl:choose>
						</span>-->
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
						<tr>
							<xsl:call-template name="colorize" />
							<!--<td width="10" style="text-align: right;">
								<xsl:value-of select="position()" />.
							</td>-->
							<td>
								<a href="/{@uuid}"><xsl:value-of select="@label" /></a>
							</td>
							<td style="text-align:right;">
								<a class="type play icononly" href="/{@uuid}/-/getM3U/playlist.m3u?sid={$sessionid}" title="{$locale/sbJukebox/actions/play}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
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

</xsl:stylesheet>