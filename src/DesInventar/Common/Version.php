<?php

namespace DesInventar\Common;

class Version
{
    protected $majorVersion = '10';
    protected $version = '10.02.014';
    protected $releaseDate = '2018-10-06';
    protected $mode = 'devel';

    public function __construct($mode)
    {
        $this->mode = $mode;
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
}
