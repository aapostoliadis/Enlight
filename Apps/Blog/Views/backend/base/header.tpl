<head>

{* Meta-Tags *}
{block name='backend/base/header/meta_tags'}
<meta charset="UTF-8">
<meta name="robots" content="noindex,nofollow" />
{/block}

{* Page title *}
<title>{block name='backend/base/header/title'}{*s name='header/title'}{/s*}{/block}</title>

{block name="backend/base/header/css"}
    <link rel="stylesheet" type="text/css" href="{link file='backend/_resources/javascript/ext-4.0.0/resources/css/ext-all.css'}" />
    <link rel="stylesheet" type="text/css" href="{link file='backend/_resources/styles/index.css'}" />
    <link rel="icon" href="{link file='backend/_resources/images/index/favicon.ico'}" type="image/x-icon">
    <link rel="shortcut icon" href="{link file='backend/_resources/images/index/favicon.ico'}" type="image/x-icon">
{/block}

{block name="backend/base/header/javascript"}
    <script type="text/javascript" src="{link file='backend/_resources/javascript/ext-4.0.0/ext-all-debug.js'}"></script>
    <script type="text/javascript" src="{link file='backend/_resources/javascript/ext-4.0.0/locale/ext-lang-en.js'}"></script>
    <script type="text/javascript" src="{link file='backend/_resources/javascript/ext-4.0.0/overrides/Ext.app.Application.js'}"></script>
    <script type="text/javascript" src="{link file='backend/_resources/javascript/ext-4.0.0/overrides/Ext.app.Controller.js'}"></script>
    <script type="text/javascript" src="{link file='backend/_resources/javascript/ext-4.0.0/overrides/Ext.app.EventBus.js'}"></script>
    <script type="text/javascript" src="{link file='backend/_resources/javascript/ext-4.0.0/overrides/Ext.form.Field.js'}"></script>
    <script type="text/javascript" src="{link file='backend/_resources/javascript/ext-4.0.0/overrides/Ext.button.Button.js'}"></script>
    <script type="text/javascript" src="{link file='backend/_resources/javascript/ext-4.0.0/overrides/Ext.toolbar.Paging.js'}"></script>
    <script type="text/javascript" src="{link file='backend/_resources/javascript/ext-4.0.0/ux/Ext.app.SubApplication.js'}"></script>
    <script type="text/javascript" src="{link file='backend/_resources/javascript/ext-4.0.0/ux/Ext.selection.CheckboxModel.js'}"></script>
    <script type="text/javascript" src="{link file='backend/_resources/javascript/ext-4.0.0/ux/Ext.ux.MultiSelect.js'}"></script>
{/block}
</head>