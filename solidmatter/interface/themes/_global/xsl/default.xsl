<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml">

	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="no"
	/>

	<xsl:variable name="lang" select="/response/metadata/system/lang" />
	<xsl:variable name="subjectid" select="$content/sbnode[@master]/@uuid" />
	<xsl:variable name="locale" select="/response/locales/locale[@lang=$lang]" />
	<xsl:variable name="commands" select="/response/metadata/commands" />
	<xsl:variable name="system" select="/response/metadata/system" />
	<xsl:variable name="content" select="/response/content" />
	<xsl:variable name="errors" select="/response/errors" />
	<xsl:variable name="stylesheets_css" select="'/theme/sb_system/css'" />
	<xsl:variable name="scripts_js" select="'/theme/sb_system/js'" />
	<xsl:variable name="images" select="'/theme/sb_system/images'" />
	<xsl:variable name="master" select="$content/sbnode[@master]" />
	<xsl:variable name="sessionid" select="/response/metadata/system/sessionid" />
	<xsl:variable name="userid" select="/response/metadata/system/userid" />
	<xsl:variable name="auth" select="$master/user_authorisations/authorisation" />
	
	<xsl:template match="/response/locales"></xsl:template>
	
	<xsl:template match="/response/metadata">
		<!-- title -->
		<title><xsl:value-of select="/response/content/sbnode/@label" /> : <xsl:value-of select="$locale/*/views/view[@id=/response/content/@view]" /></title>
		<!-- styles -->
		<xsl:for-each select="modules/*">
			<link rel="stylesheet" href="/theme/{name()}/css/styles.css" type="text/css" media="all" />
		</xsl:for-each>
		<!-- static scripts -->
		<!-- additional scripts -->
		<xsl:if test="//sbinput[@type='codeeditor']">
			<script language="Javascript" type="text/javascript" src="{$scripts_js}/edit_area/edit_area_full.js"></script>
		</xsl:if>
		<xsl:if test="commands/command">
			<script language="Javascript" type="text/javascript" src="{$scripts_js}/commands.js"></script>
			<script language="Javascript" type="text/javascript">
			<xsl:for-each select="commands/command">
				<xsl:choose>
				<xsl:when test="param">
					sbCommander.issueCommand("<xsl:value-of select="@action" />", {
					<xsl:for-each select="param">
						<xsl:value-of select="@name" />: '<xsl:value-of select="@value" />',
					</xsl:for-each>
					});
				</xsl:when>
				<xsl:otherwise>
					sbCommander.issueCommand("<xsl:value-of select="@action" />", null);
				</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
			</script>
		</xsl:if>
		<script language="Javascript" type="text/javascript">
			try {
				document.execCommand("BackgroundImageCache", false, true);
			} catch(err) {}
		</script>
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
				<th class="th2">Method</th>
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