<?php

namespace frontend\models;

use common\models\User;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * Step 2 of password reset — sets the new password via a valid token.
 */
class ResetPasswordForm extends Model
{
    public string $password = '';

    private User $_user;

    public function __construct(string $token, array $config = [])
    {
        $user = User::findByPasswordResetToken($token);

        if (!$user) {
            throw new InvalidArgumentException('Password reset token is invalid or has expired.');
        }

        $this->_user = $user;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 8],
        ];
    }

    public function resetPassword(): bool
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        $user->generateAuthKey();

        return $user->save(false);
    }
}
