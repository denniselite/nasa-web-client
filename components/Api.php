<?php
/**
 * Created by PhpStorm.
 * User: Denniselite
 * Date: 28/09/2017
 * Time: 08:04
 */

namespace app\components;

use \yii\base\Component;
use yii\httpclient\Client;
use yii\httpclient\Response;

class Api extends Component
{
    const ROUTE_NEO_BROWSE = '/neo/browse';

    const ROUTE_FEED = '/feed';

    /**
     * @var string
     */
    public $endPoint;

    /**
     * @var string
     */
    public $apiKey;

    public function getDailyNeosInfo($dateStart, $dateEnd)
    {
        $params = [
            'start_date' => $dateStart,
            'end_date' => $dateEnd,
            'detailed' => false,
            'host' => $this->endPoint . static::ROUTE_FEED
        ];
        return $this->doRequest($params);
    }

    public function getAllData($page = 0)
    {
        $params = [
            'page' => $page,
            'size' => 20,
            'host' => $this->endPoint . static::ROUTE_NEO_BROWSE
        ];
        return $this->doRequest($params);
    }
    /**
     * @param []$params
     * @return Response|null
     */
    private function doRequest($params)
    {
        $params['api_key'] = $this->apiKey;
        $response = (new Client)
            ->createRequest()
            ->setMethod('get')
            ->setUrl($params['host'])
            ->setData($params)->send();
        if (!$response->isOk) {
            throw new HttpException('Error to request data with params: ' . json_encode($params) . '; response' . json_encode($response->data));
        }
        return $response;
    }
}