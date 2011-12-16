<!DOCTYPE html>
<html lang="en">

{include file="frontend/index/header.tpl"}

  <body>

    {action name="menu" controller="index" p}

    <div class="container">

      <ul class="breadcrumb">
        <li><a href="{url action=index}">Home</a> <span class="divider">/</span></li>
        <li><a href="{url action=login}">Form</a> <span class="divider">/</span></li>
        <li><a href="#">Another one</a> <span class="divider">/</span></li>
        <li class="active">You are here</li>
      </ul>

    {block name="frontend/index/inner_container"}{/block}

    {include file="frontend/index/footer.tpl"}

    </div> <!-- /container -->

  </body>
</html>