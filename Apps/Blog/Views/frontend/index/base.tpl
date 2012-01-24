<!DOCTYPE html>
<html lang="en">

    {include file="frontend/index/header.tpl"}

    <body lang="en">

        <!-- Menu component -->
        {include file="frontend/index/menu.tpl"}

        <!-- Container -->
        <div class="container">

            <!-- Breadcrumb -->
            <ul class="breadcrumb">
            {block name="frontend/index/breadcrumbs"}
                <li>
                    <a href="{url action=index}">Home</a> <span class="divider">/</span>
                </li>
            {/block}
            </ul> <!-- //Breadcrumb -->

        <!-- Content area block -->
        {block name="frontend/index/inner_container"}{/block}

        <!-- Footer component -->
        {include file="frontend/index/footer.tpl"}

        </div> <!-- //Container -->
    </body>
</html>