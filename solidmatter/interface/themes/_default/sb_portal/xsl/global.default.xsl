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
	
	<xsl:import href="../../sb_system/xsl/global.sbform.xsl" />
	
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
	<xsl:variable name="auth" select="$master/user_authorisations/authorisation" />
	
	<xsl:template match="/response/locales"></xsl:template>
	
	<xsl:template match="/response/metadata">
		<!-- title -->
		<title><xsl:value-of select="$content/menu/node/@label" /> : <xsl:value-of select="/response/content/sbnode/@label" /></title>
		<!-- styles -->
		<!-- for now only include jukebox css! -->
		<link rel="stylesheet" href="/theme/sb_portal/css/styles.css" type="text/css" media="all" />
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
			<!-- <script language="Javascript" type="text/javascript" src="{$scripts_js}/prototype.js"></script>
			<script language="Javascript" type="text/javascript" src="{$scripts_js}/scriptaculous.js"></script>
			<script language="Javascript" type="text/javascript" src="{$scripts_js_jb}/stars.js"></script>
			<script language="Javascript" type="text/javascript" src="{$scripts_js_jb}/dynamic.js"></script> -->
		</head>
		<body>
			<div class="body">
				<div class="head">
					<h1><xsl:value-of select="$content/menu/node/@label" /></h1>
					<span class="userstuff">
						<xsl:choose>
							<xsl:when test="$system/userid">
								<a class="type logout" href="/-/login/logout">Logout</a>
								<!-- <a class="type config" href="/-/config" style="margin-right:7px;"><xsl:value-of select="$locale/sbJukebox/labels/config" /></a> -->
							</xsl:when>
							<xsl:otherwise>
								<a class="type login" href="/-/login">Login</a>
							</xsl:otherwise>
						</xsl:choose>
						
						
						
					</span>
				</div>
				<div class="menu">
					<ul>
					<xsl:for-each select="$content/menu/node/node">
						<li>
							<a href="/{@uuid}">
								<xsl:if test="@state='current' or .//*[@state='current']">
									<xsl:attribute name="class">active</xsl:attribute>
								</xsl:if>
							<xsl:value-of select="@label" />
							</a>
						</li>
					</xsl:for-each>
					</ul>
				</div>
				<div class="menu2">
					<ul>
					<xsl:for-each select="$content/menu/node/node/node">
						<li>
							<a href="/{@uuid}">
								<xsl:if test="@state='current' or .//*[@state='current']">
									<xsl:attribute name="class">active</xsl:attribute>
								</xsl:if>
							<xsl:value-of select="@label" />
							</a>
						</li>
						<xsl:if test="position() != last()">
							<li style="padding: 5px 5px 5px 5px;">|</li>
						</xsl:if>
					</xsl:for-each>
					</ul>
				</div>
				<table class="content" width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<xsl:if test="$content/menu/node/node/node/node">
							<td class="sidebar">
								<ul>
									<xsl:apply-templates select="$content/menu/node/node/node/node" />
								</ul>
							</td>
						</xsl:if>
						<td class="content">
							<xsl:call-template name="content" />
						</td>
					</tr>
				</table>
				<div class="footer"><span style="float:left;">sbPortal</span>
					<xsl:apply-templates select="/response/metadata/stopwatch" />
				</div>
			</div>
		</body>
		</html>
	</xsl:template>
	
	<xsl:template match="node">
		<li>
			<a href="/{@uuid}">
				<xsl:if test="@state='current' or .//*[@state='current']">
					<xsl:attribute name="class">active</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="@label" />
			</a>
			<xsl:if test="node">
				<ul>
					<xsl:apply-templates select="node" />
				</ul>
			</xsl:if>
		</li>
	</xsl:template>
	
	<xsl:template match="/response/metadata/stopwatch">
		<span>
			<xsl:attribute name="title">
				LOAD:<xsl:value-of select="load" />ms | 
				PHP:<xsl:value-of select="php" />ms |
				PDO:<xsl:value-of select="pdo" />ms
			</xsl:attribute>
			<xsl:value-of select="execution_time" />ms | 
			<a href="/{$content/@uuid}/{$content/@view}/{$content/@action}/?debug=1" target="_blank">XML</a>
		</span>
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
	
	<!-- break arbeitet nur mit n -->
	<xsl:template name="break">
		<xsl:param name="text" select="."/>
		<xsl:choose>
		<xsl:when test="contains($text, '&#x0D;')">
			<xsl:if test="string-length(substring-before($text, '&#x0D;')) > 0">
				<xsl:value-of select="substring-before($text, '&#x0D;')"/>
			</xsl:if>
			<br/>
			<xsl:call-template name="break">
			<xsl:with-param name="text" select="substring-after($text,'&#x0D;')"/>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:if test="string-length($text) > 0">
				<xsl:value-of select="$text"/>
			</xsl:if>
		</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
</xsl:stylesheet>