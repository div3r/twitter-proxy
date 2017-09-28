<?php

namespace AppBundle\Service;

class TwitterProxy
{
    const URL = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    const METHOD = 'GET';

    private $apiExchange;
    private $tweetsNumber;

    public function __construct
    (
        $token,
        $tokenSecret,
        $consumerKey,
        $consumerSecret,
		$fetch_tweets
    )
    {
        $this->apiExchange = new \TwitterAPIExchange([
            'oauth_access_token' => $token,
            'oauth_access_token_secret' => $tokenSecret,
            'consumer_key' => $consumerKey,
            'consumer_secret' => $consumerSecret
        ]);

        $this->tweetsNumber = $fetch_tweets;
    }

    public function getTweets($username)
    {
        $getField = '?screen_name=' . $username . '&count='.$this->tweetsNumber;

        return $this->apiExchange
            ->setGetField($getField)
            ->buildOauth(static::URL, static::METHOD)
            ->performRequest();
    }

    public function userExists($username)
    {
        $getField = '?screen_name=' . $username . '&count='.$this->tweetsNumber;

        $string = json_decode($this->apiExchange
            ->setGetField($getField)
            ->buildOauth(static::URL, static::METHOD)
            ->performRequest(),$assoc = TRUE);

        if(isset($string["errors"]))
        {
            $ctrl = false;
        }
        else
        {
            $ctrl = true;
        }

        return $ctrl;
    }
}

