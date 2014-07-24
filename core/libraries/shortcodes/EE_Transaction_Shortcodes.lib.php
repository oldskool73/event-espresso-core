<?php

if (!defined('EVENT_ESPRESSO_VERSION') )
	exit('NO direct script access allowed');

/**
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @ package			Event Espresso
 * @ author				Seth Shoultes
 * @ copyright		(c) 2008-2011 Event Espresso  All Rights Reserved.
 * @ license			http://eventespresso.com/support/terms-conditions/   * see Plugin Licensing *
 * @ link				http://www.eventespresso.com
 * @ version		 	4.0
 *
 * ------------------------------------------------------------------------
 *
 * EE_Transaction_Shortcodes
 *
 * this is a child class for the EE_Shortcodes library.  The EE_Transaction_Shortcodes lists all shortcodes related to transaction specific info.
 *
 * NOTE: if a method doesn't have any phpdoc commenting the details can be found in the comments in EE_Shortcodes parent class.
 *
 * @package		Event Espresso
 * @subpackage	libraries/shortcodes/EE_Transaction_Shortcodes.lib.php
 * @author		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */
class EE_Transaction_Shortcodes extends EE_Shortcodes {

	public function __construct() {
		parent::__construct();
	}


	protected function _init_props() {
		$this->label = __("Transaction Shortcodes", 'event_espresso');
		$this->description = __('All shortcodes specific to transaction related data', 'event_espresso');
		$this->_shortcodes = array(
			'[TXN_ID]' => __('The transaction id for the purchase.', 'event_espresso'),
			'[PAYMENT_URL]' => __('This is a link to make a payment for the event', 'event_espresso'),
			'[INVOICE_LINK]' => __('This is a full html link to the invoice', 'event_espresso'),
			'[INVOICE_URL]' => __('This is just the url for the invoice', 'event_espresso'),
			'[INVOICE_LOGO_URL]' => __('This returns the url for the logo uploaded via the invoice settings page.', 'event_espresso'),
			'[INVOICE_LOGO]' => __('This returns the logo uploaded via the invoice settings page wrapped in img_tags and with a "logo screen" classes. The image size is also set in the img tags automatically to match the uploaded logo.', 'event_espresso'),
			'[TOTAL_COST]' => __('The total cost for the transaction', 'event_espresso'),
			'[TXN_STATUS]' => __('The transaction status for the transaction.', 'event_espresso'),
			'[TXN_STATUS_ID]' => __('The ID representing the transaction status as saved in the db.  This tends to be useful for including with css classes for styling certain statuses differently from others.', 'event_espresos'),
			'[PAYMENT_STATUS]' => __('The transaction status for the transaction. This parses to the same value as the [TXN_STATUS] shortcode and still remains here for legacy support.', 'event_espresso'),
			'[PAYMENT_GATEWAY]' => __('The payment gateway used for the transaction', 'event_espresso'),
			'[AMOUNT_PAID]' => __('The amount paid with a payment', 'event_espresso'),
			'[TOTAL_OWING]' => __('The total owing on a transaction', 'event_espresso'),
			'[TKT_QTY_PURCHASED]' => __('The total number of all tickets purchased in a transaction', 'event_espresso'),
			'[TRANSACTION_ADMIN_URL]' => __('The url to the admin page for this transaction', 'event_espresso')
			);
	}


