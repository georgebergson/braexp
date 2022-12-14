<?php


namespace CreativeMail\Managers;

use Exception;
use Raygun4php\RaygunClient;

/**
 * The RaygunManager will manage the admin section of the plugin.
 *
 * @package CreativeMail\Managers
 */
final class RaygunManager
{
    // Let's make this a singleton.
    private static $instance;
    private $raygun_client;

    /**
     * RaygunManager constructor.
     */
    public function __construct()
    {
        $this->raygun_client = new RaygunClient(CE4WP_RAYGUN_PHP_KEY);
    }

    /**
     * Transmits an error to the Raygun.io API
     *
     * @param int    $err_no          The error number
     * @param string $err_str         The error string
     * @param string $err_file        The file the error occurred in
     * @param int    $err_line        The line the error occurred on
     */
    function error_handler($err_no, $err_str, $err_file, $err_line)
    {
        $this->raygun_client->SendError($err_no, $err_str, $err_file, $err_line, self::build_tags(), self::build_custom_user_data());
    }

    /**
     * Transmits an exception to the Raygun.io API
     *
     * @param Exception $exception      An exception object to transmit
     */
    function exception_handler($exception)
    {
        $this->raygun_client->SendException($exception, self::build_tags(), self::build_custom_user_data());
    }

    /**
     * Builds the tags to be sent to Raygun.io
     *
     * @throws Exception
     */
    function build_tags()
    {
        $tags = [];
        $tags['CE4WP_PLUGIN_VERSION'] = CE4WP_PLUGIN_VERSION;
        $tags['CE4WP_ENVIRONMENT'] = CE4WP_ENVIRONMENT;
        $tags['CE4WP_BUILD'] = CE4WP_BUILD_NUMBER;
        return $tags;
    }

    /**
     * Builds the custom user data to be sent to Raygun.io
     *
     * @throws Exception
     */
    function build_custom_user_data()
    {
        $userData = [];

        try {
            // Get as much metadata as possible.
            $userData['CE4WP_APP_URL'] = CE4WP_APP_URL;
            $userData['CE4WP_APP_GATEWAY_URL'] = CE4WP_APP_GATEWAY_URL;

            // User data that helps us identify the error.
            $userData['CE4WP_CONNECTED_ACCOUNT_ID'] = get_option(CE4WP_CONNECTED_ACCOUNT_ID);
            $userData['CE4WP_INSTANCE_UUID_KEY'] = get_option(CE4WP_INSTANCE_UUID_KEY);
            $userData['CE4WP_MANAGED_EMAIL_NOTIFICATIONS'] = get_option(CE4WP_MANAGED_EMAIL_NOTIFICATIONS);
            $userData['CE4WP_ACTIVATED_PLUGINS'] = get_option(CE4WP_ACTIVATED_PLUGINS);

        } catch (Exception $e) {
            // We don't want to throw an exception here, as this is just a helper function.
            // We'll just log the error and move on.
            error_log($e->getMessage());
        }

        return $userData;
    }

    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new RaygunManager();
        }

        return self::$instance;
    }
}
