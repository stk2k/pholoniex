<?php
namespace Pholoniex\Exception;

class ApiErrorResponseException extends \Exception
{
    /** @var string */
    private $api;
    
    /** @var string */
    private $response_message;
    
    /**
     * construct
     *
     * @param string $api
     * @param string $message
     */
    public function __construct($api, $message){
        $msg = 'api returned error response:' . $message;
        parent::__construct($msg);
        
        $this->api = $api;
        $this->response_message = $message;
    }
    
    /**
     * get api
     *
     * @return string
     */
    public function getApi()
    {
        return $this->api;
    }
    
    /**
     * get message
     *
     * @return string
     */
    public function getResponseMessage()
    {
        return $this->response_message;
    }
    
}