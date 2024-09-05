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

use YiHaiTao\Express\Exceptions\InvalidArgumentException;

class Express
{
    protected $type;

    protected $app_id;

    protected $app_key;

    /**
     * Express constructor.
     *
     * @param string $app_id
     * @param string $app_key
     * @param string $type
     *
     * @throws InvalidArgumentException
     */
    public function __construct($app_id, $app_key, $type = 'express100')
    {
        if (empty($app_id)) {
            throw new InvalidArgumentException('APP Id Can not be empty');
        }

        if (empty($app_key)) {
            throw new InvalidArgumentException('APP key Can not be empty');
        }

        if (!in_array(strtolower($type), ['express100', 'expressbird'])) {
            throw new InvalidArgumentException('Unsupported Type');
        }

        $this->type = $type;
        $this->app_id = $app_id;
        $this->app_key = $app_key;
    }

    /**
     * 查询物流
     *
     * @param $trackingCode
     * @param $shippingCode
     * @param array $additional
     *
     * @return array
     *
     * @throws Exceptions\HttpException
     * @throws InvalidArgumentException
     */
    public function track($trackingCode, $shippingCode, $additional = [])
    {
        if ('express100' == $this->type) {
            $phone = $additional['phone'] ?? '';
            $express = new Express100($this->app_id, $this->app_key);

            return $express->track($trackingCode, $shippingCode, $phone);
        }

        if ('expressbird' == $this->type) {
            $orderCode = $additional['order_code'] ?? '';
            $phone = $additional['phone'] ?? '';
            $requestType = $additional['request_type'] ?? '1002';
            $express = new ExpressBird($this->app_id, $this->app_key);

            return $express->track($trackingCode, $shippingCode, $phone, $orderCode, $requestType);
        }

        return [];
    }
}
