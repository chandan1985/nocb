<script type="text/javascript">
function set_state(a,b,c) {
	if(c == true){
		document.getElementById(a).style.display = 'none';
		document.getElementById(b).style.display = 'none';
	}
	else{
		document.getElementById(a).style.display = 'block';
		document.getElementById(b).style.display = 'none';
	}
}
function set_more(a) {
	if(document.getElementById(a).style.display!='block')
		document.getElementById(a).style.display = 'block';
	else
		document.getElementById(a).style.display = 'none';
}
/**
 * Create select boxes with categories that go with selected blog.
 *
 * @param object Element and instance_id of target for categories
 */
function load_blog_categories( elem, instance_id ) {
	// define data object
	var data = {
		action: 'post_feed_get_cats',
		blog_ID: elem.value,
		select_ID: elem.id,
		_ajax_nonce: '<?php echo wp_create_nonce( 'widget_feed_posts' ); ?>' 
	};
	// Get categories with this blog ID
	jQuery.post('admin-ajax.php', data, function(data) { populate_category_dropdowns( instance_id, data ) }, 'json');	
}

function populate_category_dropdowns( instance_id, data ) {
	cat_count = 0;
	
	/////// Find out how many categories are available
	for (var id in data)
		cat_count++; 
	
	///////// Container to drop in return json								
 	cat = document.getElementById(instance_id);
 	
	///////// Stop processing if no categories were returned
	if (cat_count == 0) {
		alert( 'No categories with posts for this blog. Please select another blog.' );
		return false;
	}
	///////// Build options from data and then add to select box
	//clear select box
	cat.length = 0;
		
	for (var id in data) {
		var oOption = document.createElement("OPTION");
		oOption.text=data[id]; //// + "---" + id;
		oOption.value=id;
		cat.options.add(oOption);
	}
	//add empty one 
	var oOption = document.createElement("OPTION");
	oOption.text="[None]";
	oOption.value=-1;
	cat.options.add(oOption);					
}

function load_blog_parent_categories( elem, instance_id ) {
	// define data object
	var data = {
		action: 'post_feed_get_parent_cats',
		blog_ID: elem.value,
		select_ID: elem.id,
		_ajax_nonce: '<?php echo wp_create_nonce( 'widget_feed_posts' ); ?>' 
	};
	// Get categories with this blog ID
	jQuery.post('admin-ajax.php', data, function(data) { populate_parent_category_dropdowns( instance_id, data ) }, 'json');	
}

function populate_parent_category_dropdowns( instance_id, data ) {
	cat_count = 0;
	
	/////// Find out how many categories are available
	for (var id in data)
		cat_count++; 
	
	///////// Container to drop in return json								
 	cat = document.getElementById(instance_id);
 	
	///////// Stop processing if no categories were returned
	if (cat_count == 0) {
		alert( 'No parent categories with posts for this blog. Please select another blog.' );
		return false;
	}
	///////// Build options from data and then add to select box
	//clear select box
	cat.length = 0;
		
	//add empty one 
	var oOption = document.createElement("OPTION");
	oOption.text="[None]";
	oOption.value="";
	cat.options.add(oOption);					

	for (var id in data) {
		var oOption = document.createElement("OPTION");
		oOption.text=data[id]; //// + "---" + id;
		oOption.value=id;
		cat.options.add(oOption);
	}
}
</script>