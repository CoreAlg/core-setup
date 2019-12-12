<?php

namespace CoreSetup\Services\IssueCreator;

use CoreSetup\Services\CurlService;
use CoreSetup\Services\ErrorNotifierService;

class BitbucketService
{
    protected $errorNotifier;
    protected $curl;

    protected $bitbucket_client_id;
    protected $bitbucket_client_secret;
    protected $bitbucket_user_account_name;
    protected $bitbucketrepo_slug;
    protected $environment;
    protected $dontCreate = [];

    public function __construct()
    {
        if(
            is_null($this->bitbucket_client_id) === true || 
            is_null($this->bitbucket_client_secret) === true ||
            is_null($this->bitbucket_user_account_name) === true ||
            is_null($this->bitbucketrepo_slug) === true
        )
        {
            throw new \Exception("Invalid bitbucket configuration.", 500);
        }

        $this->environtment = strtoupper(env("APP_ENV"));

        $this->errorNotifier = new ErrorNotifierService();
        $this->curl = new CurlService();
    }

    public function createIssue($exception)
    {
        if ($this->environtment === "LOCAL") {
            return false;
        }

        try {

            // get exception title
            $title = trim($exception->getMessage());

            // check the error title exists in $dontCreateIssue list
            if (is_null($title) === true || in_array($title, $this->dontCreate)) {
                return false;
            }

            // get access token
            $access_token = $this->getAccessToken();

            // check access token
            if (empty($access_token)) {
                $this->errorNotifier->notifyError("BitbucketService: no access token.");
                return false;
            }

            // endpoint to create issue
            $endpoint = "https://api.bitbucket.org/2.0/repositories/{$this->bitbucketUserAccountName}/{$this->bitbucketRepoSlug}/issues?access_token={$access_token}";


            // preparing request payload
            $requestUrl = request()->url();

            $requestPayloadArray = [
                "network_ip" => request()->getClientIp(),
                "user_agent" => request()->Header('User-Agent'),
                "request" => request()->all()
            ];

            $requestPayload = json_encode($requestPayloadArray);

            $userPayloadArray = [];

            // preparing user payload for authorized user
            if (auth()->check() === true) {
                $currentUser = auth()->user();

                $userPayloadArray = [
                    "id" => $currentUser->id,
                    "email" => $currentUser->email
                ];
            }

            $userPayload = json_encode($userPayloadArray);

            // preparing issue content
            $content = "
### Error: {$title}
###### Location:
{$exception->getFile()}:{$exception->getLine()}
###### Request URL:
{$requestUrl}
###### Request Payload:
`{$requestPayload}`
###### User Payload:
`{$userPayload}`
";

            // preparing issue payload
            $data = [
                "title" => "{$title} [{$this->environtment}]",
                "content" => [
                    "raw" => $content,
                    "markup" => "markdown"
                ],
                "kind" => "bug",
                "priority" => "major",
            ];

            // preparing authorization header
            $options = [
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Authorization: Bearer {$access_token}"
                ]
            ];

            // let's create issue
            $response = $this->curl->post($endpoint, $data, $options);

            if (in_array($response["code"], [200, 201])) {
                return true;
            } else {
                $this->errorNotifier->notifyError("Failed response from bitbucket issue creator: " . json_enocde($response));
                return false;
            }
        } catch (\Exception $ex) {
            $this->errorNotifier->notifyException($ex);
            return false;
        }
    }

    private function getAccessToken()
    {
        $endpoint = "https://bitbucket.org/site/oauth2/access_token";

        $options = [
            CURLOPT_POSTFIELDS => "grant_type=client_credentials",
            CURLOPT_USERPWD => "{$this->bitbucketClientId}:{$this->bitbucketClientSecret}",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/x-www-form-urlencoded"
            ]
        ];

        $response = $this->curl->post($endpoint, [], $options);

        return $response["data"]["access_token"] ?? "";
    }

    private function setConfiguration(): void
    {
        $config = config("issue_creator");

        $this->dontCreate = $config["dontCreate"];

        $service = $config["service"];
        $configuration = $config["configurations"][$service];

        $this->bitbucket_client_id = $configuration["bitbucket_client_id"];
        $this->bitbucket_client_secret = $configuration["bitbucket_client_secret"];
        $this->bitbucket_user_account_name = $configuration["bitbucket_user_account_name"];
        $this->bitbucketrepo_slug = $configuration["bitbucketrepo_slug"];
    }
}
