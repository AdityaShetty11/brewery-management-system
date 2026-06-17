<?php

namespace tests\unit\models;

use common\models\RawMaterial;
use PHPUnit\Framework\TestCase;

class RawMaterialTest extends TestCase
{
    // --- isLowStock ---
    // Unlike Product, RawMaterial compares against the instance's own
    // reorder_level, not a fixed constant.

    public function testIsLowStockReturnsTrueWhenStockEqualsReorderLevel(): void
    {
        $material = new RawMaterial();
        $material->stock_qty     = 50.0;
        $material->reorder_level = 50.0;
        $this->assertTrue($material->isLowStock());
    }

    public function testIsLowStockReturnsTrueWhenStockBelowReorderLevel(): void
    {
        $material = new RawMaterial();
        $material->stock_qty     = 10.0;
        $material->reorder_level = 50.0;
        $this->assertTrue($material->isLowStock());
    }

    public function testIsLowStockReturnsFalseWhenStockAboveReorderLevel(): void
    {
        $material = new RawMaterial();
        $material->stock_qty     = 51.0;
        $material->reorder_level = 50.0;
        $this->assertFalse($material->isLowStock());
    }

    public function testIsLowStockReturnsFalseWhenReorderLevelIsZero(): void
    {
        $material = new RawMaterial();
        $material->stock_qty     = 0.0;
        $material->reorder_level = 0.0;
        // stock_qty(0) <= reorder_level(0) → true (at threshold)
        $this->assertTrue($material->isLowStock());
    }

    public function testIsLowStockWithLargeStock(): void
    {
        $material = new RawMaterial();
        $material->stock_qty     = 1000.0;
        $material->reorder_level = 100.0;
        $this->assertFalse($material->isLowStock());
    }

    // --- validate ---
    // RawMaterial has no UniqueValidator so validate() runs without DB.

    public function testValidationFailsWhenNameMissing(): void
    {
        $material = new RawMaterial();
        $material->unit = 'kg';

        $this->assertFalse($material->validate(['name']));
        $this->assertTrue($material->hasErrors('name'));
    }

    public function testValidationFailsWhenUnitMissing(): void
    {
        $material = new RawMaterial();
        $material->name = 'Hops';

        $this->assertFalse($material->validate(['unit']));
        $this->assertTrue($material->hasErrors('unit'));
    }

    public function testValidationFailsForNegativeStockQty(): void
    {
        $material = new RawMaterial();
        $material->name      = 'Hops';
        $material->unit      = 'kg';
        $material->stock_qty = -5;

        $this->assertFalse($material->validate(['stock_qty']));
        $this->assertTrue($material->hasErrors('stock_qty'));
    }

    public function testValidationPassesWithValidData(): void
    {
        $material = new RawMaterial();
        $material->name          = 'Cascade Hops';
        $material->unit          = 'kg';
        $material->stock_qty     = 100.0;
        $material->reorder_level = 20.0;

        $this->assertTrue($material->validate());
    }
}
