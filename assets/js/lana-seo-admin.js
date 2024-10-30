jQuery(function () {

    /**
     * Lana tags
     * select2
     */
    var $lanaSeoTags = jQuery('#lana-seo-metabox').find('select[data-select-type="select2"]');

    $lanaSeoTags.select2({
        theme: 'lana-seo',
        tags: true,
        tokenSeparators: [',', ';']
    });

    /**
     * Lana tags in term
     * select2
     */
    var $lanaSeoTagsInTerm = jQuery('#edittag').find('select[data-select-type="select2"]');

    $lanaSeoTagsInTerm.select2({
        theme: 'lana-seo',
        tags: true,
        tokenSeparators: [',']
    });

    /**
     * Lana
     * character
     */
    jQuery('textarea#lana-seo-meta-description').keyup(function () {

        var textareaLength = parseInt(jQuery(this).val().length);
        var maxLength = parseInt(jQuery(this).attr('maxlength'));

        var remainingLength = maxLength - textareaLength;

        jQuery('#lana-seo-meta-description-chars').text(remainingLength);
    });

    /**
     * Lana
     * image upload
     */
    jQuery('body').find('input[type="button"].lana-seo-og-image-upload').on('click', function (e) {
        e.preventDefault();

        var $imageRow = jQuery(this).closest('tr.image-row'),
            $imagePreview = $imageRow.find('.lana-seo-og-image-preview'),
            $imageUrl = $imageRow.find('input#lana-seo-og-image');

        var wpMediaImageUploadFrame;

        /** wp media frame */
        wpMediaImageUploadFrame = wp.media({
            title: lana_seo_l10n['select_image'],
            library: {
                type: 'image'
            },
            multiple: false,
            state: 'library',
            editing: false
        });

        /** when close frame */
        wpMediaImageUploadFrame.on('close', function () {
            wpMediaImageUploadFrame.detach();
        });

        /**
         * select attachment
         * add attachment to image preview
         */
        wpMediaImageUploadFrame.on('select', function () {
            var selection = wpMediaImageUploadFrame.state().get('selection');

            if (selection) {
                selection.each(function (attachment, i) {
                    $imagePreview.attr('src', attachment.attributes.url);
                    $imageUrl.val(attachment.attributes.url);
                });
            }
            wpMediaImageUploadFrame.close();
        });

        /** open() */
        wpMediaImageUploadFrame.open();
    });
});