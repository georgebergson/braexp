(function( $ ) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

     var product_dependencies = $('#product_dependencies');

     var prodtitle = $('.product_dependencies_select_drop');
     var prodselect = $('#product_dependencies_select_drop');
     var prodimput = $('.ced_product_multi');

     var cattitle = $('.category_dependencies_select_drop');
     var catinput = $('.ced_category_multi');
     var catselect = $('#category_dependencies_select_drop');

      $(document).ready(function($) { 
     //    cattitle.hide(); 
     //    $('.ced_category_multi').css('display','none');
     //    catselect.hide();
      });

    $(document).ready(function($) {
     $(".product_dependencies_select_drop_ele").select2({
          //dropdownCssClass: "ced_product_multi"
    });

     $(".category_dependencies_select_drop_ele").select2({
           //containerCssClass: "ced_category_multi"
         });
    });


})( jQuery );
