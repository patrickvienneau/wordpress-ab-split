<?php

if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

class WP_Veem_AB_List_Table extends WP_List_Table {
  /**
	 * ***********************************************************************
	 * Normally we would be querying data from a database and manipulating that
	 * for use in your list table. For this example, we're going to simplify it
	 * slightly and create a pre-built array. Think of this as the data that might
	 * be returned by $wpdb->query()
	 *
	 * In a real-world scenario, you would run your own custom query inside
	 * the prepare_items() method in this class.
	 *
	 * @var array
	 * ************************************************************************
	 */
	protected $example_data = array(
    array(
      'id'          => 1,
      'name'       => 'Homepage Test',
      'status'      => 'Active',
      'pages'       => array(
        array(
          'id'      => 1,
          'test_id' => 1,
          'post_id' => 523,
          'visit_count' => 152,
          'url'     => NULL,
        ),
        array(
          'id'      => 2,
          'test_id' => 1,
          'post_id' => NULL,
          'visit_count' => 151,
          'url'     => 'http://www.apps.veem.com/Marketing',
        ),
      ),
      'created_at'  => 1524079458,
      'created_by'  => 2,
    ),
		// array(
		// 	'id'       => 1,
		// 	'title'    => '300',
		// 	'rating'   => 'R',
		// 	'director' => 'Zach Snyder',
		// ),
		// array(
		// 	'id'       => 2,
		// 	'title'    => 'Eyes Wide Shut',
		// 	'rating'   => 'R',
		// 	'director' => 'Stanley Kubrick',
		// ),
	);

