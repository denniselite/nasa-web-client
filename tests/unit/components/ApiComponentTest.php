<?php
namespace tests\models;

use app\components\Api;
use Yii;

class ApiComponentTest extends \Codeception\Test\Unit
{
    public function testEmptySettings()
    {
        $api = new Api;
        try {
            $result = $api->getAllData(0);
        } catch (\Exception $e) {
            $this->assertInstanceOf('yii\httpclient\Exception', $e);
        }
    }

    public function testWrongKeySettings()
    {
        $api = new Api;
        $api->endPoint = Yii::$app->api->endPoint;
        $api->apiKey = Yii::$app->api->apiKey . 'WRONG_KEY';
        try {
            $result = $api->getAllData(0);
        } catch (\Exception $e) {
            $this->assertInstanceOf('yii\web\ServerErrorHttpException', $e);
        }
    }

    public function testNoDataRequest()
    {
        $api = new Api;
        $api->endPoint = Yii::$app->api->endPoint;
        $api->apiKey = Yii::$app->api->apiKey;
        try {
            $result = $api->getAllData(0);
        } catch (\Exception $e) {
            $this->assertInstanceOf('yii\web\ServerErrorHttpException', $e);
        }
    }

    public function testCorrectPageResponse()
    {
        $api = new Api;
        $api->endPoint = Yii::$app->api->endPoint;
        $api->apiKey = Yii::$app->api->apiKey;
        $result = $api->getAllData(0);
        $response = $result->data;
        expect_that(isset($response['links']) && isset($response['page']) && isset($response['near_earth_objects']));
    }

    public function testCorrectFeedResponse()
    {
        $api = new Api;
        $api->endPoint = Yii::$app->api->endPoint;
        $api->apiKey = Yii::$app->api->apiKey;
        $dateTime = new \DateTime();
        $dateEnd = $dateTime->format('Y-m-d');
        $dateStart = $dateTime->sub(new \DateInterval('P3D'))->format('Y-m-d');
        $result = $api->getDailyNeosInfo($dateStart, $dateEnd);
        $response = $result->data;
        expect_that(isset($response['links']) && isset($response['element_count']) && isset($response['near_earth_objects']));
    }

    public function testWrongDatesFeedResponse()
    {
        $api = new Api;
        $api->endPoint = Yii::$app->api->endPoint;
        $api->apiKey = Yii::$app->api->apiKey;
        try {
            $result = $api->getDailyNeosInfo('wrong', 'wrong');
        } catch (\Exception $e) {
            $this->assertInstanceOf('yii\web\ServerErrorHttpException', $e);
        }
    }
}
