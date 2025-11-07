<?php

namespace App\Helpers;

class SupabaseHelper
{
    public static function getPublicUrl($bucket, $filename)
    {
        if (empty($filename)) return null;

        return "https://qdfotopajdiuailyeprh.supabase.co/storage/v1/object/public/{$bucket}/{$filename}";
    }
}