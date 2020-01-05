<?php
/**
 * ApiFilterTraitTest.php
 *
 * ApiFilterTraitTest class
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

use Floor9design\LaravelRestfulApi\Traits\ApiFilterTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Orchestra\Testbench\TestCase;

/**
 * ApiFilterTraitTest
 *
 * This test file tests the ApiFilterTrait.
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
class ApiFilterTraitTest extends TestCase
{

    /**
     * Test the ApiFilterTrait.
     *
     * @return void
     */
    public function testGetApiFilter()
    {
        // manually set up an object with values and expose them using $test->api_array_filter
        $data = [
            'test_date' => date('Y-m-d'),
            'test_string' => Hash::make('Some test text'),
            'test_json' => json_encode(['one' => 1, 'two' => 2])
        ];

        // Object properties
        $properties = [];

        foreach ($data as $property => $value) {
            $properties[] = $property;
        }

        $test = new class extends Model {
            use ApiFilterTrait;
            var $api_array_filter = [];
        };

        foreach ($data as $property => $value) {
            $test->$property = $value;
            $test->api_array_filter[] = $property;
        }

        // Now extract it using the function:
        $processed = $test->getApiFilter($test);

        // Check the pass throughs
        $this->assertEquals($data['test_date'], $processed['test_date']);
        $this->assertEquals($data['test_string'], $processed['test_string']);

        // Check the converted json strings
        $this->assertEquals(json_decode($data['test_json']), $processed['test_json']);
    }

}
