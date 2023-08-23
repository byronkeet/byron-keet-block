jQuery(document).ready(function($) {
    $('#byron-keet-refresh').on('click', function() {
        $.ajax({
            url: byronKeet.ajax_url,
            method: 'POST',
            data: {
                action: 'byron_keet_refresh_miusage_data',
                nonce: byronKeet.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            }
        });
    });
});
