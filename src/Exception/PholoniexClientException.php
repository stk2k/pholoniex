<?php
namespace Pholoniex\Exception;

class PholoniexClientException extends \Exception implements PholoniexClientExceptionInterface
{
    /**
     * construct
     *
     * @param string $message
     * @param \Exception|null $prev
     */
    public function __construct($message, $prev = null){
        parent::__construct($message,0,$prev);
    }
}