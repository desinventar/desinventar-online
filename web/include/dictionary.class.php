<script language="php">
/************************************************
 DesInventar8
 http://www.desinventar.org  
 (c) 1999-2007 Corporacion OSSO
 ***********************************************/

/* DesInventar Dictionary Object is a unique Documentation system
   to control: 
    - Contextual Help
    - Error and Information Messages 
    - Labels in forms
    - Static information Pages
    - Methodological Guide
    - MultiLanguage and traduction
*/
class Dictionary {
  var $dbh;
  var $dbdic = "di8doc.sq3";
  var $sql1 = "include/sql/doc-struct.sql";
  var $sql2 = "include/sql/doc-db.sql";

  function str2js($in_string) {
   $str = ereg_replace("[\r\n]", " \\n\\\n", $in_string);
   $str = ereg_replace('"', '\\"', $str);
   return $str; //utf8_encode($str);
  }

  function Dictionary($path) {
    $dbpath = $path . '/' . $this->dbdic;
    if (!extension_loaded('pdo')) {
      dl( "pdo.so" );
      dl( "pdo_sqlite.so" );
    }
    try {
      // Open dictionary DB or create since SQL
      //if (file_exists($dbpath))
        //chmod($dbpath, 0666);
      $this->dbh = new PDO("sqlite:" . $dbpath);
      if (filesize($dbpath)==0 && 
          file_exists($this->sql1) && 
          file_exists($this->sql2)) {
        // Create database struct. Include Languages setting
        $sqlstruct = file_get_contents($this->sql1);
        $this->dbh->exec($sqlstruct);
        // Insert LabelGroup table to order and Set all labels
        $sqllabels = file_get_contents($this->sql2);
        $this->dbh->exec($sqllabels);
      }
    } catch (PDOException $e) {
      print "Error : Cannot find dictionary file<br>\n";
      print "Error : " . $e->getMessage() . "<br/>\n";
      die();
    }
  }
  
  function existLang($langID) {
    if ($langID == "")
      return false;
    $sql = "select LangId from Language where LangId='". $langID ."';";
    foreach ($this->dbh->query($sql) as $row) {
      if (count($row) > 0) {
        return true;
      }
    }
    return false;
  }
  
  function queryLabel($labgrp, $labname, $langID) {
  	$data = '';
    $sql = "select d.DicTranslation as DTr, d.DicTechHelp as DTe, ".
            "d.DicBasDesc as DBa, d.DicFullDesc as DFu from Dictionary d,".
            " LabelGroup g where (g.LGName like '" . $labgrp . "%') ".
            "and (d.LangID='" . $langID . "') and (g.LabelName= '".
            $labname ."') and (d.DicLabelID = g.DicLabelID) ".
            "order by g.LGorder";
    foreach ($this->dbh->query($sql) as $row) {
      $data = array('DicTranslation'=>$row['DTr'],//utf8_encode($row['DTr']),
                    'DicTechHelp'=>$row['DTe'],//utf8_encode($row['DTe']),
                    'DicBasDesc'=>$row['DBa'],//utf8_encode($row['DBa']),
                    'DicFullDesc'=>$row['DFu']);//utf8_encode($row['DFu']));
    }
    return $data;
  }
  function queryLabelsFromGroup($labgrp, $langID) {
  	$dictio = '';
    $sql = "select g.LGName as lgn, g.LabelName as lbn, DicTranslation, ".
            "DicTechHelp, DicBasDesc, DicFullDesc from Dictionary d,".
            " LabelGroup g where (g.LGName like '". $labgrp ."%') and ".
            "(d.LangID='". $langID ."') and (d.DicLabelID = g.DicLabelID) ".
            "order by g.LGorder";
    foreach ($this->dbh->query($sql) as $row) {
      $grp = explode("|", $row['lgn']);
      $dictio[$grp[0].$row['lbn']] = array(
          $row['DicTranslation'],//utf8_encode($row['DicTranslation']), 
          $row['DicTechHelp'],//utf8_encode($row['DicTechHelp']),
          $this->str2js($row['DicBasDesc']), $row['DicFullDesc']);
    }
    return $dictio;
  }

