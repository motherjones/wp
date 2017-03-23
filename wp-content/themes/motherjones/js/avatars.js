(function() {
    var $ = jQuery;

    $(document).ready(function() {
        var avatar_input = $('#avatar-input'),
            avatar_remove = $('#remove-avatar'),
            avatar_display = $('#avatar-display'),
            ajax_opts = {
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'remove_avatar'
                },
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        avatar_input.show();
                        avatar_display.empty();
                    }
                }
            };

        avatar_remove.click(function() {
            if (location.search.search(/user_id/gi) > -1) {
                var matches = location.search.match(/user_id=(\d+)/);
                if (matches.length > 1) {
                    ajax_opts.data.user_id = matches[1];
                }
            }
            $.ajax(ajax_opts);
            return false;
        });

        avatar_input.find('input').change(function() {
            avatar_display.empty();
            avatar_display.html('<p>Click "Update Profile" to save your avatar.</p>');
        });
    });
}());
