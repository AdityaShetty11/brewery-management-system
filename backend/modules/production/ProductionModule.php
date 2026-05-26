<?php

namespace backend\modules\production;

/**
 * Production module — manages production orders and brewing batches.
 * Registered in backend/config/main.php under 'modules' as 'production'.
 * Requires `manageProduction` permission (brewmaster role and above).
 */
class ProductionModule extends \yii\base\Module
{
    public $controllerNamespace = 'backend\modules\production\controllers';

    public function init(): void
    {
        parent::init();
    }
}
