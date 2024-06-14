<?php

namespace Extendy\JormallSmsBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class JormallSmsService
{
    private $senderid;
    private $accname;
    private $accpass;

    private $sendsmsUrl;

    private $getbalanceUrl;


    public function __construct(string $senderid, string $accname, string $accpass, string $sendsmsUrl, string $getbalanceUrl)
    {
        $this->httpClient = new Client();
        $this->senderid = $senderid;
        $this->accname = $accname;
        $this->accpass = $accpass;
        $this->sendsmsUrl = $sendsmsUrl;
        $this->getbalanceUrl = $getbalanceUrl;
    }

    /**
     * Send SMS using Jormall SMS service provider
     * The function will send the SMS and return an array with the status of the sending process
     * The array will contain the following
     * status: true if the message was sent successfully, false if the message was not sent
     * msg: a message that describes the status of the sending process
     * msg_id: the id of the message that was sent, this id can be used to track the message in jormallsms system
     * The function will throw an exception if there is a connection error or a request error
     * The function will catch the exception and return an array with the status of the sending process
     * @param string $to
     * @param string $message
     * @param string $id
     * @return array ['status' => true|false, 'msg' => 'Message sent successfully'|'error message', 'msg_id' => '223394147']
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendSms(string $to, string $message, string $id): array
    {
        $returnArray = [];
        $formattedMessage = str_replace(['\n', '\r\n'], "\n", $message);
        try {
            $response = $this->httpClient->request('POST', $this->sendsmsUrl, [
                'form_params' => [
                    'senderid' => $this->senderid,
                    'accname' => $this->accname,
                    'accpass' => $this->accpass,
                    'numbers' => $to,
                    'msg' => $formattedMessage,
                    'id' => $id,
                ]
            ]);
            //the response is in xml format
            $xmlString = $response->getBody()->getContents();
            $xmlObject = simplexml_load_string($xmlString);
            $array = (array)$xmlObject;
            //the aray may contain the
            //if sms is sent successfully
            //Array ( [0] => MsgID = 223394147 ) where 223394147 is the message id and can be used to track the message in jormallsms system
            //if sms is not sent successfully
            //Array ( [0] => Invalid user name or password )
            //Array ( [0] => Invalid Sender ID )
            //Array ( [0] => Message Ignored, Duplicate ID 1234 )
            //that mean if array[]  had anything other that "MsgID = 223394147" format then the message was not sent successfully
            //so i will check
            if (strpos($array[0], 'MsgID') !== false) {
                $returnArray['status'] = true;
                $returnArray['msg'] = 'Message sent successfully';
                $returnArray['msg_id'] = explode('=', $array[0])[1];
            } else {
                $returnArray['status'] = false;
                $returnArray['msg'] = $array[0];
            }
        } catch (ConnectException $e) {
            // Handle connection errors, such as DNS resolution failures
            // Log the error and return a custom response or rethrow the exception
            // For example:
            // $this->logger->error('SMS service connection error', ['exception' => $e])
            $returnArray['status'] = false;
            $returnArray['msg'] = 'Connection error: Unable to reach SMS service';
        } catch (RequestException $e) {
            // Handle other request-related errors
            // Log the error and return a custom response or rethrow the exception
            // For example:
            // $this->logger->error('SMS service request error', ['exception' => $e]);
            $returnArray['status'] = false;
            $returnArray['msg'] = 'Request error:' . $e->getMessage();
        } catch (\Exception $e) {
            // Handle any other errors
            // Log the error and return a custom response or rethrow the exception
            // For example:
            // $this->logger->error('Unexpected error', ['exception' => $e]);
            $returnArray['status'] = false;
            $returnArray['msg'] = 'Unexpected error:' . $e->getMessage();
        }

        return $returnArray;
    }

    /**
     * Get the balance of the account
     * The function will return an array with the status of the request
     * The array will contain the following
     * status: true if the balance was retrieved successfully, false if the balance was not retrieved
     * balance: the current balance of the account
     * The function will throw an exception if there is a connection error or a request error
     * The function will catch the exception and return an array with the status of the request
     * @return array ['status' => true|false, 'balance' => 9994]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getBalance(): array
    {
        $returnArray = [];
        try {
            $response = $this->httpClient->request('GET', $this->getbalanceUrl, [
                'query' => [
                    'AccName' => $this->accname,
                    'AccPass' => $this->accpass,
                ]
            ]);
            //the response will be text format , for example "9994"
            //which meam 9994 pint the current balance
            //so i will to make the response as integer
            //i will remove "" from the response
            //and return it as integer
            $balanceString = $response->getBody()->getContents();
            $balance = str_replace('"', '', $balanceString);
            $balance = (int)$balance;
            $returnArray['status'] = true;
            $returnArray['balance'] = $balance;

        } catch (ConnectException $e) {
            // Handle connection errors, such as DNS resolution failures
            // Log the error and return a custom response or rethrow the exception
            // For example:
            // $this->logger->error('SMS service connection error', ['exception' => $e])
            $returnArray['status'] = false;
        } catch (RequestException $e) {
            // Handle other request-related errors
            // Log the error and return a custom response or rethrow the exception
            // For example:
            // $this->logger->error('SMS service request error', ['exception' => $e]);
            $returnArray['status'] = false;
        } catch (\Exception $e) {
            // Handle any other errors
            // Log the error and return a custom response or rethrow the exception
            // For example:
            // $this->logger->error('Unexpected error', ['exception' => $e]);
            $returnArray['status'] = false;
        }

        return $returnArray;
    }
}