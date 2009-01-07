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
	
	<xsl:import href="global.sbform.xsl" />
	
	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="no"
	/>

	<xsl:variable name="lang" select="/response/metadata/system/lang" />
	<xsl:variable name="subjectid" select="/response/content/sbnode[last()]/@uuid" />
	<xsl:variable name="locale" select="/response/locales/locale[@lang=$lang]" />
	<xsl:variable name="commands" select="/response/metadata/commands" />
	<xsl:variable name="system" select="/response/metadata/system" />
	<xsl:variable name="content" select="/response/content" />
	<xsl:variable name="master" select="$content/sbnode[@master]" />
	<xsl:variable name="stylesheets_css" select="'/theme/sb_system/css'" />
	<xsl:variable name="scripts_js" select="'/theme/sb_system/js'" />
	<xsl:variable name="scripts_js_jb" select="'/theme/sb_jukebox/js'" />
	<xsl:variable name="sessionid" select="/response/metadata/system/sessionid" />
	<xsl:variable name="currentPlaylist" select="$content/currentPlaylist/sbnode" />
	
	<xsl:template match="/response/locales"></xsl:template>
	
	<xsl:template match="/response/metadata">
		<!-- title -->
		<title><xsl:value-of select="/response/content/sbnode/@label" /> : <xsl:value-of select="$locale/*/views/view[@id=/response/content/@view]" /></title>
		<!-- styles -->
		<!-- for now only include jukebox css! -->
		<link rel="stylesheet" href="/theme/sb_jukebox/css/styles.css" type="text/css" media="all" />
		<!-- static scripts -->
		<!--<script language="Javascript" type="text/javascript" src="{$scripts_js}/edit_area/edit_area_full.js"></script>-->
	</xsl:template>
	
	<xsl:template match="/response/errors">
		<xsl:apply-templates select="exception" />
		<xsl:apply-templates select="warnings" />
		<xsl:apply-templates select="custom" />
	</xsl:template>
	
	<xsl:template match="exception">
		<style type="text/css">
			@import url(<xsl:value-of select="$stylesheets_css" />/styles_default.css);
		</style>
		<table class="exception">
			<tr>
				<th colspan="4" class="message">
					<xsl:value-of select="@type" />: <xsl:value-of select="@message" /> (<xsl:value-of select="@code" />)
				</th>
			</tr>
			<tr>
				<th class="th2">Class</th>
				<th class="th2">Function</th>
				<th class="th2">Line</th>
				<th class="th2">File</th>
			</tr>
			<xsl:for-each select="trace/*">
				<tr>
					<xsl:if test="position() = 1"><xsl:attribute name="class">root</xsl:attribute></xsl:if>
					<td><xsl:value-of select="class" /></td>
					<td><xsl:value-of select="function" /></td>
					<td><xsl:value-of select="line" /></td>
					<td><xsl:value-of select="file" /></td>
				</tr>
			</xsl:for-each>
		</table>
	</xsl:template>
	
	<xsl:template match="warnings">
		<style type="text/css">
			@import url(<xsl:value-of select="$stylesheets_css" />/styles_default.css);
		</style>
		<table class="warning">
			<tr>
				<th colspan="4" class="message">
					Warnings:
				</th>
			</tr>
			<tr>
				<th class="th2">Type</th>
				<th class="th2">Error</th>
			</tr>
			<xsl:for-each select="*">
				<tr>
					<td>
						<xsl:choose>
							<xsl:when test="@errno='1'">E_ERROR</xsl:when>
							<xsl:when test="@errno='2'">E_WARNING</xsl:when>
							<xsl:when test="@errno='4'">E_PARSE</xsl:when>
							<xsl:when test="@errno='8'">E_NOTICE</xsl:when>
							<xsl:when test="@errno='2048'">E_STRICT</xsl:when>
							<xsl:when test="@errno='4096'">E_RECOVERABLE_ERROR</xsl:when>
							<xsl:otherwise><xsl:value-of select="@errno" /></xsl:otherwise>
						</xsl:choose>
					</td>
					<td>
						<strong><xsl:value-of select="@errstr" disable-output-escaping="yes" /></strong><br/>
						<xsl:value-of select="@errfile" />, Line <xsl:value-of select="@errline" />
					</td>
				</tr>
			</xsl:for-each>
		</table>
	</xsl:template>
	
	<xsl:template name="layout">
		<html>
		<head>
			<xsl:apply-templates select="/response/metadata" />
			<script language="Javascript" type="text/javascript" src="{$scripts_js}/prototype.js"></script>
			<script language="Javascript" type="text/javascript" src="{$scripts_js}/scriptaculous.js"></script>
			<script language="Javascript" type="text/javascript" src="{$scripts_js_jb}/stars.js"></script>
			<script language="Javascript" type="text/javascript" src="{$scripts_js_jb}/dynamic.js"></script>
		</head>
		<body>
			<div class="body">
				<div class="head">
					<h1>sbJukebox</h1>
					<a class="type logout" href="/-/login/logout">Logout</a>
					<xsl:if test="$content/currentPlaylist/sbnode">
						<span class="current_playlist">
							<a class="type jumpToPlaylist" href="/{$content/currentPlaylist/sbnode/@uuid}"><xsl:value-of select="$content/currentPlaylist/sbnode/@label" /></a>
						</span>
					</xsl:if>
				</div>
				<div class="menu">
					<ul>
						<li>
							<a href="/-/library">
								<xsl:if test="$content/@view = 'library'">
									<xsl:attribute name="class">active</xsl:attribute> 
								</xsl:if>
								<span class="type menu_overview"><xsl:value-of select="$locale/sbJukebox/menu/library" /></span>
							</a>
						</li>
						<li>
							<a href="/-/albums">
								<xsl:if test="$content/@view = 'albums' or $master/@nodetype='sb_jukebox:album'">
									<xsl:attribute name="class">active</xsl:attribute> 
								</xsl:if>
								<span class="type menu_albums"><xsl:value-of select="$locale/sbJukebox/menu/albums" /></span>
							</a>
						</li>
						<li>
							<a href="/-/artists">
								<xsl:if test="$content/@view = 'artists' or $master/@nodetype='sb_jukebox:artist'">
									<xsl:attribute name="class">active</xsl:attribute> 
								</xsl:if>
								<span class="type menu_artists"><xsl:value-of select="$locale/sbJukebox/menu/artists" /></span>
							</a>
						</li>
						<li>
							<a href="/-/charts">
								<xsl:if test="$content/@view = 'charts'">
									<xsl:attribute name="class">active</xsl:attribute> 
								</xsl:if>
								<span class="type menu_charts"><xsl:value-of select="$locale/sbJukebox/menu/charts" /></span>
							</a>
						</li>
						<li>
							<a href="/-/tags">
								<xsl:if test="$content/@view = 'tags'">
									<xsl:attribute name="class">active</xsl:attribute> 
								</xsl:if>
								<span class="type menu_tags"><xsl:value-of select="$locale/sbJukebox/menu/tags" /></span>
							</a>
						</li>
						<li>
							<a href="/-/favorites">
								<xsl:if test="$content/@view = 'favorites'">
									<xsl:attribute name="class">active</xsl:attribute> 
								</xsl:if>
								<span class="type menu_favorites"><xsl:value-of select="$locale/sbJukebox/menu/favorites" /></span>
							</a>
						</li>
						<li>
							<a href="/-/playlists">
								<xsl:if test="$content/@view = 'playlists' or $master/@nodetype='sb_jukebox:playlist'">
									<xsl:attribute name="class">active</xsl:attribute> 
								</xsl:if>
								<span class="type menu_playlists"><xsl:value-of select="$locale/sbJukebox/menu/playlists" /></span>
							</a>
						</li>
					</ul>
				</div>
				<xsl:call-template name="content" />
				<div class="footer"><span style="float:left;">solidbytes Jukebox - in Library: 
					<xsl:value-of select="$content/library/library/albums" /> Albums | 
					<xsl:value-of select="$content/library/library/artists" /> Artists | 
					<xsl:value-of select="$content/library/library/tracks" /> Tracks | 
					<xsl:value-of select="$content/library/library/playlists" /> Playlists</span>
					<xsl:apply-templates select="/response/metadata/stopwatch" />
				</div>
			</div>
		</body>
		</html>
	</xsl:template>
	
	<xsl:template match="/response/metadata/stopwatch">
		<span>
			<xsl:attribute name="title">
				LOAD:<xsl:value-of select="load" />ms | 
				PHP:<xsl:value-of select="php" />ms |
				PDO:<xsl:value-of select="pdo" />ms
			</xsl:attribute>
			<xsl:value-of select="execution_time" />ms | 
			<a href="/{$content/@uuid}/{$content/@view}/{$content/@action}/debug" target="_blank">XML</a>
		</span>
	</xsl:template>
	
	<xsl:template name="simplesearch">
		<xsl:param name="form" />
		<!--<xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>-->
		<form action="{$form/@action}" method="post" class="simplesearch">
			<xsl:value-of select="$locale/system/general/labels/search/title" />:
			<xsl:apply-templates select="$form/sbinput[@type='string']" mode="inputonly" />
			<xsl:value-of select="' '" />
			<xsl:apply-templates select="$form/submit" mode="inputonly" />
		</form>
	</xsl:template>
	
	<xsl:template name="colorize">
		<xsl:choose>
			<xsl:when test="position() mod 2 = 1">
				<xsl:attribute name="class">odd</xsl:attribute>
			</xsl:when>
			<xsl:otherwise>
				<xsl:attribute name="class">even</xsl:attribute>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="render_stars">
		<xsl:param name="target" />
		<xsl:param name="voting" select="0" />
		<xsl:param name="vote" select="@vote" />
		<xsl:param name="maxstars" select="$content/library/library/max_stars" />
		<xsl:param name="stars" select="round(number(@vote) div 100 * ($maxstars - 1)) + 1" />
		<xsl:param name="starsleft" select="$content/library/library/max_stars" />
		<span id="stars_{@uuid}" class="stars"><script type="text/javascript">render_stars('<xsl:value-of select="$vote" />', <xsl:value-of select="$maxstars" />, true)</script></span>
	</xsl:template>
	
	<xsl:template name="render_timesplayed">
		<xsl:param name="times_played" select="@times_played" />
		<span class="stars"><script type="text/javascript">render_timesplayed('<xsl:value-of select="$times_played" />', 30)</script></span>
	</xsl:template>
	
	<xsl:template name="comments">
		<div class="comments">
			<h2><xsl:value-of select="$locale/system/general/labels/comments" /></h2>
			<xsl:choose>
				<xsl:when test="children[@mode='comments']/sbnode">
					<xsl:for-each select="children[@mode='comments']/sbnode">
						<div>
							<h3><span style="float:right;"><xsl:value-of select="@username" /> - <xsl:value-of select="php:functionString('datetime_mysql2local', @createdat)" /></span><xsl:value-of select="@label" /></h3>
							<p style="white-space:pre;"><xsl:value-of select="@comment" /></p>
						</div>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<div><xsl:value-of select="$locale/system/general/texts/no_comments" /></div>
				</xsl:otherwise>
			</xsl:choose>
			<br /><br /><br />
			<xsl:apply-templates select="$content/sbform[@id='addComment']" />
		</div>
	</xsl:template>
	
	<xsl:template name="render_buttons">
		<xsl:param name="nolabels" />
		<xsl:param name="withlyrics" />
		<xsl:choose>
			<xsl:when test="$nolabels or boolean('true')">
				<a class="type play icononly" href="/{@uuid}/details/getM3U/playlist.m3u?sid={$sessionid}" title="{$locale/sbJukebox/actions/play}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
				<a class="type recommend icononly" href="/{@uuid}/recommend" title="{$locale/sbJukebox/actions/recommend}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
				<xsl:if test="@nodetype='sb_jukebox:track'">
					<a class="type lyrics icononly" href="http://www.google.de/search?q=lyrics {@label}" title="{$locale/sbJukebox/actions/search_lyrics}" target="_blank"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
				</xsl:if>
				<a class="type addToFavorites icononly" href="/-/favorites/addItem/item={@uuid}" title="{$locale/sbJukebox/actions/add_to_favorites}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
				<xsl:if test="$content/currentPlaylist">
					<a class="type addToPlaylist icononly" href="/{$currentPlaylist/@uuid}/details/addItem/item={@uuid}" title="{$locale/sbJukebox/actions/add_to_playlist}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
				</xsl:if>
				<xsl:if test="@nodetype='sb_jukebox:album'">
				<!--  and $master/user_authorisations/authorisation[@name='download' and @grant_type='ALLOW'] -->
					<a class="type download icononly" href="/{@uuid}/details/download" title="{$locale/sbJukebox/actions/download}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
				</xsl:if>
			</xsl:when>
			<xsl:otherwise>
				<a class="type recommend" href="/{@uuid}/recommend" title="{$locale/sbJukebox/actions/recommend}"><xsl:value-of select="$locale/sbJukebox/actions/recommend" /></a>
				<a class="type play" href="/{@uuid}/details/getM3U/playlist.m3u?sid={$sessionid}" title="{$locale/sbJukebox/actions/play}"><xsl:value-of select="$locale/sbJukebox/actions/play" /></a>
				<xsl:value-of select="' '" />
				<xsl:if test="@nodetype='sb_jukebox:track'">
					<a class="type lyrics" href="http://www.google.de/search?q=lyrics {@label}" title="{$locale/sbJukebox/actions/search_lyrics}">Lyrics</a>
					<xsl:value-of select="' '" />
				</xsl:if>
				<a class="type addToFavorites" href="/-/favorites/addItem/item={@uuid}" title="{$locale/sbJukebox/actions/add_to_favorites}"><xsl:value-of select="$locale/sbJukebox/actions/add_to_favorites" /></a>
				<xsl:value-of select="' '" />
				<xsl:if test="$content/currentPlaylist">
					<a class="type addToPlaylist" href="/{$currentPlaylist/@uuid}/details/addItem/item={@uuid}" title="{$locale/sbJukebox/actions/add_to_playlist}"><xsl:value-of select="$locale/sbJukebox/actions/add_to_playlist" /></a>
				</xsl:if>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="render_alphanum">
		<xsl:param name="url" />
		<div class="alphalinks">
			<a href="{$url}0-9">#</a>	
			<a href="{$url}A">A</a>
			<a href="{$url}B">B</a>
			<a href="{$url}C">C</a>
			<a href="{$url}D">D</a>
			<a href="{$url}E">E</a>
			<a href="{$url}F">F</a>
			<a href="{$url}G">G</a>
			<a href="{$url}H">H</a>
			<a href="{$url}I">I</a>
			<a href="{$url}J">J</a>
			<a href="{$url}K">K</a>
			<a href="{$url}L">L</a>
			<a href="{$url}M">M</a>
			<a href="{$url}N">N</a>
			<a href="{$url}O">O</a>
			<a href="{$url}P">P</a>
			<a href="{$url}Q">Q</a>
			<a href="{$url}R">R</a>
			<a href="{$url}S">S</a>
			<a href="{$url}T">T</a>
			<a href="{$url}U">U</a>
			<a href="{$url}V">V</a>
			<a href="{$url}W">W</a>
			<a href="{$url}X">X</a>
			<a href="{$url}Y">Y</a>
			<a href="{$url}Z">Z</a>
		</div>
	</xsl:template>
	
</xsl:stylesheet>