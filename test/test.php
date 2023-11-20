<?php

include_once(__DIR__ . '/config.php');

define('tA', $configA);
define('tB', $configB);
define('tC', '333');

echo $configA;
// echo tA;
// echo tC;
// echo tD;
// echo ccc;

class testClass
{
    public static function echoTest(){
        echo tA;
    }
}

testClass::echoTest();

?>