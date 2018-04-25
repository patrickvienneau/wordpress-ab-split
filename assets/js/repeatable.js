var REPEATABLE_DATA_ATTRIBUTE = 'data-repeatable-attribute';
var REPEATABLE_DATA_ATTRIBUTE_REGEX = new RegExp('^'+REPEATABLE_DATA_ATTRIBUTE+'-(\\S+)$');
var ATTRIBUTE_VALUE_INDEX_MARKER = new RegExp('\S*{{index}}\S*');
var ATTRIBUTE_VALUE_LETTER_MARKER = new RegExp('\S*{{letter}}\S*');
var ATTRIBUTE_VALUE_LETTER_UPPERCASE_MARKER = new RegExp('\S*{{LETTER}}\S*');

jQuery(document).ready(function($){
  function isDataEmpty(data) {
    var result = true;

    $.each(data, function(key, value) {
      if (value || typeof value === 'boolean') {
        result = false;
      }

      return result;
    });

    return result;
  }

  function computeAttributes($el, index) {
    $(':attrStartsWith('+REPEATABLE_DATA_ATTRIBUTE+')', $el).each(function(){
      var $this = $(this);

      $.each(this.attributes, function(blah, attribute){
        var attributeName = attribute.name;
        var attributeValue = attribute.value;

        var matches = REPEATABLE_DATA_ATTRIBUTE_REGEX.exec(attributeName);

        if (!matches || matches.length < 2) return true;

        var attributeToModify = matches[matches.length-1];
        var computedValue = computeAttributeValue(attributeValue, index);


        switch(attributeToModify) {
          case 'text':
            $this.text(computedValue);
            break;
          default:
            $this.attr(attributeToModify, computedValue);
            break;
        }
      })
    });
  }

  function computeAttributeValue(value, index) {
    return value
      .replace(ATTRIBUTE_VALUE_INDEX_MARKER, index)
      .replace(ATTRIBUTE_VALUE_LETTER_UPPERCASE_MARKER, String.fromCharCode(65 + index))
      .replace(ATTRIBUTE_VALUE_LETTER_MARKER, String.fromCharCode(97 + index));
  }

  function populateFields($scope, data = {}) {
    if (isDataEmpty(data)) return $scope;

    $.each(data, function(key, value) {
      $('input[name$="['+key+']"]', $scope).each(function(){
        var $this = $(this);

        switch ($this.attr('type')) {
          case 'checkbox':
          case 'radio':
            if ($this.val() === value) $this.prop('checked', true);
            break;
          default:
            $this.val(value);
            break;
        }
      })
    })
  }

  $('[data-repeatable]').each(function() {
    var $this = $(this);

    var REPEATABLE_MAX_LIMIT = +$this.attr('data-repeatable-limit') || null;
    var REPEATABLE_ERRORS = $this.data('repeatable-errors') || [];
    var REPEATABLE_DATA = $.grep($this.data('repeatable-data') || [], function(data) {
      return !isDataEmpty(data);
    });

    var $repeatableOriginalCopy = $this
      .addClass('repeatable')
      .data('index', -1);
    var $repeatableContainer = $('<div>')
      .addClass('repeatable-container');
    var $repeatableControls = $('<div>')
      .addClass('repeatable-controls');
    var $repeatableControleButtonAdd = $('<button>')
      .text('Add')
      .addClass('repeatable-button-add')
      .on('click', function(e) {
        e.preventDefault();

        addNewClone();
      })
      .appendTo($repeatableControls);
    var $repeatableErrorLabel = $('<label>')
      .addClass('validation validation-error')
      .text(REPEATABLE_ERRORS.page)
      .appendTo($repeatableControls);

    var getRepeatableItems = function() {
      return $('.repeatable', $repeatableContainer);
    }

    var getRepeatableItemCount = function() {
      return getRepeatableItems().not($repeatableOriginalCopy).length
    }

    var getLastRepeatableItem = function() {
      return getRepeatableItems().last();
    }

    $repeatableOriginalCopy
      .after($repeatableContainer)
      .hide();
    $repeatableContainer
      .append($repeatableOriginalCopy)
      .append($repeatableControls);

    if (REPEATABLE_DATA.length) {
      $.each(REPEATABLE_DATA, function(ii, obj) {
        addNewClone(obj, REPEATABLE_ERRORS.pages && REPEATABLE_ERRORS.pages[ii]);
      });
    } else {
      addNewClone();
    }

    function addNewClone(data = {}, error){
      var repeatableCount = getRepeatableItemCount();
      var nextRepeatableIndex = getLastRepeatableItem().data('index') + 1;

      var $repeatableClone = $repeatableOriginalCopy
        .clone(true, true)
        .data('index', nextRepeatableIndex)
        .removeData('repeatable')
        .show();

      var $repeatableButtonRemove = $('<button>')
        .addClass('repeatable-button-remove dashicons dashicons-no-alt')
        .on('click', function() {
          $repeatableClone.remove();

          if (getRepeatableItemCount() < 1) addNewClone();
        });

      var $errorLabel = $('<div>')
        .addClass('validation validation-error')
        .text(error);

      if (REPEATABLE_MAX_LIMIT && repeatableCount >= REPEATABLE_MAX_LIMIT) return false;

      computeAttributes($repeatableClone, nextRepeatableIndex);
      populateFields($repeatableClone, data);
      
      window.veem.toggle.init($repeatableClone);
      window.veem.autocomplete.init($repeatableClone);

      $repeatableClone
        .append($repeatableButtonRemove)
        .append($errorLabel)
        .insertBefore($repeatableControls);
    }
  })
});
