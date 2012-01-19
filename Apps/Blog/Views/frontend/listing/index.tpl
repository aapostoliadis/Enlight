{extends file="frontend/index/base.tpl"}

{*extends the breadcrumbs and append the new one*}
{block name="frontend/index/breadcrumbs" append}
   <li><a href="{url controller=listing action=index}">All Posts</a> <span class="divider">/</span></li>
{/block}

{block name="frontend/index/inner_container"}
<div class="content">
    <div class="page-header">
        {if $controller=="Frontend_Listing"}
            <h1>All Posts:</h1>
        {elseif $controller=="Frontend_Search"}
            <h1>Search Result:</h1>
        {/if}
    </div>

    <!-- Example row of columns -->
    <div class="row">

        {foreach from=$posts item=post}
            <div class="span16" style="border-bottom: 1px solid #ddd;">
                <h3>{$post.headline}</h3>

                <p>{$post.content|truncate:400:"..."}</p>

                <p><a class="btn" href="{url controller=detail action=index postID=$post.id}">View details &raquo;</a>
                </p>
            </div>
        {/foreach}
    </div>
</div>


{/block}