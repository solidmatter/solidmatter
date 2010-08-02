<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml">

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
		<script type="text/javascript">
			function toggleAll() {
				var oToggle = document.getElementById('toggle');
				var oList = document.getElementById('list');
				for (var i=0; i &lt; document.massaction.marker.length; i++) {
					document.massaction.marker[i].checked = oToggle.checked;
				}
			}
			function cutMultiple() {
				alert('to be implemented...');
			}
			function deleteMultiple() {
				alert('to be implemented...');
			}
			function addToFavoritesMultiple() {
				alert('to be implemented...');
			}
		</script> 
	</head>
	<body>
		<xsl:call-template name="views" />
		<div class="workbench">
			<xsl:apply-templates select="response/errors" />
			<xsl:call-template name="renderTrash" />
		</div>
	</body>
	</html>
	</xsl:template>
		
	<xsl:template name="renderTrash">
		<form name="massaction" action="/sdsdds">
		<table class="default" width="100%" id="list">
			<thead>
				<tr>
					<!--<th></th>-->
					<th><xsl:value-of select="$locale/sbSystem/labels/name" /></th>
					<th><xsl:value-of select="$locale/sbSystem/labels/type" /></th>
					<th><xsl:value-of select="$locale/sbSystem/labels/created_at" /></th>
					<th><xsl:value-of select="$locale/sbSystem/labels/modified_at" /></th>
					<th><xsl:value-of select="$locale/sbSystem/labels/options" /></th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="$content/trash/nodes/sbnode">
					<xsl:for-each select="$content/trash/nodes/sbnode">
						<tr>
							<xsl:call-template name="colorize" />
							<!--<td width="1"><input type="checkbox" name="marker" id="marker_{@uuid}" /></td>-->
							<td>
								<a href="/{@uuid}"><span class="type {@displaytype}"><xsl:value-of select="@label" /></span></a>
							</td>
							<td>
								<xsl:variable name="type" select="@nodetype" />
								<xsl:value-of select="$locale//nodetypes/type[@id=$type]" />
							</td>
							<td>
								<xsl:value-of select="@created" />
							</td>
							<td>
								<xsl:value-of select="@modified" />
							</td>
							<td>
								<a href="/{$master/@uuid}/content/remove/?subject_uuid={@uuid}" class="option"><img src="/theme/sb_system/icons/doc_delete.gif" /></a>
								<a href="/{$master/@uuid}/content/recover/?subject_uuid={@uuid}&amp;parent_uuid={@parent}" class="option"><img src="/theme/sb_system/icons/move_up.gif" /></a>
							</td>
						</tr>
					</xsl:for-each>
					<!--<tr class="lastline"><td colspan="6">
						<input type="checkbox" id="toggle" onchange="javascript:toggleAll();" /> alle markieren | markierte
						<input type="button" value="ausschneiden" onclick="cutMultiple()" />
						<input type="button" value="löschen" onclick="deleteMultiple()" />
						<input type="button" value="zu Favoriten" onclick="addToFavoritesMultiple()" />
					</td></tr>-->
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="6"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			</tbody>
		</table>	
		</form>
	</xsl:template>

</xsl:stylesheet>