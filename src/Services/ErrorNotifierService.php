<?php

namespace CoreSetup\Services;

use Log;

class ErrorNotifierService
{
    protected $availableLogType = [
        "emergency",
        "alert",
        "critical",
        "error",
        "warning",
        "notice",
        "info",
        "debug"
    ];

    public $environment;

    public function __construct()
    {
        $this->environment = strtoupper(env("APP_ENV"));
    }

    public function add2Log(string $message, $type = "info"): void
    {
        if (!in_array($type, $this->availableLogType)) {
            Log::error("Invalid log type: [{$type}]");
        } else {
            Log::{$type}($message);
        }
    }
    public function notifyError(string $message): void
    {
        Log::error($message);
    }

    public function notifyException($exception): void
    {
        Log::debug($exception);
    }
}
