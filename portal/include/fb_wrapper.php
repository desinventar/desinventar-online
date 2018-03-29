<?php

function fb($value)
{
    if (class_exists('ChromePhp')) {
        ChromePhp::log($value);
    }
}
