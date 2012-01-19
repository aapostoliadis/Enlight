{extends file="frontend/index/base.tpl"}

{block name="frontend/index/inner_container"}
<div class="hero-unit">
    <h1>Example Blog!</h1>

    <p>This is our first Enlight based Blog Application</p>

    <p><a class="btn primary large" href="http://www.enlight.de">Learn more &raquo;</a></p>
</div>

<div class="page-header">
    <h2>Latest Posts:</h2>
</div>
<!-- Example row of columns -->
<div class="row">
    {foreach from=$teaserPosts item=post}
        <div class="span-one-third">
            <h3>{$post.headline}</h3>
            <p>{$post.content|truncate:100:"..."}</p>
            <p><a class="btn" href="{url controller=detail action=index postID=$post.id}">View details &raquo;</a></p>
        </div>
    {/foreach}
</div>
{/block}