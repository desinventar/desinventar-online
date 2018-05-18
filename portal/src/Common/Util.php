<?php

namespace DesInventar\Common;

class Util
{
    public function getLangIsoCode($lang)
    {
        $map = ['en' => 'eng', 'es' => 'spa', 'pt' => 'por', 'fr' => 'fre'];
        if (empty($map[substr($lang, 0, 2)])) {
            return 'eng';
        }
        return $map[substr($lang, 0, 2)];
    }
}
