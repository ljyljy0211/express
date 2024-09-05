<?php

/*
 * This file is part of the yihaitao/express.
 *
 * (c) YiHaiTao<306668387@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace YiHaiTao\Express;

use YiHaiTao\Express\Exceptions\HttpException;
use YiHaiTao\Express\Exceptions\InvalidArgumentException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ExpressBird
{
    protected $api = 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';

    protected $app_id;

    protected $app_key;

    protected $guzzleOptions = [];

    /**
     * Kuaidi100 constructor.
     *
     * @param $app_id
     * @param $app_key
     */
    public function __construct($app_id, $app_key)
    {
        $this->app_id = $app_id;
        $this->app_key = $app_key;
    }

    /**
     * 快递查询.
     *
     * @param string $trackingCode 快递单号
     * @param string $shippingCode 物流公司编号
     * @param string $mobile 手机号(选填)
     * @param string $orderCode 订单编号(选填)
     * @param string $requestType 请求指令类型(免费:1002,付费:8001)
     *
     * @return string
     *
     * @throws InvalidArgumentException
     * @throws HttpException
     */
    public function track($trackingCode, $shippingCode, $phone = '', $orderCode = '', $requestType = '1002')
    {
        if (empty($trackingCode)) {
            throw new InvalidArgumentException('TrackingCode is required');
        }

        if (empty($shippingCode)) {
            throw new InvalidArgumentException('ShippingCode is required');
        }

        $requestData = [
            'LogisticCode' => $trackingCode,
            'ShipperCode' => $shippingCode,
            'CustomerName' => substr($phone, -4), // 寄件人or收件人的手机号码后四位
            'OrderCode' => $orderCode,
            'Sort' => 1, // 轨迹排序，0-升序，1-降序，默认0
        ];
        $requestData = json_encode($requestData);

        $post = array(
            'EBusinessID' => $this->app_id,
            'RequestType' => $requestType,
            'RequestData' => urlencode($requestData),
            'DataType' => '2', // 请求、返回数据类型：2-json；
            'DataSign' => $this->encrypt($requestData, $this->app_key),
        );

        try {
            $response = $this->getHttpClient()->request('POST', $this->api, [
                'form_params' => $post,
            ])->getBody()->getContents();
        } catch (GuzzleException $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }

        return $response;
    }

    /**
     * 数据签名.
     *
     * @param $data
     * @param $appkey
     *
     * @return string
     */
    private function encrypt($data, $appkey)
    {
        return urlencode(base64_encode(md5($data.$appkey)));
    }

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    /**
     * @return Client
     */
    public function getGuzzleOptions()
    {
        return new Client($this->guzzleOptions);
    }

    /**
     * @param $options
     */
    public function setGuzzleOptions($options)
    {
        $this->guzzleOptions = $options;
    }
}
