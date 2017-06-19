<?php
if (isset($_SERVER["HTTP_HOST"])) {
    function fb($value)
    {
        if (class_exists('ChromePhp')) {
            ChromePhp::log($value);
        }
    }
} else {
    function fb($Msg)
    {
        print "fb: ";
        print_r($Msg);
        print "\n";
    }
}
