<?php

namespace DesInventar\Common;

class Version
{
    protected $majorVersion = '10';
    protected $version = '10.01.011';
    protected $releaseDate = '2017-07-24';
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
        $output = exec('/usr/bin/git rev-parse --short HEAD');
        if (empty($output)) {
            $output = time();
        }
        return $output;
    }
}
