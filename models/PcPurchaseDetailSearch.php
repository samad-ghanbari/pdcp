<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PcPurchaseDetail;

/**
 * PcPurchaseDetailSearch represents the model behind the search form of `app\models\PcPurchaseDetail`.
 */
class PcPurchaseDetailSearch extends PcPurchaseDetail
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'purchase_id', 'quantity'], 'integer'],
            [['equipment_type', 'equipment_brand', 'equipment_model', 'provider', 'equipment_photo', 'descriptions'], 'safe'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = PcPurchaseDetail::find();

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
            'purchase_id' => $this->purchase_id,
            'quantity' => $this->quantity,
        ]);

        $query->andFilterWhere(['ilike', 'equipment_type', $this->equipment_type])
            ->andFilterWhere(['ilike', 'equipment_brand', $this->equipment_brand])
            ->andFilterWhere(['ilike', 'equipment_model', $this->equipment_model])
            ->andFilterWhere(['ilike', 'provider', $this->provider])
            ->andFilterWhere(['ilike', 'equipment_photo', $this->equipment_photo])
            ->andFilterWhere(['ilike', 'descriptions', $this->descriptions]);

        return $dataProvider;
    }
}
