<script language="php">
/*
 DesInventar8 - http://www.desinventar.org
 (c) 1999-2009 Corporacion OSSO
*/

class Maps
{
	public $fpath = "";
	public $url = "";
	public $kml = "";	
	private $reg = "";
	
	/* This class generate mapfile's mapserver
	   q	: Region Object
	   reg	: RegionUUID
	   lev	: Level to generate effects
	   dl	: disasters list
	   range: limits, legends and color
	   info	: about map (WMS Metadata)
	   lbl	: Label to print name, code or value..
	   type	: filename, THEMATIC, SELECT, KML
  */
	function Maps($q, $reg, $lev, $dl, $range, $info, $lbl, $type) {
		$this->url = "http://". $_SERVER['HTTP_HOST'] ."/cgi-bin/". MAPSERV ."?";
		$this->reg = $reg;
		$fp = "";
		if ($type == "KML")
			$this->kml = $this->generateKML($q, $reg, $info);
		else {
			$map = "## DesInventar8.2 autogenerate mapfile\n";
			$map .= $this->setHeader($q, $reg, $info, $type);
			$gl = $q->loadGeoLevels('', -1, true);
			$map .= $this->setLayerAdm($gl, $reg, $type);
			// mapfile and html template to interactive selection
			if ($type == "SELECT")
				$fp = DATADIR ."/". $reg . "/region.map";
			else {
				// generate effects maps: type=filename | thematic=sessid
				$fp = TMPM_DIR ."/di8ms_";
				$map .= $this->setLayerEff($q, $reg, $lev, $dl, $range, $info, $lbl);
				if ($type == "THEMATIC")
					$fp .= "$reg-". session_id() .".map";
				elseif (strlen($type) > 0)
					$fp .= "$reg-$type.map";
				else
					exit();
			}
			$map .= $this->setFooter();
			$this->makefile($fp, $map);
		}
	}
	
	function makefile($fp, $map) {
	  $fh = fopen($fp, 'w') or die("Error setting file");
		fwrite($fh, $map);
		fclose($fh);
		$this->fpath = $fp;
	}
	
	public function filename() {
		return $this->fpath;
	}
	
	function setHeader($q, $reg, $inf, $typ) {
		$x = 400;
		$y = 550;
		$rinfo = $q->getDBInfo();
		$regname = $rinfo['RegionLabel'];
		$map = 
'	MAP
    IMAGETYPE		PNG
		EXTENT			-180 -90 180 90
		SIZE				'. $x .' '. $y .'
		SHAPEPATH		"'. VAR_DIR . '/' . $reg . '/"
		FONTSET			"'. FONTDIR . '"
		IMAGECOLOR	255 255 255
		PROJECTION	"proj=latlong" "ellps=WGS84" "datum=WGS84" END
		WEB';
    if ($typ == "SELECT") {
      $map .= '
      HEADER "templates/imagemap_header.html"
      FOOTER "templates/imagemap_footer.html"';
    }
    $fm = TEMP .'/di8ms_';
		if ($typ == "THEMATIC")
		  $fm .= "$reg-". session_id() .".map";
    elseif (strlen($typ) > 0)
      $fm .= "$reg-$typ.map";
    else
      exit();
    $map .= '
			#IMAGEPATH		"'. TMPM_DIR .'"
			METADATA
			  WMS_TITLE	"DesInventar Map of -'. $inf['TITLE'] .'-"
			  WMS_ABSTRACT	"Level: '. $inf['LEVEL'] .'"
			  WMS_EXTENT	"'. $inf['EXTENT'] .'"
			  WMS_TIMEEXTENT	"'. $inf['BEG'] ."/". $inf['END'] .'/P5M"
			  WMS_ONLINERESOURCE	"'. $this->url .'map="
			  WMS_SRS	"EPSG:4326 EPSG:900913"
			END
		END
		QUERYMAP
		  STYLE	HILITE
			COLOR	255 0 0
		END
		LEGEND
		  STATUS ON
		  #TRANSPARENT ON
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
	
	function setFooter() {
		return '
	END # MAP';
	}

	// Generate all Admin layers 
	function setLayerAdm($gl, $reg, $typ) {
		$map = "";
		$type = "POLYGON";
		$color = "255 255 255";
		foreach ($gl as $k=>$i) {
			foreach ($i[2] as $ly) {
				$lp = VAR_DIR . '/' . $reg ."/". $ly[1];
				if ($this->testLayer($lp, $ly[2], $ly[3])) {
					$map .= '
    LAYER
      NAME		"'. $ly[0] .'admin0'. $k .'"
      DATA		"'. $ly[1] .'"
	  GROUP		'. $reg .'
	  STATUS	OFF
	  TYPE		'. $type.'
	  PROJECTION		"init=epsg:4326" END
	  CLASSITEM		"'. $ly[2] .'"
	  LABELITEM		"'. $ly[3] .'"';
					// Selection map used in Query Design
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
						$this->makeImagemapTemplate($i[3], $i[4], $tm);
					}
					$map .= '
      CLASS
        # COLOR	'. $color;
					$map .= '
        OUTLINECOLOR 50 50 50';
					$map .= '
        LABEL
			  	TYPE TRUETYPE		FONT "arial"		SIZE 6		COLOR	0 0 89
			  	POSITION CC			PARTIALS FALSE	BUFFER 4
        END
	  END
	END';
				} //end if
			}
		}
		return $map;
	}
	
