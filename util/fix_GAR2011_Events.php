#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
/*
  DesInventar - http://www.desinventar.org
  (c) 1998-2010 Corporacion OSSO
  
  2010-10-26 Jhon H. Caicedo <jhcaiced@desinventar.org>

  Fix some personalized events of IRN database.

  SIGPAD - Colombia
*/

require_once('../web/include/loader.php');

$RegionId = 'GAR-ISDR-2011_IRN';
$us->login('diadmin','di8');
$us->open($RegionId);

$q = array();
$q['GAR-ISDR-2011_IRN'] = array('ALLUVION' => '4cf395c8-d3e7-46a1-9a40-d663cf849e7e', // FLASH FLOOD
                                'RAIN'     => 'c35a3feb-af18-4a91-af49-f3c9b6dbf25d', // RAINS
                                'STRONGWIND' => 'fd6aa6d0-a78f-4627-b73c-45641dc89884',
                                'ELECTRICSTORM' => '17c6f205-16ce-4a11-ac98-24c46004c304',
                                'HEATWAVE'      => '65567ebf-a4e6-4343-94d6-68cbc9d138b7'
							   );
$q['GAR-ISDR-2011_JOR'] = array('ALLUVION' => '83e96f52-e69f-4699-a24c-b2973ed7bf69', // FLASH FLOOD
                                'HEATWAVE' => 'b806a048-9595-4a0b-8e7f-52331f454a76', 
                                'RAIN'     => 'f5a3ce35-6ad3-4b5d-978b-a243ed5253f6'
                               );
$q['GAR-ISDR-2011_LKA'] = array('HURRICANE' => '73970f88-07c9-40fb-956c-148226a6b421',
                                'SURGE'     => '8efb1344-f5f1-4903-9693-a14c6428a10c'
                               );                              
$q['GAR-ISDR-2011_MOZ'] = array('HURRICANE' => '0c517c10-8de1-4490-9841-021d45ac80e4'
                               );
$RegionList = array_keys($q);
foreach($RegionList as $RegionId) {
	//$RegionId = 'GAR-ISDR-2011_JOR';
	fb($RegionId);
	foreach($q[$RegionId] as $Predef => $Person) {
		$query = 'UPDATE Disaster SET EventId="' . $Predef . '" WHERE EventId="' . $Person . '"';
		fb($query);
		$us->q->dreg->query($query);
		$query = 'DELETE FROM Event WHERE EventId="' . $Person . '"';
		fb($query);
		$us->q->dreg->query($query);
	}
}
$us->close();
$us->logout();
</script>
