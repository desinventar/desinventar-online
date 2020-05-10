<?php

namespace DesInventar\Helpers;

class LanguageDetect
{
    public function detect()
    {
        $acceptLanguage = getenv('HTTP_ACCEPT_LANGUAGE') ? getenv('HTTP_ACCEPT_LANGUAGE') : '';
        $languages = explode(',', "{$acceptLanguage}");
        if (count($languages) < 1) {
            return 'en';
        }
        $language = explode(';', $languages[0]);
        if (count($language) < 1) {
            return 'en';
        }
        return $language[0];
    }
}
