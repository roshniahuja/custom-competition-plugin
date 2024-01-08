jQuery(document).ready(function ($) {
    $('#submit-entry-form').submit(function (event) {
        // Prevent the default form submission
        event.preventDefault();
        // Serialize the form data
        var formData = $(this).serialize();
        // Add a nonce to the data
        formData += '&nonce=' + custom_competition_plugin_ajax.nonce;

        // Send the AJAX request
        $.ajax({
            type: 'POST',
            url: custom_competition_plugin_ajax.ajax_url,
            data: formData + '&action=handle_entry_submission',
            success: function (response) {
                // Parse the JSON response
                var data = $.parseJSON(response);

                // Check the status and display a message
                if (data.status === 'success') {
                    $('#submit-entry-form').html('<p>Entry submitted successfully!</p>');
                    // Delay the redirection by 2 seconds
                    setTimeout(function () {
                        window.location.href = '/competition-list/';
                    }, 2000);
                } else {
                    console.log('Error: ' + data.message);
                }
            },
            error: function () {
                console.log('AJAX request failed.');
            }
        });
    });
});