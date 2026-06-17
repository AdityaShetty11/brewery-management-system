<?php

namespace tests\unit\models;

use common\models\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    // --- allStatuses ---

    public function testAllStatusesReturnsAllFiveStatuses(): void
    {
        $statuses = Order::allStatuses();
        $this->assertCount(5, $statuses);
        $this->assertContains(Order::STATUS_DRAFT, $statuses);
        $this->assertContains(Order::STATUS_CONFIRMED, $statuses);
        $this->assertContains(Order::STATUS_IN_PRODUCTION, $statuses);
        $this->assertContains(Order::STATUS_DELIVERED, $statuses);
        $this->assertContains(Order::STATUS_CANCELLED, $statuses);
    }

    // --- canTransitionTo ---

    /**
     * @dataProvider validTransitionProvider
     */
    public function testCanTransitionToAllowsValidTransitions(string $from, string $to): void
    {
        $order = new Order();
        $order->status = $from;
        $this->assertTrue($order->canTransitionTo($to));
    }

    public static function validTransitionProvider(): array
    {
        return [
            'draft → confirmed'         => [Order::STATUS_DRAFT,         Order::STATUS_CONFIRMED],
            'draft → cancelled'         => [Order::STATUS_DRAFT,         Order::STATUS_CANCELLED],
            'confirmed → in_production' => [Order::STATUS_CONFIRMED,     Order::STATUS_IN_PRODUCTION],
            'confirmed → cancelled'     => [Order::STATUS_CONFIRMED,     Order::STATUS_CANCELLED],
            'in_production → delivered' => [Order::STATUS_IN_PRODUCTION, Order::STATUS_DELIVERED],
            'in_production → cancelled' => [Order::STATUS_IN_PRODUCTION, Order::STATUS_CANCELLED],
        ];
    }

    /**
     * @dataProvider invalidTransitionProvider
     */
    public function testCanTransitionToBlocksInvalidTransitions(string $from, string $to): void
    {
        $order = new Order();
        $order->status = $from;
        $this->assertFalse($order->canTransitionTo($to));
    }

    public static function invalidTransitionProvider(): array
    {
        return [
            'draft → delivered'           => [Order::STATUS_DRAFT,         Order::STATUS_DELIVERED],
            'draft → in_production'       => [Order::STATUS_DRAFT,         Order::STATUS_IN_PRODUCTION],
            'delivered → confirmed'       => [Order::STATUS_DELIVERED,     Order::STATUS_CONFIRMED],
            'delivered → cancelled'       => [Order::STATUS_DELIVERED,     Order::STATUS_CANCELLED],
            'cancelled → draft'           => [Order::STATUS_CANCELLED,     Order::STATUS_DRAFT],
            'in_production → confirmed'   => [Order::STATUS_IN_PRODUCTION, Order::STATUS_CONFIRMED],
        ];
    }

    // --- getStatusLabel ---

    public function testGetStatusLabelReturnsCorrectLabels(): void
    {
        $cases = [
            Order::STATUS_DRAFT         => 'Draft',
            Order::STATUS_CONFIRMED     => 'Confirmed',
            Order::STATUS_IN_PRODUCTION => 'In Production',
            Order::STATUS_DELIVERED     => 'Delivered',
            Order::STATUS_CANCELLED     => 'Cancelled',
        ];

        foreach ($cases as $status => $expectedLabel) {
            $order = new Order();
            $order->status = $status;
            $this->assertSame($expectedLabel, $order->getStatusLabel(), "Label mismatch for: $status");
        }
    }

    // --- isEditable ---

    public function testIsEditableReturnsTrueOnlyForDraft(): void
    {
        $draft = new Order();
        $draft->status = Order::STATUS_DRAFT;
        $this->assertTrue($draft->isEditable());

        foreach ([
            Order::STATUS_CONFIRMED,
            Order::STATUS_IN_PRODUCTION,
            Order::STATUS_DELIVERED,
            Order::STATUS_CANCELLED,
        ] as $status) {
            $order = new Order();
            $order->status = $status;
            $this->assertFalse($order->isEditable(), "Expected not editable for: $status");
        }
    }

    // --- getFormattedTotal ---

    public function testGetFormattedTotalFormatsWithDollarAndTwoDecimals(): void
    {
        $order = new Order();
        $order->total_amount = 1234.5;
        $this->assertSame('$1,234.50', $order->getFormattedTotal());
    }

    public function testGetFormattedTotalWithZeroAmount(): void
    {
        $order = new Order();
        $order->total_amount = 0;
        $this->assertSame('$0.00', $order->getFormattedTotal());
    }

    // --- getStatusBadge ---

    public function testGetStatusBadgeReturnsSpanWithLabelAndClass(): void
    {
        $order = new Order();
        $order->status = Order::STATUS_DRAFT;
        $badge = $order->getStatusBadge();
        $this->assertStringContainsString('<span', $badge);
        $this->assertStringContainsString('badge', $badge);
        $this->assertStringContainsString('Draft', $badge);
    }

    // --- transitionTo error path ---

    public function testTransitionToReturnsFalseAndAddsErrorForInvalidTransition(): void
    {
        $order = new Order();
        $order->status = Order::STATUS_DELIVERED;

        $result = $order->transitionTo(Order::STATUS_CONFIRMED);

        $this->assertFalse($result);
        $this->assertTrue($order->hasErrors('status'));
        $this->assertStringContainsString('delivered', $order->getFirstError('status'));
        $this->assertStringContainsString('confirmed', $order->getFirstError('status'));
    }

    public function testTransitionToReturnsFalseForTerminalCancelledStatus(): void
    {
        $order = new Order();
        $order->status = Order::STATUS_CANCELLED;

        $result = $order->transitionTo(Order::STATUS_DRAFT);

        $this->assertFalse($result);
        $this->assertTrue($order->hasErrors('status'));
    }

    // --- validate ---

    public function testValidationFailsWhenCustomerIdMissing(): void
    {
        $order = new Order();
        $order->status = Order::STATUS_DRAFT;

        $this->assertFalse($order->validate(['customer_id']));
        $this->assertTrue($order->hasErrors('customer_id'));
    }

    public function testValidationFailsForUnrecognisedStatus(): void
    {
        $order = new Order();
        $order->customer_id = 1;
        $order->status      = 'pending'; // not in allStatuses()

        $this->assertFalse($order->validate(['status']));
        $this->assertTrue($order->hasErrors('status'));
    }

    public function testValidationFailsForNegativeTotalAmount(): void
    {
        $order = new Order();
        $order->customer_id  = 1;
        $order->status       = Order::STATUS_DRAFT;
        $order->total_amount = -10;

        $this->assertFalse($order->validate(['total_amount']));
        $this->assertTrue($order->hasErrors('total_amount'));
    }

    public function testValidationPassesWithMinimalValidData(): void
    {
        $order = new Order();
        $order->customer_id  = 1;
        $order->status       = Order::STATUS_DRAFT;
        $order->total_amount = 0;

        $this->assertTrue($order->validate());
    }
}
