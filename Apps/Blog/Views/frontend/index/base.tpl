<!DOCTYPE html>
<html lang="en">

    {include file="frontend/index/header.tpl"}

    <body lang="en">

        {include file="frontend/index/menu.tpl"}

        <div class="container">

            <ul class="breadcrumb">

            {block name="frontend/index/breadcrumbs"}
                <li><a href="{url action=index}">Home</a> <span class="divider">/</span></li>
            {/block}

            </ul>
            {block name="frontend/index/inner_container"}{/block}

            {include file="frontend/index/footer.tpl"}

        </div>
    </body>
</html>