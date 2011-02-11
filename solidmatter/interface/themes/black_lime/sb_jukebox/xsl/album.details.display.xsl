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
			<span style="float: right;">
				<xsl:if test="$auth[@name='write'] and $jukebox/adminmode = '1'">
					<a class="type maintenance" href="/{$master/@uuid}/fix" title="{$locale/sbJukebox/actions/XXXXXXXXX}">Fix</a>
					<span style="margin-left: 15px;"></span>
				</xsl:if>
				<a class="type coverwall" href="/{$master/@uuid}/details/buildQuilt">Quilt</a>
			</span>
			<xsl:call-template name="render_alphanum">
				<xsl:with-param name="url" select="'/-/albums/-/?show='"/>
			</xsl:call-template>
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</xsl:template>
	
	<xsl:template match="sbnode">
		
		<div class="th" id="highlight_{@uuid}">
			<span class="actions">
				<xsl:call-template name="addtag">
					<xsl:with-param name="form" select="$content/sbform[@id='addTag']" />
				</xsl:call-template>
				<span style="margin-left: 15px;"></span>
				<xsl:call-template name="render_buttons" />
				<span style="margin-left: 15px;"></span>
				<xsl:call-template name="render_stars" />
				<span style="margin-left: 5px;"></span>
				<xsl:call-template name="render_votebuttons" />
			</span>
			<span class="type album"><xsl:value-of select="@label" /></span>
		</div>
		<xsl:call-template name="render_tags" />
		
		<table class="default" width="100%">
			<tbody>
				<tr class="odd">
					<td style="padding:10px;" width="160" rowspan="3">
						<a class="imglink" target="_blank" href="/{@uuid}/details/getCover/{@name}.jpg">
							<img height="154" width="166" src="/theme/sb_jukebox/images/case_150.png" alt="cover" style="background: url('/{@uuid}/details/getCover/?size=150') 15px 3px;" />
							<!--<img height="150" width="150" src="/{@uuid}/details/getCover/?size=150" alt="cover" />-->
						</a>
					</td>
					<td style="padding: 15px 15px 15px 0;">
						<table width="100%">
							<tr class="even">
								<td width="30%">
									<xsl:value-of select="$locale/sbJukebox/labels/artist" />:
								</td>
								<td width="70%">
									<span style="float:right;">
										<a class="type wikipedia icononly" target="_blank" href="http://www.wikipedia.org/wiki/{ancestors/sbnode[@nodetype='sbJukebox:Artist']/@label}" title="{$locale/sbJukebox/actions/wikipedia}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
									</span>
									<a href="/{ancestors/sbnode[@nodetype='sbJukebox:Artist']/@uuid}"><xsl:value-of select="ancestors/sbnode[@nodetype='sbJukebox:Artist']/@label" /></a> 
								</td>
							</tr>
							<tr class="odd">
								<td width="30%">
									<xsl:value-of select="$locale/sbJukebox/labels/title" />: 
								</td>
								<td>
									<xsl:value-of select="@info_title" />
								</td>
							</tr>
							<tr class="even">
								<td>
									<xsl:value-of select="$locale/sbJukebox/labels/published" />:
								</td>
								<td>
									<xsl:choose>
										<xsl:when test="@info_published = 0">
											not available
										</xsl:when>
										<xsl:otherwise>
											<xsl:value-of select="@info_published" />
										</xsl:otherwise>
									</xsl:choose>
								</td>
							</tr>
							<xsl:if test="tags/tag[starts-with(., 'Series:')]">
							<tr class="odd">
								<td>
									<xsl:value-of select="$locale/sbJukebox/labels/series" />:
								</td>
								<td>
									<xsl:for-each select="tags/tag[starts-with(., 'Series:')]">
										<a href="/-/tags/listItems/?tagid={@id}&amp;expand=albums">
											<xsl:value-of select="substring-after(., 'Series:')" />
										</a>
										<xsl:if test="position() != last()">
											<xsl:value-of select="' - '" />
										</xsl:if>
									</xsl:for-each>
								</td>
							</tr>
							</xsl:if>
						</table>
					</td>
				</tr>
			</tbody>
		
		</table>
		
		<table class="default" width="100%">
			<thead>
				<tr>
					<th colspan="7">
						<span class="type track"><xsl:value-of select="$locale/sbJukebox/labels/tracks" /></span>
					</th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="children[@mode='tracks']/sbnode">
					<xsl:for-each select="children[@mode='tracks']/sbnode">
						<!--<xsl:sort select="format-number('0000.00', @info_index)" />-->
						<xsl:if test="@info_index = 1 and position() != 1">
							<tr><th colspan="5" style="padding:4px 8px;"><hr style="border:1px solid #222;" /></th></tr>
						</xsl:if>
						<tr id="highlight_{@uuid}">
							<xsl:if test="@missing">
								<xsl:attribute name="style">background-color:#800;</xsl:attribute>
							</xsl:if>
							<xsl:call-template name="colorize" />
							<td width="{$starcolwidth}">
								<xsl:call-template name="render_stars">
									<!--<xsl:with-param name="target" select="'parent'" />
									<xsl:with-param name="voting" select="1" />-->
								</xsl:call-template>
							</td>
							<td width="10" style="text-align: right;">
								<xsl:value-of select="@info_index" />.
							</td>
							<td>
								<a href="/{@uuid}"><xsl:value-of select="@label" /></a>
								<xsl:if test="string-length(@info_lyrics) &gt; 0">
									<a class="type searchLyrics icononly" href="javascript:toggle('lyrics_{@uuid}');" style="margin-left:10px;"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
								</xsl:if>
							</td>
							<td width="10" style="text-align: right;">
								<xsl:value-of select="@info_playtime" />
							</td>
							<td width="120" style="text-align: right;">
								<xsl:call-template name="render_buttons" />
							</td>
						</tr>
						<tr id="lyrics_{@uuid}" style="display:none;">
							<xsl:call-template name="colorize" />
							<td colspan="5">
								<div style="padding:10px; text-align:center; white-space:pre; color:lightgrey; font-size:0.9em;">
									<xsl:value-of select="@info_lyrics" />
								</div>
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
		
		<xsl:call-template name="render_relationlist" />
		
		<xsl:call-template name="comments" />
		
	</xsl:template>

</xsl:stylesheet>