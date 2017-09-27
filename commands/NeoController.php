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
        if ($collectionAlreadyExists) {
            Yii::$app->mongodb->getCollection('neos')->drop();
        }
        Yii::$app->mongodb->createCommand()->createCollection('neos');
        echo "Collection 'neos' created...\n";
    }

    public function actionInit3DaysData()
    {
        $this->initCollection();
        $dateTime = new \DateTime();
        $dateEnd = $dateTime->format('Y-m-d');
        $dateStart = $dateTime->sub(new \DateInterval('P3D'))->format('Y-m-d');
        printf("Date start: %s, Date end: %s\n", $dateStart, $dateEnd);
        $params = [
            'start_date' => $dateStart,
            'end_date' => $dateEnd,
            'detailed' => false,
            'host' => 'https://api.nasa.gov/neo/rest/v1/feed'
        ];
        $response = $this->doRequest($params);
        if ($response->isOk && $this->isValidResponse($response->data)) {
            $this->saveLast3DaysData($response->data);
            printf("Success!\n");
        } else {
            printf("Invalid response!\n");
        }
    }

    public function actionInitAllData()
    {
        $this->initCollection();
        $params = [
            'page' => 0,
            'size' => 20,
            'host' => 'https://api.nasa.gov/neo/rest/v1/neo/browse'
        ];
        $response = $this->doRequest($params);
        if ($response->isOk && $this->isValidResponse($response->data)) {
            $responseData = $response->data;

            printf("Receiving NEOs: %d\n", $responseData['page']['total_elements']);
            $savedCount = $this->savePageData($responseData);
            printf("\rNeo info saved: %d", $savedCount);

            while (isset($responseData['links']) && isset($responseData['links']['next'])) {
                $params['page'] += 1;
                $response = $this->doRequest($params);

                if ($response->isOk && $this->isValidResponse($response->data)) {
                    $responseData = $response->data;
                    $savedCount += $this->savePageData($responseData);
                    printf("\rNeo info saved: %d", $savedCount);
                } else {
                    printf("Invalid response for page %d!\n", $params['page']);
                    break;
                }
            }

            printf("Completed!\n");
        } else {
            printf("Invalid response!\n");
        }
    }


    /**
     * @param []$params
     * @return Response|null
     */
    private function doRequest($params)
    {
        $params['api_key'] = 'N7LkblDsc5aen05FJqBQ8wU4qSdmsftwJagVK7UD';
        $response = (new Client)
            ->createRequest()
            ->setMethod('get')
            ->setUrl($params['host'])
            ->setData($params)->send();
        return $response;
    }

    /**
     * @param [] $data
     */
    private function saveLast3DaysData($data)
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

    private function savePageData($data)
    {
        $savedCount = 0;
        foreach ($data['near_earth_objects'] as $date => $neoInfo) {
                foreach ($neoInfo['close_approach_data'] as $neoApproachData) {
                    $neo = new Neo;
                    $neo->setAttributes([
                        'date' => $neoApproachData['close_approach_date'],
                        'is_hazardous' => (bool)$neoInfo['is_potentially_hazardous_asteroid'],
                        'name' => $neoInfo['name'],
                        'reference' => $neoInfo['neo_reference_id'],
                        'speed' => (float)$neoApproachData['relative_velocity']['kilometers_per_hour']
                    ]);
                    if ($neo->save(true)) {
                        $savedCount++;
                    }
                }
        }
        return $savedCount;
    }

    /**
     * @param [] $response
     * @return bool
     */
    private function isValidResponse($response)
    {
        return isset($response['links']) && isset($response['near_earth_objects']);
    }
}