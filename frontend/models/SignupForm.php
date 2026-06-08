<?php

namespace frontend\models;

use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Handles new customer registration.
 * After saving, the user is STATUS_INACTIVE until email is verified.
 */
class SignupForm extends Model
{
    public string $username = '';
    public string $email    = '';
    public string $password = '';

    public function rules(): array
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => User::class, 'message' => 'This username is already taken.'],
            ['username', 'string', 'min' => 2, 'max' => 50],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 100],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'This email address is already registered.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 8],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Username',
            'email'    => 'Email Address',
            'password' => 'Password',
        ];
    }

    /**
     * Creates a new inactive user and sends the verification email.
     */
    public function signup(): ?User
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email    = $this->email;
        $user->status   = User::STATUS_ACTIVE; // auto-activate (no email verification required locally)

        $user->setPassword($this->password);
        $user->generateAuthKey();

        if (!$user->save()) {
            return null;
        }

        // Assign default "customer" role via RBAC
        $auth = Yii::$app->authManager;
        $customerRole = $auth->getRole('customer');
        if ($customerRole) {
            $auth->assign($customerRole, $user->getId());
        }

        return $user;
    }

    private function sendVerificationEmail(User $user): void
    {
        Yii::$app->mailer->compose(
            ['html' => 'emailVerification-html', 'text' => 'emailVerification-text'],
            ['user' => $user]
        )
        ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
        ->setTo($user->email)
        ->setSubject('Account verification for ' . Yii::$app->name)
        ->send();
    }
}
