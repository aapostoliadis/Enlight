<?php
class Enlight_Template_TemplateResource extends Smarty_Internal_Resource_Extends
{
	public function getTemplateSource ($_template)
    {
    	parent::getTemplateSource($_template);
        $_template->template_source = $this->replaceSnippet($_template->template_source, $_template->template_filepath);
        return true;
    }
        
    protected function saveBlockData($block_content, $block_tag, $_filepath, $_template)
    {
    	$block_content = $this->replaceSnippet($block_content, $_filepath);
    	return parent::saveBlockData($block_content, $block_tag, $_filepath, $_template);
    }
    
    protected function replaceSnippet($source, $filepath)
    {
    	$_rdl = preg_quote($this->smarty->right_delimiter);
		$_ldl = preg_quote($this->smarty->left_delimiter);
		
		if(!empty($this->smarty->ignore_namespace)) {
			$_block_namespace = null;
		} elseif(preg_match("!{$this->_ldl}namespace(\s.+?)?{$this->_rdl}!msi", $source, $_namespace_match)) {
			$source = str_replace($_namespace_match[0], '', $source);
			if (preg_match("!.?name=(.*?)(?=(\s|$))!", $_namespace_match[1], $_name_match)) {
	            $_block_namespace = $_name_match[1];
	        } elseif (strpos($_namespace_match[1], 'ignore') !== false) {
	        	$_block_namespace = null;
	        } else {
				$_block_namespace = null;
	        	$this->smarty->trigger_error("\"namespace\" missing name attribute");
	        }
		} else {
			$_block_namespace = $this->getNamespace($filepath);
		}
		
		while (preg_match("!{$this->_ldl}s(e?)(\s.+?)?{$this->_rdl}(.*?){$this->_ldl}/se?{$this->_rdl}!msi", $source, $_block_match, PREG_OFFSET_CAPTURE)) {
			$_block_editable = !empty($_block_match[1][0]);
			$_block_args = $_block_match[2][0];
			$_block_default = $_block_match[3][0];
			list($_block_tag, $_block_start) = $_block_match[0];
			$_block_lenght = strlen($_block_tag);
			if (!preg_match("!(.?)(name=)(.*?)(?=(\s|$))!", $_block_args, $_match) && empty($_block_default)) {
	            $this->smarty->trigger_error("\"" . $_block_tag . "\" missing name attribute"); continue;
	        }
	        $_block_force = (bool) preg_match('#\sforce#', $_block_args);
	        $_block_name = !empty($_match[3]) ? trim($_match[3], '\'"') : $_block_default;
	        if (preg_match("!(.?)(namespace=)(.*?)(?=(\s|$))!", $_block_args, $_match)) {
	            $_namespace = trim($_match[3], '\'"');
	        } else {
	        	$_namespace = $_block_namespace;
	        }
	        $_block_content = $this->getSnippet($_block_name, $_namespace, $_block_default, $_block_force);
	        
	        if(!empty($_block_default)) {
	        	$_block_args .= ' default='.var_export($_block_default, true);
	        }
	        if(!empty($_block_namespace)) {
	        	$_block_args .= ' namespace='.var_export($_block_namespace, true);
	        }
	        if(!empty($_block_editable)) {
	        	$_block_args .= ' tag="span"';
	        }
	        
	        $_block_content = "{$this->smarty->left_delimiter}snippet$_block_args{$this->smarty->right_delimiter}".
	        	"$_block_content{$this->smarty->left_delimiter}/snippet{$this->smarty->right_delimiter}";
	        
	        $source = substr_replace($source, $_block_content, $_block_start, $_block_lenght);
		}
		
		return $source;
    }
    
    protected function getSnippet($name, $namespace, $default, $force=false)
    {
    	if(!Enlight()->Bootstrap()->hasResource('Snippets')){
    		return $default;
    	}
    	$snippet = Enlight()->Snippets()->getSnippet(empty($namespace) ? null : $namespace);
		$content = $snippet->get($name);
		if($content===null||$force) {
			$snippet->insert($name, $default);
			return $default;
		} else {
			return $content;
		}
    }
    
    protected function getNamespace($path)
    {
    	$path = realpath($path);
	    foreach ($this->smarty->template_dir as $template_dir) {
			$template_dir = realpath($template_dir);
			if(strpos($path, $template_dir)===0) {
				$namespace = substr($path, strlen($template_dir));
				$namespace = strtr($namespace, DIRECTORY_SEPARATOR, '/');
				$namespace = dirname($namespace).'/'.basename($namespace, '.tpl');
				$namespace = trim($namespace,'/');
				return $namespace;
			}
		}
		return $path;
    }
      
    public function getTemplateTimestampTypeName($_resource_type, $_resource_name)
    {
    	return filemtime($_resource_name);
    }
}