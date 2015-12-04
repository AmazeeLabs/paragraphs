(function ($) {
  /**
  * Paragraphs Drag&Drop functions
  * */
  Drupal.behaviors.paragraphs = {
   attach: function (context, settings) {
     paragraph_widget = '.field--widget-entity-reference-paragraphs'; /*Paragraph container*/

    /*Set toggles*/
     $(paragraph_widget + ' .field-multiple-table .field-label', context).each(function(){
       if (!$(this).find('.paragraph-toggle').length) {
         $(this).append('<a class="paragraph-toggle">Collapse</a>');
       }
     });

     /*Set class to outer paragraph (if there are sub paragraphs present)*/
     $(paragraph_widget).addClass('sub-paragraph-block');
     $(paragraph_widget).first().removeClass('sub-paragraph-block').addClass('main-paragraph-block');

     /*Set paragraph titles (Amazee specific)*/
     $(paragraph_widget + ' tr', context).each(function(e){
      //  if ( !$(this).parents('.sub-paragraph-block').length ) {
      //    console.log ( $('.field--name-admin-title input', this).val() );
      //  }
       if ( !$('.paragraph-type-title', this).hasClass('added_title')) {
         value = $('.field--name-admin-title input', this).length ? '<b>' + $('.field--name-admin-title input', this).val() + '</b> (' : '';
         $('.paragraph-type-title', this).prepend(value);
         $('.paragraph-type-title', this).append(')');
         $('.paragraph-type-title', this).addClass('added_title');
       }
     });

     /*Check if user changes title and live update it*/
     $('.field--name-admin-title input', context).each(function(e){
       $(this).keyup(function() {
         $(this).closest('tr').find('.paragraph-type-top .paragraph-type-title b').html($(this).val());
       });
     });

     /*Toggle function*/
     $(paragraph_widget +' .paragraph-toggle', context).each(function(e){
       $(this).click(function(e){
         e.preventDefault();
         id = '#' + $(this).parents('table').attr('id');
         draggable = id + ' > tbody > tr.draggable';
         $(paragraph_widget).toggleClass('collapsed-items');
         $(draggable).toggleClass('collapsed');
         if ( $(this).text() == 'Collapse' ) {
          $(this).text('Expand');
         } else {
           $(this).text('Collapse');
         }
       });
     });

     /*Check if user is dragging*/
     $(paragraph_widget + ' .draggable .tabledrag-handle', context).mousedown(function(){
       id = '#' + $(this).parents('table').attr('id');
       draggable = paragraph_widget + ' ' + id + ' > tbody > tr.draggable';
       $(draggable).addClass('collapsed');

       /*Add id to current paragraph to scroll to it at the end*/
       if ($(draggable + '.drag').attr('id')) {
          $(draggable + '.drag').removeAttr('id');
        } else {
          $(draggable + '.drag').attr('id', 'edit-paragraph');
        }

        $('html, body').animate({
          scrollTop: $("#edit-paragraph").offset().top - 500
        }, 0);
     });

     /*Check if user is finished dragging*/
     if( $(paragraph_widget + ' .draggable').length ) {
       $('body', context).mouseup(function(){
         if(!$(event.target).closest('.paragraph-toggle').length) {
            $(paragraph_widget).not('.collapsed-items').find('.draggable').removeClass('collapsed');
          }

          /*Scroll to*/
         if( $('#edit-paragraph', context).length ) {
           $('html, body').animate({
             scrollTop: $("#edit-paragraph").offset().top - 100
           }, 0);
           $(paragraph_widget + ' .draggable').removeAttr('id');
         }
       });
     }
   }
  };
})(jQuery);
