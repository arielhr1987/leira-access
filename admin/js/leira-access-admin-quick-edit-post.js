(function ($) {

    $(function () {
        /**
         * Create a copy of the WP inline edit post function
         * @type {inlineEditPost.edit}
         */
        var original_inline_edit = inlineEditPost.edit;

        /**
         * Overwrite the function with our own code
         * @param id
         * @return {boolean}
         */
        inlineEditPost.edit = function (id) {

            // "call" the original WP edit function
            // we don't want to leave WordPress hanging
            original_inline_edit.apply(this, arguments);

            // now we take care of our business

            // get the post ID
            var post_id = 0;
            if (typeof (id) == 'object') {
                post_id = parseInt(this.getId(id));
            }

            if (post_id > 0) {
                // define the edit row
                var edit_row = $('#edit-' + post_id);
                var post_row = $('#post-' + post_id);

                // get the data
                var tax_access = $('.inline-leira-access', post_row).text();
                tax_access = JSON.parse(tax_access);

                //uncheck all leira-access inputs
                $('.leira-access-controls input:radio', edit_row).prop("checked", false);
                $('.leira-access-controls input:checkbox', edit_row).prop("checked", false);

                if (tax_access === '') {
                    //everyone
                    $('input:radio[value=""]', edit_row).prop("checked", true);
                } else if (tax_access === 'out') {
                    //logged out
                    $('input:radio[value="out"]', edit_row).prop("checked", true);
                } else if (tax_access === 'in') {
                    //logged in
                    $('input:radio[value="in"]', edit_row).prop("checked", true);
                } else if (Array.isArray(tax_access)) {
                    //logged in with roles
                    $('input:radio[value="in"]', edit_row).prop("checked", true);
                    for (let i = 0; i < tax_access.length; i++) {
                        var role = tax_access[i];
                        $('input:checkbox[value="' + role + '"]', edit_row).prop("checked", true);
                    }
                }
            }

            return false;
        };

    });

})(jQuery);