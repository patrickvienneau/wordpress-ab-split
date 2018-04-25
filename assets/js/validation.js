var URL_REGEX = /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/;

jQuery(document).ready(function($){
  $('body').on('change', 'input[data-validate="url"]', function(){
    var $this = $(this);
    var value = $this.val();


    if (!URL_REGEX.test(value)) {
      $this.addClass('validation-error');
    } else {
      $this.removeClass('validation-error');
    }
  })

  $('body').on('keyup', 'input.validation-error[data-validate="url"]', function() {
    var $this = $(this);
    var value = $this.val();

    if (!URL_REGEX.test(value)) $this.removeClass('validation-error');
  })
});
