{extends file="frontend/index/base.tpl"}

{* Extends the breadcrumbs and appends a new entry *}
{block name="frontend/index/breadcrumbs" append}
<li>
    <a href="{url controller=listing action=index}">All Posts</a> <span class="divider">/</span>
</li>
{/block}

{block name="frontend/index/inner_container"}

<!-- Content container -->
<section class="content">

    <!-- Example row of columns -->
    <div class="row">
        {foreach from=$posts item=post}

            <!-- The blog entry -->
            <article class="span16" style="border-bottom: 1px solid #ddd;">
                <h3>{$post.headline}</h3>

                <p>{$post.content|truncate:400:"..."}</p>
                <p>
                    <a class="btn" href="{url controller=detail action=index postID=$post.id}">
                        View details &raquo;
                    </a>
                </p>
            </article> <!-- //The blog entry -->
        {/foreach}
    </div>
</section> <!-- //Content container -->
{/block}