	function makeImagemapTemplate($code, $name, $tm) {
		/*
		$data = '
  <area shape="poly" coords="[shpxy precision=0 proj=image]"
    onMouseOver="return escape(\'['. $name .']\')" onMouseOut="showText()"
    href="javascript:selectArea(\'['. $code .']\',\'['. $name .']\')" 
    alt="['. $name .']">
';
		$fp = DATADIR . "/" . $this->reg . "/" . $tm;
		$map = $data;
		$this->makefile($fp, $map);
		*/
	}
	
	// Generate standard layer with query results
	function setLayerEff($q, $reg, $lev, $dl, $range, $inf, $lbl) {
		$gl = $q->loadGeoLevels('', $lev, true);
		$map = "";
		foreach ($gl[$lev][2] as $ly) {
			$data = $ly[1];
			$code = $ly[2];
			$name = $ly[3];
			$lp = VAR_DIR . '/' . $reg ."/". $data;
			if ($this->testLayer($lp, $code, $name)) {
				// cvreg isn't set in regular base.. in vregion select region on match
				if (!isset($dl['CVReg']) ||in_array($ly[0], array_unique($dl['CVReg']))) {
					$map .= '
    LAYER
		NAME	"'. $ly[0] .'effects"
		DATA	"'. $data .'"
		GROUP	'. $reg .'
		STATUS	ON
		TYPE	POLYGON
		PROJECTION	"init=epsg:4326" END
		TRANSPARENCY	70
		CLASSITEM	"'. $code .'"
		LABELITEM	"'. $name .'"
		METADATA
			WMS_TITLE	"DesInventar Map of '. $inf['TITLE'] .'"
			WMS_ABSTRACT	"Level: '. $inf['LEVEL'] .'"
			WMS_EXTENT	"'. $inf['EXTENT'] .'"
			WMS_SRS	"EPSG:4326 EPSG:900913"
		END';
					// classify elements by ranges
					$vl = $this->classify($ly[0], $dl, $range);
					$shwlab = 'TEXT ""';
					if ($lbl == "NAME")
						$shwlab = '';
					// Generate classes with effects..
					foreach ($vl as $k=>$i) {
						if ($lbl == "CODE")
							$shwlab = 'TEXT "'. $k .'"';
						elseif ($lbl == "VALUE")
							$shwlab = 'TEXT "'. $i[2] .'"';
						$map .= '
		CLASS ';
						//Set names only in match elements
/*						if (!empty($i[0])) {
							$map .= '
				NAME "'. $i[0] .'"';
						}*/
						$map .= ' 
			EXPRESSION "'. $k .'" 
  			STYLE COLOR '. $i[1] .' OUTLINECOLOR 130 130 130 END
  			'. $shwlab .'
  			LABEL
		      TYPE TRUETYPE		FONT "arial"		SIZE	6
		      COLOR	0 0 89 		POSITION CC 		PARTIALS FALSE	BUFFER 4
			END
		END';
					} // foreach $vl
					// Generate classes with names and colors of ranges
					foreach ($range as $rk=>$ri) {
						// Define a Expression to not show others polygons...
						$map .= '
		CLASS
			NAME "'. $ri[1] .'"
			STYLE COLOR '. $ri[2]  .' OUTLINECOLOR 130 130 130 END
		END';
					}
					/* Generate null class
					if ($lbl == "VALUE")
						$shwlab = 'TEXT "0"';
					$map .= '
		  CLASS
		    NAME "No data"
        EXPRESSION (length("['. $code .']") > 0)
#		    STYLE OUTLINECOLOR 255 255 255 END 
  			'. $shwlab .'
  			LABEL
		      TYPE TRUETYPE		FONT "arial"		SIZE 6	
		      COLOR	0 0 89 		POSITION CC			PARTIALS FALSE	BUFFER 4
        END
      END';*/
					$map .= '
	END # LAYER
';
				} // if in_array
			} // if testlayer
		}
		return $map;
	}

