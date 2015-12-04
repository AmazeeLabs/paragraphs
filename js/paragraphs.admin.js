(function ($) {
  /**
  * Paragraphs Drag&Drop functions
  * */
  Drupal.behaviors.paragraphs = {
   attach: function (context, settings) {
     paragraph_widget = '.field--widget-entity-reference-paragraphs'; /*Paragraph container*/

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
          $(paragraph_widget).not('.collapsed-items').find('.draggable').removeClass('collapsed');

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
