<?php /* Smarty version Smarty 3.1-DEV, created on 2012-01-23 16:34:00
         compiled from "/Applications/MAMP/htdocs/Enlight-Github/Apps/Blog/Views/frontend/index/menu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:80217094f1d6899d878a9-61192468%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'af658dc4554123f6db06026fdd3c62e1af7f4e56' => 
    array (
      0 => '/Applications/MAMP/htdocs/Enlight-Github/Apps/Blog/Views/frontend/index/menu.tpl',
      1 => 1327332594,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '80217094f1d6899d878a9-61192468',
  'function' => 
  array (
  ),
  'version' => 'Smarty 3.1-DEV',
  'unifunc' => 'content_4f1d6899deb83',
  'variables' => 
  array (
    'activeMenu' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_4f1d6899deb83')) {function content_4f1d6899deb83($_smarty_tpl) {?><?php if (!is_callable('smarty_function_url')) include '/Applications/MAMP/htdocs/Enlight-Github/Library/Enlight/Template/Plugins/function.url.php';
?><!-- Navigation toolbar (top) -->
<nav class="topbar">
	<div class="fill">
		<div class="container">
			
			<!-- Project name -->
			<a class="brand" href="<?php echo smarty_function_url(array('action'=>'index'),$_smarty_tpl);?>
">
				Blog - Enlight
			</a> <!-- //Project name -->
      		
      		<!-- Main navigation -->
      		<ul class="nav">
        		<li<?php if ($_smarty_tpl->tpl_vars['activeMenu']->value=="index"){?> class="active"<?php }?>>
        			<a href="<?php echo smarty_function_url(array('action'=>'index'),$_smarty_tpl);?>
">Home</a>
        		</li>
        		
        		<li<?php if ($_smarty_tpl->tpl_vars['activeMenu']->value=="listing"){?> class="active"<?php }?>>
        			<a href="<?php echo smarty_function_url(array('controller'=>'listing','action'=>'index'),$_smarty_tpl);?>
">All Posts</a>
        		</li>
			</ul> <!-- //Main navigation -->
			
			<!-- Basic search component -->
			<form action="<?php echo smarty_function_url(array('controller'=>'search'),$_smarty_tpl);?>
" class="pull-right" method="post">
				<input type="text" name="searchTerm" placeholder="Search...">
			</form> <!-- //Basic search component -->
			
		</div>
	</div>
</nav> <!-- //Navigation toolbar (top) --><?php }} ?>