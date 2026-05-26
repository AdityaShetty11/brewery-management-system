<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * AuditLog ActiveRecord model.
 *
 * @property int         $id
 * @property int|null    $user_id
 * @property string      $action
 * @property string|null $model
 * @property int|null    $model_id
 * @property array|null  $old_value
 * @property array|null  $new_value
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 */
class AuditLog extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'audit_log';
    }

    /**
     * Records a critical system action. Call this from anywhere in the app.
     */
    public static function record(
        string  $action,
        ?string $model   = null,
        ?int    $modelId = null,
        mixed   $oldValue = null,
        mixed   $newValue = null
    ): void {
        $log             = new self();
        $log->user_id    = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        $log->action     = $action;
        $log->model      = $model;
        $log->model_id   = $modelId;
        $log->old_value  = $oldValue ? json_encode($oldValue) : null;
        $log->new_value  = $newValue ? json_encode($newValue) : null;
        $log->ip_address = Yii::$app->request->userIP;
        $log->user_agent = Yii::$app->request->userAgent;
        $log->save(false);
    }

    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
