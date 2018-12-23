<?php
namespace DesInventar\Legacy;

class Maps
{
    /* This class generate mapfile's mapserver
        q   : Region Object
        reg : RegionUUID
        lev : Level to generate effects
        dl  : disasters list
        range: limits, legends and color
        info    : about map (WMS Metadata)
        lbl : Label to show name, code or value..
        trans : Transparency %
        type    : filename, THEMATIC, SELECT, KML
        prmOptions : Hash with remaining options
            URL => Complete URL for DesInventar App
    */

    public function __construct(
        $session,
        $reg,
        $lev,
        $data,
        $range,
        $info,
        $lbl,
        $prmTransparency,
        $type,
        $prmOptions,
        $config
    ) {
        $this->session = $session;
        $this->options = array('Id' => time());
        $this->config = $config;
        $this->options = array_merge($this->options, $prmOptions);

        $this->reg = $reg;

        $this->saveKml($reg, $info);

        if ($type == 'THEMATIC') {
            $map = "## DesInventar mapfile\n";
            $map .= $this->setHeader($reg, $info, $type);
            $geographyLevels = $session->q->loadGeoLevels('', -1, true);
            $map .= $this->setLayerAdm($geographyLevels, $reg, $type);
            $map .= $this->setLayerEff($session, $reg, $lev, $data, $range, $info, $lbl, $prmTransparency);
            $map .= $this->setFooter();

            $sFilename = $this->config->paths['tmp_dir'] . '/map_' . $this->options['id'] .  '.map';
            $this->fpath = $sFilename;
            file_put_contents($sFilename, $map);
        }
    }

    public function saveKml($reg, $info)
    {
        $kml = $this->generateKML($this->session, $reg, $info);
        $filename = $this->config->paths['tmp_dir'] . '/map_' . $this->options['id'] . '.kml';
        file_put_contents($filename, $kml);
    }

    public function filename()
    {
        return $this->fpath;
    }

    public function getBaseUrl($protocol)
    {
        if (strpos($this->options['url'], '://') !== false) {
            return $this->options['url'];
        }
        return $protocol . '://' . $this->options['url'];
    }

    public function getMapUrl($protocol)
    {
        $url = $this->getBaseUrl($protocol) . '/wms/' . $this->options['id'];
        return $url;
    }

    public function getLogoUrl()
    {
        $url = $this->getBaseUrl($this->options['protocol_for_maps']) . '/images/desinventar_logo.png';
        return $url;
    }

    public function setHeader($reg, $inf, $typ)
    {
        $mapWidth = 400;
        $mapHeight = 550;
        $map =
        '
MAP
    IMAGETYPE PNG
    CONFIG "PROJ_LIB"  "' . $this->options['proj_lib'] . '"
    EXTENT  -180 -90 180 90
    SIZE ' . $mapWidth .' '. $mapHeight .'
    SHAPEPATH "' . str_replace('\\', '/', VAR_DIR . '/database/' . $reg) . '/"
    FONTSET "' . str_replace('\\', '/', $this->config->maps['fonts_dir']) . '"
    IMAGECOLOR 255 255 255
    PROJECTION
        "proj=latlong"
        "ellps=WGS84"
        "datum=WGS84"
    END
    WEB';
        if ($typ == 'SELECT') {
            $map .= '
            HEADER "templates/imagemap_header.html"
            FOOTER "templates/imagemap_footer.html"';
        }
        $map .= '
        METADATA
            WMS_TITLE "DesInventar Map of -'. $inf['TITLE'] .'-"
            WMS_ABSTRACT "Level: '. $inf['LEVEL'] .'"
            WMS_EXTENT "'. $inf['EXTENT'] .'"
            WMS_TIMEEXTENT "'. $inf['BEG'] ."/". $inf['END'] .'/P5M"
            WMS_ONLINERESOURCE "'. $this->getMapUrl($this->options['protocol']) .'/"
            WMS_SRS "EPSG:4326 EPSG:900913"
            # Mapserver 7.0 compatibility
            WMS_ENABLE_REQUEST "*"
        END
    END
    QUERYMAP
        STYLE HILITE
        COLOR 255 0 0
    END
    LEGEND
        STATUS ON
        KEYSIZE 18 12
        LABEL
            TYPE BITMAP
            SIZE MEDIUM
            COLOR 0 0 89
        END
    END
    OUTPUTFORMAT
        NAME png
        DRIVER "GD/PNG"
        MIMETYPE "image/png"
        IMAGEMODE RGBA
        EXTENSION "png"
        TRANSPARENT ON
END
';
        return $map;
    }

    public function setFooter()
    {
        return '
    END # MAP';
    }

