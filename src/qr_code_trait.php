<?php

namespace mycryptocheckout;

use \Exception;

/**
	@brief		Handles admin things such as settings and currencies.
	@since		2017-12-09 07:05:04
**/
trait qr_code_trait
{
	/**
		@brief		Add the QR code inputs to the settings form.
		@details	Set $form->local_settings or global_settings depending on what we're setting.
		@since		2018-04-26 17:24:27
	**/
	public function add_qr_code_inputs( $form )
	{
		$qr_code_enabled = $form->select( 'qr_code_enabled' )
			// Input description.
			->description( __( 'Enable a QR code for the wallet address on the order confirmation page.', 'mycryptocheckout' ) )
			// Input label.
			->label( __( 'QR code status', 'mycryptocheckout' ) );


		$qr_code_html = $form->textarea( 'qr_code_html' )
			// Input description.
			->description( __( 'This is the HTML code used to display the QR code. Leave empty to use the default value.', 'mycryptocheckout' ) )
			// Input label.
			->label( __( 'QR code HTML', 'mycryptocheckout' ) )
			->rows( 5, 40 );

		if ( isset( $form->form()->local_settings ) )
		{
			$qr_code_enabled->opt( 'enabled', __( 'Enabled', 'mycryptocheckout' ) );
			$qr_code_enabled->opt( 'disabled', __( 'Disabled', 'mycryptocheckout' ) );
			// Local
			$qr_code_enabled->value( $this->get_local_option( 'qr_code_enabled' ) );
			$qr_code_html->value( $this->get_local_global_file_option( 'qr_code_html' ) );

			if ( $this->is_network )
				$qr_code_enabled->opt( 'auto', __( 'Use network admin default', 'mycryptocheckout' ) );
		}
		else
		{
			// Global
			$qr_code_enabled->opt( 'enabled_all', __( 'Enabled on all sites', 'mycryptocheckout' ) );
			$qr_code_enabled->opt( 'disabled_all', __( 'Disabled on all sites', 'mycryptocheckout' ) );
			$qr_code_enabled->opt( 'default_enabled', __( 'Default enabled on all sites', 'mycryptocheckout' ) );
			$qr_code_enabled->opt( 'default_disabled', __( 'Default disabled on all sites', 'mycryptocheckout' ) );

			$qr_code_enabled->value( $this->get_site_option( 'qr_code_enabled' ) );
			$qr_code_html->value( $this->get_global_file_option( 'qr_code_html' ) );
		}
	}

	/**
		@brief		Add QR code data to the js.
		@since		2018-04-29 19:23:47
	**/
	public function qrcode_generate_checkout_javascript_data( $action )
	{
		$qr_code_enabled = $this->get_local_option( 'qr_code_enabled' );
		switch( $qr_code_enabled )
		{
			case 'auto':
				$enabled = 'auto';
			break;
			case 'disabled':
				$enabled = false;
			break;
			case 'enabled':
				$enabled = true;
			break;
		}

		if ( $enabled !== false )
			$html = $this->get_local_global_file_option( 'qr_code_html' );

		if ( $this->is_network )
		{
			$qr_code_enabled = $this->get_site_option( 'qr_code_enabled' );
			switch( $qr_code_enabled )
			{
				case 'disabled_all':
					$enabled = false;
				break;
				case 'enabled_all':
					$enabled = true;
					// Forcing enabled also forces the global html.
					$html = $this->get_global_file_option( 'qr_code_html' );
				break;
				case 'default_disabled':
					if ( $enabled === 'auto' )
						$enabled = false;
				break;
				case 'default_enabled':
					if ( $enabled === 'auto' )
						$enabled = true;
				break;
			}
		}

		if ( ! $enabled )
			return;

		$action->data->set( 'qr_code_html', $html );
		wp_enqueue_script( 'mcc_qrcode', MyCryptoCheckout()->paths( 'url' ) . '/src/static/js/qrcode.js', [ 'mycryptocheckout' ], MyCryptoCheckout()->plugin_version );
	}

	/**
		@brief		Save the QR code input data.
		@since		2018-04-26 17:25:17
	**/
	public function save_qr_code_inputs( $form )
	{
		if ( isset( $form->form()->local_settings ) )
		{
			// Local
			$this->update_local_global_disk_option( $form, 'qr_code_html' );
			$this->update_local_option( 'qr_code_enabled', $form->input( 'qr_code_enabled' )->get_post_value() );
		}
		else
		{
			// Global
			$this->update_global_disk_option( $form, 'qr_code_html' );
			$this->update_site_option( 'qr_code_enabled', $form->input( 'qr_code_enabled' )->get_post_value() );
		}
	}
}
