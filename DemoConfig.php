<?php

/**
 *  配置文件 公共方法
 */
class DemoConfig
{
    //accessKeyId
    const ACCESS_KEY_ID = '59266406e45b2900015e3b62';
    //accessKeySecret
    const ACCESS_KEY_SECRET = '12345678';
    
    /**
     * 生成32位唯一编码.
     * @return string 32位唯一编码.
     */
    static function GetUniqueId()
    {
        return (md5(uniqid(rand())));
    }
}
?>