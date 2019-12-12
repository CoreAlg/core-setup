<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'network_ip', 'login_details', 'user_agent', 'logged_in_at'
    ];

    protected $dates = ["logged_in_at"];

    public function user()
    {
        return $this->belongsTo("App\User");
    }
}