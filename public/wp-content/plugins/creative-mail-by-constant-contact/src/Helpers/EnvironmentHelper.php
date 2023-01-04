<?php declare(strict_types = 1);


namespace CreativeMail\Helpers;

use CreativeMail\Constants\EnvironmentNames;

/**
 * Class EnvironmentHelper
 *
 * @package CreativeMail\Helpers
 */
class EnvironmentHelper
{
    /**
     * Determines if the plugin is currently pointing towards a test environment.
     *
     * @returns bool
     */
    public static function is_test_environment()
    {
        return self::get_environment() !== EnvironmentNames::PRODUCTION;
    }

    /**
     * Gets the name of the environment this version of the plugin is build for.
     *
     * @return string
     */
    public static function get_environment()
    {
        $environment = CE4WP_ENVIRONMENT;

        if ($environment === "{ENV}") {
            $environment = EnvironmentNames::DEVELOPMENT;
        }

        return $environment;
    }

    /**
     * Gets the url of the app-gateway.
     *
     * @param string $path
     *
     * @return string
     */
    public static function get_app_gateway_url($path = '')
    {
        $url = CE4WP_APP_GATEWAY_URL;

        if ($url === '{GATEWAY_URL}') {
            $url = 'https://app-gateway.creativemail.com/';
        }
        if (!empty($path)) {
            $url .= $path;
        }

        return $url;
    }

    /**
     * Gets the url of the app.
     *
     * @return string
     */
    public static function get_app_url()
    {
        $url = CE4WP_APP_URL;
        if ($url === '{APP_URL}') {
            $url = 'https://app.creativemail.com/';
        }

        return $url;
    }
}
