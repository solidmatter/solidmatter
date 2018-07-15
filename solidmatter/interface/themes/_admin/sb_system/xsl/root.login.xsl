<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" 
	extension-element-prefixes="dyn">

	<xsl:import href="global.default.xsl" />
	
	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="no"
	/>
	
	<xsl:template match="/">
	<html>
	<head>
		<link rel="stylesheet" href="{$stylesheets_css}/styles_login.css" type="text/css" media="all" />
		<script type="text/javascript" language="javascript">
			if (top.frames.length > 0) {
				top.location.href = self.location;
			}
		</script>
	</head>
	<body>
		<xsl:apply-templates select="response/errors" />
		<div class="login">
			<div class="logo"><h1><b>solid</b><i>Matter</i></h1></div>
			<div class="form">
				<xsl:apply-templates select="/response/content/sbform[@id='login_backend']" />
			</div>
<!-- 			<span class="version">0.50.00</span> -->
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template match="sbform">
		<form class="default" action="{@action}" method="post" accept-charset="UTF-8">
			<table class="login">
				<xsl:apply-templates select="*" />
			</table>
		</form>
	</xsl:template>
	
	<xsl:template match="sbinput[@type='string']">
		<tr>
			<td width="30%" style="text-align:right; vertical-align:top;"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<input type="text" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
					<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
				</input>
				<xsl:if test="@errorlabel"><br/><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
			</td>
		</tr>
	</xsl:template>
	
	<xsl:template match="sbinput[@type='password']">
		<tr>
			<td width="30%" style="text-align:right; vertical-align:top;"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<input type="password" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
					<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
				</input>
				<xsl:if test="@errorlabel"><br/><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
				<xsl:if test="@name='password' and ../@errorlabel"><br /><xsl:value-of select="dyn:evaluate(../@errorlabel)" /></xsl:if>
			</td>
		</tr>
	</xsl:template>
	
	<xsl:template match="sbinput[@type='captcha']">
		<tr>
			<td width="30%" style="text-align:right; vertical-align:top;"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<img src="/-/login/getCaptcha/uid=login_backend" /><br />
				<input type="text" size="{@size}" maxlength="{@length}" name="{@name}" id="{@name}">
					<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
				</input>
				<xsl:if test="@errorlabel"><br/><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
			</td>
		</tr>
	</xsl:template>
	
	<xsl:template match="submit">
		<tr>
			<td></td>
			<td>
				<input type="submit" name="{@value}" value="{dyn:evaluate(@label)}" />
			</td>
		</tr>
	</xsl:template>

</xsl:stylesheet>