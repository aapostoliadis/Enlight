<head>

{* Meta-Tags *}
{block name='backend/base/header/meta_tags'}
<meta charset="UTF-8">
<meta name="robots" content="noindex,nofollow" />
{/block}

{* Page title *}
<title>{block name='backend/base/header/title'}{*s name='header/title'}{/s*}{/block}</title>

{block name="backend/base/header/css"}
    <link rel="stylesheet" type="text/css" href="{link file='../../Library/ExtJs/resources/css/ext-all.css'}" />
    <link rel="stylesheet" type="text/css" href="{link file='backend/_resources/styles/index.css'}" />
    <link rel="icon" href="{link file='backend/_resources/images/index/favicon.ico'}" type="image/x-icon">
    <link rel="shortcut icon" href="{link file='backend/_resources/images/index/favicon.ico'}" type="image/x-icon">
{/block}

{block name="backend/base/header/javascript"}
    <script type="text/javascript" charset="utf-8" src="{link file='../../Library/ExtJs/ext-all.js'}"></script>
    <script type="text/javascript" charset="utf-8" src="{link file='../../Library/ExtJs/locale/ext-lang-de.js'}"></script>

    <script type="text/javascript" src="{link file='../../Library/ExtJs/overrides/Ext.app.Application.js'}"></script>
    <script type="text/javascript" src="{link file='../../Library/ExtJs/overrides/Ext.app.Controller.js'}"></script>
    <script type="text/javascript" src="{link file='../../Library/ExtJs/overrides/Ext.app.EventBus.js'}"></script>
    <script type="text/javascript" src="{link file='../../Library/ExtJs/overrides/Ext.form.Field.js'}"></script>
    <script type="text/javascript" src="{link file='../../Library/ExtJs/overrides/Ext.button.Button.js'}"></script>
    <script type="text/javascript" src="{link file='../../Library/ExtJs/overrides/Ext.toolbar.Paging.js'}"></script>
    <script type="text/javascript" src="{link file='../../Library/ExtJs/ux/Ext.app.SubApplication.js'}"></script>
{/block}
</head>