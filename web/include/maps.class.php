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
			$creg = $q->getRegionFieldByID($reg, 'IsCRegion');
			if ($creg[$reg]) {
				$gc = $q->loadGeoCarto('', 0); //replace with loadCVitems
				//repeat in all items of VRegion
				foreach ($gc as $ele) {
					$gi = $q->loadGeoCarto($ele['GeographyId'], -1);
					$gl = array();
					foreach ($gi as $k=>$i) {
						if ($i['GeoLevelId'] > 0) {
							$gl[$k][0] = "Level ". $i['GeoLevelId'];
							$gl[$k][1] = '';
							$gl[$k][2] = $i['GeoLevelLayerFile'];
							$gl[$k][3] = $i['GeoLevelLayerName'];
							$gl[$k][4] = $i['GeoLevelLayerCode'];
						}
					}
					$map .= $this->setLayerAdm($gl, $reg, $type);
				}
			}
			else {
				$gl = $q->loadGeoLevels("");
				$map .= $this->setLayerAdm($gl, $reg, $type);
			}
			// mapfile and html template to interactive selection
			if ($type == "SELECT")
				$fp = DATADIR ."/". $reg . "/region.map";
			else {
				// generate effects maps: type=filename | thematic=sessid
				$fp = TMPM_DIR ."/di8ms_";
				if ($creg[$reg]) {
					echo "<pre>"; print_r($dl);
					//repeat in all items of VRegion
					// change to unique array
					foreach ($dl['CVReg'] as $ele)
						$q->loadGeoCarto($ele, -1);
				}
				else
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
			  WMS_SRS	"EPSG:4326"
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
		  $lp = VAR_DIR . '/' . $reg ."/". $i[2];
			if ($this->testLayer($lp, $i[3], $i[4])) {
				$map .= '
    LAYER
      NAME		admin0'. $k .'
      DATA		"'. $i[2] .'"
			GROUP		'. $reg .'
			STATUS	OFF
			TYPE		'. $type.'
			PROJECTION	"init=epsg:4326" END
			CLASSITEM		"'. $i[3] .'"
			LABELITEM		"'. $i[4] .'"';
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
		$gl = $q->loadGeoLevels("");
		$data = $gl[$lev][2];
		$code = $gl[$lev][3];
		$name = $gl[$lev][4];
		$map = "";
		$lp = VAR_DIR . '/' . $reg ."/". $data;
		if ($this->testLayer($lp, $code, $name)) {
			$map = '
    LAYER
		NAME	effects
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
			$vl = $this->classify($dl, $range);
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
				if (!empty($i[0])) {
					$map .= '
				NAME "'. $i[0] .'"';
				}
			$map .= ' 
  		  EXPRESSION "'. $k .'" 
  			STYLE COLOR '. $i[1] .' OUTLINECOLOR 130 130 130 END
  			'. $shwlab .'
  			LABEL
		      TYPE TRUETYPE		FONT "arial"		SIZE	6
		      COLOR	0 0 89 		POSITION CC 		PARTIALS FALSE	BUFFER 4
		  END
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
		}
  	return $map;
	}

	// Set RGB color array according to user's defined ranges..
	function classify($dl, $range) {
		$vl = array();
		$ky = array_keys($dl); // DisasterGeography, EffectVar
		$h = 0;
		if (!empty($dl)) {
			foreach ($dl[$ky[0]] as $k=>$i) {
				$li = 0;
				$assigned = false;
				$val = $dl[$ky[1]][$h];
				for ($j=0; $j < count($range) && !$assigned; $j++) {
					$ls = $range[$j][0];
					//echo "li: $li < val: $val < ls: $ls = ";
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
				$h++;
				//echo "<hr>";
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