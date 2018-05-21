<?php
namespace Pholoniex\Exception;

class ServerResponseFormatException extends \Exception implements PholoniexClientExceptionInterface
{
    /**
     * construct
     *
     * @param string $message
     */
    public function __construct($message){
        parent::__construct('API server returned illegal response:' . $message);
    }
}