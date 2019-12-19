<?php
/**
 * User: YL
 * Date: 2019/12/18
 */

namespace Jmhc\Sms\Gateways;

use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\Exceptions\GatewayErrorException;
use Overtrue\EasySms\Support\Config;

class QcloudGateway extends \Overtrue\EasySms\Gateways\QcloudGateway
{
    const ENDPOINT_BATCH_METHOD = 'tlssmssvr/sendmultisms2';

    /**
     * @inheritDoc
     */
    public function send($phoneContainer, MessageInterface $message, Config $config)
    {
        $phoneNumbers = [];
        foreach ($phoneContainer as $phone => $class) {
            $phoneNumbers[] = [
                'nationcode' => $class->getIDDCode() ?: 86,
                'mobile' => $class->getNumber(),
            ];
        }

        $data = $message->getData($this);

        $signName = !empty($data['sign_name']) ? $data['sign_name'] : $config->get('sign_name', '');

        unset($data['sign_name']);

        $msg = $message->getContent($this);
        if (!empty($msg) && '【' != mb_substr($msg, 0, 1) && !empty($signName)) {
            $msg = '【'.$signName.'】'.$msg;
        }

        $type = !empty($data['type']) ? $data['type'] : 0;
        $params = [
            'tel' => $phoneNumbers,
            'type' => $type,
            'msg' => $msg,
            'time' => time(),
            'extend' => '',
            'ext' => '',
        ];
        if (!is_null($message->getTemplate($this)) && is_array($data)) {
            unset($params['msg']);
            $params['params'] = array_values($data);
            $params['tpl_id'] = $message->getTemplate($this);
            $params['sign'] = $signName;
        }
        $random = substr(uniqid(), -10);

        $params['sig'] = $this->generateSign($params, $random);

        $url = self::ENDPOINT_URL.(count($phoneContainer) == 1 ? self::ENDPOINT_METHOD : self::ENDPOINT_BATCH_METHOD).'?sdkappid='.$config->get('sdk_app_id').'&random='.$random;

        $result = $this->request('post', $url, [
            'headers' => ['Accept' => 'application/json'],
            'json' => $params,
        ]);

        if (0 != $result['result']) {
            throw new GatewayErrorException($result['errmsg'], $result['result'], $result);
        }

        return $result;
    }
}
