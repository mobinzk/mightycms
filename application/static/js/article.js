// ======== URL
var URL = {
  create: function($content) {
    
    $.ajax({
      url: '/mightycms/articles/url',
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
    URL.create($content);
});
// ======== URL