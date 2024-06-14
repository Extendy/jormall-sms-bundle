# Jormall SMS Bundle

This is a Symfony bundle that provides an easy way to send SMS using the [Jormall SMS web service](http://josmsservice.com/). 

## Features

- Send SMS messages using the Jormall SMS API.
- Get the balance of the account.
- Handle connection and request errors gracefully.

## Requirements

- PHP 8.2 or higher
- GuzzleHttp 7.8 or higher
- Symfony Framework Bundle 7.0 or higher

## Installation

Add the following to your `composer.json`:

```json
"require": {
    "extendy/jormall-sms-bundle": "0.0.0-beta.1"
}
```

or by running:

```bash
composer require extendy/jormall-sms-bundle
```

edit tour `.env` file and add the following (and replace the values with your Jormall sms account details):

```dotenv
JORMALLSMS_SENDERID=SENDERNAME
JORMALLSMS_ACCNAME=ACCOUNTUSERNAME
JORMALLSMS_ACCPASS=ACCOUNTPASSWORD
```


## Usage

You can use the `JormallSmsService` service to send SMS messages or get the balance. Here is an example (while using it in a controller):

```php
<?php

namespace App\Controller;

use Extendy\JormallSmsBundle\Service\JormallSmsService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    private $jormallSmsService;

    public function __construct(JormallSmsService $jormallSmsService)
    {
        $this->jormallSmsService = $jormallSmsService;
    }


    #[Route('/test', name: 'app_test')]
    public function index()
    {
        $msg = "Hello, this is a test message";
        // send sms
        dd($this->jormallSmsService->sendSms("962777774221", $msg, "1"));

    }

    #[Route('/test/balance', name: 'app_test_balance')]
    public function balance()
    {
        // get sms balance
        dd($this->jormallSmsService->getBalance());

    }

}

```

## License

This package is licensed under the MIT License. See the [LICENSE](./LICENSE) file for details.

