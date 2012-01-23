{extends file="frontend/index/base.tpl"}

{block name="frontend/index/inner_container"}

<!-- Introducing banner -->
<section class="hero-unit">
    <h1>Example Blog!</h1>

    <p>This is our first Enlight based Blog Application</p>

    <p>
        <a class="btn primary large" href="http://www.enlight.de">Learn more &raquo;</a>
    </p>
</section> <!-- //Introducing banner -->

<!-- Listing - Latest blog posts -->
<section class="listing-blog-posts">
    <header class="page-header">
        <h2>Latest Posts:</h2>
    </header>

    <!-- Example row of columns -->
    <div class="row">

        {foreach from=$teaserPosts item=post}

            <!-- The blog entry -->
            <article class="span-one-third">
                <h3>{$post.headline}</h3>

                <p>{$post.content|truncate:100:"..."}</p>
                <p>
                    <a class="btn" href="{url controller=detail action=index postID=$post.id}">
                        View details &raquo;
                    </a>
                </p>
            </article> <!-- //The blog entry -->
        {/foreach}
    </div> <!-- //Example row of columns -->
</section> <!-- //Listing - Latest blog posts -->
{/block}