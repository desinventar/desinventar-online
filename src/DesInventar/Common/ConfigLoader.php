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
    private function __construct($configDir)
    {
        if (empty($configDir)) {
            $configDir = $this->getConfigDirFromEnv();
        }
        if (empty($configDir) || !file_exists($configDir)) {
            throw new \Exception('Configuration directory doesn\'t exist: ' . $configDir);
        }
        $defaultConfigFile = $configDir . '/default.json';
        if (!file_exists($defaultConfigFile)) {
            throw new \Exception('Cannot find default configuration file: ' . $defaultConfigFile);
        }
        // Load default configuration values
        $this->options = $this->readConfigFile($defaultConfigFile);

        $localName = $this->getConfigFile();
        if (!empty($localName)) {
            $localFile = $configDir . '/' . $localName;
            if (file_exists($localFile)) {
                $localOptions = $this->readConfigFile($localFile);
                $this->options = array_replace_recursive($this->options, $localOptions);
            }
        }

        $envFile = $configDir . '/custom-environment-variables.json';
        if (file_exists($envFile)) {
            $customConfig = $this->applyEnvVarsToArray($this->readConfigFile($envFile));
            $this->options = array_replace_recursive($this->options, $customConfig);
        }
        return true;
    }

    public function getConfigDirFromEnv()
    {
        if (!empty(getenv('NODE_CONFIG_DIR'))) {
            return getenv('NODE_CONFIG_DIR');
        }
        return getcwd() . '/config';
    }

    public function getConfigFile()
    {
        if (!empty(getenv('NODE_ENV'))) {
            return strtolower(getenv('NODE_ENV')) . '.json';
        }
        if (!empty(getenv('APP_ENV'))) {
            return strtolower(getenv('APP_ENV')) . '.json';
        }
        return '';
    }

    private function readConfigFile($filepath)
    {
        return json_decode(file_get_contents($filepath), true);
    }

    private function extractValuesFromArray($parentKey, $data)
    {
        $values = [];
        foreach ($data as $key => $item) {
            $totalKey = empty($parentKey) ? $key : $parentKey . '/' . $key;
            if (is_array($item)) {
                $values = array_merge($values, $this->extractValuesFromArray($totalKey, $item));
                continue;
            }
            $values[$totalKey] = getenv($item);
        }
        return $values;
    }

    private function setArrayValue(&$data, $key, $value)
    {
        $parts = explode('/', $key);
        if (count($parts) > 1) {
            return $this->setArrayValue($data[$parts[0]], implode('/', array_slice($parts, 1)), $value);
        }
        if (empty($value)) {
            unset($data[$parts[0]]);
            return false;
        }
        return $data[$parts[0]] = $value;
    }

    private function applyEnvVarsToArray($data)
    {
        $values = $this->extractValuesFromArray('', $data);
        foreach ($values as $key => $value) {
            $this->setArrayValue($data, $key, $value);
        }
        return $data;
    }

    private function __clone()
    {
        //@TODO: Implement this function
    }

    public static function getInstance($configDir)
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($configDir);
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
