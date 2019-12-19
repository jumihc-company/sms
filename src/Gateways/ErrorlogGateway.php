<?php
/**
 * User: YL
 * Date: 2019/12/18
 */

namespace Jmhc\Sms\Gateways;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Support\Config;

class ErrorlogGateway extends \Overtrue\EasySms\Gateways\ErrorlogGateway
{
    /**
     * @inheritDoc
     */
    public function send($phoneContainer, MessageInterface $message, Config $config)
    {
        return parent::send(current($phoneContainer), $message, $config);
    }
}
