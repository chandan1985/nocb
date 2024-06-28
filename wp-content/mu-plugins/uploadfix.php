<?php
add_filter('upload_dir', 'dmc_fix_broken_ms_upload_bug');
function dmc_fix_broken_ms_upload_bug($in) {
	$dir = get_option('upload_path');
	$url = get_option('upload_url_path');

	$in['basedir'] = $dir;
	$in['baseurl'] = $url;
        $dir .= $in['subdir'];
        $url .= $in['subdir'];
	$in['path'] = $dir;
	$in['url'] = $url;

	return $in;
}

?>
