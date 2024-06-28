jQuery(document).ready(function($) {

    // Add an event listener to the default category meta box
    $('#categorychecklist').on('change', function() {

        // Retrieve all selected categories
        var selectedCategories = $('#categorychecklist input:checked');

        // Prepare an array to store the selected category IDs
        var categoryIDs = [];

        // Loop through all the selected categories and add their IDs to the array
        selectedCategories.each(function() {
            categoryIDs.push($(this).val());
        });

        // Make a REST API call to retrieve all the selected categories
        $.ajax({
            url: '/wp-json/wp/v2/categories?include=' + categoryIDs.join(','),
            type: 'GET',
            success: function(data) {

                // Populate your custom meta box with the retrieved categories
                var options = '';
                $.each(data, function(index, category) {
                    options += '<option value="' + category.id + '">' + category.name + '</option>';
                });
                $('#primary_category_meta_box select').html(options);
            }
        });

    });

});
