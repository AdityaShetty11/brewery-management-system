<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User ActiveRecord model.
 *
 * @property int         $id
 * @property string      $username
 * @property string      $email
 * @property string      $password_hash
 * @property string      $auth_key
 * @property string|null $verification_token
 * @property string|null $password_reset_token
 * @property int         $status
 * @property int         $created_at
 * @property int         $updated_at
 * @property int|null    $deleted_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    // Status constants

    const STATUS_DELETED  = 0;
    const STATUS_INACTIVE = 9;   // registered but email not verified
    const STATUS_ACTIVE   = 10;

    // Password reset token expiry (in seconds)

    const PASSWORD_RESET_TOKEN_EXPIRE = 3600;   // 1 hour

    // Virtual property — set a plain-text password before save

    private ?string $_password = null;

    // ActiveRecord

    public static function tableName(): string
    {
        return 'user';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_DELETED, self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
        ];
    }

    // IdentityInterface

    public static function findIdentity($id): ?self
    {
        return static::findOne([
            'id'     => $id,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public static function findIdentityByAccessToken($token, $type = null): ?self
    {
        throw new NotSupportedException('findIdentityByAccessToken is not implemented.');
    }

    public function getId(): int
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    // Finders

    public static function findByUsername(string $username): ?self
    {
        return static::findOne([
            'username' => $username,
            'status'   => self::STATUS_ACTIVE,
        ]);
    }

    public static function findByEmail(string $email): ?self
    {
        return static::findOne([
            'email'  => $email,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public static function findByPasswordResetToken(string $token): ?self
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status'               => self::STATUS_ACTIVE,
        ]);
    }

    public static function findByVerificationToken(string $token): ?self
    {
        return static::findOne([
            'verification_token' => $token,
            'status'             => self::STATUS_INACTIVE,
        ]);
    }

    public static function isPasswordResetTokenValid(string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire    = self::PASSWORD_RESET_TOKEN_EXPIRE;

        return $timestamp + $expire >= time();
    }

    // Password helpers

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken(): void
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function generateEmailVerificationToken(): void
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken(): void
    {
        $this->password_reset_token = null;
    }

    // Soft delete

    public function softDelete(): bool
    {
        $this->deleted_at = time();
        $this->status     = self::STATUS_DELETED;
        return $this->save(false);
    }

    // Display helper

    public function getFullName(): string
    {
        return $this->username;
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE   => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_DELETED  => 'Deleted',
            default               => 'Unknown',
        };
    }

    // Relationships

}

