<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Restapi\Tests\PanelApiControllers;

use InnoCMS\Restapi\PanelApiControllers\ProductController;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Unit tests for Panel ProductController.
 * Tests method existence and basic structure without database dependencies.
 *
 * The test used to be copied from InnoShop and asserted methods InnoCMS does not have
 * (names, skuAutocomplete, import) and parameter names of the InnoShop product model.
 */
class ProductControllerTest extends TestCase
{
    private ProductController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new ProductController;
    }

    #[Test]
    public function test_controller_exists(): void
    {
        $this->assertInstanceOf(ProductController::class, $this->controller);
    }

    #[Test]
    public function test_controller_extends_base_controller(): void
    {
        $reflection  = new ReflectionClass($this->controller);
        $parentClass = $reflection->getParentClass();

        $this->assertNotFalse($parentClass);
        $this->assertEquals('InnoCMS\Restapi\PanelApiControllers\BaseController', $parentClass->getName());
    }

    public static function controller_methods(): array
    {
        return [
            'index'        => ['index'],
            'autocomplete' => ['autocomplete'],
            'store'        => ['store'],
            'show'         => ['show'],
            'update'       => ['update'],
            'patch'        => ['patch'],
            'destroy'      => ['destroy'],
        ];
    }

    #[Test]
    #[DataProvider('controller_methods')]
    public function test_the_method_exists_and_is_public(string $method): void
    {
        $this->assertTrue(method_exists($this->controller, $method));

        $reflection = new ReflectionClass($this->controller);

        $this->assertTrue($reflection->getMethod($method)->isPublic());
    }

    #[Test]
    public function test_index_method_has_request_parameter(): void
    {
        $reflection = new ReflectionClass($this->controller);
        $method     = $reflection->getMethod('index');
        $parameters = $method->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('request', $parameters[0]->getName());
    }

    #[Test]
    public function test_update_method_has_request_and_product_parameters(): void
    {
        $reflection = new ReflectionClass($this->controller);
        $method     = $reflection->getMethod('update');
        $parameters = $method->getParameters();

        $this->assertCount(2, $parameters);
        $this->assertEquals('request', $parameters[0]->getName());
        $this->assertEquals('product', $parameters[1]->getName());
    }

    #[Test]
    public function test_patch_method_has_request_and_product_parameters(): void
    {
        $reflection = new ReflectionClass($this->controller);
        $method     = $reflection->getMethod('patch');
        $parameters = $method->getParameters();

        $this->assertCount(2, $parameters);
        $this->assertEquals('request', $parameters[0]->getName());
        $this->assertEquals('product', $parameters[1]->getName());
    }
}
