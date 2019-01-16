<?php

class Templates_List_Table extends WP_List_Table {

	function __construct() {
		parent::__construct( array(
			'singular' => __( 'Template', NM_DOMAIN ),
			'plural'   => __( 'Templates', NM_DOMAIN ),
			'ajax'     => true,
		) );

		if ( ! class_exists( 'NotificationModel' ) ) {
			require_once NOTIFICATION_MAILER_DIR_PATH . '/includes/NotificationModel.php';
		}

		$this->bulk_action_handler();
		$this->action_handler();

		// screen option
		add_screen_option( 'per_page', array(
			'label'   => __( 'Number of items per page:', NM_DOMAIN ),
			'default' => 20,
			'option'  => 'templates_per_page',
		) );

		$this->prepare_items();

		add_action( 'wp_print_scripts', array( $this, '_list_table_css' ) );
	}

	static function _list_table_css() {
		?>
		<style>
			.striped > tbody > tr:nth-child(odd) {
				background-color: #fff;
			}

			.striped > tbody > tr:nth-child(even) {
				background-color: #f9f9f9;
			}

			table.templates > tbody > tr:hover {
				background: #eff;
			}

			table.templates td,
			table.templates th {
				/*vertical-align: middle;*/
			}

			table.templates .column-id {
				width: 2em;
			}

			table.templates .column-template_name {
				/*width: 8em;*/
			}

			table.templates .column-subject {
				/*width: 15%;*/
			}

			table.templates .column-template_body {
				/*width: 15%;*/
			}

			table.templates .column-description {
				/*width: 15%;*/
			}

			table.templates .column-destination_type {
				/*width: 15%;*/
			}

			table.templates .column-notification_type {
				/*width: 15%;*/
			}
		</style>
		<?php
	}

	function no_items() {
		_e( 'No Templates found, dude.', NM_DOMAIN );
	}

	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  object $item Data
	 * @param  string $column_name - Current column name
	 *
	 * @return mixed
	 */
	function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'template_name':
				$url_edit   = wp_nonce_url( admin_url( 'admin.php?page=templates&action=edit&template_id=' . $item->id ), 'template_action' );
				$url_delete = wp_nonce_url( admin_url( 'admin.php?page=templates&action=delete&template_id=' . $item->id ), 'template_action' );
				$actions    = array(
					'edit'   => sprintf( '<a href="%s">%s</a>', $url_edit, __( 'Edit', NM_DOMAIN ) ),
					'delete' => sprintf( '<a href="%s">%s</a>', $url_delete, __( 'Delete', NM_DOMAIN ) ),
				);

				return esc_html( $item->$column_name ) . $this->row_actions( $actions );
			case 'destination_type_id':
				$destination_type = NotificationModel::getDestinationTypeById( $item->$column_name );

				return $destination_type['name'];
			case 'notification_type_id':
				$notification_type = NotificationModel::getNotificationTypeById( $item->$column_name );

