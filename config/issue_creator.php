<?php

return [
    "enable" => env("ISSUE_CREATOR", false),

    "service" => env("ISSUE_CREATOR_SERVICE"),

    "configurations" => [
        "bitbucket" => [
            "bitbucket_client_id" => env("bitbucketClientId"),
            "bitbucket_client_secret" => env("bitbucketClientSecret"),
            "bitbucket_user_account_name" => env("bitbucketUserAccountName"),
            "bitbucketrepo_slug" => env("bitbucketRepoSlug", ""),
            "service" => \CoreSetup\Services\IssueCreator\BitbucketService::class
        ],
        // "test" => [
        //     "api" => env("SMS_API_URL"),
        //     "username" => env("SMS_USERNAME"),
        //     "password" => env("SMS_PASSWORD"),
        //     "version" => env("SMS_VERSION", ""),
        //     "service" => null
        // ],
    ],

    "dontCreate" => [
        "The given data was invalid.",
        "The resource owner or authorization server denied the request.",
        "The user credentials were incorrect.",
        "Unauthenticated."
    ]
];
