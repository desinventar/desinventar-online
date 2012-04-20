<script language="php">
#
# DesInventar - http://www.desinventar.org
# (c) 1998-2012 Corporación OSSO
#

class DIGraphXML extends DIGraph
{
	public function __construct($prmSession, $prmXML)
	{
		$options = query_convert_v2_to_v1($prmXML);
		parent::__construct($prmSession, $options);
	} #__construct()
} #class
</script>
