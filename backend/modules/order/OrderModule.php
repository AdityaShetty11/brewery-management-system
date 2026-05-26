<?php

namespace backend\modules\order;

/**
 * Order module — manages all sales orders and their status workflow.
 * Registered in backend/config/main.php under 'modules' as 'order'.
 */
class OrderModule extends \yii\base\Module
{
    public $controllerNamespace = 'backend\modules\order\controllers';

    public function init(): void
    {
        parent::init();
    }
}
