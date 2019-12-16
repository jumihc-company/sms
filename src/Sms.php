<?php
/**
 * User: YL
 * Date: 2019/12/14
 */

namespace Jmhc\Sms;

use Jmhc\Sms\Contracts\CacheInterface;
use Jmhc\Sms\Exceptions\SmsException;
use Jmhc\Sms\Utils\ParseResult;
use Jmhc\Sms\Utils\SmsCache;
use Overtrue\EasySms\Contracts\MessageInterface;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class Sms
{
    /**
     * 短信缓存工具
     * @var SmsCache
     */
    protected $smsCache;

    /**
     * 配置数组
     * @var array
     */
    protected $config;

    /**
     * @var EasySms
     */
    protected $easySms;

    /**
     * 发送手机
     * @var string
     */
    protected $phone;

    /**
     * 发送类型
     * @var string
     */
    protected $type = '';

    /**
     * 发送验证码
     * @var string
     */
    protected $code;

    /**
     * @var MessageInterface|array
     */
    protected $message;

    /**
     * @var array
     */
    protected $gateways = [];

    public function __construct(CacheInterface $cache, array $config)
    {
        $this->smsCache = new SmsCache($cache);
        $this->config = $config;
    }

    /**
     * 设置手机号
     * @param string $phone
     * @return $this
     */
    public function setPhone(string $phone)
    {
        $this->phone = $phone;
        $this->smsCache->setPhone($phone);
        return $this;
    }

    /**
     * 设置发送类型
     * @param string $type
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;
        $this->smsCache->setType($type);
        return $this;
    }

    /**
     * 设置发送验证码
     * @param string $code
     * @return $this
     */
    public function setCode(string $code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * 设置发送消息
     * @param MessageInterface|array $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * 设置网关
     * @param array $gateways
     * @return $this
     */
    public function setGateways(array $gateways)
    {
        $this->gateways = $gateways;
        return $this;
    }

    /**
     * 设置发送间隔
     * @param array $interval
     * @return $this
     */
    public function setInterval(array $interval)
    {
        $this->smsCache->setInterval($interval);
        return $this;
    }

    /**
     * 设置有效期
     * @param int $sec
     * @return $this
     */
    public function setValidTime(int $sec)
    {
        $this->smsCache->setValidTime($sec);
        return $this;
    }

    /**
     * 发送
     * @param bool $debug
     * @return int|mixed
     * @throws InvalidArgumentException
     * @throws NoGatewayAvailableException
     * @throws SmsException
     */
    public function send(bool $debug = false)
    {
        // 发送检测
        $this->sendCheck();

        // 调用 easySms 发送短信
        ! $debug && $this->getEasySms()->send($this->phone, $this->message, $this->gateways);

        // 设置发送成功缓存
        $this->smsCache->send($this->code);

        return $this->smsCache->sendInterval();
    }

    /**
     * 发送检测
     * @throws SmsException
     */
    protected function sendCheck()
    {
        if (! preg_match('/^1[3-9]\d{9}$/', $this->phone)) {
            throw new SmsException('Incorrect phone number format.');
        }

        if (is_null($this->code)) {
            throw new SmsException('You need to call setCode to set the code.');
        }

        if (is_null($this->message)) {
            throw new SmsException('Sending a message must.');
        }

        // 验证时间是否允许
        $interval = $this->smsCache->sendCheck();
        if ($interval !== true) {
            throw new SmsException(
                sprintf(
                    'SMS failed, please try again after %d seconds',
                    $interval
                ),
            0,
            [
                'interval' => $interval,
            ]);
        }
    }

    /**
     * 获取 EasySms 实例
     * @return EasySms
     */
    protected function getEasySms()
    {
        if (is_null($this->easySms)) {
            $this->easySms = new EasySms($this->config);
        }

        return $this->easySms;
    }
}
