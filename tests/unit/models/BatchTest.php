<?php

namespace tests\unit\models;

use common\models\Batch;
use PHPUnit\Framework\TestCase;

class BatchTest extends TestCase
{
    // --- canTransitionTo ---

    /**
     * @dataProvider validBatchTransitionProvider
     */
    public function testCanTransitionToAllowsValidTransitions(string $from, string $to): void
    {
        $batch = new Batch();
        $batch->status = $from;
        $this->assertTrue($batch->canTransitionTo($to));
    }

    public static function validBatchTransitionProvider(): array
    {
        return [
            'planned → brewing'      => [Batch::STATUS_PLANNED,    Batch::STATUS_BREWING],
            'brewing → fermenting'   => [Batch::STATUS_BREWING,    Batch::STATUS_FERMENTING],
            'fermenting → packaging' => [Batch::STATUS_FERMENTING, Batch::STATUS_PACKAGING],
            'packaging → completed'  => [Batch::STATUS_PACKAGING,  Batch::STATUS_COMPLETED],
        ];
    }

    /**
     * @dataProvider invalidBatchTransitionProvider
     */
    public function testCanTransitionToBlocksInvalidTransitions(string $from, string $to): void
    {
        $batch = new Batch();
        $batch->status = $from;
        $this->assertFalse($batch->canTransitionTo($to));
    }

    public static function invalidBatchTransitionProvider(): array
    {
        return [
            'planned → fermenting'  => [Batch::STATUS_PLANNED,    Batch::STATUS_FERMENTING],
            'planned → completed'   => [Batch::STATUS_PLANNED,    Batch::STATUS_COMPLETED],
            'completed → planned'   => [Batch::STATUS_COMPLETED,  Batch::STATUS_PLANNED],
            'completed → brewing'   => [Batch::STATUS_COMPLETED,  Batch::STATUS_BREWING],
            'brewing → packaging'   => [Batch::STATUS_BREWING,    Batch::STATUS_PACKAGING],
        ];
    }

    // --- getNextStatus ---

    public function testGetNextStatusFollowsLinearWorkflow(): void
    {
        $cases = [
            Batch::STATUS_PLANNED    => Batch::STATUS_BREWING,
            Batch::STATUS_BREWING    => Batch::STATUS_FERMENTING,
            Batch::STATUS_FERMENTING => Batch::STATUS_PACKAGING,
            Batch::STATUS_PACKAGING  => Batch::STATUS_COMPLETED,
            Batch::STATUS_COMPLETED  => null,
        ];

        foreach ($cases as $current => $expected) {
            $batch = new Batch();
            $batch->status = $current;
            $this->assertSame($expected, $batch->getNextStatus(), "getNextStatus() mismatch for: $current");
        }
    }

    // --- getStatusLabel ---

    public function testGetStatusLabelReturnsNonEmptyString(): void
    {
        foreach ([
            Batch::STATUS_PLANNED,
            Batch::STATUS_BREWING,
            Batch::STATUS_FERMENTING,
            Batch::STATUS_PACKAGING,
            Batch::STATUS_COMPLETED,
        ] as $status) {
            $batch = new Batch();
            $batch->status = $status;
            $label = $batch->getStatusLabel();
            $this->assertIsString($label);
            $this->assertNotEmpty($label, "getStatusLabel() empty for: $status");
        }
    }

    // --- transitionTo error path ---

    public function testTransitionToReturnsFalseAndAddsErrorForInvalidTransition(): void
    {
        $batch = new Batch();
        $batch->status = Batch::STATUS_COMPLETED;

        $result = $batch->transitionTo(Batch::STATUS_PLANNED);

        $this->assertFalse($result);
        $this->assertTrue($batch->hasErrors('status'));
        $this->assertStringContainsString('completed', $batch->getFirstError('status'));
        $this->assertStringContainsString('planned', $batch->getFirstError('status'));
    }

    public function testTransitionToReturnsFalseForSkippingAStep(): void
    {
        $batch = new Batch();
        $batch->status = Batch::STATUS_PLANNED;

        $result = $batch->transitionTo(Batch::STATUS_FERMENTING);

        $this->assertFalse($result);
        $this->assertTrue($batch->hasErrors('status'));
    }
}
