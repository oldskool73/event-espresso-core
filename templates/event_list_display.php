<?php
//This is the event list template page.
//This is a template file for displaying an event lsit on a page.
//There should be a copy of this file in your wp-content/uploads/espresso/ folder.
/*
 * use the following shortcodes in a page or post:
 * [EVENT_LIST]
 * [EVENT_LIST limit=1]
 * [EVENT_LIST css_class=my-custom-class]
 * [EVENT_LIST show_expired=true]
 * [EVENT_LIST show_deleted=true]
 * [EVENT_LIST show_secondary=false]
 * [EVENT_LIST show_recurrence=true]
 * [EVENT_LIST category_identifier=your_category_identifier]
 *
 * Example:
 * [EVENT_LIST limit=5 show_recurrence=true category_identifier=your_category_identifier]
 *
 */


//Print out the array of event status options
//print_r (event_espresso_get_is_active($event_id));
//Here we can create messages based on the event status. These variables can be echoed anywhere on the page to display your status message.
$status = event_espresso_get_is_active(0,$event_meta);
$status_display = ' - ' . $status['display_custom'];
$status_display_ongoing = $status['status'] == 'ONGOING' ? ' - ' . $status['display_custom'] : '';
$status_display_deleted = $status['status'] == 'DELETED' ? ' - ' . $status['display_custom'] : '';
$status_display_secondary = $status['status'] == 'SECONDARY' ? ' - ' . $status['display_custom'] : ''; //Waitlist event
$status_display_draft = $status['status'] == 'DRAFT' ? ' - ' . $status['display_custom'] : '';
$status_display_pending = $status['status'] == 'PENDING' ? ' - ' . $status['display_custom'] : '';
$status_display_denied = $status['status'] == 'DENIED' ? ' - ' . $status['display_custom'] : '';
$status_display_expired = $status['status'] == 'EXPIRED' ? ' - ' . $status['display_custom'] : '';
$status_display_reg_closed = $status['status'] == 'REGISTRATION_CLOSED' ? ' - ' . $status['display_custom'] : '';
$status_display_not_open = $status['status'] == 'REGISTRATION_NOT_OPEN' ? ' - ' . $status['display_custom'] : '';
$status_display_open = $status['status'] == 'REGISTRATION_OPEN' ? ' - ' . $status['display_custom'] : '';

//You can also display a custom message. For example, this is a custom registration not open message:
$status_display_custom_closed = $status['status'] == 'REGISTRATION_CLOSED' ? ' - <span class="espresso_closed">' . __('Regsitration is Closed', 'event_espresso') . '</span>' : '';

?>

<div id="event_data-<?php echo $event_id ?>" class="event_data <?php echo $css_class; ?> <?php echo $category_identifier; ?> event-list-display event-display-boxes ui-widget">
	<h2 id="event_title-<?php echo $event_id ?>" class="event_title ui-widget-header ui-corner-top"><a title="<?php echo stripslashes_deep($event_name) ?>" class="a_event_title" id="a_event_title-<?php echo $event_id ?>" href="<?php echo $registration_url; ?>"><?php echo stripslashes_deep($event_name) ?></a> </h2>
	<div class="event-data-display ui-widget-content ui-corner-bottom">
		<?php 				
	$thumb_url = $registration_url;
	//Thumbnail
	if( isset($event_meta['display_thumb_in_lists']) && $event_meta['display_thumb_in_lists'] == 'Y' && !empty($event_meta['event_thumbnail_url'])){ ?>
		<a id="ee-event-list-thumb" class="ee-thumbs" href="<?php echo $thumb_url ?>"> <span><img src="<?php echo $event_meta['event_thumbnail_url'] ?>" alt="" /></span> </a>
		<?php 
	}
		
