<?php

namespace DesInventar\Common;

use DesInventar\Common\Util;

class MapServer
{
    const SERVER_PORT = 'SERVER_PORT';
    const MAPID = 'MAPID';

    protected $defaultOptions = array(
        'SERVICE' => 'WMS',
        'VERSION' => '1.1.1',
        'SRS' => 'EPSG:900913',
        'FORMAT' => 'image/png',
        'REQUEST' => 'GetMap',
        'STYLES' => 'default,default',
    );
    protected $config = null;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getMapImageFormat()
    {
        return $this->defaultOptions['FORMAT'];
    }

    public function formatOptions($mapOptions)
    {
        $options = array_merge($this->defaultOptions, $mapOptions);
        if (empty($options[self::MAPID]) || $options[self::MAPID] == 'worldmap') {
            return array_merge($options, array(
                'MAP' => $this->config->maps['worldmap_dir'] . '/world_adm0.map',
                'LAYERS' => 'base',
                'TRANSPARENT' => 'false',
            ));
        }
        $options = array_merge($options, array(
            'MAP' => $this->config->maps['tmp_dir'] .'/map_' . $options[self::MAPID] . '.map',
            'TRANSPARENT' => 'true',
        ));
        unset($options[self::MAPID]);
        return $options;
    }

    public function getQueryString($options)
    {
        $options = $this->formatOptions($options);
        $queryString = '';
        foreach ($options as $key => $value) {
            if ($queryString != '') {
                $queryString .= '&';
            }
            $queryString .= $key . '=' . urlencode($value);
        }
        return $queryString . '&mde=map';
    }

    public function getBaseMapServerUrl()
    {
        // This is a call to mapserver through cgi-bin from inside the host
        $url = 'http://127.0.0.1';
        $suffix = '/cgi-bin/' . $this->config->maps['mapserver'];
        if (file_exists('/.dockerenv')) {
            // We are running inside a docker container, we have to assume the
            // local port is 80
            return $url . ':80' . $suffix;
        }
        $util = new Util();
        // We check to see if apache if using a different port
        if (! $util->isSslConnection() && (isset($_SERVER[self::SERVER_PORT]) && ($_SERVER[self::SERVER_PORT] != 80))) {
            return $url . ':' . $_SERVER[self::SERVER_PORT] . $suffix;
        }
    }

    public function getMapServerUrl($queryString)
    {
        $url = $this->getBaseMapServerUrl();
        return $url . '?' . $queryString;
    }

    public function hex2dec($prmColor)
    {
        $oHex = str_split(substr($prmColor, -6), 2);
        return hexdec($oHex[0]) . ' ' . hexdec($oHex[1]) . ' ' . hexdec($oHex[2]);
    }

    // set hash with limits, legends and colors
    public function setRanges($lim, $leg, $col)
    {
        if (count($lim) < 1) {
            return [];
        }
        $lmx = '10000000';
        $maxr = false;
        // First range is No data
        $range = [0 => [0, '= 0', '255 255 255']];
        // generate range hash with limit, legend and color
        for ($j = 0; $j < count($lim); $j++) {
            if (! isset($lim[$j])) {
                continue;
            }
            $limitValue = $lim[$j];
            if (empty($lim[$j])) {
                $limitValue = $lmx;
                $maxr = true;
            }
            $range[$j+1] = array($limitValue, $leg[$j], $this->hex2dec($col[$j]));
        }
        // if not assigned, set last range between last number and infinit
        if (!$maxr) {
            $range[$j+1] = array($lmx, (int)$lim[$j-1] + 1 . ' -> ', '30 30 30');
        }
        return $range;
    }
}
