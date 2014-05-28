// ======== Sortable
$(function(){
    // Sortable
    $('ul.pages, ul.pages ul').sortable({
        handle: '.move',
        placeholder: 'uim-state-highlight',
        forcePlaceholderSize: true,
        start: function(event, ui){
           
        },
        stop: function(event, ui){

           $.ajax({
                url: '/mightycms/pages/sort',
                method: 'POST',
                data: {
                    ids: $(this).sortable('toArray')
                }
           });

        }
    });
});
// ======== Sortable

// ======== URL
var URL = {
  create: function($content) {
    
    $.ajax({
      url: '/mightycms/pages/url',
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