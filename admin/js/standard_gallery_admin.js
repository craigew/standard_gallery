jQuery(document).ready(function () {
    if (jQuery("#addGalleryImage").length) {
        jQuery('#addGalleryImage').click(function () {
            tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true&amp;flash=0&amp;post_id=34');
            return false;
        });

        window.send_to_editor = function (html) {
            imgurl = jQuery('img', html).attr('src');
            img_class = jQuery('img', html).attr('class');
            jQuery("#attachment_id").val(parseInt(img_class.replace(/\D/g, ''), 10));
            tb_remove();
            jQuery("#gallery_detail").submit();
        }
    }
});