	protected function _parser( $shortcode ) {

		EE_Registry::instance()->load_helper( 'Template' );

		if ( !$this->_data->txn instanceof EE_Transaction )
			return '';

		switch ( $shortcode ) {
			case '[TXN_ID]' :
				return $this->_data->txn->ID();
				break;

			case '[PAYMENT_URL]' :
				$payment_url = $this->_data->txn->payment_overview_url();
				return empty( $payment_url ) ? __( 'http://dummypaymenturlforpreview.com', 'event_espresso') : $payment_url;
				break;

			case '[INVOICE_LINK]' :
				$invoice_url = $this->_data->txn->invoice_url();
				$invoice_url = empty( $invoice_url ) ? 'http://dummyinvoicelinksforpreview.com' : $invoice_url;
				return sprintf( __('%sClick here for Invoice%s', 'event_espresso'), '<a href="' . $invoice_url . '">', '</a>' );
				break; /**/

			case '[INVOICE_URL]' :
				$invoice_url = $this->_data->txn->invoice_url();
				return empty( $invoice_url ) ? 'http://dummyinvoicelinksforpreview.com' : $invoice_url;
				break;

			case '[INVOICE_LOGO_URL]' :
				return $this->_get_invoice_logo();
				break;

			case '[INVOICE_LOGO]' :
				return $this->_get_invoice_logo( TRUE );
				break;


			case "[TOTAL_COST]" :
				$total = $this->_data->txn->total();
				return ! empty($total) ? EEH_Template::format_currency( $total ) : '';
				break;

			case "[PAYMENT_STATUS]" :
				$status = $this->_data->txn->pretty_status();
				return !empty($status) ? $status : __('Unknown', 'event_espresso');
				break; /**/

			// note the [payment_status] shortcode is kind of misleading because payment status might be different from txn status so I'm adding this here for clarity.
			case "[TXN_STATUS]" :
				$status = $this->_data->txn->pretty_status();
				return !empty( $status ) ? $status : __('Unknown', 'event_espresso');
				break;

			case "[TXN_STATUS_ID]" :
				return $this->_data->txn->status_ID();
				break;

			case "[PAYMENT_GATEWAY]" :
				return $this->_get_payment_gateway();
				break;

			case "[AMOUNT_PAID]" :
				$amount = isset( $this->_data->payment ) && is_object( $this->_data->payment ) ? $this->_data->payment->amount() : 0;
				return EEH_Template::format_currency( $amount );
				break;

			case "[TOTAL_OWING]" :
				$total_owing = isset( $this->_data->txn ) && is_object($this->_data->txn) ? $this->_data->txn->remaining() : $this->_data->txn->total();
				return EEH_Template::format_currency( $total_owing );
				break;

			case "[TKT_QTY_PURCHASED]" :
				return $this->_data->total_ticket_count;
				break;

			case "[TRANSACTION_ADMIN_URL]" :
				require_once EE_CORE . 'admin/EE_Admin_Page.core.php';
				$query_args = array( 'page' => 'espresso_transactions', 'action' => 'view_transaction', 'TXN_ID' => $this->_data->txn->ID() );
				$url = EE_Admin_Page::add_query_args_and_nonce( $query_args, admin_url('admin.php') );
				return $url;
				break;

		}
		return '';
	}



	private function _get_payment_gateway() {
		if ( !is_object( $this->_data->txn ) )
			return '';
		return $this->_data->txn->selected_gateway();
	}



	/**
	 * This retrieves a logo to be used for the invoice from whatever is set on the invoice logo settings page.  If its not present then the organization logo is used if its found (set on the organzation settings page).
	 *
	 * @since %VER%
	 *
	 * @param bool $img_tags TRUE means to return with the img tag wrappers.  False just returns the url to the image.
	 *
	 * @return string url or html
	 */
	private function _get_invoice_logo( $img_tags = FALSE ) {
		$payment_settings = EE_Config::instance()->gateway->payment_settings;
		$invoice_settings = $payment_settings['invoice'];

		if ( ! empty( $invoice_settings['invoice_logo_url'] ) ) {
			$invoice_logo_url = $invoice_setttings['invoice_logo_url'];
		} else {
			$invoice_logo_url = EE_Registry::instance()->CFG->organization->logo_url;
		}

		if ( empty( $invoice_logo_url ) ) {
			return '';
		}

		if ( ! $img_tags ) {
			return $invoice_logo_url;
		}

		//image tags have been requested.
		$image_size = getimagesize( $image_size );
		return '<img class="logo screen" src="' . $invoice_logo_url . '" ' . $image_size[3] . ' alt="logo" />';
	}

} //end EE_Transaction Shortcodes library
