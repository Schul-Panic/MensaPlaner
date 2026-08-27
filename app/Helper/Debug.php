<?php

namespace App\Helper;

class Debug
{
    public static function varDumpAndDiePre(mixed $variable): void
    {
        echo '<pre>';
        var_dump($variable);
        echo '</pre>';
        die();
    }

    public static function varDumpPre(mixed $variable): void
    {
        echo '<pre>';
        var_dump($variable);
        echo '</pre>';
        die();
    }
}