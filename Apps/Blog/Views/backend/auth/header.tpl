{extends file="backend/index/header.tpl"}

{block name='backend_index_header_title'}
	Shopware Backend (c) 2010,2011 shopware AG
{/block}

{block name="backend_index_css" append}
	<link href="{link file='backend/_resources/styles/index.css'}" rel="stylesheet" type="text/css" />
{/block}

{block name="backend_index_javascript" append}
{include file="backend/auth/login_panel.tpl"}
{include file="backend/auth/viewport.tpl"}
<script type="text/javascript">
//<![CDATA[
	if (self != top) {
    	parent.location.href=self.location.href;
	}
	Ext.onReady(function(){
		{if $BrowserError}
			alert('Im Augenblick werden nur folgende Browser im Backend unterstützt: Firefox, Safari, Chrome. Eine IE-Unterstützung befindet sich in Arbeit!');
		{else}
			Auth = new Shopware.Auth.Viewport;
		{/if}
	});
//]]>
</script>
{/block}