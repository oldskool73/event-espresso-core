<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @ package			Event Espresso
 * @ author			Seth Shoultes
 * @ copyright		(c) 2008-2011 Event Espresso  All Rights Reserved.
 * @ license			http://eventespresso.com/support/terms-conditions/   * see Plugin Licensing *
 * @ link					http://www.eventespresso.com
 * @ version		 	4.0
 *
 * ------------------------------------------------------------------------
 *
 * Event List
 *
 * @package			Event Espresso
 * @subpackage	/modules/invoice/
 * @author				Brent Christensen 
 *
 * ------------------------------------------------------------------------
 */
class EED_Invoice  extends EED_Module {

	/**
	 * 	set_hooks - for hooking into EE Core, other modules, etc
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public static function set_hooks() {
	}

	/**
	 * 	set_hooks_admin - for hooking into EE Admin Core, other modules, etc
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public static function set_hooks_admin() {
	}



	/**
	 * 	run - initial module setup
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function run() {
		if ( is_readable( EVENT_ESPRESSO_PLUGINFULLPATH . 'gateways/Invoice/lib/Invoice.class.php' )) {
			require_once( EVENT_ESPRESSO_PLUGINFULLPATH . 'gateways/Invoice/lib/Invoice.class.php' );
		} else {
			$msg = __( 'An error has occured. The Invoice.class.php file could not be loaded.', 'event_espresso' );
			EE_Error::add_error( $msg, __FILE__, __FUNCTION__, __LINE__ );
		}
//		add_filter( 'FHEE_load_org_options', '__return_true' );
//		add_filter( 'FHEE_run_EE_wp', '__return_true' );
//		add_filter( 'FHEE_load_EE_Session', '__return_true' );
//		add_action( 'wp_loaded', array( $this, 'wp_loaded' ));
//		add_action( 'wp', array( $this, 'wp' ));
//		add_filter( 'the_content', array( $this, 'the_content' ));
	}




	/**
	 * 	invoice_launch
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function invoice_launch() {
		if ( $this->EE->REQ->is_set( 'id' )) {
			$id = sanitize_key( $this->EE->REQ->get( 'id' ));
			$invoice = new Invoice( $id );
			$invoice->send_invoice();
		}
	}



	/**
	 * 	download_invoice
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function download_invoice() {
		if ( $this->EE->REQ->is_set( 'id' )) {
			$id = sanitize_key( $this->EE->REQ->get( 'id' ));
			$invoice = new Invoice($_REQUEST['id']);
			// send invoice but force download
			$invoice->send_invoice( TRUE ); 
		}
	}
	



}
// End of file EED_Invoice.module.php
// Location: /modules/invoice/EED_Invoice.module.php