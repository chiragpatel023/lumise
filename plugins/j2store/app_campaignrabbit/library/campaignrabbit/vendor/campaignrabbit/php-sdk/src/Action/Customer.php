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

/**
 * Class Customer
 * @package Woocampaign\WooIncludes\Lib
 */
class Customer extends Request
{
    private $uri;
    private $request;

    public function __construct($api_token, $app_id , $domain)
    {
        $this->uri = 'customer';
        $this->request = (new Request($api_token, $app_id, $domain));
    }

    /**
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function getAllCustomers(){

        $response = $this->request->request('GET', $this->uri, '');
        $parsed_response = $this->request->parseResponse($response);
        return $parsed_response;
    }



    public function getCustomer($email){

        $response = $this->request->request('GET',$this->uri.'/get_by_email/'.$email,'');
        $parsed_response = $this->request->parseResponse($response);
        return $parsed_response;

    }


    /**
     * @param $body
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function createCustomer($body)
    {

        $json_body = json_encode($body);
        $response = $this->request->request('POST', $this->uri, $json_body);
        $parsed_response = $this->request->parseResponse($response);

        return $parsed_response;

    }
    

    /**
     * @param $body
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function updateCustomer($body, $old_email)
    {

        $json_body = json_encode($body);
        $customer_response = $this->request->request('GET',$this->uri.'/get_by_email/'.$old_email,'');
        if($customer_response->getStatusCode() != 200){
            $parsed_response = $this->request->parseResponse($customer_response);
            return $parsed_response;
        }

        $customer_response = $this->request->parseResponse($customer_response);
        $id = isset($customer_response['body']->id) ? $customer_response['body']->id:'';
        //json_decode($customer_response->getBody()->getContents(),true)['id'];
        $response = $this->request->request('PUT', $this->uri . '/' . $id, $json_body);
        $parsed_response = $this->request->parseResponse($response);

        return $parsed_response;

    }



    public function deleteCustomer($email)
    {
        $customer_response = $this->request->request('GET',$this->uri.'/get_by_email/'.$email,'');
        if($customer_response->getStatusCode() != 200){
            $parsed_response = $this->request->parseResponse($customer_response);
            return $parsed_response;
        }
        $customer_response = $this->request->parseResponse($customer_response);
        $id = isset($customer_response['body']->id) ? $customer_response['body']->id:'';

        $response = $this->request->request('DELETE', $this->uri . '/' . $id, '');
        $parsed_response = $this->request->parseResponse($response);

        return $parsed_response;

    }

}