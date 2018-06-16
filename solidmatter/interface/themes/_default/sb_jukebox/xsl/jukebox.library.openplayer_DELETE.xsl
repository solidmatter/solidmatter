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
	
	<xsl:variable name="scripts_js_jb" select="'/theme/sb_jukebox/js'" />
	<xsl:variable name="currentPlaylist" select="$content/currentPlaylist/sbnode" />
	<xsl:variable name="jukebox" select="/response/metadata/modules/sb_jukebox" />
	<xsl:variable name="starcolwidth" select="((0 - $jukebox/minstars) + $jukebox/maxstars + 1) * 16 + 5" />
	<xsl:variable name="playlist" select="$content/playlist/playlist" />
	
	<xsl:template match="/">
		<html>
		<head>
			<xsl:apply-templates select="/response/metadata">
				<xsl:with-param name="customtitle"><xsl:value-of select="$content/subject/sbnode/@label" /></xsl:with-param>
			</xsl:apply-templates>
<!-- 			<script language="Javascript" type="text/javascript" src="/theme/global/js/prototype.js"></script> -->
<!-- 			<script language="Javascript" type="text/javascript" src="/theme/global/js/scriptaculous.js"></script> -->
<!-- 			<xsl:choose> -->
<!-- 				<xsl:when test="$jukebox/votingstyle = 'RELATIVE'"> -->
<!-- 					<script language="Javascript" type="text/javascript" src="{$scripts_js_jb}/stars_relative.js"></script> -->
<!-- 				</xsl:when> -->
<!-- 				<xsl:otherwise>hotel style -->
<!-- 					<script language="Javascript" type="text/javascript" src="{$scripts_js_jb}/stars.js"></script> -->
<!-- 				</xsl:otherwise> -->
<!-- 			</xsl:choose> -->
			<script language="Javascript" type="text/javascript">
				var oPlayer = null;
				
				function init_player() {
					oPlayer = new HTML5Player('innerplayer');
					
					<xsl:for-each select="$playlist/entry">
					oPlayer.addTrack("<xsl:value-of select="uuid" />", "<xsl:value-of select="url"/>", "<xsl:value-of select="php:function('htmlspecialchars', string(label))" />");
					</xsl:for-each>
					
				}
	
<!-- 				var iMinStars = <xsl:value-of select="$jukebox/minstars" />; -->
<!-- 				var iMaxStars = <xsl:value-of select="$jukebox/maxstars" />; -->
<!-- 				var sVotingStyle = '<xsl:value-of select="$jukebox/votingstyle" />'; -->
<!-- 				init_stars(); -->
			</script>
<!-- 			<script language="Javascript" type="text/javascript" src="{$scripts_js_jb}/dynamic.js"></script> -->
			<script language="Javascript" type="text/javascript" src="{$scripts_js_jb}/html5player.js"></script>
		</head>
		<body onload="init_player()">
			<div class="body">
			<table class="default" width="100%" summary="">
			<thead>
				<tr><th><xsl:value-of select="$content/subject/sbnode/@label" />sdfgdfgdgfg</th></tr>
			</thead>
			<tbody>
				<tr>
					<td style="padding:0;"><audio id="innerplayer" src="{$playlist/entry[1]/url}" controls="true" autoplay="true" onVolumeChange="oPlayer.saveVolume()" onEnded="oPlayer.playNext()" style="width:100%" /></td>
				</tr>
				<xsl:for-each select="$playlist/entry">
					<tr id="highlight_{uuid}">
						<xsl:call-template name="colorize" />
<!-- 							<td width="{$starcolwidth}"> -->
<!-- 								<xsl:call-template name="render_stars"> -->
<!-- 									<xsl:with-param name="vote" select="@vote" /> -->
<!-- 								</xsl:call-template> -->
<!-- 							</td> -->
						<td>
							<a href="javascript:oPlayer.play('{uuid}')"><xsl:value-of select="label" /></a>
						</td>
<!-- 							<td> -->
<!-- 								<span style="float:right;"><xsl:call-template name="render_buttons" /></span> -->
<!-- 								<a href="/{@uuid}"><xsl:value-of select="@label" /></a> -->
<!-- 							</td> -->
					</tr>
				</xsl:for-each>
			</tbody>
			</table>
<!-- 			</div> -->

			</div>
		</body>
		</html>
	</xsl:template>
	
</xsl:stylesheet>