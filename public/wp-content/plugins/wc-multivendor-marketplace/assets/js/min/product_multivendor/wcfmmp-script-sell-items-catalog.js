$product_type="",$product_cat="",$product_taxonomy={},$product_vendor="",jQuery(document).ready(function(e){$wcfm_sell_items_catalog_table=e("#wcfm-sell_items_catalog").DataTable({processing:!0,serverSide:!0,responsive:!0,pageLength:parseInt(dataTables_config.pageLength),language:e.parseJSON(dataTables_language),columns:[{responsivePriority:1},{responsivePriority:1},{responsivePriority:1},{responsivePriority:2},{responsivePriority:4},{responsivePriority:5},{responsivePriority:6},{responsivePriority:1}],columnDefs:[{targets:0,orderable:!1},{targets:1,orderable:!1},{targets:2,orderable:!1},{targets:3,orderable:!0},{targets:4,orderable:!1},{targets:5,orderable:!1},{targets:6,orderable:!1},{targets:7,orderable:!1}],ajax:{type:"POST",url:wcfm_params.ajax_url,data:function(e){e.action="wcfm_ajax_controller",e.controller="wcfm-sell-items-catalog",e.product_type=$product_type,e.product_cat=$product_cat,e.product_taxonomy=$product_taxonomy,e.wcfm_ajax_nonce=wcfm_params.wcfm_ajax_nonce},complete:function(){initiateTip(),"undefined"!=typeof intiateWCFMuQuickEdit&&e.isFunction(intiateWCFMuQuickEdit)&&intiateWCFMuQuickEdit(),e(document.body).trigger("updated_wcfm-sell_items_catalog")}}}),e("#dropdown_product_type").length>0&&e("#dropdown_product_type").on("change",function(){$product_type=e("#dropdown_product_type").val(),$wcfm_sell_items_catalog_table.ajax.reload()}),e("#dropdown_product_cat").length>0&&e("#dropdown_product_cat").on("change",function(){$product_cat=e("#dropdown_product_cat").val(),$wcfm_sell_items_catalog_table.ajax.reload()}).select2($wcfm_taxonomy_select_args),e(".dropdown_product_custom_taxonomy").length>0&&e(".dropdown_product_custom_taxonomy").each(function(){e(this).on("change",function(){$product_taxonomy[e(this).data("taxonomy")]=e(this).val(),$wcfm_sell_items_catalog_table.ajax.reload()}).select2()}),e(document.body).on("updated_wcfm-sell_items_catalog",function(){e(".wcfm_sell_this_item").each(function(){e(this).click(function(t){return t.preventDefault(),confirm(wcfm_dashboard_messages.sell_this_item_confirm)&&function(e){jQuery("#wcfm-sell_items_catalog_wrapper").block({message:null,overlayCSS:{background:"#fff",opacity:.6}});var t={action:"wcfmmp_product_multivendor_clone",product_id:e.data("proid"),wcfm_ajax_nonce:wcfm_params.wcfm_ajax_nonce};jQuery.ajax({type:"POST",url:wcfm_params.ajax_url,data:t,success:function(e){$wcfm_sell_items_catalog_table&&$wcfm_sell_items_catalog_table.ajax.reload(),jQuery("#wcfm-sell_items_catalog_wrapper").unblock()}})}(e(this)),!1})})}),e(".bulk_action_checkbox_all").click(function(){e(this).is(":checked")?(e(".bulk_action_checkbox_all").attr("checked",!0),e(".bulk_action_checkbox_single").attr("checked",!0)):(e(".bulk_action_checkbox_all").attr("checked",!1),e(".bulk_action_checkbox_single").attr("checked",!1))}),e("#wcfm_bulk_add_to_my_store, #wcfm_bulk_add_to_my_store_bottom").click(function(t){if(t.preventDefault(),$selected_products=[],e(".bulk_action_checkbox_single").each(function(){e(this).is(":checked")&&$selected_products.push(e(this).val())}),0===$selected_products.length)return alert(wcfm_dashboard_messages.bulk_no_itm_selected),!1;jQuery("#wcfm-sell_items_catalog_wrapper").block({message:null,overlayCSS:{background:"#fff",opacity:.6}});var a={action:"wcfmmp_product_multivendor_bulk_clone",product_ids:$selected_products,wcfm_ajax_nonce:wcfm_params.wcfm_ajax_nonce};return jQuery.ajax({type:"POST",url:wcfm_params.ajax_url,data:a,success:function(e){$wcfm_sell_items_catalog_table&&$wcfm_sell_items_catalog_table.ajax.reload(),jQuery("#wcfm-sell_items_catalog_wrapper").unblock()}}),!1}),e(".wcfm_filters_wrap").length>0&&(e(".dataTable").before(e(".wcfm_filters_wrap")),e(".wcfm_filters_wrap").css("display","inline-block")),e(document.body).on("updated_wcfm-sell_items_catalog",function(){e.each(wcfm_sell_items_catalog_screen_manage,function(e,t){$wcfm_sell_items_catalog_table.column(e).visible(!1)})})});