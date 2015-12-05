<?php

/**
 * @file
 * Class installations to handle configuration forms on Admin UI.
 */

require_once("../../class2.php");

if(!e107::isInstalled('paypal_donation') || !getperms("P"))
{
	header("Location: " . e_BASE . "index.php");
	exit;
}

// [PLUGINS]/paypal_donation/languages/[LANGUAGE]/[LANGUAGE]_admin.php
e107::lan('paypal_donation', true, true);


/**
 * Class paypal_donation_admin.
 */
class paypal_donation_admin extends e_admin_dispatcher
{

	protected $modes = array(
		'main'     => array(
			'controller' => 'paypal_donation_admin_main_ui',
			'path'       => null,
		),
		'donation' => array(
			'controller' => 'paypal_donation_admin_donation_ui',
			'path'       => null,
		),
		'amount'   => array(
			'controller' => 'paypal_donation_admin_amount_ui',
			'path'       => null,
		)
	);

	protected $adminMenu = array(
		'main/prefs'      => array(
			'caption' => LAN_PAYPAL_DONATION_ADMIN_01,
			'perm'    => 'P',
		),
		'donation/list'   => array(
			'caption' => LAN_PAYPAL_DONATION_ADMIN_11,
			'perm'    => 'P',
		),
		'donation/create' => array(
			'caption' => LAN_PAYPAL_DONATION_ADMIN_12,
			'perm'    => 'P',
		),
		'amount/list'     => array(
			'caption' => LAN_PAYPAL_DONATION_ADMIN_24,
			'perm'    => 'P',
		),
		'amount/create'   => array(
			'caption' => LAN_PAYPAL_DONATION_ADMIN_25,
			'perm'    => 'P',
		),
	);

	protected $menuTitle = LAN_PLUGIN_PAYPAL_DONATION_NAME;
}


/**
 * Class paypal_donation_admin_main_ui.
 */
class paypal_donation_admin_main_ui extends e_admin_ui
{

	protected $pluginTitle = LAN_PLUGIN_PAYPAL_DONATION_NAME;
	protected $pluginName  = "paypal_donation";
	protected $preftabs    = array(
		LAN_PAYPAL_DONATION_ADMIN_01,
	);
	protected $prefs       = array(
		'sandbox_mode'       => array(
			'title'      => LAN_PAYPAL_DONATION_ADMIN_02,
			'type'       => 'boolean',
			'data'       => 'int',
			'writeParms' => array(
				0 => LAN_PAYPAL_DONATION_ADMIN_03,
				1 => LAN_PAYPAL_DONATION_ADMIN_04,
			),
			'tab'        => 0,
		),
		'email_sandbox'      => array(
			'title' => LAN_PAYPAL_DONATION_ADMIN_06,
			'type'  => 'text',
			'data'  => 'str',
			'tab'   => 0,
		),
		'email_live'         => array(
			'title' => LAN_PAYPAL_DONATION_ADMIN_05,
			'type'  => 'text',
			'data'  => 'str',
			'tab'   => 0,
		),
		'validate_email'     => array(
			'title'       => LAN_PAYPAL_DONATION_ADMIN_07,
			'description' => LAN_PAYPAL_DONATION_ADMIN_08,
			'type'        => 'boolean',
			'writeParms'  => 'label=yesno',
			'data'        => 'int',
			'tab'         => 0,
		),
		'logging_failed_ipn' => array(
			'title'       => LAN_PAYPAL_DONATION_ADMIN_09,
			'description' => LAN_PAYPAL_DONATION_ADMIN_10,
			'type'        => 'boolean',
			'writeParms'  => 'label=yesno',
			'data'        => 'int',
			'tab'         => 0,
		),
	);
}


/**
 * Class paypal_donation_admin_donation_ui.
 */
class paypal_donation_admin_donation_ui extends e_admin_ui
{

	/**
	 * Could be LAN constant (multi-language support).
	 *
	 * @var string plugin name
	 */
	protected $pluginTitle = LAN_PLUGIN_PAYPAL_DONATION_NAME;

