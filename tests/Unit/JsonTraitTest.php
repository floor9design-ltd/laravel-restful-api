<?php
/**
 * JsonTraitTest.php
 *
 * JsonTraitTest class
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

use Floor9design\LaravelRestfulApi\Traits\JsonTrait;
use Orchestra\Testbench\TestCase;

/**
 * JsonTraitTest
 *
 * This test file tests the RESTful API routes generically.
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
class JsonTraitTest extends TestCase
{

    /**
     * Check the maximum response number.
     *
     * @return void
     */
    public function testGetMaximumResponseNumber()
    {
        $test = new class {
            use JsonTrait;
        };

        // maximum pagination amount
        $this->assertEquals(200, $test->getMaximumResponseNumber());
    }

    /**
     * Test the JsonTrait preset array
     *
     * @return void
     */
    public function testGetJsonApiResponseArray()
    {
        $test = new class {
            use JsonTrait;
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
