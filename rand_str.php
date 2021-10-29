<?php

function random($length = 8)
{
    return substr(str_shuffle('12345678901234567890'), 0, $length);
}

$str = random(13);
var_dump($str);