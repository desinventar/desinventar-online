<?php
namespace DesInventar\Common;

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
            $options['MAP']         = $this->config->maps['worldmap_dir'] . '/world_adm0.map';
            $options['LAYERS']      = 'base';
            $options['TRANSPARENT'] = 'false';
        } else {
            $options['MAP'] = TMP_DIR .'/map_' . $options[self::MAPID] . '.map';
            $options['TRANSPARENT'] = 'true';
            unset($options[self::MAPID]);
        }
        
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

    public function getMapServerUrl($queryString)
    {
        // This is a call to mapserver through cgi-bin from inside the host
        $url = 'http://127.0.0.1';
        if (file_exists('/.dockerenv')) {
            // We are running inside a docker container, we have to assume the
            // local port is 80
            $url .= ':80';
        } else {
            // We check to see if apache if using a different port
            if (! is_ssl() && (isset($_SERVER[self::SERVER_PORT]) && ($_SERVER[self::SERVER_PORT] != 80))) {
                $url .= ':' . $_SERVER[self::SERVER_PORT];
            }
        }
        return $url . '/cgi-bin/' . MAPSERV . '?' . $queryString;
    }
}
