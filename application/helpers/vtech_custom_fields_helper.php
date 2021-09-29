<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Function used for JS to render custom field hyperlink
 * @return stirng
 */
function get_custom_fields_hyperlink_js_function()
{
    ob_start();
?>
    <script>
        function custom_fields_hyperlink(){
         var cf_hyperlink = $('body').find('.cf-hyperlink');
         $.each(cf_hyperlink,function(){
            var cfh_wrapper = $(this);
            var cfh_field_to = cfh_wrapper.attr('data-fieldto');
            var cfh_field_id = cfh_wrapper.attr('data-field-id');
            var textEl = $('body').find('#custom_fields_'+cfh_field_to+'_'+cfh_field_id+'_popover');
            var hiddenField = $("#custom_fields\\\["+cfh_field_to+"\\\]\\\["+cfh_field_id+"\\\]");
            var cfh_value = cfh_wrapper.attr('data-value');
            hiddenField.val(cfh_value);
            if($(hiddenField.val()).html() != ''){
                textEl.html($(hiddenField.val()).html());
            }
            var cfh_field_name = cfh_wrapper.attr('data-field-name');
            textEl.popover({
                html: true,
                trigger: "manual",
                placement: "top",
                title:cfh_field_name,
                content:function(){
                    return $(cfh_popover_templates[cfh_field_id]).html();
                }
            }).on("click", function(e){
                var $popup = $(this);
                $popup.popover("toggle");
                var titleField = $("#custom_fields_"+cfh_field_to+"_"+cfh_field_id+"_title");
                var urlField = $("#custom_fields_"+cfh_field_to+"_"+cfh_field_id+"_link");
                var ttl = $(hiddenField.val()).html();
                var cfUrl = $(hiddenField.val()).attr("href");
                titleField.val(ttl);
                urlField.val(cfUrl);
                $("#custom_fields_"+cfh_field_to+"_"+cfh_field_id+"_btn-save").click(function(){
                    hiddenField.val((urlField.val() != '' ? '<a href="'+urlField.val()+'" target="_blank">' + titleField.val() + '</a>' : ''));
                    textEl.html(titleField.val() == "" ? cf_translate_input_link_tip : titleField.val());
                    $popup.popover("toggle");
                });
                $("#custom_fields_"+cfh_field_to+"_"+cfh_field_id+"_btn-cancel").click(function(){
                    if(urlField.val() == ''){
                        hiddenField.val('');
                    }
                    $popup.popover("toggle");
                });
            });
        });
     }
 </script>
 <?php
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}
