<?php

namespace tests\unit\models;

use common\models\OrderItem;
use PHPUnit\Framework\TestCase;

class OrderItemTest extends TestCase
{
    // --- getFormattedSubtotal ---

    public function testGetFormattedSubtotalFormatsWithDollarAndTwoDecimals(): void
    {
        $item = new OrderItem();
        $item->subtotal = 59.9;
        $this->assertSame('$59.90', $item->getFormattedSubtotal());
    }

    public function testGetFormattedSubtotalWithLargeValue(): void
    {
        $item = new OrderItem();
        $item->subtotal = 2400.00;
        $this->assertSame('$2,400.00', $item->getFormattedSubtotal());
    }

    public function testGetFormattedSubtotalWithZero(): void
    {
        $item = new OrderItem();
        $item->subtotal = 0;
        $this->assertSame('$0.00', $item->getFormattedSubtotal());
    }

    // --- validate ---
    // OrderItem has an ExistValidator on product_id but it has skipOnError => true,
    // so validating only the basic fields won't hit the DB.

    public function testValidationFailsWhenOrderIdMissing(): void
    {
        $item = new OrderItem();
        $item->product_id = 1;
        $item->quantity   = 2;
        $item->unit_price = 10.00;

        $this->assertFalse($item->validate(['order_id']));
        $this->assertTrue($item->hasErrors('order_id'));
    }

    public function testValidationFailsWhenQuantityIsZero(): void
    {
        $item = new OrderItem();
        $item->order_id   = 1;
        $item->product_id = 1;
        $item->quantity   = 0;
        $item->unit_price = 10.00;

        $this->assertFalse($item->validate(['quantity']));
        $this->assertTrue($item->hasErrors('quantity'));
    }

    public function testValidationFailsForNegativeUnitPrice(): void
    {
        $item = new OrderItem();
        $item->order_id   = 1;
        $item->product_id = 1;
        $item->quantity   = 1;
        $item->unit_price = -5.00;

        $this->assertFalse($item->validate(['unit_price']));
        $this->assertTrue($item->hasErrors('unit_price'));
    }
}
