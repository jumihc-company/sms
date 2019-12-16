<?php
/**
 * User: YL
 * Date: 2019/12/14
 */

namespace Jmhc\Sms\Contracts;

interface CacheInterface
{
    /**
     * 读取数据
     * @param string $key
     * @return array
     */
    public function get(string $key): array ;

    /**
     * 设置数据
     * @param string $key
     * @param array $data
     * @return bool
     */
    public function set(string $key, array $data): bool ;

    /**
     * 设置过期时间
     * @param string $key
     * @param int $ttl
     * @return bool
     */
    public function expire(string $key, int $ttl): bool ;

    /**
     * 判断是否存在
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool ;

    /**
     * 删除指定键数据
     * @param string $key
     * @return bool
     */
    public function del(string $key): bool ;
}
