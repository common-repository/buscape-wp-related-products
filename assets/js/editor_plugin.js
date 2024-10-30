// Docu : http://wiki.moxiecode.com/index.php/TinyMCE:Create_plugin/3.x#Creating_your_own_plugins

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('bwprp');
	 
	tinymce.create('tinymce.plugins.bwprp', {
		
		init : function(ed, url) {
		// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');

			ed.addCommand('bwprp', function() {
				ed.windowManager.open({
					file : url + '../../../includes/window.php',
					width : 400,
					height : 110,
					inline : 1
				}, {
					plugin_url : url // Plugin absolute URL
				});
			});

			// Register example button
			ed.addButton('bwprp', {
				title : 'BuscaPé WP Related Products',
				cmd : 'bwprp',
				image : url + '../../../images/logo.png'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('bwprp', n.nodeName == 'IMG');
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
					longname  : 'BuscaPé WP Related Products',
					author 	  : 'Apiki Open Source',
					authorurl : 'http://www.apiki.com',
					infourl   : 'http://www.apiki.com',
					version   : "0.1"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('bwprp', tinymce.plugins.bwprp);
})();


