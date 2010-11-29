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
	
	<xsl:variable name="menu_path" select="translate($content/menu/sbnode/@query, '/', ':')" />
	
	<xsl:template match="/">
		<xsl:apply-templates select="response/content/contextmenu" />
		<xsl:apply-templates select="response/errors" />
	</xsl:template>
	
	<xsl:template match="response/content/contextmenu">
		<ul>
			
			<!-- create new nodes -->
			<xsl:if test="new">
				<li>
					<span class="type new"><xsl:value-of select="$locale/sbSystem/actions/new" />:</span>
					<ul>
					<xsl:for-each select="new">
						<xsl:variable name="type" select="@nodetype" />
						<li><a href="/-/structure/createChild/?parentnode={$content/contextmenu/@uuid}&amp;nodetype={$type}" target="main"><span class="type {@displaytype}"><xsl:value-of select="$locale//nodetypes/type[@id=$type]" /></span></a></li>
					</xsl:for-each>
					</ul>
				</li>
			</xsl:if>
			
			<!-- refresh -->
			<xsl:if test="@refresh='TRUE' and new">
				<hr />
			</xsl:if>
			<xsl:if test="@refresh='TRUE'">
				<li><a href="javascript:sbMenu.reloadMenuEntry('{@path}')"><span class="type refresh"><xsl:value-of select="$locale/sbSystem/actions/refresh" /></span></a></li>
			</xsl:if>
			
			<!-- paste/link -->
			<xsl:if test="@clipboard='TRUE'">
				<hr />
			</xsl:if>
			<xsl:if test="@clipboard='TRUE'">
				<li><a href="javascript:sbMenu.paste('{@uuid}');" title="{@clipboard_subject}"><span class="type paste"><xsl:value-of select="$locale/sbSystem/actions/paste" /></span></a></li>
				<!--<li><a href="javascript:sbUtilities.popupModal('/-/structure/paste/parentnode={@uuid}', 500, 500);" title="{@clipboard_subject}"><span class="type paste"><xsl:value-of select="$locale/system/general/actions/paste" /></span></a></li>-->
			</xsl:if>
			<xsl:if test="@clipboard='TRUE' and @clipboard_type='copy'">
				<li><a href="javascript:sbMenu.createLink('{@uuid}');" title="{@clipboard_subject}"><span class="type paste"><xsl:value-of select="$locale/sbSystem/actions/create_hardlink" /></span></a></li>
				<!--<li><a href="javascript:sbUtilities.popupModal('/-/structure/createLink/parentnode={@uuid}', 500, 500);" title="{@clipboard_subject}"><span class="type paste"><xsl:value-of select="$locale/system/general/actions/create_hardlink" /></span></a></li>-->
			</xsl:if>
			
			<!-- cut/copy -->
			<xsl:if test="@delete='TRUE'">
				<hr />
			</xsl:if>
			<xsl:if test="@delete='TRUE'">
				<li><a href="javascript:sbMenu.cut('{@parent}', '{@uuid}');"><span class="type cut"><xsl:value-of select="$locale/sbSystem/actions/cut" /></span></a></li>
			</xsl:if>
			<xsl:if test="@delete='TRUE'">
				<li><a href="javascript:top.sbUtilities.execute('/-/structure/copy/?parentnode={@parent}&amp;childnode={@uuid}');"><span class="type copy"><xsl:value-of select="$locale/sbSystem/actions/copy" /></span></a></li>
			</xsl:if>
			
			<!-- import/export -->
			<xsl:if test="@import = 'TRUE' or @export = 'TRUE'">
				<hr />
			</xsl:if>
			<xsl:if test="@export = 'TRUE'">
				<li><a href="/-/utilities/export_branch/?subject_uuid={@uuid}" target="_blank"><span class="type export"><xsl:value-of select="$locale/sbSystem/actions/export" /></span></a></li>
			</xsl:if>
			<xsl:if test="@import = 'TRUE'">
				<li><a href="#"><span class="type import"><xsl:value-of select="$locale/sbSystem/actions/import" /></span></a></li>
			</xsl:if>
			
			<!-- delete -->
			<xsl:if test="@delete='TRUE'">
				<hr />
				<li><a href="javascript:sbMenu.deleteItem('{@parent}', '{@uuid}');"><span class="type delete"><xsl:value-of select="$locale/sbSystem/actions/delete" /></span></a></li>
			</xsl:if>
			
			<!-- trashcan -->
			<xsl:if test="@purge='TRUE'">
				<hr />
				<!--<li><a href="javascript:sbMenu.purgeTrash('{@uuid}');"><span class="type delete"><xsl:value-of select="$locale/sbSystem/actions/purge" /></span></a></li>-->
				<li><a href="javascript:sbMenu.purge('{@uuid}');"><span class="type delete"><xsl:value-of select="$locale/sbSystem/actions/purge" /></span></a></li>
			</xsl:if>
			
		</ul>
	</xsl:template>
	
</xsl:stylesheet>