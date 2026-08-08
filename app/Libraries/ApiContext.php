<?php

namespace App\Libraries;

/**
 * Penyimpan data user API (petugas outlet) yang sedang terautentikasi.
 * Diisi oleh filter ApiAuthFilter, dibaca oleh controller API.
 */
class ApiContext
{
    public static $user = null;

    public static function setUser($user)
    {
        self::$user = $user;
    }

    public static function user()
    {
        return self::$user;
    }
}
