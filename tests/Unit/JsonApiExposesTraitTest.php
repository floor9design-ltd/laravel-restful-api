<?php
/**
 * JsonApiExposesTraitTest.php
 *
 * JsonApiExposesTraitTest class
 *
 * php 7.1+
 *
 * @category  None
 * @package   Floor9design\LaravelRestfulApi\Tests\Unit
 * @author    Rick Morice <rick@floor9design.com>
 * @copyright Floor9design Ltd
 * @license   MIT
 * @version   1.0
 * @link      https://github.com/elb98rm/laravel-restful-api
 * @link      https://floor9design.com
 * @version   1.0
 * @since     File available since Release 1.0
 *
 */

namespace Floor9design\LaravelRestfulApi\Tests\Unit;

use Floor9design\LaravelRestfulApi\Traits\JsonApiExposesTrait;
use Floor9design\LaravelRestfulApi\Traits\JsonApiTrait;
use Orchestra\Testbench\TestCase;

/**
 * JsonApiExposesTraitTest
 *
 * This tests the properties/methods implemented by the JsonTrait.
 *
 * @category  None
 * @package   Floor9design\LaravelRestfulApi\Tests\Unit
 * @author    Rick Morice <rick@floor9design.com>
 * @copyright Floor9design Ltd
 * @license   MIT
 * @version   1.0
 * @link      https://github.com/elb98rm/laravel-restful-api
 * @link      https://floor9design.com
 * @version   1.0
 * @since     File available since Release 1.0
 */
class JsonApiExposesTraitTest extends TestCase
{

    /**
     * Check the getApiArrayFilter using the default $api_array_filter default value.
     *
     * @return void
     */
    public function testGetApiArrayFilterDefault()
    {
        $test = new class {
            use JsonApiExposesTrait;
        };

        $this->assertEquals([], $test->getApiArrayFilter());
    }

    /**
     * Check the getApiArrayFilter using the custom default $api_array_filter default value.
     *
     * @return void
     */
    public function testGetApiArrayFilterCustom()
    {
        $test = $this->setUpClass();

        // maximum pagination amount
        $this->assertEquals(['exposed'], $test->getApiArrayFilter());
    }

    /**
     * Check the getAttributes.
     * Note: a positive test also proves the filtering.
     *
     * @return void
     */
    public function testGetAttributes()
    {
        $test = $this->setUpClass();

        // maximum pagination amount
        $this->assertEquals(['exposed' => 'test_exposed'], $test->getAttributes());
    }

    // Other functionality

    /**
     * @return object Anonymous testing class
     */
    private function setUpClass() {
        return new class {
            use JsonApiExposesTrait;
            var $exposed = 'test_exposed';
            var $not_exposed = 'test_not_exposed';
            protected $api_array_filter = [];

            public function __construct()
            {
                $this->api_array_filter = ['exposed'];
            }
        };
    }

}
