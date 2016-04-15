<?php

namespace mailchimp;
/**
 * Class API3
 * @package mailchimp
 * @NOTE: head and put methods were not implemented
 */
class API3 {

    private $_api_key;
    private $_api_root_endpoint = 'https://<dc>.api.mailchimp.com/3.0';
    private $_verify_ssl   = false;
    private $_allowedMethods = ['get', 'head', 'put', 'post', 'patch', 'delete'];


    function __construct($api_key)
    {
        if(!$api_key) {
            die('No Mailchimp API key provided');
        }
        $this->_api_key = $api_key;
        list(, $datacentre) = explode('-', $this->_api_key);
        $this->_api_root_endpoint = str_replace('<dc>', $datacentre, $this->_api_root_endpoint);
    }

    /**
     * @param string $method - 'get', 'head', 'put', 'post', 'patch', 'delete'
     * @param string $endpoint - MC endpoint, default root /
     * @param array $args - Arguments sent to MC, default empty
     * @param int $timeout - default 10
     * @return object
     */
    public function call($method, $endpoint = '/', $args=array(), $timeout = 10)
    {
        //Checks whether CURL is installed in PHP
        if (!function_exists('curl_init') || !function_exists('curl_setopt')) {
            die('CURL library is required and not installed on system!');
        }

        //Check whether the method requested is allowed
        $method = strtolower($method);
        if(!in_array($method, $this->_allowedMethods)) {
            die('Method not allowed.');
        }

        if(!is_array($args)) {
            die('$args must be array');
        }

        return $this->_makeRequest($method, $endpoint, $args, $timeout);
    }

    private function _makeRequest($method, $endpoint, $args=array(), $timeout = 10)
    {
        $headers = array(
            'Content-Type: application/json',
            'Authorization: apikey ' . $this->_api_key,
        );

        $url = $this->_api_root_endpoint . '/' . $endpoint ;

        $ch = curl_init();

        if($method === 'post' || $method === 'patch') {
            $json_data = json_encode($args);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        }

        if($method === 'get' || $method === 'delete') {
            $url .= '?' . http_build_query($args);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/3.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->_verify_ssl);

        $result = curl_exec($ch);
        curl_close($ch);

        if (defined('LOG_MAILCHIMP_REQUESTS') && LOG_MAILCHIMP_REQUESTS) {
            if (function_exists('ucn_log_api_call')) {
                ucn_log_api_call('makeRequest::[' . $method . '] ' . $endpoint, json_encode($args), $result);
            }
        }

        return $result ? json_decode($result, true) : false;
    }

}
