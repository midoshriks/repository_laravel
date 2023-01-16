<?php
// <!-- @mo2men -->

use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

if (!function_exists('display')) {

    // $lang = app()->getLocale();
    // dd($lang);

    function display(String $text = null)
    {
        $orig_text = $text;
        $locale =  app()->getLocale() == "" ? 'ar' : app()->getLocale();
        // dd($locale);
        if (isset($locale)) {
            app()->setLocale($locale);
        }
        // dd($locale);
        $language = app()->getLocale();
        // dd($language);
        $text = \Illuminate\Support\Str::limit($text, 150);

        if (!empty($text)) {
            $cacheId = str_replace(' ', '_', $text) . '.' . $language . '.language';
            $cacheExpiration = (int) config('system_settings.cache_expiration', 1440); // Cache for 1 day (60 * 24)
            return Cache::remember($cacheId, $cacheExpiration, function () use ($text, $language) {

                $row = \DB::table('languages')->where('phrase', '=', $text)->first();

                if ($row && optional($row)->$language) {
                    return $row->$language;
                } else {
                    if (!$row) {
                        $text2 = str_replace('_', ' ', $text);
                        $text2 = ucfirst($text);
                        \DB::insert('insert into languages (phrase, en, ar) values (?, ?, ?)', [$text, $text2, $text2]);
                        $row = \DB::table('languages')->where('phrase', '=', $text)->first();
                        return $row->$language;
                    }
                }
            });
        } else {
            return $orig_text;
        }
        return $text;
    }
}

// if (!function_exists('device')) {
//     function device()
//     {
//         $device = 'mobile';
//         $useragent = $_SERVER['HTTP_USER_AGENT'];
//         if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
//             $device = 'mobile';
//         } else {
//             $device = 'desktop';
//         }

//         return $device;
//     }
// }


// if (!function_exists('image_exist')) {
//     function image_exist($path)
//     {
//         if($path){
//             return  file_exists(\Str::replace(config('app.url').'/', '' , $path)) ? $path : false;
//         }

//         return null;
//     }
// }

if (!function_exists('send_notification')) {

    function send_notification($token_fcm, $title, $body)
    {
        if (!defined('API_ACCESS_KEY')) define('API_ACCESS_KEY', 'AAAA4INLL48:APA91bHB5Lt0o2G5ZSANTFfMnQFXvNod4Uvq57LcSb0Ogc6zUE1fjGoCub5ibrpoTkD2juu50NTXfb0h7h8pTCcGfFOSQo-OWsBqCPJLnJX5n28kBAqADw8ot6yVBWLbPYNY8Es-KHQT');

        if (!empty($token_fcm)) {
            $registrationIds = $token_fcm;

            #prep the bundle
            $msg = array(
                'title' => $title,
                'body' => $body,
                'sound' => "default",
                // 'click_action' => '.OPEN_ACTIVITY_CHAT',
                'icon-large' => "logorounded"
            );

            $fields = array(
                'registration_ids' => $registrationIds,
                'notification' => $msg,
                'data' => $msg,
            );

            $headers = array(
                'Authorization: key=' . API_ACCESS_KEY,
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);
            dd($result);
            return $result;

            // echo $result;

        }
    }


}

if (!function_exists('IPtoLocation')) {
    function IPtoLocation($ip)
    {
        $ipData = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));
        return !empty($ipData) && $ipData['status'] == 'success' ? $ipData : false;
    }
}
