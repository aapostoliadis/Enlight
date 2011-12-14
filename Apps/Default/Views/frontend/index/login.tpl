{extends file="frontend/index/base.tpl"}

{block name="frontend/index/inner_container"}
    {$form->getElement('username')->setLabel('User')}
    {$form->setAction({url action=login})->render()}
{/block}