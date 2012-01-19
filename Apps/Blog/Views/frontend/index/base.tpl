<!DOCTYPE html>
<html lang="en">

{include file="frontend/index/header.tpl"}

<body>

{action name="menu" controller="index"}

<div class="container">

    <ul class="breadcrumb">
        {block name="frontend/index/breadcrumbs"}
            <li><a href="{url action=index}">Home</a> <span class="divider">/</span></li>
        {/block}

        {*{foreach from=$breadcrumbs item=breadcrumb}*}
            {*<li><a href="{url controller=$breadcrumb.controller action=$breadcrumb.action}">{$breadcrumb.name}</a> <span class="divider">/</span></li>*}
        {*{/foreach}*}
    </ul>

{block name="frontend/index/inner_container"}{/block}

{include file="frontend/index/footer.tpl"}

</div>
<!-- /container -->

</body>
</html>