	/**
	 * TT_Example_List_Table constructor.
	 *
	 * REQUIRED. Set up a constructor that references the parent constructor. We
	 * use the parent reference to set some default configs.
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'a/b split',
			'plural'   => 'a/b splits',
			'ajax'     => false,
		) );
	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * REQUIRED! This method dictates the table's columns and titles. This should
	 * return an array where the key is the column slug (and class) and the value
	 * is the column's title text. If you need a checkbox for bulk actions, refer
	 * to the $columns array below.
	 *
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a `column_cb()` method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 * @return array An associative array containing column information.
	 */
	public function get_columns() {
		$columns = array(
			// 'cb'       => '<input type="checkbox" />',
			'name'    => _x( 'Test Name', 'Column label', 'wp-list-table-example' ),
			'type'   => _x( 'Test Type', 'Column label', 'wp-list-table-example' ),
			'statistics' => _x( 'Statistics', 'Column label', 'wp-list-table-example' ),
      'status' => _x( 'Test Status', 'Column label', 'wp-list-table-example' ),
      'created_at' => _x( 'Created At', 'Column label', 'wp-list-table-example' ),
		);

		return $columns;
	}

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
	 * you will need to register it here. This should return an array where the
	 * key is the column that needs to be sortable, and the value is db column to
	 * sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * This method merely defines which columns should be sortable and makes them
	 * clickable - it does not handle the actual sorting. You still need to detect
	 * the ORDERBY and ORDER querystring variables within `prepare_items()` and sort
	 * your data accordingly (usually by modifying your query).
	 *
	 * @return array An associative array containing all the columns that should be sortable.
	 */
	protected function get_sortable_columns() {
		$sortable_columns = array(
			'name'    => array( 'name', false ),
      'type'    => array( 'type', false ),
      'status'    => array( 'status', false ),
      'created_at'    => array( 'created_at', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Get default column value.
	 *
	 * Recommended. This method is called when the parent class can't find a method
	 * specifically build for a given column. Generally, it's recommended to include
	 * one method for each column you want to render, keeping your package class
	 * neat and organized. For example, if the class needs to process a column
	 * named 'title', it would first see if a method named $this->column_title()
	 * exists - if it does, that method will be used. If it doesn't, this one will
	 * be used. Generally, you should try to use custom column methods as much as
	 * possible.
	 *
	 * Since we have defined a column_title() method later on, this method doesn't
	 * need to concern itself with any column with a name of 'title'. Instead, it
	 * needs to handle everything else.
	 *
	 * For more detailed insight into how columns are handled, take a look at
	 * WP_List_Table::single_row_columns()
	 *
	 * @param object $item        A singular item (one full row's worth of data).
	 * @param string $column_name The name/slug of the column to be processed.
	 * @return string Text or HTML to be placed inside the column <td>.
	 */
	protected function column_default( $item, $column_name ) {
    $columns = $this->get_columns();
    $column_keys = array_keys($columns);

    if (
      in_array($column_name, $column_keys) &&
      array_key_exists($column_name, $item)
    ) return $item->{$column_name};

    return print_r( $item, true ); // Show the whole array for troubleshooting purposes.
	}

	/**
	 * Get value for checkbox column.
	 *
	 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	 * is given special treatment when columns are processed. It ALWAYS needs to
	 * have it's own method.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 */
	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],  // Let's simply repurpose the table's singular label ("movie").
			$item->id                	 // The value of the checkbox should be the record's id.
		);
	}

	/**
	 * Get title column value.
	 *
	 * Recommended. This is a custom column method and is responsible for what
	 * is rendered in any column with a name/slug of 'title'. Every time the class
	 * needs to render a column, it first looks for a method named
	 * column_{$column_title} - if it exists, that method is run. If it doesn't
	 * exist, column_default() is called instead.
	 *
	 * This example also illustrates how to implement rollover actions. Actions
	 * should be an associative array formatted as 'slug'=>'link html' - and you
	 * will need to generate the URLs yourself. You could even ensure the links are
	 * secured with wp_nonce_url(), as an expected security measure.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 */
	// protected function column_title( $item ) {
	// 	$page = wp_unslash( $_REQUEST['page'] ); // WPCS: Input var ok.
  //
	// 	// Build edit row action.
	// 	$edit_query_args = array(
	// 		'page'   => $page,
	// 		'action' => 'edit',
	// 		'movie'  => $item['id'],
	// 	);
  //
	// 	$actions['edit'] = sprintf(
	// 		'<a href="%1$s">%2$s</a>',
	// 		esc_url( wp_nonce_url( add_query_arg( $edit_query_args, 'admin.php' ), 'editmovie_' . $item['id'] ) ),
	// 		_x( 'Edit', 'List table row action', 'wp-list-table-example' )
	// 	);
  //
	// 	// Build delete row action.
	// 	$delete_query_args = array(
	// 		'page'   => $page,
	// 		'action' => 'delete',
	// 		'movie'  => $item['id'],
	// 	);
  //
	// 	$actions['delete'] = sprintf(
	// 		'<a href="%1$s">%2$s</a>',
	// 		esc_url( wp_nonce_url( add_query_arg( $delete_query_args, 'admin.php' ), 'deletemovie_' . $item['id'] ) ),
	// 		_x( 'Delete', 'List table row action', 'wp-list-table-example' )
	// 	);
  //
	// 	// Return the title contents.
	// 	return sprintf( '%1$s <span style="color:silver;">(id:%2$s)</span>%3$s',
	// 		$item['title'],
	// 		$item['id'],
	// 		$this->row_actions( $actions )
	// 	);
	// }

  /*
   * Recommended. This is a custom column method and is responsible for what
   * is rendered in any column with a name/slug of 'title'. Every time the class
   * needs to render a column, it first looks for a method named
   * column_{$column_title} - if it exists, that method is run. If it doesn't
   * exist, column_default() is called instead.
   */

  protected function column_name($item) {
		global $TEST_STATUSES_ACTIONS;
		global $TEST_STATUS_DISABLED;
		global $TEST_STATUS_ACTIVE;

    $test_edit_url = add_query_arg(
			array(
				'page' => 'veem-ab-split-edit',
				'id' => $item->id
			),
			admin_url('admin.php')
		);
		$test_delete_url = add_query_arg(
			array(
				'page' => 'veem-ab-split',
				'action' => 'delete',
				'id' => $item->id
			),
			admin_url('admin.php')
		);
		$test_toggle_url = add_query_arg(
			array(
				'page' => 'veem-ab-split',
				'action' => 'disable',
				'id' => $item->id
			),
			admin_url('admin.php')
		);

		$toggled_status = $item->status === $TEST_STATUS_ACTIVE ? $TEST_STATUS_DISABLED : $TEST_STATUS_ACTIVE;
    ?>
      <a class="row-title" href="<?php echo $test_edit_url; ?>"><?php echo $item->name; ?></a>
			<div class="row-actions">
				<span class="edit">
					<a href="<?php echo $test_edit_url; ?>" aria-label="Edit “Sample Page”">Edit</a> |
				</span>
				<span class="trash">
					<a href="<?php echo $test_delete_url; ?>" class="submitdelete" aria-label="Move “<?php echo $item->name; ?>” to the Trash">Delete</a> |
				</span>
				<span class="disable">
					<a href="<?php echo $test_toggle_url; ?>" class="submitDisable" aria-label="<?php echo $TEST_STATUSES_ACTIONS[$toggled_status]; ?> “<?php echo $item->name; ?>”"><?php echo $TEST_STATUSES_ACTIONS[$toggled_status]; ?></a>
				</span>
			</div>
    <?php
  }

  protected function column_type($item) {
    global $TEST_TYPES;
    global $TEST_TYPE_INTERNAL;
    global $TEST_TYPE_EXTERNAL;

    $test_type = $TEST_TYPE_INTERNAL;
    $pages = $item->pages;

    $hasExternalPages = array_some($pages, function ($page) {
      return isset($page->url);
    });

    if ($hasExternalPages) $test_type = $TEST_TYPE_EXTERNAL;

    return $TEST_TYPES[$test_type];
  }

  protected function column_statistics($item) {
    $pages = $item->pages;
    $chars = range('A', 'Z');

    ?>
    <ul>
    <?php

    for ($ii = 0; $ii < count($pages); $ii++) {
      $test = $pages[$ii];
      $visit_count = $test->visit_count;
			$url = get_permalink($test->id).'?noredirect';

			if ($test->isInternal()) {
				$title = get_post($test->post_id)->post_title;
			} else {
				$title = $test->url;
			}

      ?>

      <li>
        <label>
					<strong><?php echo $chars[$ii]; ?></strong>: <a target="_blank" href="<?php echo $url; ?>" title="<?php echo $title; ?>"><?php echo $visit_count; ?> <?php echo pluralize('visit', $visit_count !== 1) ?></a>
				</label>
      </li>

      <?php
    }

    ?>
    </ul>
    <?php
  }

	protected function column_status ($item) {
		global $TEST_STATUSES;

		$status = $item->status;

		return $TEST_STATUSES[$status];
	}

  protected function column_created_at ($item) {
    $date_created = $item->created_at;

    $date = date('Y/m/d', $date_created);
		$date_time = date('Y/m/d g:i:s a', $date_created);

    ?>
    <abbr title="<?php echo $date_time; ?>"><?php echo $date; ?></abbr>
    <?php
  }

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * Optional. If you need to include bulk actions in your list table, this is
	 * the place to define them. Bulk actions are an associative array in the format
	 * 'slug'=>'Visible Title'
	 *
	 * If this method returns an empty value, no bulk action will be rendered. If
	 * you specify any bulk actions, the bulk actions box will be rendered with
	 * the table automatically on display().
	 *
	 * Also note that list tables are not automatically wrapped in <form> elements,
	 * so you will need to create those manually in order for bulk actions to function.
	 *
	 * @return array An associative array containing all the bulk actions.
	 */
	protected function get_bulk_actions() {
		$actions = array(
			'delete' => _x( 'Delete', 'List table bulk action', 'wp-list-table-example' ),
		);

		return $actions;
	}

	/**
	 * Handle bulk actions.
	 *
	 * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
	 * For this example package, we will handle it in the class to keep things
	 * clean and organized.
	 *
	 * @see $this->prepare_items()
	 */
	protected function process_bulk_action() {
		// Detect when a bulk action is being triggered.
		// if ( 'delete' === $this->current_action() ) {
		// 	wp_die( 'Items deleted (or they would be if we had items to delete)!' );
		// }
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here.
	 *
	 * @global wpdb $wpdb
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 */
	function prepare_items() {
		global $wpdb; //This is used only if making any database queries

		/*
		 * First, lets decide how many records per page to show
		 */
		$per_page = 5;

		/*
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		/*
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * three other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();

		/*
		 * GET THE DATA!
		 *
		 * For information on making queries in WordPress, see this Codex entry:
		 * http://codex.wordpress.org/Class_Reference/wpdb
		 */
		$tests = getTests();

		/*
		 * This checks for sorting input and sorts the data in our array of dummy
		 * data accordingly (using a custom usort_reorder() function). It's for
		 * example purposes only.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary. In other words: remove this when
		 * you implement your own query.
		 */
		usort( $tests, array( $this, 'usort_reorder' ) );

		/*
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/*
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		$total_items = count( $tests );

		/*
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to do that.
		 */
		$data = array_slice( $tests, ( ( $current_page - 1 ) * $per_page ), $per_page );

		/*
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $tests;

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,                     // WE have to calculate the total number of items.
			'per_page'    => $per_page,                        // WE have to determine how many items to show on a page.
			'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
		) );
	}

	/**
	 * Callback to allow sorting of example data.
	 *
	 * @param string $a First value.
	 * @param string $b Second value.
	 *
	 * @return int
	 */
	protected function usort_reorder( $a, $b ) {
		// If no sort, default to title.
		$orderby = ! empty( $_REQUEST['orderby'] ) ? wp_unslash( $_REQUEST['orderby'] ) : 'title'; // WPCS: Input var ok.

		// If no order, default to asc.
		$order = ! empty( $_REQUEST['order'] ) ? wp_unslash( $_REQUEST['order'] ) : 'asc'; // WPCS: Input var ok.

		// Determine sort order.
		$result = strcmp( $a->{$orderby}, $b->{$orderby} );

		return ( 'asc' === $order ) ? $result : - $result;
	}
}
