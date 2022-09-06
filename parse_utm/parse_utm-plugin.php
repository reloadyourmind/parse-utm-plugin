<?php
/**
 *
 * @link              https://vk.com/reloadyourmind
 * @since             1.0.0
 * @package           Parse_utm
 *
 * @wordpress-plugin
 * Plugin Name:       ParseUTM
 * Plugin URI:        https://vk.com/reloadyourmind
 * Description:       Small Wordpress plugin which uses to parse UTM string from URL and print some data to browser console
 * Version:           1.0.0
 * Author:            Alexey Timchenko
 * Author URI:        https://vk.com/reloadyourmind
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       parse_utm
 */

defined('ABSPATH') or die('You have no access to run this file');

class ParseUTMPlugin
{
    private const UTM_NAME = 'utm_tack_id';
    private const CURRENT_VERSION = '1.0.0';

    public function __construct()
    {
        $this->setupVars();
        $this->initScripts();
    }

    private function setupVars()
    {
        $this->version = self::CURRENT_VERSION;
        $this->file = __FILE__;
        $this->pluginUrl = plugin_dir_url($this->file);
        $this->assetsUrl = trailingslashit($this->pluginUrl . 'assets');
        $this->pluginJsUrl = trailingslashit($this->assetsUrl . 'js');
    }

    private function initScripts()
    {
        add_action('init', array(
            $this,
            'requestHanlder'
        ));
        add_action('wp_enqueue_scripts', array(
            $this,
            'includeCustomScript'
        ));
    }

    private function isIssetCookie()
    {
        if (isset($_COOKIE[self::UTM_NAME]))
        {
            return true;
        }
        return false;
    }

    private function setCookieData(array $data)
    {
        if (!$this->isIssetCookie())
        {
            setcookie(self::UTM_NAME, $data[self::UTM_NAME]);
        }
    }

    private function getUserOS()
    {
        preg_match('/\((.+?)\)/', $_SERVER['HTTP_USER_AGENT'], $matches);
        return $matches[1];
    }

    private function getUserData()
    {
        return ['ip' => $_SERVER['REMOTE_ADDR'], 'os' => $this->getUserOS() ];
    }

    public function activate()
    {
        // Some action when plugin has been activated
        
    }

    public function deactivate()
    {
        // Some action when plugin has been deactivated
        
    }

    public function requestHanlder()
    {
        if ($_SERVER['QUERY_STRING'] !== '')
        {
            parse_str($_SERVER['QUERY_STRING'], $queryArray);
            if (array_key_exists(self::UTM_NAME, $queryArray))
            {
                $this->setCookieData([self::UTM_NAME => $queryArray[self::UTM_NAME]]);
            }
        }
    }

    public function includeCustomScript()
    {
        if ($this->isIssetCookie())
        {
            wp_enqueue_script('print-userdata-script', $this->pluginJsUrl . 'script.js', self::CURRENT_VERSION, true);
            wp_localize_script('print-userdata-script', 'userData', $this->getUserData());
        }
    }
}

if (class_exists('ParseUTMPlugin'))
{
    $parseUTMPlugin = new ParseUTMPlugin();
}

register_activation_hook(__FILE__, array($parseUTMPlugin, 'activate'));
register_deactivation_hook(__FILE__, array($parseUTMPlugin, 'deactivate'));

