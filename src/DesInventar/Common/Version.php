<?php

namespace DesInventar\Common;

class Version
{
    protected $majorVersion;
    protected $version;
    protected $releaseDate;
    protected $mode = 'devel';

    public function __construct($mode)
    {
        $this->mode = $mode;
        $this->readVersion(dirname(dirname(dirname(dirname(__FILE__)))) . '/package.json');
    }

    public function getMajorVersion()
    {
        return $this->majorVersion;
    }

    public function getVersion()
    {
        if ($this->mode !== 'devel') {
            return $this->version;
        }
        return $this->version . '-' . $this->getUrlSuffix();
    }

    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    public function getVersionArray()
    {
        return [
            'major_version' => $this->getMajorVersion(),
            'version' => $this->getVersion(),
            'release_date' => $this->getReleaseDate()
        ];
    }

    public function getUrlSuffix()
    {
        $cmd = '/usr/bin/git';
        if (!file_exists($cmd)) {
            return time();
        }
        $output = shell_exec(sprintf("which %s", escapeshellarg($cmd)));
        if (empty($output)) {
            return time();
        }

        $output = exec($cmd . ' rev-parse --short HEAD');
        if (empty($output)) {
            return time();
        }
        return $output;
    }

    protected function readVersion($filePath)
    {
        $package = $this->readJsonFile($filePath);
        $this->version = $package['version'];
        $this->releaseDate = $package['desinventar']['releaseDate'];
        $versionParts = explode('.', $this->version);
        $this->majorVersion = $versionParts[0];
    }

    private function readJsonFile($filePath)
    {
        $content = file_get_contents($filePath);
        if (!$content) {
            return false;
        }
        return json_decode($content, true);
    }
}
