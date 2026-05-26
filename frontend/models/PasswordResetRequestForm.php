<?php

namespace frontend\models;

use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Step 1 of password reset — accepts email, sends reset link.
 */
class PasswordResetRequestForm extends Model
{
    public string $email = '';

    public function rules(): array
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass'     => User::class,
                'filter'          => ['status' => User::STATUS_ACTIVE],
                'message'         => 'No active account found with that email address.',
            ],
        ];
    }

    public function sendEmail(): bool
    {
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email'  => $this->email,
        ]);

        if (!$user) {
            return false;
        }

        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }

        return Yii::$app->mailer->compose(
            ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
            ['user' => $user]
        )
        ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
        ->setTo($user->email)
        ->setSubject('Password reset for ' . Yii::$app->name)
        ->send();
    }
}
