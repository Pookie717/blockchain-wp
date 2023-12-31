<?php

namespace plainview\sdk_mcc\form2\bootstrap;

/**
	@brief		Generic Bootstrap form.
	@since		2022-07-17 12:02:52
**/
class form
	extends \plainview\sdk_mcc\form2\form
{
	/**
		@brief		Allow subclasses to modify the input description display.
		@since		2021-01-04 16:15:12
	**/
	public function prepare_input_description_display( $input )
	{
		$input->description->css_class( 'form-text text-muted' );
	}

	/**
		@brief		Allow subclasses to modify the input display.
		@since		2021-01-04 16:11:18
	**/
	public function prepare_input_display( $input )
	{
		switch( $input->type )
		{
			case 'button':
				$input->css_class( 'btn btn-primary' );
			break;
			case 'checkbox':
				$input->css_class( 'form-control form-check-input' );
			break;
			case 'file':
				$input->css_class( 'form-control-file' );
			break;
			case 'submit':
				$input->css_class( 'btn btn-primary' );
			break;
			default:
				$input->css_class( 'form-control' );
		}
	}

	/**
		@brief		Allow subclasses to modify the whole input display div that contains the label, input and description.
		@since		2021-01-04 16:27:09
	**/
	public function prepare_input_div( $input, $div )
	{
		$div->css_class( 'form-group' );
	}
}
