window.veem.toggle = {};

jQuery(document).ready(function($){
  function updateShowStates (value, $scope = document) {
    $('[data-show-toggle-type][data-show-toggle-type='+value+']', $scope).show();
    $('[data-show-toggle-type][data-show-toggle-type!='+value+']', $scope).hide()
      .find('input').val('');

    $('[data-hide-toggle-type][data-hide-toggle-type='+value+']', $scope).hide()
      .find('input').val('');
    $('[data-hide-toggle-type][data-hide-toggle-type!='+value+']', $scope).show();
  }

  window.veem.toggle.init = function($scope = document) {
    $('[data-toggle-type]', $scope).each(function(){
      var $toggleContainer = $(this);
      var $toggleInputs = $('input[name$="[type]"]', $toggleContainer);
      var $initialInput = $toggleInputs.filter(':checked').first();

      if (!$initialInput.length) $initialInput = $toggleInputs.first();

      var initialValue = $initialInput.val();

      $initialInput.prop('checked', true);
      updateShowStates(initialValue, $toggleContainer);

      $toggleInputs.on('change', function(){
        var $this = $(this);
        var val = $this.val();

        updateShowStates(val, $toggleContainer);
      })
    })
  }
});
