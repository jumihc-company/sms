<?php
/**
 * User: YL
 * Date: 2019/12/18
 */

namespace Jmhc\Sms\Utils;

use Overtrue\EasySms\Contracts\PhoneNumberInterface;
use Overtrue\EasySms\PhoneNumber;

class FormatPhone
{
    /**
     * 运行
     * @param string|array|PhoneNumberInterface $phone
     * @param string $delimiter
     * @return array
     */
    public static function run($phone, string $delimiter = ',')
    {
        if ($phone instanceof PhoneNumberInterface) {
            return [
                $phone->getNumber() => $phone,
            ];
        }

        $res = [];

        if (is_string($phone)) {
            $phone = explode($delimiter, $phone);
        }

        foreach ($phone as $v) {
            $v = \trim($v);
            if (empty($v)) continue;
            $res[$v] = $v instanceof PhoneNumberInterface ? $v : new PhoneNumber($v);
        }

        return $res;
    }
}
