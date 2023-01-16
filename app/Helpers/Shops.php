<?php

use App\Models\User;
use App\Models\Team;
use App\Helpers\Statistics;
use \Carbon\Carbon;
if (!function_exists('current_plan')) {
    function current_subscription($team_id = null)
    {

        if ($team_id) {
            $subscription = Team::where('id', $team_id)->first()->getCurrentSubscription();
        } else {
            $subscription = User::mine()->team->getCurrentSubscription();
        }
        if (!$subscription) {
            if($team_id){
                $restaurant = Team::where('id', $team_id)->first()->restaurant;
            }else{
                $restaurant = User::mine()->team->restaurant;
            }
            if($restaurant->trial_subscription && $restaurant->trial_ends_at > Carbon::now()){
                $plan = \DB::table('plan_settings')->where('id', 1)->where('active', 1)->first();
                if ($plan) {
                    $plan->settings = true;
                    return $plan;
                }
            }
            return false;
        }
        $subscription->settings = false;
        return $subscription;
    }
}


if (!function_exists('canAddMoreCategories')) {
    function canAddMoreCategories($team_id = null)
    {
        $currentSubscription = current_subscription();
        $message = display('You have to subscribe to a plan first');
        if ($currentSubscription) {
            if ($currentSubscription->settings) {
                $plan = $currentSubscription;
            } else {
                $plan = $currentSubscription->plan;
            }
            if (Statistics::team_categories_count() < $plan->categories)
                return ['status' => true];
            else
                $message = display('You have reached the maximum number of categories');
        }

        return [
            'status' => false,
            'message' => $message
        ];
    }
}


if (!function_exists('canAddMoreProducts')) {
    function canAddMoreProductsCategory($categoryId, $team_id = null)
    {
        $currentSubscription = current_subscription();
        $message = display('You have to subscribe to a plan first');
        if ($currentSubscription) {
            if ($currentSubscription->settings) {
                $plan = $currentSubscription;
            } else {
                $plan = $currentSubscription->plan;
            }
            if (Statistics::team_products_category($categoryId) < $plan->products)
                return ['status' => true];
            else
                $message = display('You have reached the maximum number of products at this category');
        }

        return [
            'status' => false,
            'message' => $message
        ];
    }
}




if (!function_exists('canSacnMoreQRCode')) {
    function canSacnMoreQRCode($team_id = null)
    {
        $currentSubscription = current_subscription($team_id);
        $message = display('You have to subscribe to a plan first');
        if ($currentSubscription) {
            if ($currentSubscription->settings) {
                $plan = $currentSubscription;
            } else {
                $plan = $currentSubscription->plan;
            }
            if ($plan->infinity == 1 || Statistics::team_scan_qrcode($currentSubscription->settings, $team_id) < $plan->scan)
                return ['status' => true];
            else
                $message = display('You have reached the maximum number of scans of the qrcode');
        }

        return [
            'status' => false,
            'message' => $message
        ];
    }
}



if (!function_exists('warning')) {
    function warning()
    {
        $warning = [];
        $currentSubscription = current_subscription();
        $message = display('You have to subscribe to a plan first');
        $team = User::mine()->team;
        if ($currentSubscription) {
          
            if($currentSubscription->settings){
                    $ends_at = new Carbon($team->restaurant->trial_ends_at);
                    $now = Carbon::now();
                    if($ends_at->diff($now)->days <= 7){
                        $warning[] = display('Your Subscription will be finish through') . ' ' . Carbon::parse($team->restaurant->trial_ends_at)->diffForHumans();
                    }
            }else{
                $ends_at = new Carbon($currentSubscription->ends_at);
                $now = Carbon::now();
                if($ends_at->diff($now)->days <= 7){
                    $warning[] = display('Your Subscription will be finish through') . ' ' . Carbon::parse($team->restaurant->trial_ends_at)->diffForHumans();
                }
            }
        }

        return $warning;
    }
}
