jQuery(function ($) {
    window.original_send_to_editor = window.send_to_editor;
    var wpstoryboardgalleries = {
        admin_thumb_ul: '',
        init: function () {
            this.admin_thumb_ul = $('#wpstoryboardgalleries_thumbs');

            this.admin_thumb_ul.sortable({
                placeholder: 'wpstoryboardgalleries_placeholder',
                handle: 'img',
                start: function (event, ui) {
                    $(ui.placeholder).height($(ui.item[0]).height());
                },
                change: function(event, ui) {
                    $(ui.placeholder).height($(ui.item[0]).height());
                }
            }).on('click', '.wpstoryboardgalleries_remove', function () {
                if (confirm('Are you sure you want to delete this?')) {
                    $(this).parent().remove();
                }
                return false;
            }).on('click', '.wpstoryboardgalleries_link_panel', function () {
                var $obj = $(this);
                var val = $obj.is(':checked');
                var linktext = $obj.parent().parent().find('.linkonlyinput');
                if (val) {
                    linktext.show();
                } else {
                    linktext.hide();
                    linktext.val('');
                }
            });
            
            $('#wpstoryboardgalleries_delete_all_button').on('click', function () {
                if (confirm('Are you sure you want to delete all the images in the gallery?')) {
                    wpstoryboardgalleries.admin_thumb_ul.empty();
                }
                return false;
            });
            $('#wpstoryboardgalleries_upload_button').on('click', function () {
                window.send_to_editor = function(html) {
                    var imageid = $(html).find('img').attr('class').match(/wp\-image\-([0-9]+)/)[1];
                    wpstoryboardgalleries.get_thumbnail(imageid);
                    tb_remove();
                    window.send_to_editor = window.original_send_to_editor;
                }
                var title = 'Select Image';
                tb_show( title, 'media-upload.php?post_id=' + POST_ID + '&amp;type=image&amp;TB_iframe=1' );
                return false;
            });
            
            $('#wpstoryboardgalleries_add_attachments_button').on('click', function() {
                var included = [];
                $('#wpstoryboardgalleries_thumbs input[type=hidden]').each(function (i, e) {
                    included.push($(this).val());
                });
                wpstoryboardgalleries.get_all_thumbnails(POST_ID, included);
            });
            this.autocomplete();
        },
        autocomplete: function (ele) {
            var ele = $('.wpstoryboardgalleries_link');
            ele.autocomplete('destroy');
            ele.autocomplete({
                source: function (request, callback) {
                    var data = {
                        action: 'wpstoryboardgalleries_get_pages',
                        term: request.term
                    };
                    jQuery.post(ajaxurl, data, function(response) {
                        callback(response);
                    });
                },
                minLength: 2
            });
        },
        get_thumbnail: function (id) {
            var data = {
                action: 'wpstoryboardgalleries_get_thumbnail',
                imageid: id
            };
            jQuery.post(ajaxurl, data, function(response) {
                var ele = $(response);
                wpstoryboardgalleries.admin_thumb_ul.append(ele);
                wpstoryboardgalleries.autocomplete();
            });
        },
        get_all_thumbnails: function (post_id, included) {
            var data = {
                action: 'wpstoryboardgalleries_get_all_thumbnail',
                post_id: post_id,
                included: included
            };
            $.post(ajaxurl, data, function(response) {
                wpstoryboardgalleries.admin_thumb_ul.append(response);
                wpstoryboardgalleries.autocomplete();
            });
        }
    };
    wpstoryboardgalleries.init();
});