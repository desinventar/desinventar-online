<?php
namespace DesInventar\Common;

class MapServer
{
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
        if (empty($options['MAPID']) || $options['MAPID'] == 'worldmap') {
            $options['MAP']         = $this->config->maps['worldmap_dir'] . '/world_adm0.map';
            $options['LAYERS']      = 'base';
            $options['TRANSPARENT'] = 'false';
        } else {
            $options['MAP'] = TMP_DIR .'/map_' . $options['MAPID'] . '.map';
            $options['TRANSPARENT'] = 'true';
            unset($options['MAPID']);
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
        $queryString .= '&mde=map';
        return $queryString;
    }

    public function getMapServerUrl($queryString)
    {
        $url = 'http://' . $_SERVER['SERVER_ADDR'];
        if (! is_ssl() && isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] != 80)) {
            $url .= ':' . $_SERVER['SERVER_PORT'];
        }
        $url .= '/cgi-bin/' . MAPSERV . '?' . $queryString;
        return $url;
    }
}
