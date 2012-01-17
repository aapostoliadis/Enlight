{extends file="backend/base/header.tpl"}

{block name="backend/base/header/javascript" append}
<script type="text/javascript">

    Ext.define('Enlight.app.Application', {
    	extend: 'Ext.app.Application',
    	name: 'Enlight',
    	singleton: true
    });
    Ext.Loader.setConfig('enabled', true);
    Ext.Loader.setPath('Enlight.apps', '{url module=backend action=index}', '');
    Ext.onReady(function(){
        Enlight.app.Application.addSubApplication({ name: 'Enlight.apps.Auth' });
    });
</script>
{/block}