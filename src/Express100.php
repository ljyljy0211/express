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

class Express100
{
    protected $api = 'https://poll.kuaidi100.com/poll/query.do';

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
     * @param string $shippingCode 物流公司单号
     * @param string $phone
     *
     * @return string
     *
     * @throws InvalidArgumentException
     * @throws HttpException
     */
    public function track($trackingCode, $shippingCode, $phone = '')
    {
        if (empty($trackingCode)) {
            throw new InvalidArgumentException('TrackingCode is required');
        }

        if (empty($shippingCode)) {
            throw new InvalidArgumentException('ShippingCode is required');
        }

        if ('shunfeng' == $shippingCode && empty($phone)) {
            throw new InvalidArgumentException('This Order Need PhoneNumber');
        }

        $post['customer'] = $this->app_id;
        $data = [
            'com' => $shippingCode,
            'num' => $trackingCode,
            'resultv2' => '1',
        ];

        if (!empty($phone)) {
            $data['phone'] = $phone;
        }

        $post['param'] = json_encode($data, JSON_UNESCAPED_UNICODE);
        $post['sign'] = strtoupper(md5($post['param'].$this->app_key.$this->app_id));

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
