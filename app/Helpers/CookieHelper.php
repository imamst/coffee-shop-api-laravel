<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cookie;

class CookieHelper
{
    public static function getCookieValueFromQueue(string $name)
    {
        $queuedCookies = Cookie::getQueuedCookies();
        for ($i=0;$i<count($queuedCookies);$i++) {
            if ($queuedCookies[$i]->getName() == $name) {
                return $queuedCookies[$i]->getValue();
            }
        }
        return null;
    }
}