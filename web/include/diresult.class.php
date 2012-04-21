<script language="php">
#
# DesInventar - http://www.desinventar.org
# (c) 1998-2012 Corporación OSSO
#

class DIResult
{
	private $options_default_common = array(
		'LangIsoCode' => 'eng'
	);
	public function __construct($prmSession, $prmOptions)
	{
		$this->session = $prmSession;
		if (! isset($prmOptions['Common']['LangIsoCode']))
		{
			$prmOptions['Common']['LangIsoCode'] = $prmSession->LangIsoCode;
		}
		$this->options = array();
		$this->options = array_merge($this->options, $prmOptions);
		$this->options['Common'] = $this->options_default_common;
		$this->options['Common'] = array_merge($this->options['Common'], $prmOptions['Common']);
	} #__construct()
} #class
</script>
