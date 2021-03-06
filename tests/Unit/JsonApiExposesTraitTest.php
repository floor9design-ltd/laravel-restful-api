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
 * @link      https://github.com/floor9design-ltd/laravel-restful-api
 * @link      https://floor9design.com
 * @version   1.0
 * @since     File available since Release 1.0
 *
 */

namespace Floor9design\LaravelRestfulApi\Tests\Unit;

use Floor9design\LaravelRestfulApi\Traits\JsonApiExposesTrait;
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
 * @link      https://github.com/floor9design-ltd/laravel-restful-api
 * @link      https://floor9design.com
 * @version   1.0
 * @since     File available since Release 1.0
 */
class JsonApiExposesTraitTest extends TestCase
{

    /**
     * Check the getApiFilter using the default $api_filter default value.
     *
     * @return void
     */
    public function testGetApiFilterDefault()
    {
        $test = new class {
            use JsonApiExposesTrait;
        };

        $this->assertEquals([], $test->getApiFilter());
    }

    /**
     * Check the getApiFilter using the custom default $api_filter default value.
     *
     * @return void
     */
    public function testGetApiFilterCustom()
    {
        $test = $this->setUpClass();

        // maximum pagination amount
        $this->assertEquals(['exposed', 'exposed_json'], $test->getApiFilter());
    }

    /**
     * Check the getAttributes.
     * Note: a positive test also proves the filtering.
     *
     * @return void
     */
    public function testGetApiAttributes()
    {
        $test = $this->setUpClass();

        $expected_response = [
            'exposed' => 'test_exposed',
            'exposed_json' => json_decode(json_encode(['some' => 'content']))
        ];

        // maximum pagination amount
        $this->assertEquals($expected_response, $test->getApiAttributes());
    }

    // Other functionality

    /**
     * @return object Anonymous testing class
     */
    private function setUpClass()
    {
        return new class {
            use JsonApiExposesTrait;

            var $exposed = 'test_exposed';
            var $exposed_json;
            var $not_exposed = 'test_not_exposed';
            protected $api_filter = [];

            public function __construct()
            {
                $this->api_filter = ['exposed', 'exposed_json'];
                $this->exposed_json = json_encode(['some' => 'content']);
            }
        };
    }

}
