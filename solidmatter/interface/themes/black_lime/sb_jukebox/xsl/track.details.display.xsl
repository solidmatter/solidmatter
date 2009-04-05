<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform php" 
	exclude-element-prefixes="html sbform" 
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
				<xsl:with-param name="form" select="$content/sbform[@id='searchAlbums']" />
			</xsl:call-template>
		</div>
		<div class="nav">
			
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</xsl:template>
	
	<xsl:template match="sbnode">
			
		<div class="th">
			<span class="albumdetails" style="float:right;">
				<xsl:call-template name="addtag">
					<xsl:with-param name="form" select="$content/sbform[@id='addTag']" />
				</xsl:call-template>
				<span style="margin-left: 15px;"></span>
				<xsl:call-template name="render_buttons" />
				<span style="margin-left: 15px;"></span>
				<xsl:call-template name="render_stars" />
			</span>
			<span class="type track"><xsl:value-of select="@label" /></span>
		</div>
		<div class="tags">
			<xsl:for-each select="tags/tag">
				<a href="/-/tags/listItems/?tagid={@id}">
					<xsl:value-of select="." />
				</a>
				<xsl:if test="position() != last()">
					<xsl:value-of select="' - '" />
				</xsl:if>
			</xsl:for-each>
		</div>
		
		<table class="default" width="100%">
			<tbody>
				<tr class="odd">
					<td style="padding:10px;" width="160" rowspan="3">
						<a class="imglink" target="_blank" href="/{@uuid}/details/getCover/{ancestors/sbnode[@nodetype='sbJukebox:Album']/@name}.jpg">
							<img height="154" width="166" src="/theme/sb_jukebox/images/case_150.png" alt="cover" style="background: url('/{ancestors/sbnode[@nodetype='sbJukebox:Album']/@uuid}/details/getCover/?size=150') 15px 3px;" />
							<!--<a class="imglink" target="_blank" href="/{@uuid}/details/getCover/{ancestors/sbnode[@nodetype='sbJukebox:Album']/@name}.jpg"><img height="150" width="150" src="/{@uuid}/details/getCover/?size=150" alt="cover" /></a>-->
						</a>
					</td>
					<td style="padding: 15px 15px 15px 0;">
						<table width="100%">
							<tr class="even">
								<td width="25%">
									<xsl:value-of select="$locale/sbJukebox/labels/artist" />:
								</td>
								<xsl:variable name="track_artist" select="$content/track_artist/sbnode" />
								<td width="75%">
									<a href="/{$track_artist/@uuid}"><xsl:value-of select="$track_artist/@label" /></a>
								</td>
							</tr>
							<tr class="odd">
								<td>
									<xsl:value-of select="$locale/sbJukebox/labels/title" />: 
								</td>
								<td>
									<xsl:value-of select="@info_title" />
								</td>
							</tr>
							<tr class="even">
								<td>
									<xsl:value-of select="$locale/sbJukebox/labels/album" />:
								</td>
								<td>
									<a href="/{ancestors/sbnode[@nodetype='sbJukebox:Album']/@uuid}"><xsl:value-of select="ancestors/sbnode[@nodetype='sbJukebox:Album']/@label" /></a>
								</td>
							</tr>
							<tr class="odd">
								<td>
									<xsl:value-of select="$locale/sbJukebox/labels/playtime" />:
								</td>
								<td>
									<xsl:value-of select="@info_playtime" />
								</td>
							</tr>
							<tr class="even">
								<td>
									<xsl:value-of select="$locale/sbJukebox/labels/bitrate" />:
								</td>
								<td>
									<xsl:value-of select="round(@enc_bitrate div 1000)" /> Kbps (<xsl:value-of select="@enc_mode" />)
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</tbody>
		
		</table>
		
		<xsl:call-template name="render_relationlist" />
		
		<xsl:call-template name="comments" />
		
	</xsl:template>

</xsl:stylesheet>