	/**
	 * @var string plugin name
	 */
	protected $pluginName = 'paypal_donation';

	/**
	 * Base event trigger name to be used. Leave blank for no trigger.
	 *
	 * @var string event name
	 */
	protected $eventName = 'paypal-donation';

	protected $table = "paypal_donation";

	protected $pid = "pd_id";

	/**
	 * Default (db) limit value.
	 *
	 * @var integer
	 */
	protected $perPage = 0;

	/**
	 * @var boolean
	 */
	protected $batchDelete = true;

	/**
	 * @var string SQL order, false to disable order, null is default order
	 */
	protected $listOrder = "pd_title ASC";

	/**
	 * @var array UI field data
	 */
	protected $fields = array(
		'checkboxes'       => array(
			'title'   => '',
			'type'    => null,
			'width'   => '5%',
			'forced'  => true,
			'thclass' => 'center',
			'class'   => 'center',
		),
		'pd_id'            => array(
			'title'    => LAN_PAYPAL_DONATION_ADMIN_13,
			'type'     => 'number',
			'width'    => '5%',
			'forced'   => true,
			'readonly' => true,
			'thclass'  => 'center',
			'class'    => 'center',
		),
		'pd_title'         => array(
			'title'    => LAN_PAYPAL_DONATION_ADMIN_14,
			'type'     => 'text',
			'inline'   => true,
			'width'    => 'auto',
			'thclass'  => 'left',
			'readonly' => false,
			'validate' => true,
		),
		'pd_description'   => array(
			'title'     => LAN_PAYPAL_DONATION_ADMIN_15,
			'type'      => 'textarea',
			'inline'    => true,
			'width'     => 'auto',
			'thclass'   => 'left',
			'readParms' => 'expand=...&truncate=150&bb=1',
			'readonly'  => false,
		),
		'pd_custom_amount' => array(
			'title'      => LAN_PAYPAL_DONATION_ADMIN_20,
			'type'       => 'boolean',
			'writeParms' => 'label=yesno',
			'data'       => 'int',
		),
		'pd_goal_date'     => array(
			'title'   => LAN_PAYPAL_DONATION_ADMIN_22,
			'type'    => 'datestamp',
			'inline'  => true,
			'width'   => 'auto',
			'thclass' => 'center',
			'class'   => 'center',
		),
		'pd_goal_amount'   => array(
			'title'   => LAN_PAYPAL_DONATION_ADMIN_21,
			'type'    => 'number',
			'inline'  => true,
			'width'   => 'auto',
			'thclass' => 'center',
			'class'   => 'center',
		),
		'pd_currency'      => array(
			'title'      => LAN_PAYPAL_DONATION_ADMIN_23,
			'type'       => 'dropdown',
			'width'      => 'auto',
			'readonly'   => false,
			'inline'     => true,
			'filter'     => true,
			'writeParms' => array(
				'EUR' => 'EUR',
				'USD' => 'USD',
				'AUD' => 'AUD',
				'CAD' => 'CAD',
				'CZK' => 'CZK',
				'DKK' => 'DKK',
				'HKD' => 'HKD',
				'HUF' => 'HUF', // This currency does not support decimals.
				'ILS' => 'ILS',
				'JPY' => 'JPY', // This currency does not support decimals.
				'MXN' => 'MXN',
				'NOK' => 'NOK',
				'NZD' => 'NZD',
				'PHP' => 'PHP',
				'PLN' => 'PLN',
				'GBP' => 'GBP',
				'RUB' => 'RUB',
				'SGD' => 'SGD',
				'SEK' => 'SEK',
				'CHF' => 'CHF',
				'TWD' => 'TWD', // This currency does not support decimals.
				'THB' => 'THB',
			),
			'readParms'  => array(
				'EUR' => 'EUR',
				'USD' => 'USD',
				'AUD' => 'AUD',
				'CAD' => 'CAD',
				'CZK' => 'CZK',
				'DKK' => 'DKK',
				'HKD' => 'HKD',
				'HUF' => 'HUF', // This currency does not support decimals.
				'ILS' => 'ILS',
				'JPY' => 'JPY', // This currency does not support decimals.
				'MXN' => 'MXN',
				'NOK' => 'NOK',
				'NZD' => 'NZD',
				'PHP' => 'PHP',
				'PLN' => 'PLN',
				'GBP' => 'GBP',
				'RUB' => 'RUB',
				'SGD' => 'SGD',
				'SEK' => 'SEK',
				'CHF' => 'CHF',
				'TWD' => 'TWD', // This currency does not support decimals.
				'THB' => 'THB',
			),
			'thclass'    => 'center',
			'class'      => 'center',
		),
		'pd_status'        => array(
			'title'      => LAN_PAYPAL_DONATION_ADMIN_16,
			'type'       => 'dropdown',
			'width'      => 'auto',
			'readonly'   => false,
			'inline'     => true,
			'batch'      => true,
			'filter'     => true,
			'writeParms' => array(
				1 => LAN_PAYPAL_DONATION_ADMIN_17,
				0 => LAN_PAYPAL_DONATION_ADMIN_18,
			),
			'readParms'  => array(
				1 => LAN_PAYPAL_DONATION_ADMIN_17,
				0 => LAN_PAYPAL_DONATION_ADMIN_18,
			),
			'thclass'    => 'center',
			'class'      => 'center',
		),
		'options'          => array(
			'title'   => LAN_PAYPAL_DONATION_ADMIN_19,
			'type'    => null,
			'width'   => '10%',
			'forced'  => true,
			'thclass' => 'center last',
			'class'   => 'center',
			'sort'    => true,
		),
	);

