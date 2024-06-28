jQuery(document).ready(function ($) {
    var customUploaderHeader;

    $('#upload_logo_button').on('click', function (e) {
        e.preventDefault();

        // If the uploader object already exists, open it
        if (customUploaderHeader) {
            customUploaderHeader.open();
            return;
        }

        // Create the media uploader
        customUploaderHeader = wp.media({
            title: 'Choose Site Logo',
            button: {
                text: 'Select Logo'
            },
            multiple: false
        });

        // When a file is selected or uploaded, set the value of the
        customUploaderHeader.on('select', function () {
            var attachment1 = customUploaderHeader.state().get('selection').first().toJSON();
            $('#site_logo').val(attachment1.url);
            $('#logo_preview').html('<img src="' + attachment1.url + '" style="max-width: 200px; height: auto;" />');
        });

        // Open the media uploader
        customUploaderHeader.open();
    });


    var customUploaderHeader2;
    $('#upload_search_icon_button').on('click', function (e) {
        e.preventDefault();

        // If the uploader object already exists, open it
        if (customUploaderHeader2) {
            customUploaderHeader2.open();
            return;
        }

        // Create the media uploader
        customUploaderHeader2 = wp.media({
            title: 'Choose Search icon',
            button: {
                text: 'Search icon'
            },
            multiple: false
        });

        // When a file is selected or uploaded, set the value of the
        customUploaderHeader2.on('select', function () {
            var attachment11 = customUploaderHeader2.state().get('selection').first().toJSON();
            $('#search_icon').val(attachment11.url);
            $('#search_icon_preview').html('<img src="' + attachment11.url + '" style="max-width: 23px; height: auto;" />');
        });

        // Open the media uploader
        customUploaderHeader2.open();
    });


    var customUploaderHeader1;
    $('#upload_btm_logo').on('click', function (e) {
        e.preventDefault();

        // If the uploader object already exists, open it
        if (customUploaderHeader1) {
            customUploaderHeader1.open();
            return;
        }

        // Create the media uploader
        customUploaderHeader1 = wp.media({
            title: 'Choose BTM Logo',
            button: {
                text: 'Select BTM Logo'
            },
            multiple: false
        });

        // When a file is selected or uploaded, set the value of the
        customUploaderHeader1.on('select', function () {
            var attachment2 = customUploaderHeader1.state().get('selection').first().toJSON();
            $('#btm_logo').val(attachment2.url);
            $('#btm_logo_preview').html('<img src="' + attachment2.url + '" style="max-width: 200px; height: auto;" />');
        });

        // Open the media uploader
        customUploaderHeader1.open();
    });

    // ==============================================================================================================================================

    var customUploader;

    $('#upload_logo_button').on('click', function (e) {
        e.preventDefault();

        // If the uploader object already exists, open it
        if (customUploader) {
            customUploader.open();
            return;
        }

        // Create the media uploader
        customUploader = wp.media({
            title: 'Choose Site Logo',
            button: {
                text: 'Select Logo'
            },
            multiple: false
        });

        // When a file is selected or uploaded, set the value of the
        customUploader.on('select', function () {
            var attachment3 = customUploader.state().get('selection').first().toJSON();
            $('#footer_site_logo').val(attachment3.url);
            $('#logo_preview').html('<img src="' + attachment3.url + '" style="max-width: 200px; height: auto;" />');
        });

        // Open the media uploader
        customUploader.open();
    });

    var customUploader1;
    $('#upload_btm_logo_button').on('click', function(e) {
        e.preventDefault();

        // If the uploader object already exists, open it
        if (customUploader1) {
            customUploader1.open();
            return;
        }

        // Create the media uploader
        customUploader1 = wp.media({
            title: 'Choose Footer BTM Logo',
            button: {
                text: 'Select Footer BTM Logo'
            },
            multiple: false
        });

        // When a file is selected or uploaded, set the value of the
        customUploader1.on('select', function() {
            var attachment4 = customUploader1.state().get('selection').first().toJSON();
            $('#footer_btm_logo').val(attachment4.url);
            $('#footer_btm_logo_preview').html('<img src="' + attachment4.url + '" style="max-width: 200px; height: auto;" />');
        });

        // Open the media uploader
        customUploader1.open();
    });

});