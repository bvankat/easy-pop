<?php
/**
 * Plugin Name:       Easy Pop - Perfect Popups for Wordpress
 * Description:       Create beautiful modals and popups using the WordPress block editor with powerful targeting and display options.
 * Version:           0.2.0
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Author:            Hanscom Park Studio
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       modal-builder
 *
 * @package ModalBuilder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the modal custom post type.
 */
function modal_builder_register_post_type() {
	
	$custom_icon = 'data:image/svg+xml;base64,' . base64_encode('
		<svg xmlns="M7.43,20s-.03-.02-.07-.02c-1.3,0-2.61,0-3.91,0l-1.7-10.88C.57,8.75-.16,7.69.03,6.55c.17-.99,1.15-1.74,2.2-1.84-.13-.31-.53-.45-.56-.82-.07-.76.46-1.59,1.14-1.99.39-.24,1.26-.52,1.69-.28.08.04.57.48.62.44.09-.89.8-1.74,1.74-1.98.85-.22,1.68.02,2.31.56,1.5-1.33,3.84-.43,4.06,1.42.32-.12.49-.48.87-.52.99-.09,2.04.61,2.4,1.44.18.42.3.9-.01,1.28-.05.05-.43.41-.37.45.96.09,1.87.72,2.14,1.6.37,1.19-.39,2.41-1.64,2.79l-.03.04-1.68,10.85c-2.41,0-4.82,0-7.23,0-.04,0-.07.02-.07.02-.06,0-.11,0-.17,0ZM9.75,2.84c-.46.42-1.26.21-1.4-.38-.05-.19-.01-.33-.11-.51-.31-.58-1.3-.54-1.53.08-.17.47.24.69.12,1.15-.11.43-.84.79-1.29.75-.64-.05-.61-.67-1.11-.82-.69-.2-1.32.45-1.02,1.05.2.4.78.38.85.93.05.39-.32,1.06-.73,1.19-.5.16-.69-.16-1.14-.13-.93.07-1.03,1.46.13,1.54.46.03.99,0,1.46,0,3.46.01,6.94.01,10.4,0,.46,0,.99.03,1.44,0,1.25-.08,1.02-1.67-.1-1.53-.35.04-.46.27-.91.12-.36-.12-.75-.74-.75-1.07,0-.7.75-.63.9-1.15.18-.58-.36-1.1-1-.96-.55.11-.53.77-1.14.83-.38.04-1.03-.24-1.22-.54-.34-.54.15-.76.06-1.24-.14-.7-1.25-.82-1.57-.16-.15.31-.01.58-.32.87ZM6.68,9.21h-1.67s0,9.23,0,9.23h1.67s0-9.23,0-9.23ZM9.99,9.22h-1.67v9.22h1.67v-9.22ZM13.33,9.23h-1.67v9.21h1.67v-9.21Z"/>
		</svg>
	');
	
	$labels = array(
		'name'                  => _x( 'Popups', 'Post type general name', 'modal-builder' ),
		'singular_name'         => _x( 'Popup', 'Post type singular name', 'modal-builder' ),
		'menu_name'             => _x( 'Popups', 'Admin Menu text', 'modal-builder' ),
		'name_admin_bar'        => _x( 'Popup', 'Add New on Toolbar', 'modal-builder' ),
		'add_new'               => __( 'Add New', 'modal-builder' ),
		'add_new_item'          => __( 'Add New Popup', 'modal-builder' ),
		'new_item'              => __( 'New Popup', 'modal-builder' ),
		'edit_item'             => __( 'Edit Popup', 'modal-builder' ),
		'view_item'             => __( 'View Popup', 'modal-builder' ),
		'all_items'             => __( 'All Popups', 'modal-builder' ),
		'search_items'          => __( 'Search Popups', 'modal-builder' ),
		'not_found'             => __( 'No popups found.', 'modal-builder' ),
		'not_found_in_trash'    => __( 'No popups found in Trash.', 'modal-builder' ),
		'item_published'        => __( 'Popup published.', 'modal-builder' ),
		'item_updated'          => __( 'Popup updated.', 'modal-builder' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'menu_icon'          => $custom_icon,
		'query_var'          => false,
		'rewrite'            => false,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 20,
		'supports'           => array( 'title', 'editor', 'custom-fields' ),
		'show_in_rest'       => true,
		'template'           => array(
			array( 'core/paragraph', array(
				'placeholder' => __( 'Start designing your popup content here using any blocks...', 'modal-builder' ),
			) ),
		),
	);

	register_post_type( 'modal', $args );
}
add_action( 'init', 'modal_builder_register_post_type' );

/**
 * Add settings submenu page.
 */
function modal_builder_add_settings_page() {
	add_submenu_page(
		'edit.php?post_type=modal',
		__( 'Default Settings', 'modal-builder' ),
		__( 'Settings', 'modal-builder' ),
		'manage_options',
		'modal-builder-settings',
		'modal_builder_render_settings_page'
	);
}
add_action( 'admin_menu', 'modal_builder_add_settings_page' );

/**
 * Get default settings.
 */
function modal_builder_get_defaults() {
	$defaults = array(
		'modal_trigger_type' => 'page_load',
		'modal_trigger_delay' => 0,
		'modal_trigger_scroll_percentage' => 50,
		'modal_trigger_click_selector' => '',
		'modal_trigger_inactivity_seconds' => 30,
		'modal_trigger_scroll_element' => '',
		'modal_page_views_threshold' => 0,
		'modal_session_threshold' => 0,
		'modal_frequency_limit' => 0,
		'modal_frequency_period' => 'month',
		'modal_user_targeting' => 'all',
		'modal_devices' => 'all',
		'modal_browsers' => 'all',
		'modal_schedule_start' => '',
		'modal_schedule_end' => '',
		'modal_referrer_filter' => '',
		'modal_position' => 'center',
		'modal_animation' => 'fade',
		'modal_overlay_opacity' => 75,
		'modal_show_close_button' => true,
		'modal_backdrop_close' => true,
		'modal_esc_close' => true,
	);

	$saved_defaults = get_option( 'modal_builder_defaults', array() );
	return wp_parse_args( $saved_defaults, $defaults );
}

/**
 * Render settings page.
 */
function modal_builder_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_POST['modal_builder_save_defaults'] ) && check_admin_referer( 'modal_builder_defaults' ) ) {
		$defaults = array();
		$fields = array(
			'modal_trigger_type',
			'modal_trigger_delay',
			'modal_trigger_scroll_percentage',
			'modal_trigger_click_selector',
			'modal_trigger_inactivity_seconds',
			'modal_trigger_scroll_element',
			'modal_page_views_threshold',
			'modal_session_threshold',
			'modal_frequency_limit',
			'modal_frequency_period',
			'modal_user_targeting',
			'modal_devices',
			'modal_browsers',
			'modal_schedule_start',
			'modal_schedule_end',
			'modal_referrer_filter',
			'modal_position',
			'modal_animation',
			'modal_overlay_opacity',
		);

		foreach ( $fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				$defaults[ $field ] = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
			}
		}

		$defaults['modal_show_close_button'] = isset( $_POST['modal_show_close_button'] );
		$defaults['modal_backdrop_close'] = isset( $_POST['modal_backdrop_close'] );
		$defaults['modal_esc_close'] = isset( $_POST['modal_esc_close'] );

		update_option( 'modal_builder_defaults', $defaults );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Default settings saved successfully!', 'modal-builder' ) . '</p></div>';
	}

	$defaults = modal_builder_get_defaults();
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Popup Default Settings', 'modal-builder' ); ?></h1>
		<p><?php echo esc_html__( 'These default settings will be applied to all new popups. Existing popups will not be affected.', 'modal-builder' ); ?></p>

		<form method="post" action="">
			<?php wp_nonce_field( 'modal_builder_defaults' ); ?>

			<h2 class="title"><?php echo esc_html__( 'Triggers', 'modal-builder' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Trigger Type', 'modal-builder' ); ?></th>
					<td>
						<select name="modal_trigger_type" id="modal_trigger_type">
							<option value="page_load" <?php selected( $defaults['modal_trigger_type'], 'page_load' ); ?>><?php echo esc_html__( 'On Page Load', 'modal-builder' ); ?></option>
							<option value="scroll" <?php selected( $defaults['modal_trigger_type'], 'scroll' ); ?>><?php echo esc_html__( 'On Scroll', 'modal-builder' ); ?></option>
							<option value="click" <?php selected( $defaults['modal_trigger_type'], 'click' ); ?>><?php echo esc_html__( 'On Click', 'modal-builder' ); ?></option>
							<option value="exit_intent" <?php selected( $defaults['modal_trigger_type'], 'exit_intent' ); ?>><?php echo esc_html__( 'Exit Intent', 'modal-builder' ); ?></option>
							<option value="inactivity" <?php selected( $defaults['modal_trigger_type'], 'inactivity' ); ?>><?php echo esc_html__( 'After Inactivity', 'modal-builder' ); ?></option>
							<option value="scroll_element" <?php selected( $defaults['modal_trigger_type'], 'scroll_element' ); ?>><?php echo esc_html__( 'On Scroll To Element', 'modal-builder' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Delay (seconds)', 'modal-builder' ); ?></th>
					<td><input type="number" name="modal_trigger_delay" value="<?php echo esc_attr( $defaults['modal_trigger_delay'] ); ?>" min="0" max="60" /></td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Scroll Percentage', 'modal-builder' ); ?></th>
					<td><input type="number" name="modal_trigger_scroll_percentage" value="<?php echo esc_attr( $defaults['modal_trigger_scroll_percentage'] ); ?>" min="0" max="100" /></td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Click Selector', 'modal-builder' ); ?></th>
					<td><input type="text" name="modal_trigger_click_selector" value="<?php echo esc_attr( $defaults['modal_trigger_click_selector'] ); ?>" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Inactivity Duration (seconds)', 'modal-builder' ); ?></th>
					<td><input type="number" name="modal_trigger_inactivity_seconds" value="<?php echo esc_attr( $defaults['modal_trigger_inactivity_seconds'] ); ?>" min="5" max="300" /></td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Scroll Element Selector', 'modal-builder' ); ?></th>
					<td><input type="text" name="modal_trigger_scroll_element" value="<?php echo esc_attr( $defaults['modal_trigger_scroll_element'] ); ?>" class="regular-text" /></td>
				</tr>
			</table>

			<h2 class="title"><?php echo esc_html__( 'Display Rules', 'modal-builder' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Page Views Threshold', 'modal-builder' ); ?></th>
					<td><input type="number" name="modal_page_views_threshold" value="<?php echo esc_attr( $defaults['modal_page_views_threshold'] ); ?>" min="0" max="50" /></td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Session Threshold', 'modal-builder' ); ?></th>
					<td><input type="number" name="modal_session_threshold" value="<?php echo esc_attr( $defaults['modal_session_threshold'] ); ?>" min="0" max="20" /></td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Frequency Limit', 'modal-builder' ); ?></th>
					<td><input type="number" name="modal_frequency_limit" value="<?php echo esc_attr( $defaults['modal_frequency_limit'] ); ?>" min="0" max="50" /></td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Frequency Period', 'modal-builder' ); ?></th>
					<td>
						<select name="modal_frequency_period">
							<option value="session" <?php selected( $defaults['modal_frequency_period'], 'session' ); ?>><?php echo esc_html__( 'Per Session', 'modal-builder' ); ?></option>
							<option value="day" <?php selected( $defaults['modal_frequency_period'], 'day' ); ?>><?php echo esc_html__( 'Per Day', 'modal-builder' ); ?></option>
							<option value="week" <?php selected( $defaults['modal_frequency_period'], 'week' ); ?>><?php echo esc_html__( 'Per Week', 'modal-builder' ); ?></option>
							<option value="month" <?php selected( $defaults['modal_frequency_period'], 'month' ); ?>><?php echo esc_html__( 'Per Month', 'modal-builder' ); ?></option>
							<option value="lifetime" <?php selected( $defaults['modal_frequency_period'], 'lifetime' ); ?>><?php echo esc_html__( 'Lifetime', 'modal-builder' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default User Targeting', 'modal-builder' ); ?></th>
					<td>
						<select name="modal_user_targeting">
							<option value="all" <?php selected( $defaults['modal_user_targeting'], 'all' ); ?>><?php echo esc_html__( 'All Users', 'modal-builder' ); ?></option>
							<option value="logged_in" <?php selected( $defaults['modal_user_targeting'], 'logged_in' ); ?>><?php echo esc_html__( 'Logged In Only', 'modal-builder' ); ?></option>
							<option value="logged_out" <?php selected( $defaults['modal_user_targeting'], 'logged_out' ); ?>><?php echo esc_html__( 'Logged Out Only', 'modal-builder' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Device Targeting', 'modal-builder' ); ?></th>
					<td>
						<select name="modal_devices">
							<option value="all" <?php selected( $defaults['modal_devices'], 'all' ); ?>><?php echo esc_html__( 'All Devices', 'modal-builder' ); ?></option>
							<option value="desktop" <?php selected( $defaults['modal_devices'], 'desktop' ); ?>><?php echo esc_html__( 'Desktop Only', 'modal-builder' ); ?></option>
							<option value="tablet" <?php selected( $defaults['modal_devices'], 'tablet' ); ?>><?php echo esc_html__( 'Tablet Only', 'modal-builder' ); ?></option>
							<option value="mobile" <?php selected( $defaults['modal_devices'], 'mobile' ); ?>><?php echo esc_html__( 'Mobile Only', 'modal-builder' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Browser Detection', 'modal-builder' ); ?></th>
					<td>
						<select name="modal_browsers">
							<option value="all" <?php selected( $defaults['modal_browsers'], 'all' ); ?>><?php echo esc_html__( 'All Browsers', 'modal-builder' ); ?></option>
							<option value="chrome" <?php selected( $defaults['modal_browsers'], 'chrome' ); ?>><?php echo esc_html__( 'Chrome Only', 'modal-builder' ); ?></option>
							<option value="firefox" <?php selected( $defaults['modal_browsers'], 'firefox' ); ?>><?php echo esc_html__( 'Firefox Only', 'modal-builder' ); ?></option>
							<option value="safari" <?php selected( $defaults['modal_browsers'], 'safari' ); ?>><?php echo esc_html__( 'Safari Only', 'modal-builder' ); ?></option>
							<option value="edge" <?php selected( $defaults['modal_browsers'], 'edge' ); ?>><?php echo esc_html__( 'Edge Only', 'modal-builder' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Referrer Filter', 'modal-builder' ); ?></th>
					<td><input type="text" name="modal_referrer_filter" value="<?php echo esc_attr( $defaults['modal_referrer_filter'] ); ?>" class="regular-text" /></td>
				</tr>
			</table>

			<h2 class="title"><?php echo esc_html__( 'Display Options', 'modal-builder' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Position', 'modal-builder' ); ?></th>
					<td>
						<select name="modal_position">
							<option value="center" <?php selected( $defaults['modal_position'], 'center' ); ?>><?php echo esc_html__( 'Center', 'modal-builder' ); ?></option>
							<option value="top-left" <?php selected( $defaults['modal_position'], 'top-left' ); ?>><?php echo esc_html__( 'Top Left', 'modal-builder' ); ?></option>
							<option value="top-center" <?php selected( $defaults['modal_position'], 'top-center' ); ?>><?php echo esc_html__( 'Top Center', 'modal-builder' ); ?></option>
							<option value="top-right" <?php selected( $defaults['modal_position'], 'top-right' ); ?>><?php echo esc_html__( 'Top Right', 'modal-builder' ); ?></option>
							<option value="bottom-left" <?php selected( $defaults['modal_position'], 'bottom-left' ); ?>><?php echo esc_html__( 'Bottom Left', 'modal-builder' ); ?></option>
							<option value="bottom-center" <?php selected( $defaults['modal_position'], 'bottom-center' ); ?>><?php echo esc_html__( 'Bottom Center', 'modal-builder' ); ?></option>
							<option value="bottom-right" <?php selected( $defaults['modal_position'], 'bottom-right' ); ?>><?php echo esc_html__( 'Bottom Right', 'modal-builder' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Animation Effect', 'modal-builder' ); ?></th>
					<td>
						<select name="modal_animation">
							<option value="fade" <?php selected( $defaults['modal_animation'], 'fade' ); ?>><?php echo esc_html__( 'Fade', 'modal-builder' ); ?></option>
							<option value="slide-up" <?php selected( $defaults['modal_animation'], 'slide-up' ); ?>><?php echo esc_html__( 'Slide Up', 'modal-builder' ); ?></option>
							<option value="slide-down" <?php selected( $defaults['modal_animation'], 'slide-down' ); ?>><?php echo esc_html__( 'Slide Down', 'modal-builder' ); ?></option>
							<option value="slide-left" <?php selected( $defaults['modal_animation'], 'slide-left' ); ?>><?php echo esc_html__( 'Slide Left', 'modal-builder' ); ?></option>
							<option value="slide-right" <?php selected( $defaults['modal_animation'], 'slide-right' ); ?>><?php echo esc_html__( 'Slide Right', 'modal-builder' ); ?></option>
							<option value="zoom-in" <?php selected( $defaults['modal_animation'], 'zoom-in' ); ?>><?php echo esc_html__( 'Zoom In', 'modal-builder' ); ?></option>
							<option value="zoom-out" <?php selected( $defaults['modal_animation'], 'zoom-out' ); ?>><?php echo esc_html__( 'Zoom Out', 'modal-builder' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Overlay Opacity', 'modal-builder' ); ?></th>
					<td><input type="number" name="modal_overlay_opacity" value="<?php echo esc_attr( $defaults['modal_overlay_opacity'] ); ?>" min="0" max="100" /></td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Close Button', 'modal-builder' ); ?></th>
					<td><input type="checkbox" name="modal_show_close_button" value="1" <?php checked( $defaults['modal_show_close_button'], true ); ?> /></td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default Backdrop Close', 'modal-builder' ); ?></th>
					<td><input type="checkbox" name="modal_backdrop_close" value="1" <?php checked( $defaults['modal_backdrop_close'], true ); ?> /></td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Default ESC Key Close', 'modal-builder' ); ?></th>
					<td><input type="checkbox" name="modal_esc_close" value="1" <?php checked( $defaults['modal_esc_close'], true ); ?> /></td>
				</tr>
			</table>

			<?php submit_button( __( 'Save Default Settings', 'modal-builder' ), 'primary', 'modal_builder_save_defaults' ); ?>
		</form>
	</div>
	<?php
}

/**
 * Register meta fields with default values for new popups.
 */
function modal_builder_register_meta() {
	$defaults = modal_builder_get_defaults();

	// Trigger settings
	register_post_meta( 'modal', 'modal_trigger_type', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_trigger_type'],
	) );

	register_post_meta( 'modal', 'modal_trigger_delay', array(
		'type'         => 'number',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_trigger_delay'],
	) );

	register_post_meta( 'modal', 'modal_trigger_scroll_percentage', array(
		'type'         => 'number',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_trigger_scroll_percentage'],
	) );

	register_post_meta( 'modal', 'modal_trigger_click_selector', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_trigger_click_selector'],
	) );

	register_post_meta( 'modal', 'modal_trigger_inactivity_seconds', array(
		'type'         => 'number',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_trigger_inactivity_seconds'],
	) );

	register_post_meta( 'modal', 'modal_trigger_scroll_element', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_trigger_scroll_element'],
	) );

	// Display rules
	register_post_meta( 'modal', 'modal_page_views_threshold', array(
		'type'         => 'number',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_page_views_threshold'],
	) );

	register_post_meta( 'modal', 'modal_session_threshold', array(
		'type'         => 'number',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_session_threshold'],
	) );

	register_post_meta( 'modal', 'modal_frequency_limit', array(
		'type'         => 'number',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_frequency_limit'],
	) );

	register_post_meta( 'modal', 'modal_frequency_period', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_frequency_period'],
	) );

	register_post_meta( 'modal', 'modal_user_targeting', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_user_targeting'],
	) );

	register_post_meta( 'modal', 'modal_devices', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_devices'],
	) );

	register_post_meta( 'modal', 'modal_browsers', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_browsers'],
	) );

	register_post_meta( 'modal', 'modal_schedule_start', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_schedule_start'],
	) );

	register_post_meta( 'modal', 'modal_schedule_end', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_schedule_end'],
	) );

	register_post_meta( 'modal', 'modal_referrer_filter', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_referrer_filter'],
	) );

	// Display options
	register_post_meta( 'modal', 'modal_position', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_position'],
	) );

	register_post_meta( 'modal', 'modal_animation', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_animation'],
	) );

	register_post_meta( 'modal', 'modal_overlay_opacity', array(
		'type'         => 'number',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_overlay_opacity'],
	) );

	register_post_meta( 'modal', 'modal_show_close_button', array(
		'type'         => 'boolean',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_show_close_button'],
	) );

	register_post_meta( 'modal', 'modal_backdrop_close', array(
		'type'         => 'boolean',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_backdrop_close'],
	) );

	register_post_meta( 'modal', 'modal_esc_close', array(
		'type'         => 'boolean',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => $defaults['modal_esc_close'],
	) );
}
add_action( 'init', 'modal_builder_register_meta' );