?>
		<div class="event-meta">
			<p id="p_event_price-<?php echo $event_id ?>" class="event_price"> <span class="section-title">
				<?php  echo __('Price: ', 'event_espresso'); ?>
				</span> <?php echo  $org_options['currency_symbol'].$event->event_cost; ?> </p>
			<p id="event_date-<?php echo $event_id ?>"> <span class="section-title">
				<?php _e('Date:', 'event_espresso'); ?>
				</span> <?php echo event_date_display($start_date, get_option('date_format')) ?> </p>
		</div>
		<?php
	//Available spaces
	if ($display_reg_form == 'Y' && $externalURL == '') { ?>
		<p id="available_spaces-<?php echo $event_id ?>"> <span class="section-title">
			<?php _e('Available Spaces:', 'event_espresso') ?>
			</span><?php echo get_number_of_attendees_reg_limit($event_id, 'available_spaces', 'All Seats Reserved') ?> </p>
		<?php
	}
	
	//Event description
	if ( !empty($event_desc) ){
		if ( isset($org_options['template_settings']['display_description_in_event_list']) && $org_options['template_settings']['display_description_in_event_list'] == 'Y' ) {
			//Show short descriptions
			if ( isset($org_options['template_settings']['display_short_description_in_event_list']) && $org_options['template_settings']['display_short_description_in_event_list'] == 'Y' ) {
				$event_desc = array_shift(explode('<!--more-->', $event_desc));
			}
?>
		<div class="event-desc ui-corner-all">
			<p><?php echo espresso_format_content($event_desc); ?></p>
		</div>
		<?php 
		}
		
	}
	
	//Address
	if ($location != '' && isset( $org_options['template_settings']['display_address_in_event_list'] ) && $org_options['template_settings']['display_address_in_event_list'] == 'Y') { ?>
		<p class="event_address" id="event_address-<?php echo $event_id ?>"> <span class="section-title"><?php echo __('Address:', 'event_espresso'); ?></span> <br />
			<span class="address-block"><?php echo stripslashes_deep($location); ?> <span class="google-map-link">
			<?php  echo $google_map_link; ?>
			</span> </span> </p>
		<?php 
	}
	
	//Google map
	if(isset($event_meta['enable_for_gmap']) && $event_meta['enable_for_gmap'] == 'Y'){ 
		if( function_exists('ee_gmap_display') && isset( $org_options['map_settings']['ee_display_map_no_shortcodes'] ) && ( $org_options['map_settings']['ee_display_map_no_shortcodes'] == 'Y' )){
			echo ee_gmap_display($ee_gmap_location, $event_id);
		}
	}
	
	//Social media buttons
	do_action( 'action_hook_espresso_social_display_buttons', $event_id);
	
	//Get the number of attendees. Please visit http://eventespresso.com/forums/?p=247 for available parameters for the get_number_of_attendees_reg_limit() function.
	$num_attendees = get_number_of_attendees_reg_limit($event_id, 'num_attendees'); 
	
	if ($num_attendees >= $reg_limit) { 
		if ($overflow_event_id != '0' && $allow_overflow == 'Y') { ?>
		<p id="register_link-<?php echo $overflow_event_id ?>" class="register-link-footer"> <a class="a_register_link ui-priority-secondary ui-state-default" id="a_register_link-<?php echo $overflow_event_id ?>" href="<?php echo espresso_reg_url($overflow_event_id); ?>" title="<?php echo stripslashes_deep($event_name) ?>">
			<?php _e('Join Waiting List', 'event_espresso'); ?>
			</a> </p>
		<?php
		}
	} else {
		//Multiple event registration
		
		// Load the multi event link.
		//Un-comment these next lines to check if the event is active
		//echo event_espresso_get_status($event_id);
		//print_r( event_espresso_get_is_active($event_id));
	
		if ($multi_reg && event_espresso_get_status(0,$event_meta) == 'ACTIVE') {
	
			$params = array(
				//REQUIRED, the id of the event that needs to be added to the cart
				'event_id' => $event_id,
				'anchor_class' => 'class="cart-link ui-priority-primary ui-state-default ui-state-hover ui-state-focus ui-corner-all"',
				//REQUIRED, Anchor of the link, can use text or image
				'anchor' => __("Add to Cart", 'event_espresso'), //'anchor' => '<img src="' . EVENT_ESPRESSO_PLUGINFULLURL . 'images/cart_add.png" />',
				//REQUIRED, if not available at this point, use the next line before this array declaration
				// $event_name = get_event_field('event_name', EVENTS_DETAIL_TABLE, ' WHERE id = ' . $event_id);
				'event_name' => $event_name,
				//OPTIONAL, will place this term before the link
				'separator' => __(" &nbsp; Or &nbsp;", 'event_espresso')
			);
		
			$cart_link = event_espresso_cart_link($params);
		}
		
		if ($display_reg_form == 'Y') {
			?>
		<p id="register_link-<?php echo $event_id ?>" class="register-link-footer"> <a class="a_register_link ui-priority-primary ui-state-default ui-state-hover ui-state-focus ui-corner-all" id="a_register_link-<?php echo $event_id ?>" href="<?php echo $registration_url; ?>" title="<?php echo stripslashes_deep($event_name) ?>">
			<?php _e('Register', 'event_espresso'); ?>
			</a> <?php echo isset($cart_link) && $externalURL == '' ? $cart_link : ''; ?> </p>
		<?php } else { ?>
		<p id="register_link-<?php echo $event_id ?>" class="register-link-footer"> <a class="a_register_link ui-priority-primary ui-state-default ui-state-hover ui-state-focus ui-corner-all" id="a_register_link-<?php echo $event_id ?>" href="<?php echo $registration_url; ?>" title="<?php echo stripslashes_deep($event_name) ?>">
			<?php _e('View Details', 'event_espresso'); ?>
			</a> <?php echo isset($cart_link) && $externalURL == '' ? $cart_link : ''; ?> </p>
		<?php
		}
	}
	?>
	</div>
	<!-- / .event-data-display --> 
</div>
<!-- / .event-display-boxes --> 
