<?php
/**
 * Created by PhpStorm.
 * User: Martijn
 * Date: 2020-02-10
 * Time: 13:42
 */

namespace CreativeMail\Modules\Contacts\Handlers;

define('CE4WP_WC_EVENTTYPE', 'WordPress - WooCommerce');

use CreativeMail\Managers\RaygunManager;
use CreativeMail\Modules\Contacts\Models\ContactAddressModel;
use CreativeMail\Modules\Contacts\Models\ContactModel;
use Exception;

class WooCommercePluginHandler extends BaseContactFormPluginHandler
{
    const CHECKOUT_CONSENT_CHECKBOX_ID = 'ce4wp_checkout_consent_checkbox';
    const CHECKOUT_CONSENT_CHECKBOX_VALUE = 'ce4wp_checkout_consent';
    const CHECKOUT_CONSENT_CHECKBOX_VALUE_OLD = 'ce_checkout_consent';
    var $isSync = false;

    public function convertToContactModel($orderId)
    {
        $all_orders = null;
        $contactModel = new ContactModel();
        $products_detail = get_post_meta($orderId);
        $order = wc_get_order($orderId);

        if (is_object($order) && is_a($order, 'WC_Order')) {
            $all_orders = wc_get_orders(array('email' => $order->get_billing_email()));
        }

        $number_of_orders =  count(is_array($all_orders) ? $all_orders : array());

        if (isset($products_detail)) {
            if (!empty($products_detail["_billing_email"]) && isset($products_detail["_billing_email"][0]) && !empty($products_detail["_billing_email"][0])) {
                $contactModel->setEmail($products_detail["_billing_email"][0]);
            } else {
                return $contactModel;
            }

            if (!empty($products_detail["_billing_first_name"])) {
                $contactModel->setFirstName($products_detail["_billing_first_name"][0]);
            }
            if (!empty($products_detail["_billing_last_name"])) {
                $contactModel->setLastName($products_detail["_billing_last_name"][0]);
            }

            $contactAddress = $this->getContactAddressFromOrder($products_detail);

            if (!empty($contactAddress)) {
                $contactModel->setContactAddress($contactAddress);
            }

            if (!empty($contactModel->getEmail())) {
                $contactModel->setEventType(CE4WP_WC_EVENTTYPE);
                $contactModel->setOptActionBy(2);
                $contactModel->setOptIn($this->isSync);
                $contactModel->setOptOut(false);
            }

            if (!empty($products_detail["_billing_phone"])) {
                $contactModel->setPhone($products_detail["_billing_phone"][0]);
            }

            if (!empty($number_of_orders)) {
                $contactModel->setNumberOfOrders($number_of_orders);
            }

            $this->setConsentValues($contactModel, $products_detail);
        }
        return $contactModel;
    }

    function setConsentValues($contactModel, $products_detail){
        $checkbox_value = null;

        if (!empty($_POST[self::CHECKOUT_CONSENT_CHECKBOX_ID])) {
            $checkbox_value = esc_attr($_POST[self::CHECKOUT_CONSENT_CHECKBOX_ID]);
        } else if (!empty($products_detail[self::CHECKOUT_CONSENT_CHECKBOX_ID])) {
            $checkbox_value = $products_detail[self::CHECKOUT_CONSENT_CHECKBOX_ID];
        } else if (!empty($_POST[self::CHECKOUT_CONSENT_CHECKBOX_VALUE])) {
            $checkbox_value = esc_attr($_POST[self::CHECKOUT_CONSENT_CHECKBOX_VALUE]);
        } else if (!empty($products_detail[self::CHECKOUT_CONSENT_CHECKBOX_VALUE])) {
            $checkbox_value = $products_detail[self::CHECKOUT_CONSENT_CHECKBOX_VALUE][0]; //this value appears to be in array;
        } else if (!empty($products_detail[self::CHECKOUT_CONSENT_CHECKBOX_VALUE_OLD])) {
            $checkbox_value = $products_detail[self::CHECKOUT_CONSENT_CHECKBOX_VALUE_OLD][0]; //this value appears to be in array;
        }

        if (!is_null($checkbox_value)) {
            $contactModel->setOptActionBy(1);
            if ($checkbox_value == true){
                $contactModel->setOptIn(true);
            } elseif ($checkbox_value == false){
                $contactModel->setOptIn(false);
            }
        }
    }

    function getContactAddressFromOrder($products_detail)
    {
        $contactAddress = new ContactAddressModel();

        if (isset($products_detail)) {
            if (!empty($products_detail["_billing_address_1"])) {
                $contactAddress->setAddress($products_detail["_billing_address_1"][0]);
            }
            if (!empty($products_detail["_billing_address_2"])) {
                $contactAddress->setAddress2($products_detail["_billing_address_2"][0]);
            }
            if (!empty($products_detail["_billing_city"])) {
                $contactAddress->setCity($products_detail["_billing_city"][0]);
            }
            if (!empty($products_detail["_billing_country"])) {
                $contactAddress->setCountryCode($products_detail["_billing_country"][0]);
            }
            if (!empty($products_detail["_billing_postcode"])) {
                $contactAddress->setPostalCode($products_detail["_billing_postcode"][0]);
            }
            if (!empty($products_detail["_billing_state"])) {
                $contactAddress->setStateCode($products_detail["_billing_state"][0]);
            }
        }
        return $contactAddress;
    }

    public function ceHandlerWooCommerceNewOrder($order_id)
    {
        try {
            $order = wc_get_order($order_id);
            if (is_object($order) && is_a($order, 'WC_Order')) {
                $this->upsertContact($this->convertToContactModel($order->get_id()));
            }
        } catch (Exception $exception) {
            RaygunManager::get_instance()->exception_handler($exception);
        }
    }

    public function registerHooks()
    {
        add_action('woocommerce_checkout_order_created', array($this, 'ceHandlerWooCommerceNewOrder'), 10, 1);
    }

    public function unregisterHooks()
    {
        remove_action('woocommerce_checkout_order_created', array($this, 'ceHandlerWooCommerceOrder'));
    }

    public function get_contacts($limit = null)
    {
        if (!is_int($limit) || $limit <= 0) {
            $limit = null;
        }

        $backfillArray = array();

        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'shop_order',
            'post_status' => array_keys(wc_get_order_statuses())
        );

        if ($limit != null) {
            $args['posts_per_page'] = $limit;
        }

        $products_orders = get_posts($args);

        foreach ($products_orders as $products_order) {

            $contactModel = null;
            try {
                $this->isSync = true;
                $contactModel = $this->convertToContactModel($products_order->ID);
            } catch (Exception $exception) {
                // silent exception
                continue;
            }

            if (!empty($contactModel->getEmail())) {
                array_push($backfillArray, $contactModel);
            }
        }

        if (!empty($backfillArray)) {
            return $backfillArray;
        }

        return null;
    }

    function __construct()
    {
        parent::__construct();
    }
}