    public function setLayerAdm($geographyLevels, $reg, $typ)
    {
        $map = "";
        $type = "POLYGON";
        $col = 50;
        foreach ($geographyLevels as $k => $i) {
            foreach ($i[2] as $ly) {
                $lp = VAR_DIR . '/database/' . $reg ."/". $ly[1];
                if ($this->testLayer($lp, $ly[2], $ly[3])) {
                    $map .= '
    LAYER
        NAME "'. $ly[0] .'admin0'. $k .'"
        DATA "'. $ly[1] .'"
        GROUP '. $reg .'
        STATUS OFF
        TYPE '. $type.'
        PROJECTION "init=epsg:4326" END
        CLASSITEM "'. $ly[2] .'"
        LABELITEM "'. $ly[3] .'"';
                    if ($typ == "SELECT") {
                        $tm = "templates/imagemap_". $reg ."_". $k .".html";
                        $map .= '
        CLASS
            EXPRESSION ("['. $i[4] .']" in "%ids%")
            STYLE
                COLOR "#BD9E5D" # "#A27528"
                OUTLINECOLOR 50 50 50
            END
        END
        TEMPLATE "'. $tm .'"';
                    }
                    $map .= '
        CLASS
            OUTLINECOLOR '. $col .' '. $col .' '. $col;
                        $col += 50;
                        $map .= '
            LABEL
                    TYPE TRUETYPE  FONT "arial"  SIZE 6  COLOR 0 0 89
                    POSITION CC   PARTIALS FALSE BUFFER 4
            END
        END
    END';
                }
            }
        }
        return $map;
    }

    public function setLayerEff($session, $reg, $lev, $data, $range, $inf, $lbl, $prmTransparency)
    {
        $geographyLevels = $session->q->loadGeoLevels('', $lev, true);
        $map = '';
        foreach ($geographyLevels[$lev][2] as $ly) {
            $value = $ly[1];
            $code = $ly[2];
            $name = $ly[3];
            $lp = VAR_DIR . '/database/' . $reg ."/". $value;
            if ($this->testLayer($lp, $code, $name)) {
                if (!isset($data['CVReg']) || in_array($ly[0], array_unique($data['CVReg']))) {
                    $map .= '
    LAYER
        NAME "'. $ly[0] .'effects"
        DATA "'. $value .'"
        GROUP '. $reg .'
        STATUS ON
        TYPE POLYGON
        PROJECTION "init=epsg:4326" END
        TRANSPARENCY '. $prmTransparency .'
        CLASSITEM "'. $code .'"
        LABELITEM "'. $name .'"
        METADATA
            WMS_TITLE "DesInventar Map of '. $inf['TITLE'] .'"
            WMS_ABSTRACT "Level: '. $inf['LEVEL'] .'"
            WMS_EXTENT "'. $inf['EXTENT'] .'"
            WMS_SRS "EPSG:4326 EPSG:900913"
            # Mapserver 7.0 compatibility
            WMS_ENABLE_REQUEST "*"
        END';
                    $vl = $this->classify($ly[0], $data, $range);
                    $shwlab = 'TEXT ""';
                    if ($lbl == "NAME") {
                        $shwlab = '';
                    }
                    foreach ($vl as $k => $i) {
                        if ($lbl == "CODE") {
                            $shwlab = 'TEXT "'. $k .'"';
                        } elseif ($lbl == "VALUE") {
                            $shwlab = 'TEXT "'. $i[2] .'"';
                        }
                        $map .= '
        CLASS ';
                        if (!empty($i[0]) && !isset($data['CVReg'])) {
                            $map .= '
            NAME "'. $i[0] .'"';
                        }
                        $map .= '
            EXPRESSION "'. $k .'"
            STYLE COLOR '. $i[1] .' OUTLINECOLOR 130 130 130 END
            '. $shwlab .'
            LABEL
                TYPE TRUETYPE  FONT "arial"  SIZE 6
                COLOR 0 0 89 POSITION CC PARTIALS FALSE BUFFER 4
            END
        END';
                    }
                    if (isset($data['CVReg'])) {
                        foreach ($range as $rk => $ri) {
                            $map .= '
        CLASS
            NAME "'. $ri[1] .'"
            STYLE COLOR '. $ri[2]  .' OUTLINECOLOR 130 130 130 END
        END';
                        }
                    }
                    $map .= '
    END # LAYER
';
                }
            }
        }
        return $map;
    }

