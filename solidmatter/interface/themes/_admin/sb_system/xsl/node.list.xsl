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
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</body>
	</html>
	</xsl:template>
		
	<xsl:template match="sbnode">
		<form name="massaction" action="/sdsdds" class="default">
		<table class="default" width="100%" id="list">
			<thead>
				<tr>
					<th></th>
					<th><xsl:value-of select="$locale/sbSystem/labels/name" /></th>
					<th><xsl:value-of select="$locale/sbSystem/labels/type" /></th>
					<th><xsl:value-of select="$locale/sbSystem/labels/created_at" /></th>
					<th><xsl:value-of select="$locale/sbSystem/labels/modified_at" /></th>
					<th><xsl:value-of select="$locale/sbSystem/labels/options" /></th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="children[@mode='list']/sbnode">
					<xsl:for-each select="children[@mode='list']/sbnode">
						<tr>
							<xsl:call-template name="colorize" />
							<td width="1"><input type="checkbox" name="marker" id="marker_{@uuid}" /></td>
							<td>
								<a class="highlighted type {@displaytype}" href="/{@uuid}"><span class=""><xsl:call-template name="localize"><xsl:with-param name="label" select="@label" /></xsl:call-template></span></a>
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
								<xsl:if test="position() != 1">
									<a href="/-/structure/orderBefore/?subject={$subjectid}&amp;source={@name}&amp;destination={preceding-sibling::*[1]/@name}" class="option highlighted"><img src="/theme/sb_system/icons/move_up.gif" /></a>
								</xsl:if>
								<xsl:if test="position() != last()">
									<a href="/-/structure/orderBefore/?subject={$subjectid}&amp;source={following-sibling::*[1]/@name}&amp;destination={@name}" class="option highlighted"><img src="/theme/sb_system/icons/move_down.gif" /></a>
								</xsl:if>
								<xsl:if test="boolean('true')">
									<a href="javascript:top.sbUtilities.popupModal('/-/structure/deleteChild/?parentnode={$master/@uuid}&amp;childnode={@uuid}', 500, 250, 'sbCommander.issueCommand(\'reloadTree\', null);')" class="option highlighted"><img src="/theme/sb_system/icons/doc_delete.gif" /></a>
								</xsl:if>
							</td>
						</tr>
					</xsl:for-each>
					<tfoot>
						<tr>
							<td colspan="6" style="text-align:left;">
								<input type="checkbox" id="toggle" onchange="javascript:toggleAll();" /> alle markieren | markierte
								<input type="button" value="ausschneiden" onclick="cutMultiple()" />
								<input type="button" class="warning" value="löschen" onclick="deleteMultiple()" />
								<input type="button" value="zu Favoriten" onclick="addToFavoritesMultiple()" />
							</td>
						</tr>
					</tfoot>
				</xsl:when>
				<xsl:otherwise>
					<tfoot>
						<tr><td colspan="6"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
					</tfoot>
				</xsl:otherwise>
			</xsl:choose>
			</tbody>
		</table>	
		</form>
	</xsl:template>

</xsl:stylesheet>