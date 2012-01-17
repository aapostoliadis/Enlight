<!DOCTYPE html>
<html lang="en">
{include file="frontend/index/header.tpl"}
  <body>
    {action name="menu" controller="index"}
    <div class="container">

    {* action name="breadcrumb" controller="index" *}

    {block name="frontend/index/inner_container"}{/block}

    {include file="frontend/index/footer.tpl"}
    </div>
  </body>
</html>