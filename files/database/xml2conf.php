#!/usr/bin/php -d session.save_path='/tmp'
<script language="php">
	//require_once('../../web/include/loader.php');
	$FileName    = $argv[1];
	$LangIsoCode = $argv[2];

	$dom = new DomDocument('1.0','UTF-8');
	$dom->load($FileName);

	$xp = new domxpath($dom);
	foreach($xp->query('Group') as $tree)
	{
		$xp1 = new domxpath($dom);
		$GroupName = $tree->getAttribute('name');
		print "\n";
		print '[' . $GroupName . ']' . "\n";
		foreach($xp1->query('Group[@name="' . $GroupName . '"]/Message') as $tree1)
		{
			$Message = $tree1->getAttribute('id');
			$xp2 = new domxpath($dom);
			foreach($xp2->query('Group[@name="' . $GroupName . '"]/Message[@id="' . $Message . '"]/Text[@LangIsoCode="' . $LangIsoCode . '"]') as $node)
			{
				$value = $node->nodeValue;
				print $Message . '=' . $value . "\n";
			}
		}
	}
	exit(0);
</script>
