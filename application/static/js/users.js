// Check password
$('#confirm_password, #password').keyup(function(e) {
    $this = $(this);
    $value = $('#confirm_password').val();
    $passwordValue = $('#password').val();

    console.log($passwordValue.length);

    if($this.context.name == 'password') {
      if($passwordValue.length < 6){
        $this.addClass('required');
        $('label[for="password"]').text('Password' + ' (Minimum characters: 6)');
      } else {
        $this.removeClass('required');
      }
    }

    // console.log($this.context.name);
    if($this.context.name == 'confirm_password') {
      if($value && $passwordValue && $value === $passwordValue) {
        $('label[for="confirm_password"]').text('Confirmation Password');
        $this.removeClass('required');
      } else {
        $('label[for="confirm_password"]').text('Confirmation Password' + ' - Needs to match the password');
        $this.addClass('required');
      }
    }

    if(!$value && !$passwordValue) {
       $('#confirm_password').removeClass('required');
       $('#password').removeClass('required');
    }
});