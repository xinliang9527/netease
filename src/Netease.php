<?php

namespace Xinliang\Netease;

use Xinliang\Netease\Exceptions\HttpException;
use Xinliang\Netease\Traits\HasHttpRequest;

class Netease
{
    use HasHttpRequest;
    private  $AppKey;
    private  $AppSecret;
    const ENDPOINT_TEMPLATE = 'https://api.netease.im/nimserver/';

    public function __construct($AppKey = '',$AppSecret = '')
    {
//        $this->config = ;
        $this->AppKey = $AppKey;
        $this->AppSecret = $AppSecret;
    }


    public function send($servername, $command, array $params = [])
    {
        try {
            $result = $this->postJson($this->buildEndpoint($servername, $command), $params);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }

        if (0 === $result['ErrorCode'] && 'OK' === $result['ActionStatus']) {
            return $result;
        }
        return false;
    }

    protected function buildEndpoint(string $servername, string $command): string
    {
        $query = http_build_query([
            'AppKey' => $this->config->get('sdk_app_id'),
            'identifier' => $this->config->get('identifier'),
            'usersig' => $this->generateSign($this->config->get('identifier')),
            'random' => mt_rand(0, 4294967295),
            'contenttype' => self::ENDPOINT_FORMAT,
        ]);

        return \sprintf(self::ENDPOINT_TEMPLATE, self::ENDPOINT_VERSION, $servername, $command, $query);
    }

    public function checkSumBuilder()
    {
        //此部分生成随机字符串
        $hex_digits = self::HEX_DIGITS;
        $this->Nonce = '';
        for ($i = 0; $i < 128; ++$i) {            //随机字符串最大128个字符，也可以小于该数
            $this->Nonce .= $hex_digits[rand(0, 15)];
        }
        $this->CurTime = (string) (time());    //当前时间戳，以秒为单位

        $join_string = $this->AppSecret.$this->Nonce.$this->CurTime;
        $this->CheckSum = sha1($join_string);
        //print_r($this->CheckSum);
    }


}
