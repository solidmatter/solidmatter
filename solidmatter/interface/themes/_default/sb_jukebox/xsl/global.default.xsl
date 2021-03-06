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
	
	<xsl:import href="../../../_global/xsl/default.xsl" />
	<xsl:import href="../../../_global/xsl/sbform.xsl" />
	
	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="no"
	/>

	<xsl:variable name="scripts_js_jb" select="'/theme/sb_jukebox/js'" />
	<xsl:variable name="currentPlaylist" select="$content/currentPlaylist/sbnode" />
	<xsl:variable name="jukebox" select="/response/metadata/modules/sb_jukebox" />
	<xsl:variable name="starcolwidth" select="((0 - $jukebox/minstars) + $jukebox/maxstars + 1) * 16 + 5" />
	
	<xsl:template match="/response/metadata" priority="10">
		<xsl:param name="customtitle" />
		<!-- title -->
		<xsl:choose>
			<xsl:when test="$customtitle">
				<title><xsl:value-of select="$customtitle" /></title>
			</xsl:when>
			<xsl:otherwise>
				<title><xsl:value-of select="/response/content/sbnode/@label" /> : <xsl:value-of select="$locale/*/views/view[@id=/response/content/@view]" /></title>
			</xsl:otherwise>
		</xsl:choose>
		<!-- styles -->
		<!-- for now only include jukebox css! -->
		<link rel="stylesheet" href="/theme/sb_jukebox/css/styles.css" type="text/css" media="all" />
		<!-- static scripts -->
		<!--<script language="Javascript" type="text/javascript" src="{$scripts_js}/edit_area/edit_area_full.js"></script>-->
	</xsl:template>
	
	<xsl:template name="frameset">
		<html>
		<head>
			<xsl:apply-templates select="/response/metadata" />
			<style>

			    body,
			    html {
				    height: 100%;
				    margin: 0;
				    padding: 0;
			    }
			
			    #content_iframe {
				    position: absolute;
				    top: 0;
				    left: 0;
				    border: 0;
				    height: 100%;
				    width: 100%;
			    }
			    
			    #player_iframe {
				    position: absolute;
				    top: 0;
				    right: 0;
				    height: 100%;
				    width: 0px;
				    border: 0;
				    border-left: 2px solid #333;
				    display: none;
				    overflow: hidden;
				}

    		</style>
		</head>
		<body>
			<iframe id="content_iframe" src="/-/library/info/foobar?render_content" />
			<iframe id="player_iframe" src="" />
		</body>
		</html>
	</xsl:template>
	
	<xsl:template name="layout">
		<html>
		<head>
			<xsl:apply-templates select="/response/metadata" />
			<script language="Javascript" type="text/javascript" src="/theme/global/js/prototype.js"></script>
			<script language="Javascript" type="text/javascript" src="/theme/global/js/scriptaculous.js"></script>
			<script language="Javascript" type="text/javascript" src="{$scripts_js_jb}/stars.js"></script>
			<script language="Javascript" type="text/javascript">
				var iMinStars = <xsl:value-of select="$jukebox/minstars" />;
				var iMaxStars = <xsl:value-of select="$jukebox/maxstars" />;
				var sVotingStyle = '<xsl:value-of select="$jukebox/votingstyle" />';
				init_stars();
			</script>
			<script language="Javascript" type="text/javascript" src="{$scripts_js_jb}/dynamic.js"></script>
		</head>
		<body>
			<xsl:if test="$content/currentPlaylist/sbnode">
			<script language="Javascript" type="text/javascript">
				var sActivePlaylistUUID = '<xsl:value-of select="$content/currentPlaylist/sbnode/@uuid" />'
			</script>
			</xsl:if>
			<div id="confirmation" style="display:none;">
				<div class="frame">
					<h1><xsl:value-of select="$locale/sbSystem/labels/are_you_sure" /></h1>
					<a href="javascript:yes();" class="confirm"><xsl:value-of select="$locale/sbSystem/actions/ok" /></a>
					<a href="javascript:no();" class="cancel"><xsl:value-of select="$locale/sbSystem/actions/cancel" /></a>
					<br />
				</div>
			</div>
			<div class="sidebar">
				<div class="head">
					<h1>sbJukebox</h1>
					<!-- current playlist and iteration over writable playlists -->
					<div class="current_playlist">
						<div style="text-align:right;">
							<xsl:choose>
								<xsl:when test="$content/currentPlaylist/sbnode">
									<a id="current_playlist_link" class="type jumpToPlaylist" href="/{$content/currentPlaylist/sbnode/@uuid}"><xsl:value-of select="$content/currentPlaylist/sbnode/@label" /></a>
								</xsl:when>
								<xsl:otherwise>
									<a id="current_playlist_link" class="type jumpToPlaylist" href="" style="display:none;"><xsl:value-of select="' '" /></a>
								</xsl:otherwise>
							</xsl:choose>
							<a class="type expand icononly" href="javascript:toggle('writablePlaylists');" title="" style="margin: 0 3px;"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
						</div>
						<div id="writablePlaylists" style="display:none;">
							<hr style="border:1px solid #222;" />
							<ul>
							<xsl:choose>
								<xsl:when test="$content/writablePlaylists/nodes/sbnode">
									<xsl:for-each select="$content/writablePlaylists/nodes/sbnode">
										<li><a id="select_{@uuid}" class="type jumpToPlaylist" href="javascript:select_playlist('{@uuid}')"><xsl:value-of select="@label" /></a></li>
									</xsl:for-each>
								</xsl:when>
								<xsl:otherwise>
									<li><xsl:value-of select="$locale/sbJukebox/texts/no_writable_playlists" /></li>
								</xsl:otherwise>
							</xsl:choose>
							</ul>
						</div>
					</div>
				</div>
				<!-- left sidebar - menu -->
				<xsl:call-template name="menu" />
			</div>
			<div class="body">
				<xsl:call-template name="content" />
				<div class="footer">
					<span style="position:absolute; right:5px; bottom:5px;"><xsl:apply-templates select="/response/metadata/stopwatch" /></span>
				</div>
			</div>
		</body>
		</html>
	</xsl:template>
	
	<xsl:template name="menu">
		<div class="menu">
					<ul>
						<li>
							<a href="/-/library?render_content=true">
								<xsl:if test="$content/@view = 'library'">
									<xsl:attribute name="class">active</xsl:attribute> 
								</xsl:if>
								<span class="type menu_overview"><xsl:value-of select="$locale/sbJukebox/menu/library" /></span>
							</a>
						</li>
						<li>
							<a href="/-/albums">
								<xsl:if test="$content/@view = 'albums' or $master/@nodetype='sbJukebox:Album'">
									<xsl:attribute name="class">active</xsl:attribute> 
								</xsl:if>
								<span class="type menu_albums"><xsl:value-of select="$locale/sbJukebox/menu/albums" /></span>
							</a>
						</li>
						<li>
							<a href="/-/artists">
								<xsl:if test="$content/@view = 'artists' or $master/@nodetype='sbJukebox:Artist'">
									<xsl:attribute name="class">active</xsl:attribute> 
								</xsl:if>
								<span class="type menu_artists"><xsl:value-of select="$locale/sbJukebox/menu/artists" /></span>
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
							<a href="/-/charts">
								<xsl:if test="$content/@view = 'charts'">
									<xsl:attribute name="class">active</xsl:attribute> 
								</xsl:if>
								<span class="type menu_charts"><xsl:value-of select="$locale/sbJukebox/menu/charts" /></span>
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
								<xsl:if test="$content/@view = 'playlists' or $master/@nodetype='sbJukebox:Playlist'">
									<xsl:attribute name="class">active</xsl:attribute> 
								</xsl:if>
								<span class="type menu_playlists"><xsl:value-of select="$locale/sbJukebox/menu/playlists" /></span>
							</a>
						</li>
						<li>
							<a href="/-/config">
								<xsl:if test="$content/@view = 'config'">
									<xsl:attribute name="class">active</xsl:attribute> 
								</xsl:if>
								<span class="type menu_config"><xsl:value-of select="$locale/sbJukebox/labels/config" /></span>
							</a>
						</li>
						<li>
							<a href="/-/login/logout" target="_parent">
								<span class="type menu_logout"><xsl:value-of select="$locale/sbJukebox/labels/logout" /></span>
							</a>
						</li>
					</ul>
				</div>
				<div class="menufooter">
					<div class="menufooter_top">
						
					</div>
					<div class="menufooter_info">
						in Library:<br />
						<xsl:value-of select="$jukebox/albums" /> Albums<br /> 
						<xsl:value-of select="$jukebox/artists" /> Artists<br /> 
						<xsl:value-of select="$jukebox/tracks" /> Tracks<br /> 
						<xsl:value-of select="$jukebox/playlists" /> Playlists<br />
					</div>
				</div>
	</xsl:template>
	
	<xsl:template match="/response/metadata/stopwatch">
		<span>
			<xsl:attribute name="title">LOAD: <xsl:value-of select="load" />ms
