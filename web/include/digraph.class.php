<script language="php">
#
# DesInventar - http://www.desinventar.org
# (c) 1998-2012 Corporación OSSO
#

class DIGraph extends DIResult
{
	private $options_default = array(
		'General' => array(
			'LangIsoCode' => 'eng'
		),
		'Graph' => array(
			'Type'          => 'HISTOGRAM',
			'SubType'       => 0,
			'Title'         => '',
			'Kind'          => 'BAR',
			'Feel'          => '3D',
			'Period'        => 'YEAR',
			'Stat'          => '',
			'Tendency'      => '',
			'TendencyLabel' => '',
			'FieldLabel'    => array('Number of records'),
			'Field'         => array('D.DisasterId||'),
			'Scale'         => array('textint'),
			'Data'          => array('NONE'),
			'Mode'          => array('NORMAL')
		)
	);

	public function __construct($prmSession, $prmOptions)
	{
		parent::__construct($prmSession);
		$this->session = $prmSession;
		if (isset($prmOptions['prmGraph']))
		{
			$prmOptions['Graph'] = $prmOptions['prmGraph'];
			unset($prmOptions['prmGraph']);
		}
		if (! isset($prmOptions['General']['LangIsoCode']))
		{
			$prmOptions['General']['LangIsoCode'] = $prmSession->LangIsoCode;
		}
		$this->options = $this->options_default;
		$this->options = array_merge($this->options, $prmOptions);
		$this->options['Graph']   = array_merge($this->options_default['Graph'], $prmOptions['Graph']);
		$this->options['General'] = array_merge($this->options_default['General'], $prmOptions['General']);
	} #__construct()

	public function preProcessData()
	{
		foreach($this->options['Graph']['Field'] as $key => $value)
		{
			if ($value == '')
			{
				unset($this->options['Graph']['Field'][$key]);
				unset($this->options['Graph']['Scale'][$key]);
				unset($this->options['Graph']['Data'][$key]);
				unset($this->options['Graph']['Mode'][$key]);
				unset($this->options['Graph']['Tendency'][$key]);
			}
		}
	} #preProcessData()

	public function execute()
	{
		$this->preProcessData();

		$us = $this->session;
		$options = $this->options;

		# load levels to display in totalizations
		foreach ($us->q->loadGeoLevels('', -1, false) as $k=>$i)
		{
			$st['GraphGeographyId_'. $k] = array($i[0], $i[1]);
		}

		$lg = $options['General']['LangIsoCode'];
		$dic = array_merge(array(), $st);
		$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Graph', $lg));
		$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Effect', $lg));
		$dic = array_merge($dic, $us->q->queryLabelsFromGroup('Sector', $lg));
		$dic = array_merge($dic, $us->q->getEEFieldList('True'));
		$prmGraph = $options['Graph'];

		# Process QueryDesign Fields and count results
		$qd  = $us->q->genSQLWhereDesconsultar($options);
		$sqc = $us->q->genSQLSelectCount($qd);	
		$c   = $us->q->getresult($sqc);
		$NumRecords = $c['counter'];

		$prmGraph['SubType'] = (int)$prmGraph['SubType'];
		if ($prmGraph['SubType'] == GRAPH_HISTOGRAM_TEMPORAL)
		{
			$prmGraph['VarList'] = 'D.DisasterBeginTime';
		}
		elseif ($prmGraph['SubType'] == GRAPH_HISTOGRAM_EVENT)
		{
			$prmGraph['VarList'] = 'D.DisasterBeginTime|D.EventId';
		}
		elseif ($prmGraph['SubType'] == GRAPH_HISTOGRAM_CAUSE)
		{
			$prmGraph['VarList'] = 'D.DisasterBeginTime|D.CauseId';
		}
		elseif ($prmGraph['SubType'] == GRAPH_COMPARATIVE_EVENT)
		{
			$prmGraph['VarList'] = 'D.EventId';
		}
		elseif ($prmGraph['SubType'] == GRAPH_COMPARATIVE_CAUSE)
		{
			$prmGraph['VarList'] = 'D.CauseId';
		}
		elseif (($prmGraph['SubType'] >= 100) && ($prmGraph['SubType'] < 200) )
		{
			$k = $prmGraph['SubType'] - 100;
			$prmGraph['VarList'] = 'D.DisasterBeginTime|D.GeographyId_' . $k;
		}
		elseif ($prmGraph['SubType'] >= 200)
		{
			$k = $prmGraph['SubType'] - 200;
			$prmGraph['VarList'] = 'D.GeographyId_' . $k;
		}
		else
		{
			$prmGraph['VarList'] = $prmGraph['SubType'];
		}
		$options['Graph'] = $prmGraph;

		$sImageURL  = WWWDATA . '/graphs/graph_'. session_id() . '_' . time() . '.png';
		$sImageFile = WWWDIR  . '/graphs/graph_'. session_id() . '_' . time() . '.png';

		# Process Configuration options to Graphic
		$ele = array();
		foreach (explode('|', $prmGraph['VarList']) as $itm)
		{
			if ($itm == 'D.DisasterBeginTime')
			{
				# Histogram
				if (isset($prmGraph['Stat']) && strlen($prmGraph['Stat'])>0)
				{
					$ele[] = $prmGraph['Stat'] .'|'. $itm;
				}
				else
				{
					$ele[] = $prmGraph['Period'] .'|'. $itm;
				}
			}
			elseif (substr($itm, 2, 11) == 'GeographyId')
			{
				$gl = explode('_', $itm);
				$ele[] = $gl[1] .'|'. $gl[0];
			}
			else
			{
				$ele[] = '|'. $itm;
			}
		} # foreach

		$options['NumberOfVerticalAxis'] = 1;
		$options['FieldList'] = $prmGraph['Field'];
		$options['NumberOfVerticalAxis'] = count($options['FieldList']);
		
		$opc['Group'] = $ele;
		$opc['Field'] = $prmGraph['Field'];
		$sql = $us->q->genSQLProcess($qd, $opc);
		$dislist = $us->q->getassoc($sql);
		if (!empty($dislist))
		{
			# Process results data
			$dl = $us->q->prepareList($dislist, 'GRAPH');
			$gl = array();
			# Translate Labels to Selected Language
			foreach ($dl as $k=>$i)
			{
				$kk = substr($k, 0, -1); # Select Hay marked like EffectsXX_
				$k2 = substr($k, 2);
				if (isset($dic['Graph'. $k][0]))
				{
					$dk = $dic['Graph'. $k][0];
				}
				elseif (isset($dic['Graph'. $k2][0]))
				{
					$dk = $dic['Graph'. $k2][0];
				}
				elseif (isset($dic[$k][0]))
				{
					$dk = $dic[$k][0];
				}
				elseif (isset($dic[$kk][0]))
				{
					$dk = $dic[$kk][0];
				}
				else
				{
					$dk = $k;
				}
				$gl[$dk] = $i;
			}

			$options['DateRange'] = $us->getDateRange($options['D_RecordStatus']);
			# Construct Graphic Object and Show Page
			$g = new Graphic($us, $options, $gl);
			# Wrote graphic to file
			$g->Stroke($sImageFile);

			$this->output = array();
			$this->output['NumRecords']  = $NumRecords;
			$this->output['QueryDetail'] = $us->q->getQueryDetails($dic, $options);
			$this->output['ImageURL']    = $sImageURL . '?'. rand(1,3000);
			$this->output['ImageFile']   = $sImageFile;
		} #if
	} #execute()
} #class
</script>