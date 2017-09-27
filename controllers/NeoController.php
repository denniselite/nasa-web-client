<?php
/**
 * Created by PhpStorm.
 * User: Denniselite
 * Date: 27/09/2017
 * Time: 03:10
 */

namespace app\controllers;

use app\models\Neo;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;

class NeoController extends ActiveController
{
    public $modelClass = 'app\models\Neo';

    public function actionHazardous()
    {
        $provider = new ActiveDataProvider(['query' => Neo::find()]);
        $provider->query->andWhere(['is_hazardous' => true]);
        return [
            'error' => null,
            'response' => $provider->getModels(),
        ];
    }

    public function actionFastest($hazardous)
    {
        if (($hazardous !== 'true') && ($hazardous !== 'false')) {
            throw new BadRequestHttpException('The hazardous parameter should contains only true or false');
        }

        $fastest = Neo::find()
            ->andWhere(['is_hazardous' => ($hazardous === 'true')])
            ->orderBy('speed DESC')
            ->one();
        return [
            'error' => null,
            'response' => $fastest,
        ];
    }
}