<?php

namespace backend\modules\product;

/**
 * Product module — manages product categories and the product catalogue.
 * Registered in backend/config/main.php under 'modules' as 'product'.
 */
class ProductModule extends \yii\base\Module
{
    public $controllerNamespace = 'backend\modules\product\controllers';

    public function init(): void
    {
        parent::init();
    }
}
