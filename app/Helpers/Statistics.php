<?php

namespace App\Helpers;

use \App\Models\User;
use \App\Models\Team;

class Statistics
{
    public static function team_categories_count($team_id = null)
    {
        return User::mine()->team->categories->count();
    }


    public static function team_products_category($categoryId,  $team_id = null)
    {
        return User::mine()->team->products($categoryId)->count();
    }



    public static function team_scan_qrcode($planSettings = null, $team_id = null)
    {
        if ($team_id) {
            if ($planSettings)
                return Team::where('id', $team_id)->first()->restaurant->scans;

            return Team::where('id', $team_id)->first()->getCurrentSubscription()->scans;
        }
    }
}
