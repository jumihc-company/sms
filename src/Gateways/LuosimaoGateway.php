<?php
/**
 * User: YL
 * Date: 2019/12/18
 */

namespace Jmhc\Sms\Gateways;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Support\Config;

class LuosimaoGateway extends \Overtrue\EasySms\Gateways\LuosimaoGateway
{
    /**
     * @inheritDoc
     */
    public function send($phoneContainer, MessageInterface $message, Config $config)
    {
        $phoneNumbers = [];
        foreach ($phoneContainer as $phone => $class) {
            $phoneNumbers[] = $class->getNumber();
        }
        $phoneNumbers = implode(',', $phoneNumbers);

        $endpoint = $this->buildEndpoint('sms-api', count($phoneContainer) == 1 ? 'send' : 'send_batch');

        $result = $this->post($endpoint, [
            'mobile' => $phoneNumbers,
            'message' => $message->getContent($this),
        ], [
            'Authorization' => 'Basic '.base64_encode('api:key-'.$config->get('api_key')),
        ]);

        if ($result['error']) {
            throw new GatewayErrorException($result['msg'], $result['error'], $result);
        }

        return $result;
    }
}
