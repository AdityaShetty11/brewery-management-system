<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * CustomerContact ActiveRecord model.
 *
 * @property int         $id
 * @property int         $company_id
 * @property int|null    $user_id
 * @property string      $first_name
 * @property string      $last_name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $role
 * @property string|null $notes
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 */
class CustomerContact extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'customer_contact';
    }

    public function rules(): array
    {
        return [
            [['company_id', 'first_name', 'last_name'], 'required'],
            [['company_id', 'user_id'], 'integer'],
            [['first_name', 'last_name'], 'string', 'max' => 75],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 100],
            [['phone'], 'string', 'max' => 30],
            [['role'], 'string', 'max' => 100],
            [['notes'], 'string'],
            [['company_id'], 'exist', 'skipOnError' => true,
                'targetClass' => CustomerCompany::class,
                'targetAttribute' => ['company_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'         => 'ID',
            'company_id' => 'Company',
            'user_id'    => 'Portal User',
            'first_name' => 'First Name',
            'last_name'  => 'Last Name',
            'email'      => 'Email',
            'phone'      => 'Phone',
            'role'       => 'Role / Title',
            'notes'      => 'Notes',
            'created_at' => 'Created',
            'updated_at' => 'Last Updated',
        ];
    }

    // Soft delete

    public static function find(): ActiveQuery
    {
        return parent::find()->andWhere(['customer_contact.deleted_at' => null]);
    }

    public function softDelete(): bool
    {
        $this->deleted_at = date('Y-m-d H:i:s');
        return $this->save(false);
    }

    // Relationships

    public function getCompany(): ActiveQuery
    {
        return $this->hasOne(CustomerCompany::class, ['id' => 'company_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    // Helpers

    public function getFullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
