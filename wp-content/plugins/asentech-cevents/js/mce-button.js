(function() {
	tinymce.PluginManager.add('cvents', function( editor, url ) {
		var sh_tag = 'cvents';

		//helper functions 
		function getAttr(s, n) {
			n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
			return n ?  window.decodeURIComponent(n[1]) : '';
		};
		//add popup
		editor.addCommand('cvents_popup', function(ui, v) {
			//setup defaults
			var etitle = '';
			if (v.etitle)
				etitle = v.etitle;

			var ecode = '';
			if (v.ecode)
				ecode = v.ecode;
			

			editor.windowManager.open( {
				title: 'Cvents Shortcode Generator',
				body: [
					{
						type: 'textbox',
						name: 'etitle',
						label: 'Cvent Title',
						value: etitle,
						tooltip: 'Enter the title'
					},
					
					{
						type: 'textbox',
						multiline: true,
						name: 'ecode',
						label: 'Cvent Code',
						value: ecode,
						tooltip: 'Enter the code',
						minWidth: 350,
						minHeight: 150
						
					},
				
				],
				onsubmit: function( e ) {
					if (typeof e.data.etitle != 'undefined' && e.data.etitle.length < 1) {
						alert('Event title is required');
						return;	
					}

					if (typeof e.data.ecode != 'undefined' && e.data.ecode.length < 1) {
						alert('Event code is required');
						return;	
					}
					
				var shortcode_str = "[" + sh_tag + " etitle='" + e.data.etitle + " ' ecode= '" + e.data.ecode + " '][/" + sh_tag + "]";
					editor.insertContent( shortcode_str);
				}
			});
	      	});

		//add button
		editor.addButton('cvents', {
			//icon: 'squadup',
			classes: 'cvents-button', 
			tooltip: 'Create Your Cvents Shortcode',
			text: 'Cvents Shortcode Generator',
			onclick: function() {
				editor.execCommand('cvents_popup','',{
					etitle : '',
					ecode : '',
				});
			}
		});

		//open popup on placeholder double click
		// editor.on('DblClick',function(e) {
			// var cls  = e.target.className.indexOf('wp-cvents');
			// if ( e.target.nodeName == 'IMG' && e.target.className.indexOf('wp-cvents') > -1 ) {
				// var title = e.target.attributes['data-sh-attr'].value;
				// title = window.decodeURIComponent(title);
				// console.log(title);
				// var content = e.target.attributes['data-sh-content'].value;
				// editor.execCommand('cvents_popup','',{
					// etitle : getAttr(title,'etitle'),
					// ecode : getAttr(title,'ecode'),
				// });
			// }
		// });
	});
})();