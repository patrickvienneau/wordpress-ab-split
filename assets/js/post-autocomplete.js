window.veem.autocomplete = {};

jQuery(document).ready(function($){
  function tap(fn) {
    return function(value) {
      fn(value);

      return value;
    }
  }

  function search(keyword) {
    return $.ajax({
      url: ajaxurl,
      type: 'get',
      data: {
        action: 'get_posts_by_keyword',
        keyword: keyword,
      }
    });
  }

  function fetch(id) {
    return $.ajax({
      url: ajaxurl,
      type: 'get',
      data: {
        action: 'get_post_by_id',
        id: id,
      }
    })
  }

  function buildItems(posts = []) {
    var items = [];

    if (!posts.length) {
      items.push($('<li>No pages found.</li>').addClass('no-hover'));
    } else {
      for (var ii = 0; ii < posts.length; ii++) {
        var post = posts[ii];

        items.push(
          $('<li>').append(
            $('<a>').text(post.post_title).data('post', post)
          )
        );
      }
    }

    return $.map(items, function($item, i) {
      return $item.addClass('dropdown-item');
    });
  }

  window.veem.autocomplete.init = function($scope = document) {
    $('[data-post-autocomplete]', $scope).each(function(){
      var $this = $(this);
      var STATE = {};

      function setState(nextState) {
        var prevState = STATE;

        STATE = Object.assign({}, prevState, nextState);

        $this.trigger('state-change', [STATE, prevState]);
      }

      var $input = $this
        .attr('type', 'hidden');
      var $dropdownContainer = $('<div>')
        .addClass('dropdown-container');
      var $dropdown = $('<ul>')
        .addClass('dropdown')
        .appendTo($dropdownContainer)
        .hide();
      var $autoCompleteInput = $('<input>')
        .addClass('autocomplete-input')
        .addClass($input.attr('class'))
        .attr('autocomplete', 'off');
      var $autoCompleteInputLabel = $('<a>')
        .addClass('tag')
        .attr('target', '_blank')
        .hide();
      var $autoCompleteInputContainer = $('<div>')
        .addClass('autocomplete-input-container')
        .append($autoCompleteInput)
        .append($autoCompleteInputLabel);

      if ($input.val()) {
        fetch($input.val())
          .then(function(post) {
            setState({
              post: post,
            })
          });
      }

      $input.after($dropdownContainer);
      $dropdownContainer
        .prepend($autoCompleteInputContainer)
        .prepend($input);

      $autoCompleteInput.on('keyup', function(value) {
        var value = $autoCompleteInput.val();

        setState({
          input: value,
        });
      });

      $dropdownContainer.on('click', function(e) {
        e.stopPropagation();
      })

      $dropdown.on('click', 'a', function(e) {
        e.preventDefault();

        var $this = $(this);
        var post = $this.data('post');

        setState({
          isOpen: false,
          post: post,
        })

        $autoCompleteInput.val('');


      })

      $autoCompleteInput.on('focus', function(){
        setState({
          isOpen: true,
        })
      })

      $(document).on('click', function() {
        setState({
          isOpen: false,
        });
      })

      $input.on('state-change', function(e, state, prevState) {
        if (state.input && state.input.length > 3) {

          if (state.input !== prevState.input) {
            search(state.input)
              .then(function(posts) {
                setState({
                  posts: posts,
                })
              });
          }
        }

        if (state.posts && state.posts.length > 0) {
          $dropdown.empty().append(buildItems(state.posts));
        }

        if (state.isOpen && state.posts && state.posts.length) {
          $dropdown.show();
        } else {
          $dropdown.hide();
        }

        if (state.post) {
          var title = state.post.post_title;
          var postID = state.post.ID;

          $autoCompleteInputLabel
            .show()
            .text(title)
            .attr('href', window.veem_base.BASE_URL+'/?noredirect&p='+postID);
            $dropdown.hide();
            $input.val(postID);
        } else {
          $autoCompleteInputLabel
            .text('')
            .attr('href', '')
            .hide();
        }
      })
    });
  }
});
