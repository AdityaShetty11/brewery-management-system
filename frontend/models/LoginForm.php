<?php

namespace frontend\models;

use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Validates credentials and logs the user in.
 * Writes an audit_log entry on every successful login.
 */
class LoginForm extends Model
{
    public string $username    = '';
    public string $password    = '';
    public bool   $rememberMe  = true;

    private ?User $_user = null;

    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username'   => 'Username',
            'password'   => 'Password',
            'rememberMe' => 'Remember Me',
        ];
    }

    /**
     * Custom validator: checks password against the hash in the DB.
     */
    public function validatePassword(string $attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();

        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError($attribute, 'Incorrect username or password.');
        }
    }

    public function login(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $duration = $this->rememberMe ? 3600 * 24 * 30 : 0;
        $loggedIn = Yii::$app->user->login($this->getUser(), $duration);

        return $loggedIn;
    }

    private function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }
        return $this->_user;
    }
}
