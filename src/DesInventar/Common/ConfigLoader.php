<?php
/**
 * ConfigLoader Class
 * Based on: http://codereview.stackexchange.com/questions/4162/php-config-file-loader-class
 *
 * example usage
 * $config = Config::getInstance(PATH TO FILE, FILE TYPE);
 * echo $config->ip;
 * echo $config->db['host'];
 * example array file
 * <?php
 * return array(
 *    'db' => array(
 *     'host' => 'localhost',
 *     'user' => 'user1',
 *     'pass' => 'mypassword'),
 *     'ip' => '123456',
 * )
 */

namespace DesInventar\Common;

class ConfigLoader
{
    private static $instance = null;
    public $options = array();

    /**
     * Retrieves php array file, json file, or ini file and builds array
     * @param string $filepath Full path to where the file is located
     * @param string $type is the type of file.  can be "ARRAY" "JSON" "INI"
     */
    private function __construct($customConfigFileList, $defaultConfigFile, $type = 'php')
    {
        if (!file_exists($defaultConfigFile)) {
            throw new \Exception('Cannot find default configuration file: ' . $defaultConfigFile);
        }
        // Load default configuration values
        $this->options = $this->readConfigFile($defaultConfigFile, $type);

        // Find the first available custom config file and merge
        foreach ($customConfigFileList as $customConfigFile) {
            if (! file_exists($customConfigFile)) {
                continue;
            }

            // Attempt to load a local configuration file which overrides
            // some of the default settings
            $customConfig = $this->readConfigFile($customConfigFile, $type);
            $this->options = array_replace_recursive($this->options, $customConfig);
            break;
        }
        return true;
    }

    private function readConfigFile($filepath, $type)
    {
        $result = array();
        switch ($type) {
            case 'php':
                $result = include $filepath;
                break;

            case 'ini':
                $result = parse_ini_file($filepath, true);
                break;

            case 'json':
                $result = json_decode(file_get_contents($filepath), true);
                break;

            //TO-DO add Database option for settings. Table = id, property, value
            case 'database':
                $result = json_decode(file_get_contents($filepath), true);
                break;
        }
        return $result;
    }

    private function __clone()
    {
        //@TODO: Implement this function
    }

    public static function getInstance($customConfigFileList, $defaultConfigFile, $type = 'php')
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($customConfigFileList, $defaultConfigFile, $type);
        }
        return self::$instance;
    }

    /**
     * Retrieve value with constants being a higher priority
     * @param $key Array Key to get
     */
    public function &__get($key)
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }
        trigger_error("Key $key does not exist", E_USER_NOTICE);
        return false;
    }

    /**
     * Set a new or update a key / value pair
     * @param $key Key to set
     * @param $value Value to set
     */
    public function __set($key, $value)
    {
        $this->options[$key] = $value;
    }
}
