// ======== Preview Image
var ImagePreview = {
    show: function($input, $this){
      
        var reader = new FileReader();
        reader.onload = function (e) {

            var image = new Image();
            image.src = e.target.result;
            image.onload = function() {
                $li = $this.parent();
                $preview = $li.find('.existing_file');
                $imgWidth = this.width;
                $imgHeight = this.height;
                $widthExist = $li.attr('width');
                $heightExist = $li.attr('height');

                $preview.css('background-image', 'url(' + this.src + ')' );
                $preview.css('background-color', '#fff');
                if(!$widthExist){
                    $preview.css('width', $imgWidth);
                }
                if(!$heightExist){
                    $preview.css('min-height', $imgHeight);
                }
            };

        };
          
        reader.readAsDataURL($input);

    }
};

$('html').on('change','input[type="file"]',function(){
    var $this  = $(this);
    var $input = this.files;

    if ($input && $input[0]) {
        $existing_file = $this.parent().find('.existing_file');
        ImagePreview.show($input[0],$this);

        // Add cancel/remove button 
        $remove = $this.parent().find('.remove');
        if($remove.length === 0) {
            $existing_file.after('<span class="uim-button red remove delete">Remove</span>');
        }

        if($existing_file.find('input').length !== 0) {
            $existing_file.unwrap();
            $existing_file.find('input').remove();
        }
    }

});

$('.add-new-file').on('click', function(e){
    e.preventDefault();
    var $this = $(this);
    var $parent = $this.parent().parent().parent();
    var name = $parent.find('ul').attr('data-field');
    var count = $parent.find('.file').length + 1;

    $('<li><input class="file" type="file" name="' + name + '_file_upload_item_' + count + '" /><span class="uim-button red remove delete">remove</span></li>').insertAfter($parent.find('li:last'));

});

$('html').on('click', '.uim_file_list .remove', function(){
    var $this = $(this);
    var $parent = $this.parent();

    $existing_file = $parent.find('.existing_file');
    $existing_file.css('background-image', '');
    $existing_file.css('background-color', '#f9f9f9');
    $parent.find('.file').val('');

    if($existing_file.find('input').length !== 0) {
        $existing_file.unwrap();
        $existing_file.find('input').remove();
    }

    $this.remove();
});
// ======== Preview Image


// Validate template inputs
$('form.validate').on('click', 'button[type="submit"]', function(e){

    e.preventDefault();
    var $form = $(this).parents('form').last();

    // check URL error
    if( $($form).find('.error').size() < 1 ) {
    } else {
        alert('The URL is already exist, please choose a new one.');
        $form.find('.error:first').focus();
        return false;
    }

    $($form).find('input[is_required="true"], textarea[is_required="true"]').each(function(e, input){
        input = $(input);
        if (input.val() === ''){
            input.addClass('required');
        } else {
            input.removeClass('required');
        }
    });

    if ($form.find('.required').size() < 1){
        $form.submit();
    } else {
        alert('Required fields have been highlighted for your attention');
        $form.find('.required:first').focus();
    }

});

$('.pages, .blog, .users, .shop').on('click', '.delete', function(e){
    if (!confirm('Are you sure you want to delete this?')){
        e.preventDefault();
    }
});

// Template section navigation
$('.sub-nav a').on('click', function(e){

    e.preventDefault();
    var $this = $(this);
    var target = $this.attr('data-target');
    $this.parent().parent().find('li').removeClass('selected');
    $this.parent().addClass('selected');

    var $container = $('.uim-form');

    $container.find('.ui_input_wrapper').addClass('uim-hidden');
    $container.find('.' + target).removeClass('uim-hidden');

});



// ======== Template variations 
$('.add-new-item').on('click', function(e){

    e.preventDefault();
    var $this = $(this);
    var $variations = $this.parent().find('.variations');
    var template = $this.parent().find('.variations .template').html();

    var time = Math.round(+new Date()/1000);

    template = template.replace(/_END_/gi, '_END_new' + time + '_');

    $variations.prepend('<section class="new-variation">' + template + '</section>');

});

