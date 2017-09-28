<?php

use yii\helpers\Json;

class MainRouteCest
{
    public function _before(\FunctionalTester $I)
    {
        $I->amOnPage('/');
    }

    public function openMainPage(\FunctionalTester $I)
    {
        $array = ['hello' => 'world!'];
        $I->see(Json::encode($array));
        $I->seeResponseCodeIs(200);
    }

}
