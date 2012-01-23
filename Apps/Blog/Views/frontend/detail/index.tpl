{extends file="frontend/index/base.tpl"}

{* Extends the breadcrumbs and appends a new entry *}
{block name="frontend/index/breadcrumbs" append}
<li>
    <a href="{url controller=detail action=index postID=$post.id}">{$post.headline}</a> <span class="divider">/</span>
</li>
{/block}

{block name="frontend/index/inner_container"}

<!-- Detail view of the selected blog entry -->
<section class="content space">
    <header class="page-header">
        <h1>{$post.headline}
            <small>created on : {$post.creation_date|date_format:"%d.%m.%Y"}</small>
        </h1>
    </header>

    <article class="row" role="article">
        <div class="span16">
            <p>{$post.content}</p>
        </div>
    </article>
</section> <!-- //Detail view of the selected blog entry -->

<!-- Other interessing blog entries -->
<section class="interesting-topics">

    <header class="page-header">
        <h2>Another interesting topic(s):</h2>
    </header>

    <!-- Example row of columns -->
    <div class="row">
        {foreach from=$teaserPosts item=post}
            <article class="span-one-third">
                <h3>{$post.headline}</h3>

                <p>{$post.content|truncate:100:"..."}</p>
                <p>
                    <a class="btn" href="{url controller=detail action=index postID=$post.id}">View details &raquo;</a>
                </p>
            </article>
        {/foreach}
    </div>
</section> <!-- //Other interessing blog entries -->
{/block}