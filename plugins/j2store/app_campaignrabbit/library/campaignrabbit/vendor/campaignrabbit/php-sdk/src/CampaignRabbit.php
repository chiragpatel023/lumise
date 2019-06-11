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
namespace CampaignRabbit\CampaignRabbit;
class CampaignRabbit {
    protected $loader;
    protected $plugin_name;
    protected $version;
    protected $domain;
    protected $base_uri;
    public function __construct($domain = '') {
        // server protocol
        $protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
        // domain name
        $server_domain = $_SERVER['SERVER_NAME'];
        $this->domain = !empty($domain) ? $domain : $protocol.$server_domain;
        $this->base_uri = 'https://app.campaignrabbit.com/api/v1/';
    }

    /**
     * Set Domain
    */
    public function set_domain($domain){
        $this->domain = $domain;
    }

    /**
     *
     */
    public function get_domain(){
        return trim($this->domain,'/');
    }

    /**
     *
     */
    public function get_base_uri(){
        return $this->base_uri;
    }

}