	/**
	 * @var array default fields activated on List view
	 */
	protected $fieldpref = array(
		'checkboxes',
		'pd_title',
		'pd_description',
		'pd_goal_amount',
		'pd_currency',
		'pd_goal_date',
		'pd_status',
		'options',
	);

	/**
	 * User defined init.
	 */
	public function init()
	{
	}

	/**
	 * User defined pre-create logic, return false to prevent DB query execution.
	 *
	 * @param $new_data
	 * @param $old_data
	 * @return boolean
	 */
	public function beforeCreate($new_data, $old_data)
	{
	}

	/**
	 * User defined after-create logic.
	 *
	 * @param $new_data
	 * @param $old_data
	 * @param $id
	 */
	public function afterCreate($new_data, $old_data, $id)
	{
	}

	/**
	 * User defined pre-update logic, return false to prevent DB query execution.
	 *
	 * @param $new_data
	 * @param $old_data
	 * @return mixed
	 */
	public function beforeUpdate($new_data, $old_data)
	{
	}

	/**
	 * User defined after-update logic.
	 *
	 * @param $new_data
	 * @param $old_data
	 */
	public function afterUpdate($new_data, $old_data, $id)
	{
	}

	/**
	 * User defined pre-delete logic.
	 */
	public function beforeDelete($data, $id)
	{
		return true;
	}

	/**
	 * User defined after-delete logic.
	 */
	public function afterDelete($deleted_data, $id, $deleted_check)
	{
	}

}


/**
 * Class paypal_donation_admin_amount_ui.
 */
class paypal_donation_admin_amount_ui extends e_admin_ui
{

	/**
	 * Could be LAN constant (multi-language support).
	 *
	 * @var string plugin name
	 */
	protected $pluginTitle = LAN_PLUGIN_PAYPAL_DONATION_NAME;

	/**
	 * @var string plugin name
	 */
	protected $pluginName = 'paypal_donation';

	/**
	 * Base event trigger name to be used. Leave blank for no trigger.
	 *
	 * @var string event name
	 */
	protected $eventName = 'paypal-donation-amount';

	protected $table = "paypal_donation_amount";

	protected $pid = "pda_id";

