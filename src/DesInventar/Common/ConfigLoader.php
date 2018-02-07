<?php

namespace DesInventar\Common;

class ConfigLoader
{
    public $options = array();

    public function __construct($configDir)
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
        $path = getcwd();
        do {
            $testPath = $path . '/config';
            if (file_exists($testPath)) {
                return $testPath;
            }
            $path = dirname($path);
        } while ($path);
        return '';
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

    public function &__get($key)
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }
        trigger_error("Key $key does not exist", E_USER_NOTICE);
        return false;
    }

    public function __set($key, $value)
    {
        $this->options[$key] = $value;
    }
}
