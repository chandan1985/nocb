jQuery(document).ready(function () {
	jQuery('#upload_logo').click(function () {
		post_id = 1;
		tb_show('', 'media-upload.php?post_id=' + post_id + '&type=image&TB_iframe=true');
		return false;
	});
	window.send_to_editor = function (html) {
		imgurl = jQuery('img', html).attr('src');
		jQuery('#logo_data').val(imgurl);
		tb_remove();
	}
	jQuery('#upload_parent_logo').click(function () {
		post_id = 1;
		window.send_to_editor = window.send_parent_logo_to_editor_clone;
		tb_show('', 'media-upload.php?post_id=' + post_id + '&type=image&TB_iframe=true');
		return false;
	});
	window.send_parent_logo_to_editor_clone = function (html) {
		imgurl = jQuery('img', html).attr('src');
		jQuery('#parent_logo_data').val(imgurl);
		tb_remove();
	}
	/* Footer Logo Upload Fields */
	jQuery('#upload_logo1').click(function () {
		post_id = 1;
		window.send_to_editor = window.send_to_editor_clone1;
		tb_show('', 'media-upload.php?post_id=' + post_id + '&type=image&TB_iframe=true');
		return false;
	});
	window.send_to_editor_clone1 = function (html) {
		imgurl = jQuery('img', html).attr('src');
		jQuery('#footer_logo1').val(imgurl);
		tb_remove();
	}

	jQuery('#upload_logo2').click(function () {
		post_id = 1;
		window.send_to_editor = window.send_to_editor_clone2;
		tb_show('', 'media-upload.php?post_id=' + post_id + '&type=image&TB_iframe=true');
		return false;
	});
	window.send_to_editor_clone2 = function (html) {
		imgurl = jQuery('img', html).attr('src');
		jQuery('#footer_logo2').val(imgurl);
		tb_remove();
	}

	jQuery('#upload_logo3').click(function () {
		post_id = 1;
		window.send_to_editor = window.send_to_editor_clone3;
		tb_show('', 'media-upload.php?post_id=' + post_id + '&type=image&TB_iframe=true');
		return false;
	});
	window.send_to_editor_clone3 = function (html) {
		imgurl = jQuery('img', html).attr('src');
		jQuery('#footer_logo3').val(imgurl);
		tb_remove();
	}

	jQuery('#upload_logo4').click(function () {
		post_id = 1;
		window.send_to_editor = window.send_to_editor_clone4;
		tb_show('', 'media-upload.php?post_id=' + post_id + '&type=image&TB_iframe=true');
		return false;
	});
	window.send_to_editor_clone4 = function (html) {
		imgurl = jQuery('img', html).attr('src');
		jQuery('#footer_logo4').val(imgurl);
		tb_remove();
	}

	jQuery('#upload_logo5').click(function () {
		post_id = 1;
		window.send_to_editor = window.send_to_editor_clone5;
		tb_show('', 'media-upload.php?post_id=' + post_id + '&type=image&TB_iframe=true');
		return false;
	});
	window.send_to_editor_clone5 = function (html) {
		imgurl = jQuery('img', html).attr('src');
		jQuery('#footer_logo5').val(imgurl);
		tb_remove();
	}

	jQuery('#upload_logo6').click(function () {
		post_id = 1;
		window.send_to_editor = window.send_to_editor_clone6;
		tb_show('', 'media-upload.php?post_id=' + post_id + '&type=image&TB_iframe=true');
		return false;
	});
	window.send_to_editor_clone6 = function (html) {
		imgurl = jQuery('img', html).attr('src');
		jQuery('#footer_logo6').val(imgurl);
		tb_remove();
	}
});