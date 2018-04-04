<?php
namespace Pholoniex\Http;

use Pholoniex\PholoniexClient;

class HttpPostRequest extends CurlRequest
{
    /** @var string  */
    private $url;
    
    /** @var resource  */
    private $post_data;
    
    /** @var array  */
    private $options;
    
    /**
     * Construct
     *
     * @param PholoniexClient $client
     * @param string $url
     * @param array $post_data
     * @param array $options
     */
    public function __construct($client, $url, $post_data = null, $options = null)
    {
        parent::__construct($client);
    
        $this->url = $url;
        $this->post_data = $post_data ? $post_data : array();
        $this->options = $options ? $options : array();
    }
    
    /**
     * get post data
     *
     * @return array
     */
    public function getPostData()
    {
        return $this->post_data;
    }
    
    /**
     * get http headers
     *
     * @return array
     */
    public function getHttpHeaders()
    {
        $http_deaders = parent::getDefaultHttpHeaders();
    
        $http_deaders['Content-Type'] = 'application/x-www-form-urlencoded';
    
        if (isset($this->options['http_headers'])){
            $http_deaders = array_merge($http_deaders, $this->options['http_headers']);
        }
        return $http_deaders;
    }
    
    /**
     * get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * get extra options
     *
     * @return array
     */
    public function getExtraOptions()
    {
        return array(
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($this->post_data, '', '&'),
        );
    }
}