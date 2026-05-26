<?php

namespace frontend\models;

use common\models\User;
use yii\base\Model;

/**
 * Activates the user account using the email verification token.
 */
class VerifyEmailForm extends Model
{
    public string $token = '';

    private ?User $_user = null;

    public function rules(): array
    {
        return [
            ['token', 'required'],
            ['token', 'validateToken'],
        ];
    }

    public function validateToken(string $attribute): void
    {
        if (!$this->hasErrors()) {
            $this->_user = User::findByVerificationToken($this->token);

            if (!$this->_user) {
                $this->addError($attribute, 'Verification token is invalid or has already been used.');
            }
        }
    }

    public function verifyEmail(): ?User
    {
        if (!$this->validate()) {
            return null;
        }

        $user = $this->_user;
        $user->status             = User::STATUS_ACTIVE;
        $user->verification_token = null;

        return $user->save(false) ? $user : null;
    }
}
