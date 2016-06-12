<?php

function fb($value)
{
    $fb = FirePHP::getInstance(true);
    $fb->fb($value);
}
