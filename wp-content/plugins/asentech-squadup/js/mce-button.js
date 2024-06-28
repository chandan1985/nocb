(function() {
	tinymce.PluginManager.add('squadup', function( editor, url ) {
		var sh_tag = 'squadup';

		//helper functions 
		function getAttr(s, n) {
			n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
			return n ?  window.decodeURIComponent(n[1]) : '';
		};
		//add popup
		editor.addCommand('squadup_popup', function(ui, v) {
			//setup defaults
			var stitle = '';
			if (v.stitle)
				stitle = v.stitle;

			var simage = '';
			if (v.simage)
				simage = v.simage;
			
			// var sroot = '';
			// if (v.sroot)
				// sroot = v.sroot;

			var suid = '';
			if (v.suid)
				suid = v.suid;

			var seid = '';
			if (v.seid)
				seid = v.seid;

			var semail = 'true';
			if (v.semail)
				semail = v.semail;

			var sbrand = '';
			if (v.sbrand)
				sbrand = v.sbrand;

			var sticket = 'true';
			if (v.sticket)
				sticket = v.sticket;

			var sdesc = 'true';
			if (v.sdesc)
				sdesc = v.sdesc;

			var scart = 'true';
			if (v.scart)
				scart = v.scart;


			editor.windowManager.open( {
				title: 'SquadUp Shortcode Generator',
				body: [
					{
						type: 'textbox',
						name: 'stitle',
						label: 'SquadUp Title',
						value: stitle,
						tooltip: 'Enter the title'
					},
					{
						type: 'textbox',
						name: 'simage',
						label: 'SquadUp Image URL',
						value: simage,
						tooltip: 'Enter the image URL'
					},

					// {
						// type: 'textbox',
						// name: 'sroot',
						// label: 'SquadUp Root *',
						// value: sroot,
						// tooltip: 'Enter the root value'
					// },

					{
						type: 'textbox',
						name: 'suid',
						label: 'SquadUp UserID *',
						value: suid,
						tooltip: 'Enter User ID'
					},

					{
						type: 'textbox',
						name: 'seid',
						label: 'SquadUp EventID *',
						value: seid,
						tooltip: 'Enter Event ID'
					},

					{
						type: 'listbox',
						name: 'semail',
						label: 'SquadUp Email',//saquib.rahmani@asentech.com
						value: semail,
						'values': [
							{text: 'True', value: 'true'},
							{text: 'False', value: 'false'}
						],
						tooltip: 'Select True or False'
					},
					// {
					// 	type: 'textbox',
					// 	name: 'content',
					// 	label: 'Panel Content',
					// 	value: content,
					// 	multiline: true,
					// 	minWidth: 300,
					// 	minHeight: 100
					// }

					{
						type: 'textbox',
						name: 'sbrand',
						label: 'Branding Position *',
						value: sbrand,
						tooltip: 'bottom'
					},

					{
						type: 'listbox',
						name: 'sticket',
						label: 'Insurance',
						value: sticket,
						'values': [
							{text: 'True', value: 'true'},
							{text: 'False', value: 'false'}
						],
						tooltip: 'Select True or False'
					},

					{
						type: 'listbox',
						name: 'sdesc',
						label: 'Enable Description',
						value: sdesc,
						'values': [
							{text: 'True', value: 'true'},
							{text: 'False', value: 'false'}
						],
						tooltip: 'Select True or False'
					},

					{
						type: 'listbox',
						name: 'scart',
						label: 'Enable Cart',
						value: scart,
						'values': [
							{text: 'True', value: 'true'},
							{text: 'False', value: 'false'}
						],
						tooltip: 'Select True or False'
					}
				],
				onsubmit: function( e ) {
					if (typeof e.data.suid != 'undefined' && e.data.suid.length < 1) {
						alert('UserID is required');
						return;	
					}

					if (typeof e.data.seid != 'undefined' && e.data.seid.length < 1) {
						alert('EventID is required');
						return;	
					}

					// if (typeof e.data.sroot != 'undefined' && e.data.sroot.length < 1) {
						// alert('Root is required');
						// return;	
					// }

					if (typeof e.data.sbranding != 'undefined' && e.data.sbranding.length < 1) {
						alert('Branding position is required');
						return;	
					}

						
					var shortcode_str = '[' + sh_tag + ' semail="'+e.data.semail+'"';
					//check for stitle
					if (typeof e.data.stitle != 'undefined' && e.data.stitle.length)
						shortcode_str += ' stitle="' + e.data.stitle + '"';
					//check for simage
					if (typeof e.data.simage != 'undefined' && e.data.simage.length)
						shortcode_str += ' simage="' + e.data.simage + '"';
					//check for sroot
					// if (typeof e.data.sroot != 'undefined' && e.data.sroot.length)
						// shortcode_str += ' sroot="' + e.data.sroot + '"';
					//check for suid
					if (typeof e.data.suid != 'undefined' && e.data.suid.length)
						shortcode_str += ' suid="' + e.data.suid + '"';
					//check for seid
					if (typeof e.data.seid != 'undefined' && e.data.seid.length)
						shortcode_str += ' seid="' + e.data.seid + '"';
					//check for sbrand
					if (typeof e.data.sbrand != 'undefined' && e.data.sbrand.length)
						shortcode_str += ' sbrand="' + e.data.sbrand + '"';
					//check for sticket
					if (typeof e.data.sticket != 'undefined' && e.data.sticket.length)
						shortcode_str += ' sticket="' + e.data.sticket + '"';
					//check for sdesc
					if (typeof e.data.sdesc != 'undefined' && e.data.sdesc.length)
						shortcode_str += ' sdesc="' + e.data.sdesc + '"';
					//check for scart
					if (typeof e.data.scart != 'undefined' && e.data.scart.length)
						shortcode_str += ' scart="' + e.data.scart + '"';

					//add panel content
					shortcode_str += '][/' + sh_tag + ']';
					//insert shortcode to tinymce
					editor.insertContent( shortcode_str);
				}
			});
	      	});

		//add button
		editor.addButton('squadup', {
			//icon: 'squadup',
			classes: 'squadup-button', 
			tooltip: 'Create Your SquadUp Shortcode',
			text: 'SquadUp Shortcode Generator',
			onclick: function() {
				editor.execCommand('squadup_popup','',{
					stitle : '',
					simage : '',
					// sroot : '',
					suid : '',
					seid : '',
					semail   : 'true',
					sbrand: '',
					sticket: 'false',
					sdesc: 'true',
					scart: 'true',
				});
			}
		});

		//open popup on placeholder double click
		editor.on('DblClick',function(e) {
			var cls  = e.target.className.indexOf('wp-squadup');
			if ( e.target.nodeName == 'IMG' && e.target.className.indexOf('wp-squadup') > -1 ) {
				var title = e.target.attributes['data-sh-attr'].value;
				title = window.decodeURIComponent(title);
				console.log(title);
				var content = e.target.attributes['data-sh-content'].value;
				editor.execCommand('squadup_popup','',{
					stitle : getAttr(title,'stitle'),
					simage : getAttr(title,'simage'),
					// sroot : getAttr(title,'sroot'),
					suid : getAttr(title,'suid'),
					seid : getAttr(title,'seid'),
					semail   : getAttr(title,'semail'),
					sbrand: getAttr(title,'sbrand'),
					sticket: getAttr(title,'sticket'),
					sdesc: getAttr(title,'sdesc'),
					scart: getAttr(title,'scart'),
				});
			}
		});
	});
})();