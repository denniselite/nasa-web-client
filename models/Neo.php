<?php
/**
 * Created by PhpStorm.
 * User: Denniselite
 * Date: 27/09/2017
 * Time: 03:11
 */

namespace app\models;

use yii\mongodb\ActiveRecord;

/**
 * Class Neo
 * @property string $date
 * @property bool $is_hazardous
 * @property string $name
 * @property string reference
 * @property float $speed
 *
 * @package app\models
 */
class Neo extends ActiveRecord
{

    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'neos';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge([
            [['date', 'reference', 'name', 'speed', 'is_hazardous'], 'safe'],
        ], parent::rules());
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'date', 'reference', 'name', 'speed', 'is_hazardous'];
    }
}