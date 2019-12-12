<?php

namespace CoreSetup\Services;

use App\AuditLog;
use Carbon, Auth, DB;

class AuditLogService
{
    public $paginatedList = true;

    public function __construct()
    { }

    public function lists($data = null)
    {
        $search_query = [];

        $item_per_page = session()->get("settings.item_per_page", 25);
        $order = session()->get('settings.data_order', 'desc');

        $query = AuditLog::select([
            "id",
            "auditable_type",
            "event",
            "created_at",
            "created_by",
            DB::raw("data->>'$.network_ip' as network_ip")
        ])
            ->with("user");

        // if(isset($data["search"])){

        //     $search_query = [
        //         "search" => $data["search"]
        //     ];

        //     $query->where(function($q) use($data){
        //         $q->orWhere("number", "LIKE", "%".$data["search"]."%");
        //     });
        // }

        $query->orderBy('id', $order);

        if ($this->paginatedList === true) {
            $auditLogs = $query->paginate($item_per_page)->appends($search_query);
            $auditLogs->pagination_summary = get_pagination_summary($auditLogs);
        } else {
            $auditLogs = $query->get();
        }

        return $auditLogs;
    }

    public function add(string $event, $model, array $meta = [])
    {
        if (env("APP_ENV") === "testing") {
            return true;
        }

        $original = $model->getOriginal();
        $changes = $model->toArray();

        $data = [
            "original" => $original,
            "changes" => $changes,
            "user_agent" => request()->Header('User-Agent'),
            "network_ip" => request()->getClientIp(),
            "meta" => $meta
        ];
        
        return AuditLog::create([
            "auditable_type" => get_class($model),
            "auditable_id" => $model->id,
            "event" => strtoupper($event),
            "data" => json_encode($data),
            "created_by" => isset(auth()->user()->id) ? auth()->user()->id : 0
        ]);
    }

    public function getById($id)
    {
        return AuditLog::find($id);
    }
}
