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
		<div class="toolbar">
			
		</div>
		<div class="nav">
			
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/playlists" />
		</div>
	</xsl:template>
	
	<xsl:template match="playlists">
		
		<div class="th">
			<span class="actions">
				<xsl:call-template name="newPlaylist">
					<xsl:with-param name="form" select="$content/sbform[@id='newPlaylist']" />
				</xsl:call-template>
			</span>
			<span class="type playlist"><xsl:value-of select="$locale/sbJukebox/menu/playlists"/></span>
		</div>
		
		<table class="default" width="100%" summary="">
			<tbody>
			<xsl:choose>
				<xsl:when test="row">
					<xsl:for-each select="row">
						<tr id="highlight_{@uuid}">
							<xsl:call-template name="colorize" />
							<td width="{$starcolwidth}">
								<xsl:call-template name="render_stars" />
							</td>
							<td>
								<a href="/{@uuid}"><xsl:value-of select="@label" /></a><br />
							</td>
							<td>
								<xsl:value-of select="@numtracks" />
							</td>
							<td>
								<xsl:value-of select="@user" />
							</td>
							<td width="38">
								<xsl:choose>
									<xsl:when test="$jukebox/playertype = 'HTML5'">
										<a class="type play icononly" href="javascript:open_player('/{@uuid}/playlist/openPlayer?sid={$sessionid}')"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
									</xsl:when>
									<xsl:otherwise>
										<a class="type play icononly" href="/{@uuid}/details/getM3U/playlist.m3u?sid={$sessionid}" title="{$locale/sbJukebox/actions/play}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
									</xsl:otherwise>
								</xsl:choose>
								<a class="type addToPlaylist icononly" href="javascript:add_to_playlist('{@uuid}');" title="{$locale/sbJukebox/actions/add_to_playlist}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
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
	
	<xsl:template name="newPlaylist">
		<xsl:param name="form" />
		<xsl:if test="$form" >
			<script type="text/javascript" language="javascript">
				function showNewPlaylistForm() {
					document.newPlaylist.style.display='inline';
					document.newPlaylist.elements[0].focus();
					document.newPlaylist.previousSibling.style.display='none';
				}
			</script>
			<!--<xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>-->
			<a href="javascript:showNewPlaylistForm();" style="line-height:25px;" class="type create"><xsl:value-of select="$locale/sbJukebox/labels/new_playlist" /></a>
			<form action="{$form/@action}" name="newPlaylist" id="newPlaylist" method="post" class="newPlaylist" style="display:none;">
				<xsl:apply-templates select="$form/sbinput[@type='string']" mode="inputonly" />
				<xsl:value-of select="' '" />
				<xsl:apply-templates select="$form/submit" mode="inputonly" />
			</form>
		</xsl:if>
	</xsl:template>

</xsl:stylesheet>