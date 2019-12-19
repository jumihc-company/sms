<?php
/**
 * User: YL
 * Date: 2019/12/18
 */

namespace Jmhc\Sms\Gateways;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\Support\Config;

class YunzhixunGateway extends \Overtrue\EasySms\Gateways\YunzhixunGateway
{
    /**
     * @inheritDoc
     */
    public function send($phoneContainer, MessageInterface $message, Config $config)
    {
        $data = $message->getData($this);

        $phoneNumbers = [];
        foreach ($phoneContainer as $phone => $class) {
            $phoneNumbers[] = $class->getNumber();
        }
        if (isset($data['mobiles'])) {
            $phoneNumbers = array_unique(array_filter(array_merge($phoneNumbers, $data['mobiles'])));
        }

        $function = isset($data['mobiles']) || count($phoneNumbers) > 1 ? self::FUNCTION_BATCH_SEND_SMS : self::FUNCTION_SEND_SMS;

        $endpoint = $this->buildEndpoint('sms', $function);

        $params = $this->buildParams(implode(',', $phoneNumbers), $message, $config);

        return $this->execute($endpoint, $params);
    }

    /**
     * @inheritDoc
     */
    protected function buildParams($phoneNumbers, MessageInterface $message, Config $config)
    {
        $data = $message->getData($this);

        return [
            'sid' => $config->get('sid'),
            'token' => $config->get('token'),
            'appid' => $config->get('app_id'),
            'templateid' => $message->getTemplate($this),
            'uid' => isset($data['uid']) ? $data['uid'] : '',
            'param' => isset($data['params']) ? $data['params'] : '',
            'mobile' => $phoneNumbers,
        ];
    }
}
