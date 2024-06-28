				window.onload = function() {
				if (document.getElementById("post_author_override")) {
				document.getElementById("post_author_override").onchange = function() {
				 var optionValue = document.getElementById('post_author_override');
				  
				 newswire_option_handle();
				 optionValue.onchange = (event) => { newswire_option_handle(); }
				 
				 function newswire_option_handle() {

			  			 var inputText = getSelectedText('post_author_override');

					     var res = inputText.match(/associated press|bloomberg/ig);
						 
					     if (res) {

					        	if (document.getElementById("we_own_it")) {
				 					var options= document.getElementById('we_own_it').options;
				 					var option_length = options.length;
									for (var i= 0; i < option_length; i++) {
									    if (options[i].value==='No') {
									        options[i].selected= true;
									        break;
									    }
									}
				 				}

				 				if (document.getElementById("acf-field-we_own_it")) { 
				 					var options= document.getElementById('acf-field-we_own_it').options;
									var option_length = options.length;
									for (var i= 0; i<option_length; i++) {
									    if (options[i].value==='No') {
									        options[i].selected= true;
									        break;
									    }
									}
				 				}
					      }

					    //else {

					    // if (document.getElementById("we_own_it")) {
		 				// 	var options= document.getElementById('we_own_it').options;
		 				// 	var option_length = options.length;
							// for (var i= 0; i < option_length; i++) {
							//     if (options[i].value==='Yes') {
							//         options[i].selected= true;
							//         break;
							//     }
							// }
		 				// }
						
		 				// if (document.getElementById("acf-field-we_own_it")) { 
		 				// 	var options= document.getElementById('acf-field-we_own_it').options;
							// var option_length = options.length;
							// for (var i= 0; i<option_length; i++) {
							//     if (options[i].value==='Yes') {
							//         options[i].selected= true;
							//         break;
							//     }
							//   }
		 				//    }
					    //  }

				 } //function end here
			 };
			 }// if end here
		  }; // onload end here



			function getSelectedText(elementId) {
			    var elt = document.getElementById(elementId);

			    if (elt.selectedIndex == -1)
			        return null;

			    return elt.options[elt.selectedIndex].text;
			}