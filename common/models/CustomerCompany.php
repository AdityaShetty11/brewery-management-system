<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * CustomerCompany ActiveRecord model.
 *
 * @property int         $id
 * @property string      $name
 * @property string|null $industry
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 * @property string|null $city
 * @property string|null $country
 * @property string|null $notes
 * @property int|null    $created_by
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 */
class CustomerCompany extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'customer_company';
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 150],
            [['industry', 'city', 'country'], 'string', 'max' => 100],
            [['phone'], 'string', 'max' => 30],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 100],
            [['address', 'notes'], 'string'],
            [['created_by'], 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'         => 'ID',
            'name'       => 'Company Name',
            'industry'   => 'Industry',
            'phone'      => 'Phone',
            'email'      => 'Email',
            'address'    => 'Address',
            'city'       => 'City',
            'country'    => 'Country',
            'notes'      => 'Notes',
            'created_by' => 'Created By',
            'created_at' => 'Created',
            'updated_at' => 'Last Updated',
        ];
    }

    // Soft delete

    public static function find(): ActiveQuery
    {
        return parent::find()->andWhere(['deleted_at' => null]);
    }

    public function softDelete(): bool
    {
        $this->deleted_at = date('Y-m-d H:i:s');
        return $this->save(false);
    }

    // Relationships

    public function getContacts(): ActiveQuery
    {
        return $this->hasMany(CustomerContact::class, ['company_id' => 'id'])
                    ->andWhere(['customer_contact.deleted_at' => null]);
    }

    public function getInteractions(): ActiveQuery
    {
        return $this->hasMany(CrmInteraction::class, ['company_id' => 'id'])
                    ->andWhere(['crm_interaction.deleted_at' => null])
                    ->orderBy(['crm_interaction.interaction_at' => SORT_DESC]);
    }

    public function getCreatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    // Helpers

    public function getFullAddress(): string
    {
        return implode(', ', array_filter([$this->address, $this->city, $this->country]));
    }
}
