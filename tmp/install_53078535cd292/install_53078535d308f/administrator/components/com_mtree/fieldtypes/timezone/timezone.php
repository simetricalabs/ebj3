<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_timeZone extends mFieldType {
	function getInputHTML() {
		$timezone = array (
			JHtml::_('select.option', -12, JText::_('(UTC -12:00) International Date Line West')),
			JHtml::_('select.option', -11, JText::_('(UTC -11:00) Midway Island, Samoa')),
			JHtml::_('select.option', -10, JText::_('(UTC -10:00) Hawaii')),
			JHtml::_('select.option', -9.5, JText::_('(UTC -09:30) Taiohae, Marquesas Islands')),
			JHtml::_('select.option', -9, JText::_('(UTC -09:00) Alaska')),
			JHtml::_('select.option', -8, JText::_('(UTC -08:00) Pacific Time (US &amp; Canada)')),
			JHtml::_('select.option', -7, JText::_('(UTC -07:00) Mountain Time (US &amp; Canada)')),
			JHtml::_('select.option', -6, JText::_('(UTC -06:00) Central Time (US &amp; Canada), Mexico City')),
			JHtml::_('select.option', -5, JText::_('(UTC -05:00) Eastern Time (US &amp; Canada), Bogota, Lima')),
			JHtml::_('select.option', -4.5, JText::_('(UTC -04:30) Venezuela')),
			JHtml::_('select.option', -4, JText::_('(UTC -04:00) Atlantic Time (Canada), Caracas, La Paz')),
			JHtml::_('select.option', -3.5, JText::_('(UTC -03:30) St. John\'s, Newfoundland, Labrador')),
			JHtml::_('select.option', -3, JText::_('(UTC -03:00) Brazil, Buenos Aires, Georgetown')),
			JHtml::_('select.option', -2, JText::_('(UTC -02:00) Mid-Atlantic')),
			JHtml::_('select.option', -1, JText::_('(UTC -01:00) Azores, Cape Verde Islands')),
			JHtml::_('select.option', 0, JText::_('(UTC 00:00) Western Europe Time, London, Lisbon, Casablanca')),
			JHtml::_('select.option', 1, JText::_('(UTC +01:00) Amsterdam, Berlin, Brussels, Copenhagen, Madrid, Paris')),
			JHtml::_('select.option', 2, JText::_('(UTC +02:00) Istanbul, Jerusalem, Kaliningrad, South Africa')),
			JHtml::_('select.option', 3, JText::_('(UTC +03:00) Baghdad, Riyadh, Moscow, St. Petersburg')),
			JHtml::_('select.option', 3.5, JText::_('(UTC +03:30) Tehran')),
			JHtml::_('select.option', 4, JText::_('(UTC +04:00) Abu Dhabi, Muscat, Baku, Tbilisi')),
			JHtml::_('select.option', 4.5, JText::_('(UTC +04:30) Kabul')),
			JHtml::_('select.option', 5, JText::_('(UTC +05:00) Ekaterinburg, Islamabad, Karachi, Tashkent')),
			JHtml::_('select.option', 5.5, JText::_('(UTC +05:30) Bombay, Calcutta, Madras, New Delhi, Colombo')),
			JHtml::_('select.option', 5.75, JText::_('(UTC +05:45) Kathmandu')),
			JHtml::_('select.option', 6, JText::_('(UTC +06:00) Almaty, Dhaka')),
			JHtml::_('select.option', 6.30, JText::_('(UTC +06:30) Yagoon')),
			JHtml::_('select.option', 7, JText::_('(UTC +07:00) Bangkok, Hanoi, Jakarta')),
			JHtml::_('select.option', 8, JText::_('(UTC +08:00) Beijing, Perth, Singapore, Hong Kong')),
			JHtml::_('select.option', 8.75, JText::_('(UTC +08:00) Western Australia')),
			JHtml::_('select.option', 9, JText::_('(UTC +09:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk')),
			JHtml::_('select.option', 9.5, JText::_('(UTC +09:30) Adelaide, Darwin, Yakutsk')),
			JHtml::_('select.option', 10, JText::_('(UTC +10:00) Eastern Australia, Guam, Vladivostok')),
			JHtml::_('select.option', 10.5, JText::_('(UTC +10:30) Lord Howe Island (Australia)')),
			JHtml::_('select.option', 11, JText::_('(UTC +11:00) Magadan, Solomon Islands, New Caledonia')),
			JHtml::_('select.option', 11.30, JText::_('(UTC +11:30) Norfolk Island')),
			JHtml::_('select.option', 12, JText::_('(UTC +12:00) Auckland, Wellington, Fiji, Kamchatka')),
			JHtml::_('select.option', 12.75, JText::_('(UTC +12:45) Chatham Island')),
			JHtml::_('select.option', 13, JText::_('(UTC +13:00) Tonga')),
			JHtml::_('select.option', 14, JText::_('(UTC +14:00) Kiribati'))
		);

		return JHtml::_('select.genericlist',  $timezone, $this->getInputFieldName(1), 'size="1" id="'.$this->getInputFieldName(1).'"', 'value', 'text', $this->getInputValue());
	}

	function parseValue($value)
	{
		if( is_numeric($value) )
		{
			return $value;
		} else {
			return 0;
		}
	}

	function getOutput($view=1)
	{
		$value = (int) $this->getValue();
		$return = number_format( $value, 2, ':', '' );
		if( $value > 0 ) {
			$return = 'UTC +' . $return;
		} else{
			$return = 'UTC ' . $return;
		}
		return $return;
	}
}
?>