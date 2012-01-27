{extends file="frontend/index/base.tpl"}

{block name="frontend/index/inner_container"}
<div class="logo">
    <div class="enlight-logo">
        <a href="http://www.enlight.de/wiki/shopware.php">Enlight - Open Source eCommerce Framework</a>
    </div>
</div>
<div class="inner">

    <div class="content">
        <div class="alert-message success">
            <p><strong>Congratulations!</strong> Enlight has been successfully installed!</p>
        </div>

        <div class="page-header">
            <h1>
                Welcome to Enlight&nbsp;<small>- It works!</small>
            </h1>
        </div>
        <div class="description">
            Congratulations, you have successfully installed Enlight. Now you can use one of the most advanced pure
            e-commerce frameworks that do exist. Now you have the opportunity to write your own applications whilst you
            are offered detailed advice. You will find initial information on our Enlight website. In addition to that,
            you will get hints and tricks on the following sites:
        </div>
        <div class="list">
            <dl>
                <dt><a href="http://api.enlight.de" target="_blank">API documentation</a></dt>
                <dd>A detailed source code documentation of the particular Enlight classes</dd>

                <dt>
                    <a href="http://www.enlight.de/wiki/_detail_679_499.html" target="_blank">
                        Beginners guide
                    </a>
                </dt>
                <dd>Write your first application with Enlight</dd>

                <dt><a href="http://www.enlight.de/wiki/Tutorials_cat_500.html" target="_blank">Tutorials</a></dt>
                <dd>Read through the implementation of the different Enlight components</dd>

                <dt><a href="http://www.enlight.de/wiki/Komponenten_cat_501.html" target="_blank">Components</a></dt>
                <dd>Learn more about the different Enlight components </dd>
            </dl>
        </div>
    </div>
</div>
{/block}