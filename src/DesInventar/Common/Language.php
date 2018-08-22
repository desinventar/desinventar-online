<?php

namespace DesInventar\Common;

class Language
{
    const ISO_639_1 = 1;
    const ISO_639_2 = 2;

    protected $map3to2 = ['eng' => 'en', 'spa' => 'es', 'por' => 'pt', 'fre' => 'fr'];
    protected $map2to3 = ['en' => 'eng', 'es' => 'spa', 'pt' => 'por', 'fr' => 'fre'];

    public function isValidLanguage($lang)
    {
        return isset($this->map2to3[$lang]);
    }

    public function getLanguageIsoCode($lang, $type)
    {
        switch ($type) {
            case self::ISO_639_1:
                $map = $this->map3to2;
                $key = substr($lang, 0, 3);
                break;
            case self::ISO_639_2:
            default:
                $map = $this->map2to3;
                $key = substr($lang, 0, 2);
                break;
        }
        if (empty($map[$key])) {
            return reset($map);
        }
        return $map[$key];
    }
}
