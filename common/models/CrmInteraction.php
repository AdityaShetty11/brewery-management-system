<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * CrmInteraction ActiveRecord model.
 *
 * @property int         $id
 * @property int         $company_id
 * @property int|null    $contact_id
 * @property int         $staff_id
 * @property string      $type        call|email|meeting|note
 * @property string      $summary
 * @property string      $interaction_at
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 */
class CrmInteraction extends ActiveRecord
{
    const TYPE_CALL    = 'call';
    const TYPE_EMAIL   = 'email';
    const TYPE_MEETING = 'meeting';
    const TYPE_NOTE    = 'note';

    public static function tableName(): string
    {
        return 'crm_interaction';
    }

    public function rules(): array
    {
        return [
            [['company_id', 'staff_id', 'type', 'summary'], 'required'],
            [['company_id', 'contact_id', 'staff_id'], 'integer'],
            [['type'], 'in', 'range' => [self::TYPE_CALL, self::TYPE_EMAIL, self::TYPE_MEETING, self::TYPE_NOTE]],
            [['summary'], 'string'],
            [['interaction_at'], 'safe'],
            [['company_id'], 'exist', 'skipOnError' => true,
                'targetClass' => CustomerCompany::class,
                'targetAttribute' => ['company_id' => 'id'],
            ],
            [['contact_id'], 'exist', 'skipOnError' => true, 'allowArray' => false,
                'targetClass' => CustomerContact::class,
                'targetAttribute' => ['contact_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'             => 'ID',
            'company_id'     => 'Company',
            'contact_id'     => 'Contact',
            'staff_id'       => 'Logged By',
            'type'           => 'Type',
            'summary'        => 'Summary / Notes',
            'interaction_at' => 'Date & Time',
            'created_at'     => 'Created',
        ];
    }

    // Soft delete

    public static function find(): ActiveQuery
    {
        return parent::find()->andWhere(['crm_interaction.deleted_at' => null]);
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

    public function getContact(): ActiveQuery
    {
        return $this->hasOne(CustomerContact::class, ['id' => 'contact_id']);
    }

    public function getStaff(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'staff_id']);
    }

    // Static helpers

    public static function typeLabels(): array
    {
        return [
            self::TYPE_CALL    => 'Call',
            self::TYPE_EMAIL   => 'Email',
            self::TYPE_MEETING => 'Meeting',
            self::TYPE_NOTE    => 'Note',
        ];
    }

    public static function typeIcons(): array
    {
        return [
            self::TYPE_CALL    => 'bi-telephone-fill',
            self::TYPE_EMAIL   => 'bi-envelope-fill',
            self::TYPE_MEETING => 'bi-people-fill',
            self::TYPE_NOTE    => 'bi-journal-text',
        ];
    }

    public function getTypeLabel(): string
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }

    public function getTypeIcon(): string
    {
        return self::typeIcons()[$this->type] ?? 'bi-chat';
    }

    public static function typeBadgeClass(): array
    {
        return [
            self::TYPE_CALL    => 'bg-primary',
            self::TYPE_EMAIL   => 'bg-info text-dark',
            self::TYPE_MEETING => 'bg-success',
            self::TYPE_NOTE    => 'bg-secondary',
        ];
    }

    public function getTypeBadge(): string
    {
        $class = self::typeBadgeClass()[$this->type] ?? 'bg-secondary';
        return "<span class=\"badge {$class}\"><i class=\"bi {$this->getTypeIcon()} me-1\"></i>{$this->getTypeLabel()}</span>";
    }
}
