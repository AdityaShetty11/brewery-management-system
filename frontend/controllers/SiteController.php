<?php

namespace frontend\controllers;

use frontend\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * Handles public pages and all authentication actions.
 */
class SiteController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow'   => true,
                        'roles'   => ['?'],   // guests only
                    ],
                    [
                        'actions' => ['logout'],
                        'allow'   => true,
                        'roles'   => ['@'],   // authenticated only
                    ],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return [
            'error'   => ['class' => 'yii\web\ErrorAction'],
            'captcha' => ['class' => 'yii\captcha\CaptchaAction', 'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null],
        ];
    }

    // Public pages

    public function actionIndex(): string
    {
        return $this->render('index');
    }

    public function actionAbout(): string
    {
        return $this->render('about');
    }

    public function actionContact(): string
    {
        return $this->render('contact');
    }

    // Auth

    public function actionLogin(): \yii\web\Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout(): \yii\web\Response
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionSignup(): \yii\web\Response|string
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->signup()) {
                Yii::$app->session->setFlash('success',
                    'Registration successful! You can now sign in.'
                );
                return $this->redirect(['site/login']);
            }
        }

        return $this->render('signup', ['model' => $model]);
    }

    public function actionVerifyEmail(string $token): \yii\web\Response
    {
        try {
            $model = new VerifyEmailForm(['token' => $token]);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $user = $model->verifyEmail();

        if ($user) {
            Yii::$app->user->login($user);
            Yii::$app->session->setFlash('success', 'Your email has been verified. Welcome!');
        } else {
            Yii::$app->session->setFlash('error', 'Verification failed. The link may have expired.');
        }

        return $this->goHome();
    }

    public function actionRequestPasswordReset(): \yii\web\Response|string
    {
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }

            Yii::$app->session->setFlash('error',
                'Sorry, we could not reset the password for that email. Please try again.'
            );
        }

        return $this->render('requestPasswordResetToken', ['model' => $model]);
    }

    public function actionResetPassword(string $token): \yii\web\Response|string
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved. You can now log in.');
            return $this->goHome();
        }

        return $this->render('resetPassword', ['model' => $model]);
    }
}
