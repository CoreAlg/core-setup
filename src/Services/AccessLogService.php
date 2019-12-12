<?php

namespace App\Services;

use App\AccessLog;
use Carbon;

class AccessLogService
{
    public $paginatedList = true;

    public function lists($data=null)
    {
        $search_query = [];

        $item_per_page = session()->get("settings.item_per_page", 25);
        $order = session()->get('settings.data_order', 'desc');

        $query = AccessLog::select([
            "*"
        ]);

        // if(isset($data["search"])){

        //     $search_query = [
        //         "search" => $data["search"]
        //     ];

        //     $query->where(function($q) use($data){
        //         $q->orWhere("number", "LIKE", "%".$data["search"]."%");
        //     });
        // }

        $query->orderBy('id',$order);

        if($this->paginatedList === true){
            $accessLogs = $query->paginate($item_per_page)->appends($search_query);
            $accessLogs->pagination_summary = get_pagination_summary($accessLogs);
        }else{
            $accessLogs = $query->get();
        }        

        return $accessLogs;
    }

    public function add($user, array $login_details)
    {
        if(isset(auth()->user()->user_type)){
            $login_details["user_type"] = auth()->user()->user_type;
        }

        return AccessLog::create([
            "user_id" => $user->id,
            "network_ip" => request()->getClientIp(),
            "login_details" => json_encode($login_details),
            "user_agent" => request()->Header('User-Agent'),
            "logged_in_at" => Carbon::now()
        ]);

    }
}