PHP: <xsl:value-of select="php" />ms
PDO: <xsl:value-of select="pdo" />ms</xsl:attribute>
			<xsl:value-of select="execution_time" />ms | 
			<a target="_blank">
				<xsl:attribute name="href">
					/<xsl:value-of select="$content/@uuid" />/<xsl:value-of select="$content/@view" />/<xsl:value-of select="$content/@action" />/?debug=1<xsl:for-each select="$parameters/param">&amp;<xsl:value-of select="@id" />=<xsl:value-of select="." /></xsl:for-each>
				</xsl:attribute>
				XML
			</a>
		</span>
	</xsl:template>
	
	<xsl:template name="simplesearch">
		<xsl:param name="form" />
		<!--<xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>-->
		<form action="{$form/@action}" name="simplesearch" method="get" class="simplesearch">
			<xsl:value-of select="$locale/sbSystem/labels/search/title" />:
			<xsl:apply-templates select="$form/sbinput[@type='string']" mode="inputonly" />
			<xsl:apply-templates select="$form/sbinput[@type='hidden']" mode="inputonly" />
			<xsl:value-of select="' '" />
			<xsl:apply-templates select="$form/submit" mode="inputonly" />
		</form>
	</xsl:template>
	
	<xsl:template name="addtag">
		<xsl:param name="form" />
		<xsl:if test="$form" >
			<script type="text/javascript" language="javascript">
				function showTagForm() {
					document.addTag.style.display='inline';
					document.addTag.elements[0].focus();
					document.addTag.previousSibling.style.display='none';
				}
			</script>
			<!--<xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>-->
			<a href="javascript:showTagForm();" style="line-height:25px;" class="type create"><xsl:value-of select="$locale/sbSystem/actions/new_tag" /></a>
			<form action="{$form/@action}" name="addTag" id="addTag" method="post" class="addtag" style="display:none;">
				<xsl:apply-templates select="$form/sbinput[@type='autocomplete']" mode="inputonly" />
				<xsl:apply-templates select="$form/sbinput[@type='checkbox']" mode="inputonly" />
				<xsl:value-of select="' '" />
				<xsl:apply-templates select="$form/submit" mode="inputonly" />
			</form>
		</xsl:if>
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
	
	<xsl:template name="iconize">
		<xsl:choose>
			<xsl:when test="@nodetype = 'sbJukebox:Artist'">
				<xsl:attribute name="class">type artist</xsl:attribute>
			</xsl:when>
			<xsl:when test="@nodetype = 'sbJukebox:Album'">
				<xsl:attribute name="class">type album</xsl:attribute>
			</xsl:when>
			<xsl:when test="@nodetype = 'sbJukebox:Track'">
				<xsl:attribute name="class">type track</xsl:attribute>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="render_tags">
		<xsl:if test="tags/tag">
			<div class="tags">
				<xsl:for-each select="tags/tag">
					<a href="/-/tags/listItems/?tagid={@id}">
						<xsl:choose>
							<xsl:when test="contains(., 'Genre:')">
								<xsl:value-of select="substring-after(., 'Genre:')" />
							</xsl:when>
							<xsl:when test="contains(., 'Year:')">
								<xsl:value-of select="substring-after(., 'Year:')" />
							</xsl:when>
							<xsl:when test="contains(., 'Encoding:')">
								<xsl:value-of select="substring-after(., 'Encoding:')" />
							</xsl:when>
							<xsl:when test="contains(., 'Series:')">
								<xsl:value-of select="substring-after(., 'Series:')" />
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="." />
							</xsl:otherwise>
						</xsl:choose>
					</a>
					<xsl:if test="$auth[@name='write'] and $jukebox/adminmode = '1'">
						<xsl:value-of select="' '" />
						<a class="type remove icononly" href="/{$master/@uuid}/votes/removeTag/?tagid={@id}" title="{$locale/sbJukebox/actions/remove}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
					</xsl:if>
					<xsl:if test="position() != last()">
						<xsl:value-of select="' - '" />
					</xsl:if>
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="render_stars">
		<xsl:param name="vote" select="@vote" />
		<!--<xsl:param name="stars" select="round(number(@vote) div 100 * ($maxstars - 1)) + 1" />-->
		<span class="stars js-{@uuid}"><script type="text/javascript">render_stars('<xsl:value-of select="$vote" />', '<xsl:value-of select="@uuid" />', true)</script></span>
	</xsl:template>
	
	<xsl:template name="render_timesplayed">
		<xsl:param name="times_played" select="@times_played" />
		<span class="stars"><script type="text/javascript">render_timesplayed('<xsl:value-of select="$times_played" />', 25)</script></span>
	</xsl:template>
	
	<xsl:template name="render_buttons">
		<xsl:param name="with_favorites" select="'true'" />
		<xsl:choose>
			<xsl:when test="$jukebox/playertype = 'HTML5'"><a class="type play icononly" href="javascript:open_player('/{@uuid}/playlist/openPlayer?sid={$sessionid}')" title="{$locale/sbJukebox/actions/play}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a></xsl:when>
			<xsl:otherwise><a class="type play icononly" href="/{@uuid}/details/getM3U/playlist.m3u?sid={$sessionid}" title="{$locale/sbJukebox/actions/play}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a></xsl:otherwise>
		</xsl:choose>
		<xsl:if test="@nodetype != 'sbJukebox:Playlist'">
			<a class="type recommend icononly" href="/{@uuid}/recommend" title="{$locale/sbJukebox/actions/recommend}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
		</xsl:if>
		<xsl:if test="@nodetype='sbJukebox:Track'">
			<a class="type searchLyrics icononly" href="http://www.google.de/search?q=lyrics {@label}" title="{$locale/sbJukebox/actions/search_lyrics}" target="_blank"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
			<a class="type videos icononly" href="http://www.youtube.com/results?search_query={@label}" title="{$locale/sbJukebox/actions/search_videos}" target="_blank"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
		</xsl:if>
		<xsl:if test="@nodetype != 'sbJukebox:Playlist' and $with_favorites">
			<a class="type addToFavorites icononly" href="javascript:add_to_favorites('{@uuid}');" title="{$locale/sbJukebox/actions/add_to_favorites}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
		</xsl:if>
		<xsl:if test="@nodetype='sbJukebox:Album' or @nodetype='sbJukebox:Track' or @nodetype='sbJukebox:Playlist'">
			<a class="type addToPlaylist icononly" href="javascript:add_to_playlist('{@uuid}');" title="{$locale/sbJukebox/actions/add_to_playlist}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
		</xsl:if>
		<xsl:if test="@nodetype='sbJukebox:Album' or @nodetype='sbJukebox:Playlist'">
		<!--  and $master/user_authorisations/authorisation[@name='download' and @grant_type='ALLOW'] -->
			<a class="type download icononly" href="/{@uuid}/details/download" title="{$locale/sbJukebox/actions/download}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
		</xsl:if>
		<xsl:if test="@nodetype = 'sbJukebox:Artist'">
			<a class="type wikipedia icononly" target="_blank" href="http://www.wikipedia.org/wiki/{@label}" title="{$locale/sbJukebox/actions/wikipedia}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="render_votebuttons">
		<a class="type voteDetails icononly" href="/{$master/@uuid}/votes/showDetails" title="{$locale/sbJukebox/actions/vote_details}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
	</xsl:template>
	
	<xsl:template name="render_alphanum">
		<xsl:param name="url" />
		<span class="alphalinks">
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
		</span>
	</xsl:template>
	
	<xsl:template name="render_albumlist">
		<xsl:param name="albumlist" />
		<xsl:choose>
			<xsl:when test="$albumlist/*">
				<xsl:for-each select="$albumlist/child::*">
					<tr id="highlight_{@uuid}">
						<xsl:if test="@missing">
							<xsl:attribute name="style">background-color:#800;</xsl:attribute>
						</xsl:if>
						<xsl:call-template name="colorize" />
						<td width="60">
							<a class="imglink" href="/{@uuid}">
								<img height="52" width="56" src="/theme/sb_jukebox/images/case_50.png" alt="cover" style="background: url('/{@uuid}/details/getCover/?size=50') 5px 1px;" />
							</a>
						</td>
						<td>
							<a href="/{@uuid}">
								<!--<xsl:choose>
									<xsl:when test="position()=1">
										<xsl:attribute name="accesskey"><xsl:value-of select="substring(@label, 2, 1)" /></xsl:attribute>
									</xsl:when>
									<xsl:otherwise>
										<xsl:variable name="prevpos" select="position()-1" />
										<xsl:if test="substring(@label, 2, 1) != substring(preceding-sibling::*[position() = $prevpos]/@label, 2, 1)">
											<xsl:attribute name="accesskey"><xsl:value-of select="substring(@label, 2, 1)" /></xsl:attribute>
											<xsl:value-of select="substring(@label, 2, 1)" />
											
										</xsl:if>
									</xsl:otherwise>
								</xsl:choose>-->
								<xsl:value-of select="@label" />
								<xsl:if test="@published">
									[<xsl:value-of select="@published" />]
								</xsl:if>
								<xsl:if test="@info_published">
									[<xsl:value-of select="@info_published" />]
								</xsl:if>
							</a>
							<xsl:if test="@info_type != 'DEFAULT'">
								<span style="color:grey;">
									- <xsl:value-of select="$locale/sbJukebox/Album/info_type_options/option[@id = current()/@info_type]" />
								</span>
							</xsl:if>
							<br />
							<xsl:call-template name="render_stars" />
						</td>
						<td align="right">
							<xsl:call-template name="render_buttons" />
							<br />
							<a class="type expand" href="javascript:toggle_albumdetails('{@uuid}');" style="position:relative; top:12px; left:-5px;"><xsl:value-of select="$locale/sbJukebox/labels/more" /></a>
						</td>
					</tr>
					<tr style="display:none;">
						<xsl:call-template name="colorize" />
						<td></td>
						<td colspan="2" id="details_{@uuid}"></td>
					</tr>
				</xsl:for-each>
			</xsl:when>
			<xsl:otherwise>
				<!--<tr><td colspan="5"><xsl:value-of select="$locale/sbJukebox/texts/no_albums" /></td></tr>-->
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="addRelation">
		<xsl:param name="form" />
		<xsl:if test="$form" >
			<script type="text/javascript" language="javascript">
				function showRelationForm() {
					document.addRelation.style.display='inline';
					document.addRelation.elements[0].focus();
					document.addRelation.previousSibling.style.display='none';
				}
			</script>
			<!--<xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>-->
			<a href="javascript:showRelationForm();" class="type create"><xsl:value-of select="$locale/sbSystem/actions/new_relation" /></a>
			<form action="{$form/@action}#relations" name="addRelation" id="addRelation" method="post" class="addRelation" style="display:none;">
				<xsl:apply-templates select="$form/sbinput[@type='relation']" mode="inputonly" />
				<xsl:value-of select="' '" />
				<xsl:apply-templates select="$form/submit" mode="inputonly" />
			</form>
		</xsl:if>
	</xsl:template>
	<xsl:template name="render_relationlist">
		<div class="th" id="relations">
			<span style="float:right;">
				<xsl:call-template name="addRelation">
					<xsl:with-param name="form" select="$content/sbform[@id='addRelation']" />
				</xsl:call-template>
			</span>
			<span class="type relation"><xsl:value-of select="$locale/sbJukebox/labels/relations" /></span>
		</div>
		<table class="default" width="100%">
			<tbody>
				<xsl:choose>
				<xsl:when test="existingRelations/relation">
					<xsl:for-each select="existingRelations/relation">
						<xsl:sort select="@id" />
						<xsl:sort select="@target_label" />
						<tr>
							<xsl:call-template name="colorize" />
							<td width="30%">
								<xsl:choose>
								<xsl:when test="dyn:evaluate(concat('$locale//relations/relation[@id=&quot;', @id, '&quot;]'))">
									<xsl:value-of select="dyn:evaluate(concat('$locale//relations/relation[@id=&quot;', @id, '&quot;]'))"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="@id" />
								</xsl:otherwise>
								</xsl:choose>
							</td>
							<td>
								<a href="/{@target_uuid}">
									<xsl:value-of select="@target_label" />
								</a>
							</td>
							<td width="10">
							<xsl:if test="($auth[@name='write'] and $jukebox/adminmode = '1')">
								<a class="type remove icononly" href="/{$master/@uuid}/votes/removeRelation/?type_relation={@id}&amp;target_relation={@target_uuid}" title="{$locale/sbJukebox/actions/remove}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
							</xsl:if>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<!--<tr><td><xsl:value-of select="$locale/sbSystem/texts/no_relations" /></td></tr>-->
				</xsl:otherwise>
				</xsl:choose>
			</tbody>
		</table>
	</xsl:template>
	
	<xsl:template name="addComment">
		<xsl:param name="form" />
		<xsl:if test="$form" >
			<script type="text/javascript" language="javascript">
				function showCommentForm() {
					document.getElementById('addComment').style.display='inline';
					document.addComment.elements[0].focus();
					/*document.addComment.previousSibling.style.display='none';*/
				}
			</script>
			<!--<xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>-->
			<div class="odd" style="display:none; text-align:center;" id="addComment">
				<form action="{$form/@action}#comments" name="addComment" method="post" class="addComment" style="padding:10px; vertical-align:top;">
					<xsl:apply-templates select="$form/sbinput[@type='text']" mode="inputonly" />
					<br />
					<xsl:apply-templates select="$form/submit" mode="inputonly" />
				</form>
			</div>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="comments">
		<div class="th" id="comments">
			<xsl:if test="$content/sbform[@id='addComment']">
			<span style="float:right;">
				<a href="javascript:showCommentForm();" class="type create"><xsl:value-of select="$locale/sbSystem/actions/new_comment" /></a>
			</span>
			</xsl:if>
			<span class="type comment"><xsl:value-of select="$locale/sbSystem/labels/comments" /></span>
		</div>
		<xsl:call-template name="addComment">
			<xsl:with-param name="form" select="$content/sbform[@id='addComment']" />
		</xsl:call-template>
		<table class="default" width="100%" summary="SUMMARY">
		<xsl:choose>
			<xsl:when test="children[@mode='comments']/sbnode">
				<xsl:for-each select="children[@mode='comments']/sbnode">
					<tr>
						<xsl:call-template name="colorize" />
						<td width="10%">
							<xsl:value-of select="@username" />
						</td>
						<td>
							<xsl:call-template name="break">
								<xsl:with-param name="text" select="@comment" />
							</xsl:call-template>
						</td>
						<td width="15%" style="text-align:right;">
							<xsl:value-of select="php:functionString('datetime_mysql2local', string(@created), string($locale/sbSystem/formats/datetime_short))" />
						</td>
						<td width="10">
						<xsl:if test="@createdby = $system/userid or ($auth[@name='write'] and $jukebox/adminmode = '1')">
							<a class="type remove icononly" href="/{$master/@uuid}/votes/removeComment/?comment={@uuid}" title="{$locale/sbJukebox/actions/remove}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
						</xsl:if>
						</td>
					</tr>
				</xsl:for-each>
			</xsl:when>
			<xsl:otherwise>
				<!--<tr><td><xsl:value-of select="$locale/sbSystem/texts/no_comments" /></td></tr>-->
			</xsl:otherwise>
		</xsl:choose>
		</table>
	</xsl:template>
	
</xsl:stylesheet>