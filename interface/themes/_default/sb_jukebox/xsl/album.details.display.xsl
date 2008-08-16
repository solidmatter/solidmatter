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
		<div class="nav">
			<xsl:call-template name="simplesearch">
				<xsl:with-param name="form" select="$content/sbform[@id='searchAlbums']" />
			</xsl:call-template>
			<xsl:call-template name="render_alphanum">
				<xsl:with-param name="url" select="'/-/albums/-/show='"/>
			</xsl:call-template>
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</xsl:template>
	
	<xsl:template match="sbnode">
		
		<div class="albumcontainer">
			
			<h2>
				<div class="albumdetails" style="float:right;">
					<xsl:call-template name="render_buttons" />
					Vote: 
					<xsl:call-template name="render_stars">
						<xsl:with-param name="voting" select="1" />
					</xsl:call-template>
				</div>
				<span class="type album"><xsl:value-of select="@label" /></span>
			</h2>
			
			<table class="default" width="100%">
				<tbody>
					<tr class="odd">
						<td style="padding:10px;" width="160" rowspan="3">
							<a class="imglink" target="_blank" href="/{@uuid}/details/getCover"><img height="150" width="150" src="/{@uuid}/details/getCover/size=150" alt="cover" /></a>
							<a href="/{@uuid}/details/buildQuilt">Quilt</a>
						</td>
						<td style="padding: 15px 15px 15px 0;">
							<table width="100%">
								<tr class="even">
									<td>
										<xsl:value-of select="$locale/sbJukebox/labels/artist" />:
									</td>
									<td>
										<a href="/{ancestors/sbnode[@nodetype='sb_jukebox:artist']/@uuid}"><xsl:value-of select="ancestors/sbnode[@nodetype='sb_jukebox:artist']/@label" /></a>
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
								<tr class="odd">
									<td>
										<xsl:value-of select="$locale/system/general/labels/tags" />:
									</td>
									<td>
										<xsl:for-each select="tags/tag">
											<a href="/-/tags/listItems/tagid={@id}">
												<xsl:value-of select="." />
											</a>
											<xsl:if test="position() != last()">
												<xsl:value-of select="' - '" />
											</xsl:if>
										</xsl:for-each>
									</td>
								</tr>
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
							<tr>
								<xsl:call-template name="colorize" />
								<td width="80">
									<xsl:call-template name="render_stars">
										<!--<xsl:with-param name="target" select="'parent'" />
										<xsl:with-param name="voting" select="1" />-->
									</xsl:call-template>
								</td>
								<!--<td width="20">
									<a class="imglink" href="/{@uuid}/song/play/sessionid={$system/sessionid}">
										<img src="/theme/sb_jukebox/images/play.png" alt="play" />
									</a>
								</td>-->
								<td width="10" style="text-align: right;">
									<xsl:value-of select="@info_index" />.
								</td>
								<td>
									<a href="/{@uuid}"><xsl:value-of select="@label" /></a>
								</td>
								<td width="10" style="text-align: right;">
									<xsl:value-of select="@info_playtime" />
								</td>
								<td width="150" style="text-align: right;">
									<xsl:call-template name="render_buttons">
										<xsl:with-param name="nolabels" select="'true'" />
										<xsl:with-param name="withlyrics" select="'true'" />
									</xsl:call-template>
								</td>
							</tr>
						</xsl:for-each>
					</xsl:when>
					<xsl:otherwise>
						<tr><td colspan="5"><xsl:value-of select="$locale/system/general/texts/no_subobjects" /></td></tr>
					</xsl:otherwise>
				</xsl:choose>
				
				</tbody>
			</table>
		
		</div>
			
		<xsl:call-template name="comments" />
		
	</xsl:template>

</xsl:stylesheet>