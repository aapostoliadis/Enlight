{extends file="frontend/index/base.tpl"}

{*extends the breadcrumbs and append the new one*}
{block name="frontend/index/breadcrumbs" append}
   <li><a href="{url controller=detail action=index postID=$post.id}">{$post.headline}</a> <span class="divider">/</span></li>
{/block}

{block name="frontend/index/inner_container"}
<article class="content space">
    <header class="page-header">
        <h1>{$post.headline}
            <small>created on : {$post.creation_date|date_format:"%d.%m.%Y"}</small>
        </h1>
    </header>

    <section class="row" role="article">
        <div class="span16">
            <p>{$post.content}</p>
        </div>
    </section>
</article>

<section class="interesting-topics">

    <header class="page-header">
        <h2>Another interesting topics:</h2>
    </header>

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
</section>
{/block}