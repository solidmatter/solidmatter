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
			<!--<xsl:call-template name="simplesearch">
				<xsl:with-param name="form" select="$content/sbform[@id='searchAlbums']" />
			</xsl:call-template>-->
		</div>
		<div class="nav">
			<span style="float: right;">
				<xsl:if test="$auth[@name='write'] and $jukebox/adminmode = '1'">
					<a class="type maintenance" href="/{$master/@uuid}/fix" title="{$locale/sbJukebox/actions/XXXXXXXXX}">Fix</a>
				</xsl:if>
			</span>
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</xsl:template>
	
	<xsl:template match="sbnode">
			
		<div class="th" id="highlight_{@uuid}">
			<span class="actions" style="float:right;">
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
			<span class="type track"><xsl:value-of select="@label" /></span>
		</div>
		<xsl:call-template name="render_tags" />
		
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
									<span style="float:right;">
										<a class="type wikipedia icononly" target="_blank" href="http://www.wikipedia.org/wiki/{$track_artist/@label}" title="{$locale/sbJukebox/actions/wikipedia}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
									</span>
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
									<xsl:value-of select="@enc_bitrate" /> Kbps (<xsl:value-of select="@enc_mode" />)
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</tbody>
		
		</table>
		
		<xsl:variable name="form" select="$content/sbform[@id='editLyrics']" />
		<div class="th" id="lyrics">
			<xsl:if test="$form">
			<script type="text/javascript" language="javascript">
				function showLyricsForm() {
					document.getElementById('editLyrics').style.display='inline';
					document.editLyrics.elements[0].focus();
					if (document.getElementById('showLyrics')) {
						document.getElementById('showLyrics').style.display='none';
					}
				}
			</script>
			<span style="float:right;">
				<a href="javascript:showLyricsForm();" class="type create"><xsl:value-of select="$locale/sbJukebox/actions/edit_lyrics" /></a>
			</span>
			</xsl:if>
			<span class="type lyrics" id=""><xsl:value-of select="$locale/sbJukebox/Track/info_lyrics" /></span>
		</div>
		<xsl:if test="$form">
		<div class="odd" style="display:none; text-align:center;" id="editLyrics">
			<form action="{$form/@action}#lyrics" name="editLyrics" method="post" class="editLyrics" style="padding:10px; vertical-align:top; font-size:0.8em;">
				<xsl:apply-templates select="$form/sbinput[@type='text']" mode="inputonly" />
				<br />
				<xsl:apply-templates select="$form/submit" mode="inputonly" />
			</form>
		</div>
		</xsl:if>
		<xsl:if test="string-length(@info_lyrics) &gt; 0">
			<div class="odd" style="padding:10px; white-space:pre; text-align:center; color:lightgrey; font-size:0.8em;" id="showLyrics">
				<xsl:value-of select="@info_lyrics" />
			</div>
		</xsl:if>
		
		<xsl:call-template name="render_relationlist" />
		
		<xsl:call-template name="comments" />
		
	</xsl:template>

</xsl:stylesheet>