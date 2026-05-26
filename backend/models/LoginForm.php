<?php

namespace backend\models;

use common\models\AuditLog;
use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Backend login form — only allows STATUS_ACTIVE users who hold
 * at least the "staff" role (or above).
 */
class LoginForm extends Model
{
    public string $username   = '';
    public string $password   = '';
    public bool   $rememberMe = true;

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

    public function validatePassword(string $attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();

        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError($attribute, 'Incorrect username or password.');
            return;
        }

        // Only staff-level and above can access backend
        $auth       = Yii::$app->authManager;
        $hasAccess  = $auth->checkAccess($user->id, 'staff')
                   || $auth->checkAccess($user->id, 'brewmaster')
                   || $auth->checkAccess($user->id, 'warehouse')
                   || $auth->checkAccess($user->id, 'admin');

        if (!$hasAccess) {
            $this->addError($attribute, 'You do not have permission to access the admin panel.');
        }
    }

    public function login(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $duration = $this->rememberMe ? 3600 * 24 * 30 : 0;
        $loggedIn = Yii::$app->user->login($this->getUser(), $duration);

        if ($loggedIn) {
            AuditLog::record('backend.login');
        }

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
