<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" extension-element-prefixes="dyn">

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
		<xsl:apply-templates select="/response/metadata" />
		<script language="Javascript" type="text/javascript">
			var timer = window.setInterval("refresh()", 3000);
			function refresh() {
				window.location.href = document.URL;
			}
		</script>
	</head>
	<body>
		<xsl:apply-templates select="response/errors" />
		<xsl:apply-templates select="response/content" />
	</body>
	</html>
	</xsl:template>
	
	<xsl:template match="response/content">
		<div class="progress">
			<div class="container">
				<div class="progressbar" style="width:{status/@percentage}%;"></div>
			</div>
			<xsl:value-of select="status/@status" />
		</div>
	</xsl:template>
	
</xsl:stylesheet>