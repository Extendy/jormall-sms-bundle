<?php

namespace Extendy\JormallSmsBundle\Service;

use GuzzleHttp\Client;

class JormallSmsService
{
    private $senderid;
    private $accname;
    private $accpass;

    public function __construct(string $senderid, string $accname, string $accpass)
    {
        $this->httpClient = new Client();
        $this->senderid = $senderid;
        $this->accname = $accname;
        $this->accpass = $accpass;
    }

    public function sendSms(string $to, string $message,string $id): bool
    {
        dd("logic for seneding sms is here");
        return $response->getStatusCode() === 200;
    }
}