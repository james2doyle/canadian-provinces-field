<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Canadian Provinces and Territories Field Type
 *
 * @package		Addons\Field Types
 * @author		James Doyle
 * @author		Adam Fairholm
 */
class Field_provinces
{
	public $field_type_slug			= 'provinces';
	public $db_col_type				= 'varchar';
	public $version					= '1.0.0';
	public $author					= array('name' => 'James Doyle', 'url' => 'http://ohdoylerules.com');
	public $custom_parameters		= array('provinces_display', 'default_provinces');

	// --------------------------------------------------------------------------

	/**
	 * Buncha places
	 *
	 * @access 	public
	 * @var 	array
	 */
	public $raw_provinces = array(
		'ON' => 'Ontario',
		'QC' => 'Quebec',
		'NS' => 'Nova Scotia',
		'NB' => 'New Brunswick',
		'MB' => 'Manitoba',
		'BC' => 'British Columbia',
		'PE' => 'Prince Edward Island',
		'SK' => 'Saskatchewan',
		'AB' => 'Alberta',
		'NL' => 'Newfoundland and Labrador',
		'YT' => 'Yukon Territory',
		'NWT' => 'Northwest Territories',
		'NVT' => 'Nunavut'
		);

	// --------------------------------------------------------------------------

	/**
	 * Output form input
	 *
	 * @param	array
	 * @param	array
	 * @return	string
	 */
	public function form_output($data, $entry_id, $field)
	{
		// Default is abbr for backwards compat.
		if ( ! isset($data['custom']['provinces_display']))
		{
			$data['custom']['provinces_display'] = 'abbr';
		}

		// Value
		// We only use the default value if this is a new
		// entry.
		if ( ! $data['value'] and ! $entry_id)
		{
			$value = (isset($field->field_data['default_provinces'])) ? $field->field_data['default_provinces'] : null;
		}
		else
		{
			$value = $data['value'];
		}

		return form_dropdown($data['form_slug'], $this->provinces($field->is_required, $data['custom']['provinces_display']), $value, 'id="'.$data['form_slug'].'"');
	}

	// --------------------------------------------------------------------------

	/**
	 * Pre Output for Plugin
	 *
	 * Has two options:
	 *
	 * - abbr
	 * - full
	 *
	 * @param	array
	 * @param	array
	 * @return	string
	 */
	public function pre_output_plugin($input, $data)
	{
		if ( ! $input) return null;

		return array(
			'abbr'	=> $input,
			'full' 	=> $this->raw_provinces[$input]
			);
	}

	// --------------------------------------------------------------------------

	/**
	 * Output form input
	 *
	 * @param	array
	 * @param	array
	 * @return	string
	 */
	public function pre_output($input, $data)
	{
		// Default is abbr for backwards compat.
		if( ! isset($data['provinces_display']) ):

			$data['provinces_display'] = 'abbr';

		endif;

		$provinces = $this->provinces('yes', $data['provinces_display']);

		return ( isset($provinces[$input]) ) ? $provinces[$input] : null;
	}

	// --------------------------------------------------------------------------

	/**
	 * Do we want the provinces full name of abbreviation?
	 *
	 * @access	public
	 * @return	string
	 */
	public function param_provinces_display($value = null)
	{
		$options = array(
			'full' => $this->CI->lang->line('streams:provinces.full'),
			'abbr' => $this->CI->lang->line('streams:provinces.abbr')
			);

		return form_dropdown('provinces_display', $options, $value);
	}

	// --------------------------------------------------------------------------

	/**
	 * Default Country Parameter
	 *
	 * @access 	public
	 * @return 	string
	 */
	public function param_default_provinces($input)
	{
		// Return a drop down of countries
		// but we don't require them to give one.
		return form_dropdown('default_provinces', $this->provinces('no', 'full'), $input);
	}

	// --------------------------------------------------------------------------

	/**
	 * provinces
	 *
	 * Returns an array of provinces
	 *
	 * @access	private
	 * @return	array
	 */
	private function provinces($is_required, $provinces_display = 'abbr')
	{
		if( $provinces_display != 'abbr' and $provinces_display != 'full') $provinces_display = 'abbr';
		$choices = array();
		if($is_required == 'no') $choices[null] = get_instance()->config->item('dropdown_choose_null');
		$provinces = array();
		if($provinces_display == 'abbr'):
			foreach($this->raw_provinces as $abbr => $full):
				$provinces[$abbr] = $abbr;
			endforeach;
			else:
				$provinces = $this->raw_provinces;
			endif;
			return array_merge($choices, $provinces);
		}

	}