$('.variations').on('click', '.new-variation > .remove', function(){
    if (confirm('Are you sure you want to remove this item?')){
        $(this).parent().remove();
    }
});


$('.variations').on('click', '.collapse', function(){
    $(this).find('span').toggleClass('icon-collapse-up');
    $(this).parent().parent().find('.fields').toggle();
});

$('.variations').sortable({
    handle: '.move',
    placeholder: 'uim-state-highlight',
    forcePlaceholderSize: true,
    start: function(event, ui){
       $(this).find('.uim-state-highlight').height($(ui.helper).height());
    }
});

$('.variations .variation').on('click', '.actions .delete', function(e){
    e.preventDefault();
    if (confirm('Are you sure you want to delete this item?')){
        $(this).closest('.variation').remove();
    }
});

$('.variation .published').on('click', function(e){
    e.preventDefault();
    $this = $(this);
    $field = $this.next('input[type="hidden"]');

    $this.removeClass('unpublished').removeClass('published');

    if ($this.text() == 'Published'){
        $this.text('Unpublished');
        $this.removeClass('green').addClass('red');
        $field.val('0');
    } else {
        $this.text('Published');
        $this.removeClass('red').addClass('green');
        $field.val('1');
    }

});

$('.ui_input_wrapper').on('click', '.add-variation', function(){

    var $containter, $list, $selected, $select;
    var template;

    // Set context
    $container = $(this).parent();
    $list = $container.find('.ui_select_variations');
    $selected = $container.find('select option:selected');
    $select = $container.find('select');

    // Check if variation already exists
    if ($selected.val() === ''){

        alert('You must select an item to add.');

    } else if ($list.find('li[data-val="' + $selected.val() + '"][data-text="' + $selected.text() + '"]').size() < 1){

        // Select data
        template =  '<li data-val="' + $selected.val() + '" data-text="' + $selected.text() + '">' +
                        '<span class="name">' + $selected.text() + '</span>' +
                        '<span class="actions"><a href="#" class="uim-button delete red remove_variation">Delete</a></span>' +
                        '<div class="move"><span class="icon-move"></span></div>' +
                        '<input type="hidden" name="" value="' + $selected.val() + '" />' +
                    '</li>';

        $list.append(template);


        // Serialize the inputs so that inputs have unique names
        $list.find('li').each(function(item){
            var count = item + 1;
            $(this).find('input[type="hidden"]').attr('name', $select.attr('name') + '_select_variation_' + count);
        });

    } else {

        alert('Variation "' + $selected.text() + '" has already been added.');

    }


});

$('.ui_select_variations').sortable({
    handle: '.move',
});
$('.ui_select_variations').on('click', '.remove_variation', function(e){
    e.preventDefault();
    if (confirm('Are you sure you want to delete this item?')){
        $(this).parent().parent().remove();
    }
});
// ======== Template variations  

// Count down for max length
var $value;
$('textarea, input').keyup(function(e) {
    $this = $(this);
    $maxlength = $this.attr('maxlength');
    $name = $this.attr('name');
    
    if(!$value) {
        $value = $('label[for="meta_description"]').text();
    }

    $countDown = $maxlength - $this.val().length;
    if($maxlength && $maxlength !== undefined) {
        $('label[for="meta_description"]').text($value + ' (Maximum characters: ' + $maxlength + ' - ' + $countDown + ' characters left)');

    }

});

// Alert 
$('.uim-alert').on('click', function(e){
    if(e.target.className) {
        $(this).slideUp('fast');
    }
});

// ======== Permissions 
$('.checkbox input').on('click', function(){
    $name = $(this).attr('value');

    if($name == 'all') {
        if ($(this).is(':checked')){
            $('.checkbox input[type="checkbox"]').prop('checked', true);
        } else {
            $('.checkbox input[type="checkbox"]').removeAttr('checked');
        }
    } else {
        $('.checkbox input[value="all"]').removeAttr('checked');
    }
    
});
// ======== Permissions