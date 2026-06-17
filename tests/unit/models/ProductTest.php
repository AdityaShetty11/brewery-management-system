<?php

namespace tests\unit\models;

use common\models\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    // --- isLowStock ---

    public function testIsLowStockReturnsTrueWhenStockAtThreshold(): void
    {
        $product = new Product();
        $product->stock_qty = Product::LOW_STOCK_THRESHOLD; // exactly 10
        $this->assertTrue($product->isLowStock());
    }

    public function testIsLowStockReturnsTrueWhenStockBelowThreshold(): void
    {
        $product = new Product();
        $product->stock_qty = 0;
        $this->assertTrue($product->isLowStock());
    }

    public function testIsLowStockReturnsFalseWhenStockAboveThreshold(): void
    {
        $product = new Product();
        $product->stock_qty = Product::LOW_STOCK_THRESHOLD + 1; // 11
        $this->assertFalse($product->isLowStock());
    }

    // --- getFormattedPrice ---

    public function testGetFormattedPriceFormatsWithDollarAndTwoDecimals(): void
    {
        $product = new Product();
        $product->unit_price = 29.9;
        $this->assertSame('$29.90', $product->getFormattedPrice());
    }

    public function testGetFormattedPriceWithLargeValue(): void
    {
        $product = new Product();
        $product->unit_price = 1500.00;
        $this->assertSame('$1,500.00', $product->getFormattedPrice());
    }

    public function testGetFormattedPriceWithZero(): void
    {
        $product = new Product();
        $product->unit_price = 0;
        $this->assertSame('$0.00', $product->getFormattedPrice());
    }

    // --- getPackagingLabel ---

    public function testGetPackagingLabelReturnsCorrectLabels(): void
    {
        $cases = [
            Product::PACK_KEG    => 'Keg',
            Product::PACK_CAN    => 'Can',
            Product::PACK_BOTTLE => 'Bottle',
            Product::PACK_OTHER  => 'Other',
        ];

        foreach ($cases as $type => $expected) {
            $product = new Product();
            $product->packaging_type = $type;
            $this->assertSame($expected, $product->getPackagingLabel(), "Label mismatch for: $type");
        }
    }

    public function testGetPackagingLabelFallsBackToRawTypeForUnknown(): void
    {
        $product = new Product();
        $product->packaging_type = 'barrel';
        $this->assertSame('barrel', $product->getPackagingLabel());
    }

    // --- getPackagingIcon ---

    public function testGetPackagingIconReturnsStringForAllTypes(): void
    {
        foreach ([Product::PACK_KEG, Product::PACK_CAN, Product::PACK_BOTTLE, Product::PACK_OTHER] as $type) {
            $product = new Product();
            $product->packaging_type = $type;
            $icon = $product->getPackagingIcon();
            $this->assertNotEmpty($icon, "Empty icon for: $type");
            $this->assertStringStartsWith('bi-', $icon);
        }
    }

    public function testGetPackagingIconFallsBackToDefaultForUnknown(): void
    {
        $product = new Product();
        $product->packaging_type = 'barrel';
        $this->assertSame('bi-box', $product->getPackagingIcon());
    }

    // --- packagingLabels static ---

    public function testPackagingLabelsCoversAllFourTypes(): void
    {
        $labels = Product::packagingLabels();
        $this->assertArrayHasKey(Product::PACK_KEG,    $labels);
        $this->assertArrayHasKey(Product::PACK_CAN,    $labels);
        $this->assertArrayHasKey(Product::PACK_BOTTLE, $labels);
        $this->assertArrayHasKey(Product::PACK_OTHER,  $labels);
    }
}