	// Set RGB color array according to user's defined ranges..
	function classify($pfx, $dl, $range) {
		$vl = array();
		$ky = array_keys($dl); // [0]CVReg, [1]DisasterGeography, [2]EffectVar
		$h = 0;
		if ($pfx == '') {	// isn't VRegion
			$geo = 0;
			$eff = 1;
		}
		else {
			$geo = 1;
			$eff = 2;
		}
		if (!empty($dl)) {
			//echo "<pre>". $pfx; print_r($dl);
			foreach ($dl[$ky[$geo]] as $k=>$i) {
				if (!isset($dl['CVReg']) || $dl['CVReg'][$k] == $pfx) {
					$li = 0;
					$assigned = false;
					$val = $dl[$ky[$eff]][$k];
					for ($j=0; $j < count($range) && !$assigned; $j++) {
						$ls = $range[$j][0];
						//echo "$i :: li: $li < val: $val < ls: $ls = ";
						if ($li <= $val && $val <= $ls) {
							$assigned = true;
							$vl[$i] = array($range[$j][1], $range[$j][2], $val);
							$range[$j][1] = "";
							//print_r($vl[$i]);
						}
						else
							$li = $ls + 1;
						//echo "<br>";
					}
					//echo "<hr>";
				}
			}
		}
		return $vl;
	}

  function genColor() {
  	$v1 = rand(0, 255);
    $v2 = rand(0, 255);
    $v3 = rand(0, 255);
    return $v1 ." ". $v2 ." ". $v3;
  }

  function testLayer($lp, $code, $name) {
    if (testMap($lp) && !empty($code) && !empty($name))
      return true;
    else
      return false;
  }
  
  function generateKML($q, $reg, $info) {
    $fp = urlencode(TMPM_DIR ."/di8ms_$reg-". session_id() .".map");
    $dinf = $q->getDBInfo();
    $regn = $dinf['RegionLabel'];
    $desc	= $dinf['RegionDesc'];
    $lon = (int) (($dinf['GeoLimitMinX'] + $dinf['GeoLimitMaxX']) / 2);
    $lat = (int) (($dinf['GeoLimitMinY'] + $dinf['GeoLimitMaxY']) / 2);
    // print info in kml
    $xml = 
'<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://earth.google.com/kml/2.2">
<Folder>
	<name>DesInventar</name>
	<open>1</open>
	<description><![CDATA[<body style="background-color: #FFFFFF">
<p><font face="Arial, Helvetica, sans-serif">
<font color="#008080" face="Arial, Helvetica, sans-serif">DesInventar8</font>
<p><font face="Arial, Helvetica, sans-serif">
<b>Base de datos '. $regn .'</b><br><br>'. $desc . '</font></p>
</body>]]></description>
	<LookAt>
		<longitude>'. $lon .'</longitude>
		<latitude>'. $lat .'</latitude>
		<altitude>0</altitude>
		<range>3000000</range>
		<tilt>0.5</tilt>
		<heading>-5.5</heading>
	</LookAt>
	<GroundOverlay>
		<name>DesInventar '. $regn .'</name>
		<open>1</open>
		<Icon>
			<href>'. $this->url . 'MAP='. $fp .'&amp;LAYERS=effects&amp;SERVICE=WMS&amp;SRS=EPSG%3A4326&amp;REQUEST=GetMap&amp;HEIGHT=600&amp;STYLES=default,default&amp;WIDTH=800&amp;VERSION=1.1.1&amp;TRANSPARENT=true&amp;LEGEND=true&amp;FORMAT=image/png</href>
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
			<href>'. $this->url .'MAP='. $fp .'&amp;SERVICE=WMS&amp;VERSION=1.1.1&amp;REQUEST=getlegendgraphic&amp;LAYER=effects&amp;FORMAT=image/png</href>
		</Icon>
		<overlayXY x="0" y="0" xunits="fraction" yunits="fraction"/>
		<screenXY x="0.005" y="0.02" xunits="fraction" yunits="fraction"/>
		<rotationXY x="0.5" y="0.5" xunits="fraction" yunits="fraction"/>
		<size x="0" y="0" xunits="pixels" yunits="pixels"/>
	</ScreenOverlay>
	<ScreenOverlay id="DILogo">
		<name>&lt;a href=&quot;http://'. $_SERVER['HTTP_HOST'] .'/&quot;&gt;DesInventar8 Online&lt;/a&gt;</name>
		<Icon>
			<href>http://'. $_SERVER['HTTP_HOST'] .'/images/di_logo2.png</href>
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
  
  function printKML() {
    return $this->kml;
  }
}

</script>