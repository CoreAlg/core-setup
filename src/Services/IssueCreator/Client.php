<?php

namespace CoreSetup\Services\IssueCreator;

class Client
{
    protected $enable;
    protected $serviceProvider;

    public function __construct()
    {
        $this->setServiceProvider();
    }

    public function createIssue($exception) :void
    {
        $this->serviceProvider->createIssue($exception);
    }

    private function setServiceProvider()
    {
        $config = config("issue_creator");

        $this->enable = $config["enable"];

        if ($this->enable === false) {
            return false;
        }

        if (empty($config["service"])) {
            throw new \Exception("No issue creator service provided.");
        }

        $service = $config["service"];
        $serviceProvider = $config["configurations"][$service]["service"];
        $this->serviceProvider = new $serviceProvider;

        return true;
    }
}