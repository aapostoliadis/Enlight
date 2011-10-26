<?php
class Enlight_Template_TemplateDefault extends Smarty_Internal_Template
{
    public function extendsBlock($spec, $content, $mode = 'replace')
    {
    	if (strpos($content, $this->smarty->left_delimiter . '$smarty.block.child' . $this->smarty->right_delimiter) !== false) {
    		if (isset($this->block_data[$spec])) {
    			$content = str_replace($this->smarty->left_delimiter.'$smarty.block.child'.$this->smarty->right_delimiter, $this->block_data[$spec]['source'], $content);
    			unset($this->block_data[$spec]);
    		} else {
    			$content = str_replace($this->smarty->left_delimiter.'$smarty.block.child'.$this->smarty->right_delimiter, '', $content);
    		}
    	}
    	if (isset($this->block_data[$spec])) {
    		if (strpos($this->block_data[$spec]['source'], '%%%%SMARTY_PARENT%%%%') !== false) {
    			$content = str_replace('%%%%SMARTY_PARENT%%%%', $content, $this->block_data[$spec]['source']);
    		} elseif ($this->block_data[$spec]['mode'] == 'prepend') {
    			$content = $this->block_data[$spec]['source'].$content;
    		} elseif ($this->block_data[$spec]['mode'] == 'append') {
    			$content .= $this->block_data[$spec]['source'];
    		}
    	}
    	$this->block_data[$spec] = array('source'=>$content, 'mode'=>$mode);
    }

	public function extendsTemplate($template_name)
    {
    	$this->template_resource .= '|'.$template_name;
    	$this->resource_name .= '|'.$template_name;
    	//$this->template->compile_id .= '|'.$template_name;
    	$this->template_filepath = null;
    }
    
    public function renderTemplate()
    {
    	$obLevel = ob_get_level();
    	try {
    		return parent::renderTemplate();
    	} catch (Exception $e) {
			while (ob_get_level() > $obLevel) {
				ob_get_clean();
			}
    		throw $e;
    	}
    }
    
    /*
    public function compileTemplateSource ()
    {
    	$org = $this->smarty->force_compile;
    	$this->smarty->force_compile = true;
    	var_dump($this->resource_name); die();
    	parent::compileTemplateSource();
    	$this->smarty->force_compile = $org;
    }
    */
    
    protected function loadTemplateResourceHandler ($resource_type)
    {
    	if($resource_type=='extends') {
    		return new Enlight_Template_TemplateResource($this->smarty);
    	}
    	return parent::loadTemplateResourceHandler($resource_type);
    }
}