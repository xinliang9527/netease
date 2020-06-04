<?php

namespace Xinliang\Netease;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Xinliang\Netease\Exceptions\HttpException;
use Xinliang\Netease\Traits\HasHttpRequest;

class Netease
{
    use HasHttpRequest;
    private  $AppKey;
    private  $AppSecret;
    private  $Nonce;
    private  $CurTime;
    private  $CheckSum;

    const ENDPOINT_TEMPLATE = 'https://api.netease.im/nimserver/%s/%s';

    public function __construct()
    {
        $neteaseConfig = config('netease');
        $this->AppKey = $neteaseConfig['AppKey'];
        $this->AppSecret = $neteaseConfig['AppSecret'];
    }

    public function send($servername, $command, array $params = [])
    {
        try {
            $this->checkSumBuilder();
            $headers = [
                'AppKey' => $this->AppKey,
                'Nonce' => $this->Nonce,
                'CurTime' => $this->CurTime,
                'CheckSum' => $this->CheckSum,
            ];
            $result = $this->post($this->buildEndpoint($servername, $command),$params,$headers);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
        return $result;
    }

    protected function buildEndpoint(string $servername, string $command): string
    {
        return \sprintf(self::ENDPOINT_TEMPLATE,  $servername, $command);
    }

    protected function checkSumBuilder()
    {
        $this->Nonce = Str::random(32);
        $this->CurTime = Carbon::now()->timestamp; //当前时间戳，以秒为单位
        $join_string = $this->AppSecret.$this->Nonce.$this->CurTime;
        $this->CheckSum = sha1($join_string);
    }


}
