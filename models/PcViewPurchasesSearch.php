<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PcViewPurchases;

/**
 * PcViewPurchasesSearch represents the model behind the search form of `app\models\PcViewPurchases`.
 */
class PcViewPurchasesSearch extends PcViewPurchases
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'area', 'creator_id', 'created_at', 'modifier_id', 'modified_at'], 'integer'],
            [['title', 'lom', 'factor', 'creator', 'modifier', 'purchase_code'], 'safe'],
            [['done'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function toPersianDigits($str)
    {
        $persianDigits = [
            '0' => '۰',
            '1' => '۱',
            '2' => '۲',
            '3' => '۳',
            '4' => '۴',
            '5' => '۵',
            '6' => '۶',
            '7' => '۷',
            '8' => '۸',
            '9' => '۹'
        ];

        return strtr($str, $persianDigits);
    }

    public function toEnglishDigits($str)
    {
        $englishDigits = [
            '۰' => '0',
            '۱' => '1',
            '۲' => '2',
            '۳' => '3',
            '۴' => '4',
            '۵' => '5',
            '۶' => '6',
            '۷' => '7',
            '۸' => '8',
            '۹' => '9'
        ];

        return (int)strtr($str, $englishDigits);
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
        $query = PcViewPurchases::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'area' => $this->area,
            'creator_id' => $this->creator_id,
            'created_at' => $this->created_at,
            'modifier_id' => $this->modifier_id,
            'modified_at' => $this->modified_at,
            'done' => $this->done,
        ]);

        $query->andFilterWhere(['ilike', 'title', $this->title])
            ->andFilterWhere(['ilike', 'lom', $this->lom])
            ->andFilterWhere(['ilike', 'factor', $this->factor])
            ->andFilterWhere(['ilike', 'creator', $this->creator])
            ->andFilterWhere(['ilike', 'modifier', $this->modifier])
            ->andFilterWhere(['ilike', 'purchase_code', $this->toPersianDigits($this->purchase_code)]);

        return $dataProvider;
    }
}
