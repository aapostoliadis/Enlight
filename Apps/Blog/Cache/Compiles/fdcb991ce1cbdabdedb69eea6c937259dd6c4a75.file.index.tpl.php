<?php /* Smarty version Smarty 3.1-DEV, created on 2012-01-23 16:28:46
         compiled from "/Applications/MAMP/htdocs/Enlight-Github/Apps/Blog/Views/frontend/index/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4377831394f1d6899b4f5f9-05083600%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fdcb991ce1cbdabdedb69eea6c937259dd6c4a75' => 
    array (
      0 => '/Applications/MAMP/htdocs/Enlight-Github/Apps/Blog/Views/frontend/index/index.tpl',
      1 => 1327332346,
      2 => 'file',
    ),
    'fe3aefaed3cc990e0e7af40efa446f136693e1b0' => 
    array (
      0 => '/Applications/MAMP/htdocs/Enlight-Github/Apps/Blog/Views/frontend/index/base.tpl',
      1 => 1327332400,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4377831394f1d6899b4f5f9-05083600',
  'function' => 
  array (
  ),
  'version' => 'Smarty 3.1-DEV',
  'unifunc' => 'content_4f1d6899c962d',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4f1d6899c962d')) {function content_4f1d6899c962d($_smarty_tpl) {?><?php if (!is_callable('smarty_function_url')) include '/Applications/MAMP/htdocs/Enlight-Github/Library/Enlight/Template/Plugins/function.url.php';
if (!is_callable('smarty_modifier_truncate')) include '/Applications/MAMP/htdocs/Enlight-Github/Library/Smarty/plugins/modifier.truncate.php';
?><!DOCTYPE html>
<html lang="en">

    <?php echo $_smarty_tpl->getSubTemplate ("frontend/index/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


    <body lang="en">

        <?php echo $_smarty_tpl->getSubTemplate ("frontend/index/menu.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		
		<!-- Container -->
        <div class="container">

			<!-- Breadcrumb -->
            <ul class="breadcrumb">
	            
	                <li>
	                	<a href="<?php echo smarty_function_url(array('action'=>'index'),$_smarty_tpl);?>
">Home</a> <span class="divider">/</span>
	                </li>
	            
            </ul> <!-- //Breadcrumb -->
            
            <!-- Content area block -->
            

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
	
		<?php  $_smarty_tpl->tpl_vars['post'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['post']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['teaserPosts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['post']->key => $_smarty_tpl->tpl_vars['post']->value){
$_smarty_tpl->tpl_vars['post']->_loop = true;
?>
		
			<!-- The blog entry -->
			<article class="span-one-third">
				<h3><?php echo $_smarty_tpl->tpl_vars['post']->value['headline'];?>
</h3>
				
				<p><?php echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['post']->value['content'],100,"...");?>
</p>
				<p>
					<a class="btn" href="<?php echo smarty_function_url(array('controller'=>'detail','action'=>'index','postID'=>$_smarty_tpl->tpl_vars['post']->value['id']),$_smarty_tpl);?>
">
						View details &raquo;
					</a>
				</p>
			</article> <!-- //The blog entry -->
    	<?php } ?>
	</div> <!-- //Example row of columns -->
</section> <!-- //Listing - Latest blog posts -->


            <?php echo $_smarty_tpl->getSubTemplate ("frontend/index/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>


        </div> <!-- //Container -->
    </body>
</html><?php }} ?>