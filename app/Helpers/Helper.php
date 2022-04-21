<?php

use Illuminate\Support\Facades\DB;

class Helper
{
    public static function ApiSuccessStatus()
    {
        $apistatus = "success";

        return $apistatus;
    }

    public static function ApiFailedStatus()
    {
        $apistatus = "failed";

        return $apistatus;
    }
//
    public static function ApiErrorStatus()
    {
        $apistatus = "error";

        return $apistatus;
    }

    public static function mac_check($token, $macid)
    {
        $pat = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))->first();

        if ($macid == $pat->mac_id) {
            return true;
        } else {
            return false;
        }

    }

    public static function hashtags($string)
    {
        if ($string != null) {
            // preg_match_all("/(#\w+)/u", $string, $matches);
            preg_match_all("/#(\\w+)/u", $string, $matches);
            if ($matches) {
                $hashtagsArray = array_count_values($matches[1]);
                $hashtags = array_keys($hashtagsArray);
            }
            return $hashtags;
        }

    }

}
