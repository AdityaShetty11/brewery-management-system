<?php

namespace backend\modules\crm;

use Yii;
use yii\filters\AccessControl;

/**
 * CRM module — manages customer companies, contacts, and interaction history.
 *
 * All actions in this module require the `manageCrm` permission.
 * Registered in backend/config/main.php under 'modules'.
 */
class CrmModule extends \yii\base\Module
{
    public $controllerNamespace = 'backend\modules\crm\controllers';

    public function init(): void
    {
        parent::init();
        Yii::$app->user->loginUrl = ['/site/login'];
    }
}
