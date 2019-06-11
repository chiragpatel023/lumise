<?php
/**
 * @package     Campaign Rabbit
 * @author      Alagesan, J2Store <support@j2store.org>
 * @copyright   Copyright (c) 2018 J2Store . All rights reserved.
 * @license     GNU/GPL license: v3 or later
 * @link        http://j2store.org
 * --------------------------------------------------------------------------------
 *
 * */
namespace CampaignRabbit\CampaignRabbit\Action;

use CampaignRabbit\CampaignRabbit;
use GuzzleHttp\Client;


/**
 * Class Request
 */
class Request{


    private $campaignrabbit;


    /**
     * @var
     */
    private $api_token;

    /**
     * @var
     */
    private $app_id;


    function __construct($api_token, $app_id,$domain = '')
    {
        $this->campaignrabbit = new CampaignRabbit\CampaignRabbit($domain);

        $this->api_token = $api_token;

        $this->app_id = $app_id;
    }



    function request($method, $uri, $body){

        try {

            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->campaignrabbit->get_base_uri()
            ]);

            $response=$client->request($method, $uri, [
                'body' => $body,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->api_token,
                    'Request-From-Domain' => $this->campaignrabbit->get_domain(),
                    'App-Id' => $this->app_id,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'

                ]]);



        } catch (\Exception $e) {
            $response = $e->getResponse();
        }

        return $response;

    }


    function parseResponse($response){

        $ex_body = $response->getBody()->getContents();
        $ex_body = !empty($ex_body) ? json_decode($ex_body) : new \stdClass();
        $parsed_response = array(
            'message'=> $response->getReasonPhrase(),
            'code'=>$response->getStatusCode(),
            'body'=> isset($ex_body->data) ? $ex_body->data: $ex_body
        );
        //$parsed_response['body'] = isset($parsed_response['body']) ? json_decode($parsed_response['body']): new stdClass();

        return $parsed_response;

    }





}