  function querySecLabelFromGroup($labgrp, $langID) {
    $sql = "select g.LGName as lgn, g.LabelName as lbn, DicTranslation, ".
            "DicTechHelp, DicBasDesc, DicFullDesc from Dictionary d,".
            " LabelGroup g where (g.LGName like '". $labgrp ."%') and ".
            "(d.LangID='". $langID ."') and (d.DicLabelID = g.DicLabelID) ".
            "order by g.LGorder";
    foreach ($this->dbh->query($sql) as $row) {
      $grp = explode("|", $row['lgn']);
      $dictio[$grp[0].$row['lbn']] = $grp[2];
    }
    return $dictio;
  }

  function loadAllGroups($langID) {
    $sql = "select g.LGName as lgn, g.LabelName as lbn, DicTranslation, ".
            "DicTechHelp, DicBasDesc, DicFullDesc from Dictionary d,".
            " LabelGroup g where (d.LangID='" . $langID . "') and ".
            "(d.DicLabelID = g.DicLabelID) order by g.LGorder";
    foreach ($this->dbh->query($sql) as $row) {
      $grp = explode("|", $row['lgn']);
      $dictio[$grp[0].$row['lbn']] = array($row['DicTranslation'], $row['DicTechHelp'], $row['DicBasDesc'], $row['DicFullDesc']);
    }
    return $dictio;
  }

  function loadAllLabels() {
    $sql = "select DicLabelID, LGName, LabelName from LabelGroup order by LGName";
    $diction = array();
    foreach ($this->dbh->query($sql) as $row) {
      $dictio[$row['DicLabelID']] = $row['LGName'] .'|'. $row['LabelName'];
    }
    return $dictio;
  }

  function loadAllLang() {
    $sql = "select LangID, LangName, LangNameEN, LangNotes, LangAdmin from Language";
    foreach ($this->dbh->query($sql) as $row) {
      $lang[$row['LangID']] = array($row['LangName'],$row['LangNameEN'],$row['LangNotes'],$row['LangAdmin']);
    }
    return $lang;
  }
  
  function insertLang($langID, $langname, $langname2, $langnotes, $langadmin) {
    if (!$this->existLang($langID)) {
      $sql = "insert into Language values ('". $langID ."','". $langname .
            "','". $langname2 ."','". $langnotes ."','". $langadmin ."','false');";
      $this->dbh->exec($sql);
      return true;
    }
    else
      return false;
  }

  function activateLang($langID) {
    $sql = "update Language set LangActive='true' where LangID='". $langID ."');";
    $this->dbh->exec($sql);
  }

  function deactivateLang($langID) {
    $sql = "update Language set LangActive='false' where LangID='". $langID ."');";
    $this->dbh->exec($sql);
  }
  
  function findDicLabelID($labgrp, $labname) {
    $sql = "select DicLabelID from LabelGroup where LGName like '". $labgrp .
            "%' and LabelName='". $labname ."';";
    foreach ($this->dbh->query($sql) as $row) {
      $diclabelID = $row['DicLabelID'];
    }
    return $diclabelID;
  }
  
  function existLabel($diclabelID) {
    $sql = "select DicLabelID from Dictionary where DicLabelID='". 
            $diclabelID . "';";
    foreach ($this->dbh->query($sql) as $row) {
      $diclabelID = $row['DicLabelID'];
      if ($diclabelID != null)
        return true;
    }
    return false;
  }

  function updateDicLabel($labgrp, $labname, $translation, $techhelp, $basdesc, $fulldesc, $langID) {
    if (!$this->existLang($langID))
      return false;
    else {
      $diclabID = $this->findDicLabelID($labgrp, $labname);
      if (!$this->existLabel($diclabID))
        $sql = "insert into Dictionary values ('". $diclabID ."','". $langID .
              "','". $translation ."','". $techhelp ."','". $basdesc ."','".
              $fulldesc ."');";
      else
        $sql = "update Dictionary set LangID='". $langID ."', DicTranslation='".
              $translation ."', DicTechHelp='". $techhelp ."', DicBasDesc='".
              $basdesc ."', DicFullDesc='".$fulldesc .
              "' where DicLabelID='". $diclabID ."';";
      $this->dbh->exec($sql);
    }
    return true;
  }
  
}//end Class

</script>