				return $notification_type['name'];
			case 'subject':
			case 'template_body':
			case 'description':
			case 'id':
				return $item->$column_name;
			default:
				return print_r( $item, true );
		}

	}

	/**
	 * Define the sortable columns
	 *
	 * @return array
	 */
	function get_sortable_columns() {
		return array(
			'template_name' => array( 'template_name', false ),
		);
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb'                   => '<input type="checkbox" />',
			'id'                   => __( 'ID', NM_DOMAIN ),
			'template_name'        => __( 'Name', NM_DOMAIN ),
			'subject'              => __( 'Subject', NM_DOMAIN ),
			'template_body'        => __( 'Template Body', NM_DOMAIN ),
			'description'          => __( 'Description', NM_DOMAIN ),
			'destination_type_id'  => __( 'Destination Type', NM_DOMAIN ),
			'notification_type_id' => __( 'Notification Type', NM_DOMAIN ),
		);

		return $columns;
	}

	/**
	 * Define which columns are hidden
	 *
	 * @return array
	 */
	public function get_hidden_columns() {
		return array();
	}

	/**
	 * Allows you to sort the data by the variables set in the $_REQUEST
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return mixed
	 */
	private function sort_data( $a, $b ) {
		// Set defaults
		$orderby = 'template_name';
		$order   = 'asc';
		// If orderby is set, use this as the sort column
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$orderby = $_REQUEST['orderby'];
		}
		// If order is set use this as the order
		if ( ! empty( $_REQUEST['order'] ) ) {
			$order = $_REQUEST['order'];
		}
		$result = strcmp( $a->$orderby, $b->$orderby );
		if ( $order === 'asc' ) {
			return $result;
		}

		return - $result;
	}

	protected function get_bulk_actions() {
		return array(
			'delete' => __( 'Delete', NM_DOMAIN ),
		);
	}

	function column_cb( $item ) {
		echo '<input type="checkbox" name="template_ids[]" id="cb-select-' . $item->id . '" value="' . $item->id . '" />';
	}

	/**
	 * Prepare the items for the table to process
	 *
	 * @return void
	 */
	function prepare_items() {
		$this->_column_headers = $this->get_column_info();

		$perPage     = get_user_meta( get_current_user_id(), get_current_screen()->get_option( 'per_page', 'option' ), true ) ?: 20;
		$currentPage = $this->get_pagenum();

		$args = array(
			'orderby'     => ! empty( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : '',
			'order'       => ! empty( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'ASC',
			'per_page'    => ! empty( $perPage ) ? $perPage : 20,
			'page_number' => ! empty( $currentPage ) ? $currentPage : 1,
			's'           => ! empty( $_REQUEST['s'] ) ? $_REQUEST['s'] : '',
		);
		$data = $this->table_data( $args );

		$totalItems = $data['found'];

		$this->set_pagination_args( array(
			'total_items' => $totalItems,
			'per_page'    => $perPage
		) );
		$this->items = $data['data'];

	}

	/**
	 * Get the table data
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	private function table_data( $args = array() ) {

		$arg = array(
			'orderby'     => ! empty( $args['orderby'] ) ? $args['orderby'] : '',
			'order'       => ! empty( $args['order'] ) ? $args['order'] : 'ASC',
			'per_page'    => ! empty( $args['per_page'] ) ? $args['per_page'] : 20,
			'page_number' => ! empty( $args['page_number'] ) ? $args['page_number'] : 1,
			's'           => ! empty( $args['s'] ) ? $args['s'] : '',
		);

		$data = NotificationModel::getTemplatesList( $arg );

		return $data;
	}

	// Extra handlers. In the middle of the bulk actions and pagination.
	function extra_tablenav( $which ) {
//		echo '<div class="alignleft actions">HTML with form fields (select). Inside form...</div>';
	}

	private function bulk_action_handler() {

		if ( empty( $_REQUEST['template_ids'] ) || empty( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		if ( ! $action = $this->current_action() ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-' . $this->_args['plural'] ) ) {
			wp_die( 'nonce error' );
		}

		if ( 'delete' == $action ) {
			NotificationModel::deleteTemplatesByIds( $_REQUEST['template_ids'] );
		}

	}

	private function action_handler() {

		if ( empty( $_REQUEST['action'] ) || empty( $_REQUEST['template_id'] ) ) {
			return;
		}

		if ( isset( $_REQUEST['action'] ) && 'delete' == $_REQUEST['action'] && ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'template_action' ) ) {
			wp_die( 'nonce error' );
		}

		if ( isset( $_REQUEST['action'] ) && 'delete' == $_REQUEST['action'] ) {
			if ( ! empty( $_REQUEST['template_id'] ) ) {
				NotificationModel::deleteTemplateById( absint( $_REQUEST['template_id'] ) );
			}
		}

	}

	/**
	 * Generates content for a single row of the table
	 *
	 * @param object $item The current item
	 */
	public function single_row( $item ) {

		echo '<tr>';
		$this->single_row_columns( $item );
		echo '</tr>';

	}

}