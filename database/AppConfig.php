<?php

class AppConfig
{

    public static function get($key,$client)
    {

        if ($client === 'Admin') {
            return adminConfig::configs($key);

        }
        return false;
    }
}