/**
 * Enqueue modal scripts and styles on frontend.
 */
function modal_builder_enqueue_frontend_assets() {
	if ( is_admin() ) {
		return;
	}

	$modals = get_posts( array(
		'post_type'      => 'modal',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	) );

	if ( empty( $modals ) ) {
		return;
	}

	$modal_data = array();
	foreach ( $modals as $modal ) {
		$schedule_start = get_post_meta( $modal->ID, 'modal_schedule_start', true );
		$schedule_end = get_post_meta( $modal->ID, 'modal_schedule_end', true );
		$current_time = current_time( 'timestamp' );

		// Check schedule
		if ( $schedule_start && strtotime( $schedule_start ) > $current_time ) {
			continue;
		}
		if ( $schedule_end && strtotime( $schedule_end ) < $current_time ) {
			continue;
		}

		// Check user targeting
		$user_targeting = get_post_meta( $modal->ID, 'modal_user_targeting', true ) ?: 'all';
		if ( $user_targeting === 'logged_in' && ! is_user_logged_in() ) {
			continue;
		}
		if ( $user_targeting === 'logged_out' && is_user_logged_in() ) {
			continue;
		}

		$modal_data[] = array(
			'id'                      => $modal->ID,
			'content'                 => apply_filters( 'the_content', $modal->post_content ),
			'triggerType'             => get_post_meta( $modal->ID, 'modal_trigger_type', true ) ?: 'page_load',
			'triggerDelay'            => (int) get_post_meta( $modal->ID, 'modal_trigger_delay', true ),
			'triggerScrollPercentage' => (int) get_post_meta( $modal->ID, 'modal_trigger_scroll_percentage', true ),
			'triggerClickSelector'    => get_post_meta( $modal->ID, 'modal_trigger_click_selector', true ),
			'triggerInactivitySeconds' => (int) get_post_meta( $modal->ID, 'modal_trigger_inactivity_seconds', true ),
			'triggerScrollElement'    => get_post_meta( $modal->ID, 'modal_trigger_scroll_element', true ),
			'pageViewsThreshold'      => (int) get_post_meta( $modal->ID, 'modal_page_views_threshold', true ),
			'sessionThreshold'        => (int) get_post_meta( $modal->ID, 'modal_session_threshold', true ),
			'frequencyLimit'          => (int) get_post_meta( $modal->ID, 'modal_frequency_limit', true ),
			'frequencyPeriod'         => get_post_meta( $modal->ID, 'modal_frequency_period', true ) ?: 'month',
			'devices'                 => get_post_meta( $modal->ID, 'modal_devices', true ) ?: 'all',
			'browsers'                => get_post_meta( $modal->ID, 'modal_browsers', true ) ?: 'all',
			'referrerFilter'          => get_post_meta( $modal->ID, 'modal_referrer_filter', true ),
			'position'                => get_post_meta( $modal->ID, 'modal_position', true ) ?: 'center',
			'animation'               => get_post_meta( $modal->ID, 'modal_animation', true ) ?: 'fade',
			'overlayOpacity'          => (int) get_post_meta( $modal->ID, 'modal_overlay_opacity', true ),
			'showCloseButton'         => (bool) get_post_meta( $modal->ID, 'modal_show_close_button', true ),
			'backdropClose'           => (bool) get_post_meta( $modal->ID, 'modal_backdrop_close', true ),
			'escClose'                => (bool) get_post_meta( $modal->ID, 'modal_esc_close', true ),
		);
	}

	if ( empty( $modal_data ) ) {
		return;
	}

	wp_enqueue_style(
		'modal-builder-frontend',
		plugins_url( 'build/style-index.css', __FILE__ ),
		array(),
		'0.1.0'
	);

	wp_enqueue_script(
		'modal-builder-frontend',
		plugins_url( 'build/view.js', __FILE__ ),
		array(),
		'0.1.0',
		true
	);

	wp_localize_script( 'modal-builder-frontend', 'modalBuilderData', array(
		'modals' => $modal_data,
	) );
}
add_action( 'wp_enqueue_scripts', 'modal_builder_enqueue_frontend_assets' );

/**
 * Enqueue editor assets.
 */
function modal_builder_enqueue_editor_assets() {
	$screen = get_current_screen();
	if ( ! $screen || $screen->post_type !== 'modal' ) {
		return;
	}

	wp_enqueue_script(
		'modal-builder-editor',
		plugins_url( 'build/index.js', __FILE__ ),
		array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data' ),
		'0.1.0',
		true
	);

	wp_enqueue_style(
		'modal-builder-editor',
		plugins_url( 'build/index.css', __FILE__ ),
		array( 'wp-edit-post' ),
		'0.1.0'
	);
}
add_action( 'admin_enqueue_scripts', 'modal_builder_enqueue_editor_assets' );