(function ($) {

    // we create a copy of the WP inline edit post function
    var $wp_inline_edit = inlineEditTax.edit;

    // and then we overwrite the function with our own code
    inlineEditTax.edit = function (id) {

        // "call" the original WP edit function
        // we don't want to leave WordPress hanging
        $wp_inline_edit.apply(this, arguments);

        // now we take care of our business

        // get the post ID
        var tax_id = 0;
        if (typeof (id) == 'object') {
            tax_id = parseInt(this.getId(id));
        }

        if (tax_id > 0) {
            // define the edit row
            var edit_row = $('#edit-' + tax_id);
            var tax_row = $('#tag-' + tax_id);

            // get the data
            var tax_access = $('.inline-leira-access', tax_row).text();
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
    };

    /**
     * Adds an event handler to the form submit on the term overview page.
     *
     * Cancels default event handling and event bubbling.
     */
    $(function () {
        $('#submit').click(function () {
            var form = $(this).parents('form');

            if (!validateForm(form))
                return false;

            /**
             * Empty values
             */
            $('input:radio', form).prop('checked', false);
            $('input:radio[value=""]', form).prop("checked", true);
            $('input:checkbox', form).prop('checked', false);

            return false;
        });
    })

})(jQuery);