    public function classify($pfx, $data, $range)
    {
        $vl = array();
        $ky = array_keys($data);
        $h = 0;
        if ($pfx == '') {
            // isn't VRegion
            $geo = 0;
            $eff = 1;
        } else {
            $geo = 1;
            $eff = 2;
        }
        if (!empty($data)) {
            foreach ($data[$ky[$geo]] as $k => $i) {
                if (!isset($data['CVReg']) || $data['CVReg'][$k] == $pfx) {
                    $li = 0;
                    $assigned = false;
                    $val = $data[$ky[$eff]][$k];
                    for ($j=0; $j < count($range) && !$assigned; $j++) {
                        $ls = $range[$j][0];
                        if ($li <= $val && $val <= $ls) {
                            $assigned = true;
                            $vl[$i] = array($range[$j][1], $range[$j][2], $val);
                            $range[$j][1] = "";
                        } else {
                            $li = $ls + 1;
                        }
                    }
                }
            }
        }
        return $vl;
    }

    public function genColor()
    {
        $v1 = rand(0, 255);
        $v2 = rand(0, 255);
        $v3 = rand(0, 255);
        return $v1 . ' '  . $v2 . ' ' . $v3;
    }

    public function testLayer($lp, $code, $name)
    {
        $bReturn = testMap($lp) && !empty($code) && !empty($name);
        return $bReturn;
    }

    public function getEyeAltitude($areaX, $areaY)
    {
        if ($areaX > $areaY) {
            return intval($areaX * 110000);
        }
        return intval($areaY * 110000);
    }

    public function generateKML($session, $reg, $info)
    {
        $url = $this->getMapUrl($this->options['protocol_for_maps']);
        $url .= '/effects/?SRS=EPSG%3A4326&amp;HEIGHT=600&amp;STYLES=default,default' .
            '&amp;WIDTH=800&amp;VERSION=1.1.1&amp;TRANSPARENT=true&amp;LEGEND=true' .
            '&amp;FORMAT=image/png';
        $dinf = $session->q->getDBInfo($lg);
        $regn = $dinf['RegionLabel|'];
        $desc = $dinf['RegionDesc'];

        $MinX = $dinf['GeoLimitMinX|'];
        $MaxX = $dinf['GeoLimitMaxX|'];
        $MinY = $dinf['GeoLimitMinY|'];
        $MaxY = $dinf['GeoLimitMaxY|'];

        $lon =($MinX + $MaxX) / 2;
        $lat =($MinY + $MaxY) / 2;


        $AreaX = abs($MaxX - $MinX);
        $AreaY = abs($MaxY - $MinY);
        $EyeAltitude = $this->getEyeAltitude($AreaX, $AreaY);
        $xml =
        '<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://earth.google.com/kml/2.2">
<Folder>
    <name>DesInventar</name>
    <open>1</open>
    <Description>
        <![CDATA[<body style="background-color: #ffffff">
            <p>
                <font color="#008080" face="Arial, Helvetica, sans-serif">DesInventar</font>
            </p>
            <p>
                <font face="Arial, Helvetica, sans-serif">
                <b>Base de datos '. $regn .'</b><br /><br />'. $desc . '</font>
            </p>
        </body>]]>
    </Description>
    <LookAt>
        <longitude>'. $lon .'</longitude>
        <latitude>'. $lat .'</latitude>
        <altitude>0</altitude>
        <range>' . $EyeAltitude . '</range>
        <tilt>0.5</tilt>
        <heading>-5.5</heading>
    </LookAt>
    <GroundOverlay>
        <name>DesInventar '. $regn .'</name>
        <open>1</open>
        <Icon>
            <href>'. $url . '</href>
            <viewRefreshMode>onStop</viewRefreshMode>
            <viewRefreshTime>1</viewRefreshTime>
            <viewBoundScale>1</viewBoundScale>
            <visibility>1</visibility>
        </Icon>
        <LatLonBox>
            <north>180.0</north>
            <south>-180.0</south>
            <east>90.0</east>
            <west>-90.0</west>
        </LatLonBox>
    </GroundOverlay>
    <ScreenOverlay id="NWILEGEND">
        <name>Leyenda</name>
        <Icon>
            <href>'. $url .'/legend/</href>
        </Icon>
        <overlayXY x="0" y="0" xunits="fraction" yunits="fraction"/>
        <screenXY x="0.005" y="0.02" xunits="fraction" yunits="fraction"/>
        <rotationXY x="0.5" y="0.5" xunits="fraction" yunits="fraction"/>
        <size x="0" y="0" xunits="pixels" yunits="pixels"/>
    </ScreenOverlay>
    <ScreenOverlay id="DesInventarLogo">
        <name>DesInventar Project</name>
        <Icon>
            <href>' . $this->getLogoUrl() . '</href>
        </Icon>
        <overlayXY x="0" y="1" xunits="fraction" yunits="fraction"/>
        <screenXY x="0.005" y="0.995" xunits="fraction" yunits="fraction"/>
        <rotationXY x="0.5" y="0.5" xunits="fraction" yunits="fraction"/>
        <size x="0" y="0" xunits="pixels" yunits="pixels"/>
    </ScreenOverlay>
</Folder>
</kml>';
        return $xml;
    }
}
