<?php
/**
 * Created by PhpStorm.
 * User: Denniselite
 * Date: 27/09/2017
 * Time: 04:38
 */

namespace app\commands;

use app\models\Neo;
use Yii;
use yii\console\Controller;
use yii\httpclient\Client;
use yii\httpclient\Response;

class NeoController extends Controller
{
    private function initCollection()
    {
        $collectionAlreadyExists = false;
        $collections = Yii::$app->mongodb->createCommand()->listCollections();
        foreach ($collections as $c) {
            $collectionAlreadyExists = ($c['name'] === 'neos');
        }
        if (!$collectionAlreadyExists) {
            Yii::$app->mongodb->createCommand()->createCollection('neos');
            echo "Collection 'neos' created...\n";
        }
    }
    public function actionInitData()
    {
        $this->initCollection();
        $dateTime = new \DateTime();
        $dateEnd = $dateTime->format('Y-m-d');
        $dateStart = $dateTime->sub(new \DateInterval('P3D'))->format('Y-m-d');
        printf("Date start: %s, Date end: %s\n", $dateStart, $dateEnd);
        $response = $this->doRequest($dateStart, $dateEnd);
        if ($response->isOk && $this->isValidResponse($response->data)) {
            $this->saveData($response->data);
            printf("Success!\n");
        } else {
            printf("Invalid response!\n");
        }
    }

    /**
     * @param $dateStart
     * @param $dateEnd
     * @return Response|null
     */
    private function doRequest($dateStart, $dateEnd)
    {
        $apiKey = 'N7LkblDsc5aen05FJqBQ8wU4qSdmsftwJagVK7UD';
        $host = 'https://api.nasa.gov/neo/rest/v1/feed';
        $response = (new Client)
            ->createRequest()
            ->setMethod('get')
            ->setUrl($host)
            ->setData([
                'start_date' => $dateStart,
                'end_date' => $dateEnd,
                'detailed' => false,
                'api_key' => $apiKey
            ])->send();
        return $response;
    }

    /**
     * @param [] $data
     */
    private function saveData($data)
    {
        printf("Received NEOs: %d\n", $data['element_count']);
        $savedCount = 0;
        foreach ($data['near_earth_objects'] as $date => $neos) {
            foreach ($neos as $neoInfo) {
                foreach ($neoInfo['close_approach_data'] as $neoApproachData) {
                    $neo = new Neo;
                    $neo->setAttributes([
                        'date' => $date,
                        'is_hazardous' => (bool)$neoInfo['is_potentially_hazardous_asteroid'],
                        'name' => $neoInfo['name'],
                        'reference' => $neoInfo['neo_reference_id'],
                        'speed' => (float)$neoApproachData['relative_velocity']['kilometers_per_hour']
                    ]);
                    if ($neo->save(true)) {
                        $savedCount++;
                        printf("\rNeo info saved: %d", $savedCount);
                    }
                }
            }
        }
        printf("\n");
    }

    /**
     * @param [] $response
     * @return bool
     */
    private function isValidResponse($response)
    {
        return isset($response['links']) && isset($response['element_count']) && isset($response['near_earth_objects']);
    }
}