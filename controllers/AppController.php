<?php
/**
 * Created by PhpStorm.
 * User: Denniselite
 * Date: 27/09/2017
 * Time: 03:15
 */

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AppController extends Controller
{
    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $result = ['hello' => 'world!'];
        Yii::$app->response->data  = $result;
        Yii::$app->end();
    }

    public function actionError()
    {
        if (
            is_null($exception = Yii::$app->getErrorHandler()->exception) ||
            $exception instanceof NotFoundHttpException
        ) {
            $exception = new NotFoundHttpException( 'Route is not found.', 404);
        }

        Yii::$app->response->statusCode = $exception->statusCode;
        $result = [
            'error' => [
                'status' => Yii::$app->getResponse()->statusCode,
                'message' => $exception->getMessage()
            ],
            'response' => null
        ];

        Yii::$app->response->data  = $result;
        Yii::$app->end();
    }
}