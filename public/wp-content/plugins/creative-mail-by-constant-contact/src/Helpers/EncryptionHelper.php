<?php


namespace CreativeMail\Helpers;

use CreativeMail\Managers\RaygunManager;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\BadFormatException;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Key;
use Exception;

class EncryptionHelper
{

    /**
     * Will get the previously used encryption key, or will generate a new key of no key is present.
     *
     * @return Key
     * @throws BadFormatException
     * @throws EnvironmentIsBrokenException
     */
    private static function get_encryption_key()
    {
        $key = get_option(CE4WP_ENCRYPTION_KEY_KEY, null);
        if ($key === null) {
            $key = Key::createNewRandomKey();
            update_option(CE4WP_ENCRYPTION_KEY_KEY, $key->saveToAsciiSafeString());
        }
        else {
            if (is_string($key)) {
                $key = Key::loadFromAsciiSafeString($key);
            }
        }

        return $key;
    }

    /**
     * Will update an existing option or create the option if it is not available.
     *
     * @param $option   string The name of the option.
     * @param $value    mixed  The value that should be stored encrypted
     * @param $autoload bool Should this option be auto loaded.
     *
     * @return bool
     * @throws BadFormatException
     * @throws EnvironmentIsBrokenException
     */
    public static function update_option($option, $value, $autoload = null)
    {
        return update_option($option, Crypto::encrypt($value, self::get_encryption_key()), $autoload);
    }

    /**
     * Will store and encrypt the option.
     *
     * @param $option   string The name of the option.
     * @param $value    mixed  The value that should be stored encrypted
     * @param $autoload bool Should this option be auto loaded.
     *
     * @throws BadFormatException
     * @throws EnvironmentIsBrokenException
     */
    public static function add_option($option, $value, $autoload = true)
    {
        add_option($option, Crypto::encrypt($value, self::get_encryption_key()), '', $autoload);
    }

    /**
     * Will load and decrypt the option.
     *
     * @param $option  string The name of the option you want to load.
     * @param bool $default The fallback value that should be used when the option is not available.
     *
     * @return mixed
     */
    public static function get_option($option, $default = false)
    {
        $encrypted = get_option($option, $default);
        if ($encrypted === $default ) {
            return $default;
        } else {
            try {
                if (is_string($encrypted)) {
                    return Crypto::decrypt($encrypted, self::get_encryption_key());
                }
            } catch ( Exception $e ) {
                RaygunManager::get_instance()->exception_handler($e);
            }
        }
        return $encrypted;
    }
}
