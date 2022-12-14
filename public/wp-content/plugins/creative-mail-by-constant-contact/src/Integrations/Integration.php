<?php

namespace CreativeMail\Integrations;

use CreativeMail\Modules\Contacts\Handlers\BaseContactFormPluginHandler;

/**
 * Class Integration
 *
 * Describes an integration between Creative Mail and WordPress.
 *
 * @package CreativeMail\Integrations
 */
class Integration
{
    private $name;
    private $class;
    private $integrationHandler;
    private $slug;
    private $hide_from_suggestions;
    private $url;
    private $has_multiple_plugins;
    private $hide_from_active_list;

    /**
     * Integration constructor.
     *
     * @param $slug                  string The slug that you want to use for this integration.
     * @param $name                  string The display name of the plugin
     * @param $class                 string The path the plugin class that should be used to check if the plugin required for this integration is installed.
     * @param $integration_handler   object The class that should be instantiated when this integration gets activated.
     * @param $hide_from_suggestions boolean If you want to hide this plugin from the suggestion list, set this to true
     * @param $url                   string The link to the plugin store, default will set it based on the slug
     * @param $has_multiple_plugins  boolean If true the plugin will search using the basename (some plugins have different directories depending on license)
     * @param $hide_from_active_list boolean If you want to hide this plugin from the active plugins list, set this to true
     */
    public function __construct($slug, $name, $class, $integration_handler, $hide_from_suggestions, $url = null, $has_multiple_plugins = false, $hide_from_active_list = false)
    {
        $this->slug = $slug;
        $this->name = $name;
        $this->class = $class;
        $this->integrationHandler = $integration_handler;
        $this->hide_from_suggestions = $hide_from_suggestions;
        $this->url = is_null($url) ? admin_url("plugin-install.php?tab=plugin-information&plugin=$slug&TB_iframe=true&width=772&height=1144") : $url;
        $this->has_multiple_plugins = $has_multiple_plugins;
        $this->hide_from_active_list = $hide_from_active_list;
    }

    /**
     * Gets the slug assigned to this integration.
     *
     * @return string
     */
    public function get_slug()
    {
        return $this->slug;
    }

    /**
     * Gets the display name assigned to this integration.
     *
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Gets the path to the main class of the plugin that is required for this integration.
     *
     * @return string
     */
    public function get_class()
    {
        return $this->class;
    }

    /**
     * Gets the name of the class that should be instantiated when activating this integration.
     *
     * @return object
     */
    public function get_integration_handler()
    {
        return $this->integrationHandler;
    }

    /**
     * Gets if this integration should be hidden from the suggestion list
     *
     * @return bool
     */
    public function is_hidden_from_suggestions()
    {
        return $this->hide_from_suggestions;
    }

    /**
     * Gets if this integration should be hidden from the active plugins list
     *
     * @return bool
     */
    public function is_hidden_from_active_list()
    {
        return $this->hide_from_active_list;
    }

    /**
     * Gets the market url of the plugin
     *
     * @return bool
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     * Use basename if integration has multiple plugins with different directories
     *
     * @return bool
     */
    public function use_basename()
    {
        return $this->has_multiple_plugins;
    }
}
