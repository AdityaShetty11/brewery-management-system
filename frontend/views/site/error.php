<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */

$this->title = $name;
?>

<div class="text-center py-5">
    <div class="display-1 fw-bold text-muted">:(</div>
    <h2 class="mt-3"><?= nl2br(\yii\helpers\Html::encode($name)) ?></h2>
    <p class="text-muted"><?= nl2br(\yii\helpers\Html::encode($message)) ?></p>
    <a href="<?= \yii\helpers\Url::home() ?>" class="btn btn-dark mt-3">
        <i class="bi bi-house me-1"></i>Go Home
    </a>
</div>
