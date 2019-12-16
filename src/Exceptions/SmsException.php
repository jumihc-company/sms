<?php
/**
 * User: YL
 * Date: 2019/12/14
 */

namespace Jmhc\Sms\Exceptions;

use Exception;
use Throwable;

class SmsException extends Exception
{
    protected $data;

    public function __construct(string $message = "", int $code = 0, $data = [])
    {
        $this->setData($data);
        parent::__construct($message, $code, null);
    }

    protected function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
