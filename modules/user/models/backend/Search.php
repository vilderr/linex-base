<?php

namespace linex\base\modules\user\models\backend;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use linex\base\modules\user\models\User;
use linex\base\modules\user\UserModule;

/**
 * Class Search
 * @package linex\base\modules\user\models\backend
 */
class Search extends Model
{
    public $id;
    public $username;
    public $email;
    public $status;
    public $date_from;
    public $date_to;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['username', 'email'], 'safe'],
            [['date_from', 'date_to'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'created_at' => UserModule::t('USER_CREATED'),
            'updated_at' => UserModule::t('USER_UPDATED'),
            'username'   => UserModule::t('USER_USERNAME'),
            'email'      => UserModule::t('USER_EMAIL'),
            'status'     => UserModule::t('USER_STATUS'),
            'date_from'  => UserModule::t('USER_DATE_FROM'),
            'date_to'    => UserModule::t('USER_DATE_TO'),
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }
        $query->andFilterWhere([
            'id'     => $this->id,
            'status' => $this->status,
        ]);
        $query
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['>=', 'created_at', $this->date_from ? strtotime($this->date_from . ' 00:00:00') : null])
            ->andFilterWhere(['<=', 'created_at', $this->date_to ? strtotime($this->date_to . ' 23:59:59') : null]);

        return $dataProvider;
    }
}