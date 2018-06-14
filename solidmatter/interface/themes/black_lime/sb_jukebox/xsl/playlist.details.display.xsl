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
		<script language="Javascript" type="text/javascript">
			sDisplayedPlaylistUUID = '<xsl:value-of select="$master/@uuid" />';
			<xsl:if test="$master/user_authorisations/authorisation[@name='write']">
				bWriteAllowed = true;
			</xsl:if>
		</script>
		<div class="toolbar">
			<xsl:call-template name="import">
				<xsl:with-param name="form" select="$content/sbform[@id='importM3U']" />
			</xsl:call-template>
		</div>
		<div class="nav">
			<span style="float: right;">
				<span id="playlist_actions" style="display:none;">
					<xsl:value-of select="$locale/sbSystem/labels/selected_items" />: 
				</span>
				<span id="playlist_actions_copy" style="display:none;">
					<a class="type addToPlaylist" href="javascript:submit_form('copy');"><xsl:value-of select="$locale/sbSystem/actions/copy" /></a>
				</span>
				<span id="playlist_actions_move" style="display:none;">
					<xsl:value-of select="' '" />
					<a class="type addToPlaylist" href="javascript:submit_form('move');"><xsl:value-of select="$locale/sbSystem/actions/move" /></a>
				</span>
				<span id="playlist_actions_remove" style="display:none;">
					<xsl:value-of select="' '" />
					<a class="type remove" href="javascript:submit_form('remove');"><xsl:value-of select="$locale/sbSystem/actions/remove" /></a>
				</span>
				<xsl:if test="$master/user_authorisations/authorisation[@name='write']">
					<span style="margin-left: 25px;"></span>
					<a class="type remove" href="javascript:request_confirmation('/{$master/@uuid}/details/clear');"><xsl:value-of select="$locale/sbSystem/actions/remove_all" /></a>
				</xsl:if>
			</span>
			<xsl:choose>
				<xsl:when test="$jukebox/playertype = 'HTML5'">
					<a class="type play" href="javascript:open_player('/{$master/@uuid}/playlist/openPlayer?sid={$sessionid}')"><xsl:value-of select="$locale/sbJukebox/actions/play" /></a>
					<a class="type play" href="javascript:open_player('/{$master/@uuid}/playlist/openPlayer?sid={$sessionid}&amp;shuffle=true')"><xsl:value-of select="$locale/sbJukebox/actions/play_random" /></a>
				</xsl:when>
				<xsl:otherwise>
					<a class="type play" href="/{$master/@uuid}/details/getM3U/playlist.m3u?sid={$sessionid}"><xsl:value-of select="$locale/sbJukebox/actions/play" /></a>
					<a class="type play" href="/{$master/@uuid}/details/getM3U/playlist.m3u?random=true&amp;sid={$sessionid}"><xsl:value-of select="$locale/sbJukebox/actions/play_random" /></a>
				</xsl:otherwise>
			</xsl:choose>
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</xsl:template>
	
	<xsl:template match="sbnode">
		
		<form id="playlist_form" method="post" action="/dummy/action.php">
		
		<div class="th" id="highlight_{@uuid}">
			<div class="albumdetails" style="float:right;">
				<!-- bloody form does not submit via submit() alone, so we have to use a crappy workaround -->
				<input id="playlist_form_submit" type="submit" name="submitbutton" value="submit" style="display:none;" />
				<!--<xsl:if test="@nodetype='sbJukebox:Playlist' and $master/user_authorisations/authorisation[@name='add_titles']">
					<xsl:choose>
						<xsl:when test="@uuid = $currentPlaylist/@uuid">
							<a class="type activated icononly" href="/{@uuid}/details/activate" title="{$locale/sbJukebox/actions/activate}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
						</xsl:when>
						<xsl:otherwise>
							<a class="type activate icononly" href="/{@uuid}/details/activate" title="{$locale/sbJukebox/actions/activate}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:if>
				<span style="margin-left: 15px;"></span>-->
				<xsl:call-template name="render_buttons"/>
				<span style="margin-left: 15px;"></span>
				<xsl:call-template name="render_stars" />
				<span style="margin-left: 5px;"></span>
				<xsl:call-template name="render_votebuttons" />
			</div>
			<span class="type playlist"><xsl:value-of select="@label" /></span>
		</div>
		
		<ul class="sortable" id="playlist">
			<xsl:choose>
				<xsl:when test="children[@mode='playlist']/sbnode">
					<xsl:for-each select="children[@mode='playlist']/sbnode">
						<li style="position:relative;top:0;left:0;" id="item_{@uuid}">
							<xsl:call-template name="colorize" />
							<input class="helper" type="checkbox" id="check_{@uuid}" name="items[]" value="{@uuid}" onclick="toggle_checked('{@uuid}')" style="margin-right: 8px;" />
							<xsl:if test="$master/user_authorisations/authorisation[@name='write']">
								<a style="position:absolute;top:5px;right:3px;" class="type remove icononly" href="javascript:remove('{@uuid}')" title="{$locale/sbJukebox/actions/remove}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
							</xsl:if>
							<span style="width:{$starcolwidth}; vertical-align:middle; padding: 0 5px 0 0;" >
								<xsl:call-template name="render_stars" />
							</span>
							<span class="type {translate(@nodetype, ':', '_')}"><a href="/{@uuid}" style="position:relative; top:-1px;"><xsl:value-of select="@label" /></a></span>
							<xsl:if test="string-length(@info_lyrics) &gt; 0">
								<a class="type searchLyrics icononly" href="javascript:toggle('lyrics_{@uuid}');" style="margin-left:10px;"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
							</xsl:if>
							<div id="lyrics_{@uuid}" style="padding:10px; text-align:center; white-space:pre; color:lightgrey; font-size:0.9em; display:none;">
								<xsl:value-of select="@info_lyrics" />
							</div>
						</li>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<!--<li><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></li>-->
				</xsl:otherwise>
			</xsl:choose>
		</ul>
		
		</form>
		
		<script language="Javascript" type="text/javascript">
			
			//--------------------------------------------------------------
			// init
			//
			<xsl:choose>
				<xsl:when test="$master/user_authorisations/authorisation[@name='write']">
					var oPlaylist = $('playlist');
					var aInitialState = getOrder(oPlaylist);
					Sortable.create('playlist', { onChange: redraw, onUpdate: reorder } );
				</xsl:when>
				<xsl:otherwise>
					var aListElements = $$('ul.sortable li');
					for (var i=0; i&lt;aListElements.length; i++) {
						aListElements[i].style.cursor = 'auto';
					}
				</xsl:otherwise>
			</xsl:choose>
			
		</script>
			
		<xsl:call-template name="comments" />
		
	</xsl:template>
	
	<xsl:template name="import">
		<xsl:param name="form" />
		<!--<xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>-->
		<form action="{$form/@action}" name="import" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
			<xsl:value-of select="$locale/sbJukebox/labels/upload_playlist" />:
			<xsl:apply-templates select="$form/sbinput[@type='fileupload']" mode="inputonly" />
			<xsl:value-of select="' '" />
			<xsl:apply-templates select="$form/submit" mode="inputonly" />
		</form>
	</xsl:template>

</xsl:stylesheet>