	/**
	 * Default (db) limit value.
	 *
	 * @var integer
	 */
	protected $perPage = 0;

	/**
	 * @var boolean
	 */
	protected $batchDelete = false;

	/**
	 * @var string SQL order, false to disable order, null is default order
	 */
	protected $listOrder = "title ASC";

	/**
	 * @var array UI field data
	 */
	protected $fields = array(
		'checkboxes'   => array(
			'title'   => '',
			'type'    => null,
			'width'   => '5%',
			'forced'  => true,
			'thclass' => 'center',
			'class'   => 'center',
		),
		'pda_id'       => array(
			'title'    => LAN_PAYPAL_DONATION_ADMIN_13,
			'type'     => 'number',
			'width'    => '5%',
			'forced'   => true,
			'readonly' => true,
			'thclass'  => 'center',
			'class'    => 'center',
		),
		'pda_donation' => array(
			'title'      => LAN_PAYPAL_DONATION_ADMIN_26,
			'type'       => 'dropdown',
			'width'      => 'auto',
			'readonly'   => false,
			'inline'     => false,
			'filter'     => true,
			'writeParms' => array(),
			'readParms'  => array(),
			'thclass'    => 'center',
			'class'      => 'center',
		),
		'pda_label'    => array(
			'title'    => LAN_PAYPAL_DONATION_ADMIN_27,
			'type'     => 'text',
			'inline'   => true,
			'width'    => 'auto',
			'thclass'  => 'left',
			'readonly' => false,
			'validate' => true,
		),
		'pda_value'    => array(
			'title'   => LAN_PAYPAL_DONATION_ADMIN_28,
			'type'    => 'number',
			'width'   => 'auto',
			'thclass' => 'center',
			'class'   => 'center',
		),
		'options'      => array(
			'title'   => LAN_PAYPAL_DONATION_ADMIN_19,
			'type'    => null,
			'width'   => '10%',
			'forced'  => true,
			'thclass' => 'center last',
			'class'   => 'center',
			'sort'    => true,
		),
	);

	/**
	 * @var array default fields activated on List view
	 */
	protected $fieldpref = array(
		'checkboxes',
		'pda_id',
		'pda_donation',
		'pda_label',
		'pda_value',
		'options',
	);

	/**
	 * User defined init.
	 */
	public function init()
	{
		$db = e107::getDb();
		$db->select('paypal_donation', 'pd_id, pd_title', 'ORDER BY pd_title ASC', true);

		$options = array();
		while($row = $db->fetch())
		{
			$options[$row['pd_id']] = $row['pd_title'];
		}

		$this->fields['pda_donation']['writeParms'] = $options;
		$this->fields['pda_donation']['readParms'] = $options;
	}

	/**
	 * User defined pre-create logic, return false to prevent DB query execution.
	 *
	 * @param $new_data
	 * @param $old_data
	 * @return mixed
	 */
	public function beforeCreate($new_data, $old_data)
	{
	}

	/**
	 * User defined after-create logic.
	 *
	 * @param $new_data
	 * @param $old_data
	 * @param $id
	 */
	public function afterCreate($new_data, $old_data, $id)
	{
	}

	/**
	 * User defined pre-update logic, return false to prevent DB query execution.
	 *
	 * @param $new_data
	 * @param $old_data
	 * @return boolean
	 */
	public function beforeUpdate($new_data, $old_data)
	{
	}

	/**
	 * User defined after-update logic.
	 *
	 * @param $new_data
	 * @param $old_data
	 */
	public function afterUpdate($new_data, $old_data, $id)
	{
	}

	/**
	 * User defined pre-delete logic.
	 */
	public function beforeDelete($data, $id)
	{
		return true;
	}

	/**
	 * User defined after-delete logic.
	 */
	public function afterDelete($deleted_data, $id, $deleted_check)
	{
	}

}


new paypal_donation_admin();

require_once(e_ADMIN . 'auth.php');
e107::getAdminUI()->runPage();
require_once(e_ADMIN . 'footer.php');
exit;
