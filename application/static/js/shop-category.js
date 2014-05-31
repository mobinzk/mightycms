// ======== URL
var URL = {
  create: function($content) {
    
    $.ajax({
      url: '/mightycms/shop/urlCategory',
      type: 'post',
      data: 'content=' + $content,
      dataType: 'json',

      success: function(data){
        console.log(data);

        $this   = $('#url');
        $mainID = $('input[name="id"]').val();
        $this.val(data.url);
        if(data.result === false || $mainID === data.id) {
          $this.removeClass('error');
        } else {
          console.log('error');
          $this.addClass('error');
        }
      }
    });

  }
};

$('#name').keyup(function(e) {
    $content = $(this).val();
    URL.create(escape($content));
});
// ======== URL

// ======== Sortable
$(function(){
    var fixHelper = function(e, ui) {
      ui.children().each(function() {
        $(this).width($(this).width());
      });
      return ui;
    };

    // Sortable
    $('.uim-table tbody').sortable({
        handle: '.move',
        placeholder: 'uim-state-highlight',
        forcePlaceholderSize: true,
        helper: fixHelper,
        // connectWith: "ul",
        start: function(event, ui){
           
        },
        stop: function(event, ui){
          console.log($(this).sortable('toArray'));

           $.ajax({
                url: '/mightycms/shop/sort-categories',
                method: 'POST',
                data: {
                    ids: $(this).sortable('toArray')
                }
           });

        }
    }).disableSelection();
});
// ======== Sortable