<?php
/**
 * JsonApiTraitTest.php
 *
 * JsonApiTraitTest class
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

use Floor9design\LaravelRestfulApi\Traits\JsonApiTrait;
use Orchestra\Testbench\TestCase;

/**
 * JsonApiTraitTest
 *
 * This tests the properties/methods implemented by the JsonApiTrait.
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
class JsonApiTraitTest extends TestCase
{

    /**
     * Check the maximum response number.
     *
     * @return void
     */
    public function testGetJsonApiMaximumResponseNumber()
    {
        $test = new class {
            use JsonApiTrait;
        };

        // maximum pagination amount
        $this->assertEquals(200, $test->getJsonApiMaximumResponseNumber());
    }

    /**
     * Test the JsonApiTrait preset array
     *
     * @return void
     */
    public function testGetJsonApiResponseArray()
    {
        $test = new class {
            use JsonApiTrait;
        };

        // Ensure the base response values are set up:
        $json_api_response_array = [
            'data' => [],
            'errors' => [],
            'meta' => [
                'status' => null
            ]
        ];

        $this->assertEquals($json_api_response_array, $test->getJsonApiResponseArray());
    }

}
