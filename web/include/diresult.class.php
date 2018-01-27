<?php
/*
 DesInventar - http://www.desinventar.org
 (c) Corporación OSSO
*/
namespace DesInventar\Legacy;

class DIResult
{
    private $options_default_common = array(
        'LangIsoCode' => 'eng'
    );
    public function __construct($prmSession, $prmOptions)
    {
        $this->session = $prmSession;
        if (! isset($prmOptions['Common']['LangIsoCode'])) {
            $prmOptions['Common']['LangIsoCode'] = $prmSession->LangIsoCode;
        }
        $this->options = array();
        $this->options = array_merge($this->options, $prmOptions);
        $this->options['Common'] = $this->options_default_common;
        $this->options['Common'] = array_merge($this->options['Common'], $prmOptions['Common']);
    }
}
