<?php
/**
 * File: Ip2AdInfo.php
 * PROJECT_NAME: php-solution
 */

class Ip2AdInfo
{
    protected $apiHost = "https://apis.map.qq.com";
    protected $apiPath = "/ws/location/v1/ip";

    /** @var string */
    protected $key = "";
    /** @var string */
    protected $sk = "";

    protected $result;

    public function __construct(string $key, string $sk)
    {
        $this->key = $key;
        $this->sk  = $sk;
    }

    protected function apiUrl(): string
    {
        return sprintf("%s%s", $this->apiHost, $this->apiPath);
    }

    /**
     * 获取ip信息 （接口使用签名校验方式）
     * @param string $ip
     */
    public function ipInfo(string $ip): self
    {
        $sign         = md5($this->apiPath . '?ip=' . $ip . '&key=' . $this->key . $this->sk);
        $url          = $this->apiUrl() . '?ip=' . $ip . '&key=' . $this->key . '&sig=' . $sign;
        $result       = Curl::simpleGet($url);
        $this->result = json_decode($result, true);
        return $this;
    }

    /**
     * 获取接口返回值
     */
    public function res()
    {
        return $this->result;
    }

    /**
     * 判断接口返回是否成功 成功返回数组 否则返回false
     * @return array|false
     */
    public function isSuccess()
    {
        if (isset($this->result["status"]) && $this->result['status'] == 0) {
            return $this->result;
        }
        return false;
    }


}