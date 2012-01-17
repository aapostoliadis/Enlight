<script type="text/javascript">
//<![CDATA[
var Auth = Ext.extend(Ext.Viewport,{
	forceFit: true,
	hideMode: 'offsets',
	layout: 'absolute',
	initComponent:function() {
		this.items = [this.LoginPanel];
		Auth.superclass.initComponent.call(this);
	},
	constructor: function(config){
		Ext.apply(this,config);
		this.loadWidgets();
		Auth.superclass.constructor.call(this);
	},
	loadWidgets: function(){
		this.LoginPanel = new Shopware.Auth.Components.LoginPanel({ parent:this });
	},
	initEvents: function(){
	}
});
Shopware.Auth.Viewport = Auth;
//]]>
</script>