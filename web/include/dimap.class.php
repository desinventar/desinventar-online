<?php
/*
 DesInventar - http://www.desinventar.org
 (c) Corporación OSSO
*/
namespace DesInventar\Legacy;

class DIMap extends DIResult
{
    private $options_default_map = array(
    );
    public function __construct($prmSession, $prmOptions)
    {
        parent::__construct($prmSession, $prmOptions);
        $this->options['Map']   = array_merge($this->options_default_map, $prmOptions['Map']);
    }
}
