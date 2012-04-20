<script language="php">
#
# DesInventar - http://www.desinventar.org
# (c) 1998-2012 Corporación OSSO
#

class DIGraphXML extends DIGraph
{
	public function __construct($prmSession, $prmXML)
	{
		#$xml_doc = new SimpleXMLElement($prmXML);
		$options = array();
		parent::__construct($prmSession, $options);
	} #__construct()
} #class
</script>
