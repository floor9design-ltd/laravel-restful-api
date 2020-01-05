<?php
/**
 * ApiJson501TraitTest.php
 *
 * ApiJson501TraitTest class
 *
 * php 7.1+
 *
 * @category  None
 * @package   Floor9design\LaravelRestfulApi\Tests\Functional
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

namespace Floor9design\LaravelRestfulApi\Tests\Functional;

use Floor9design\LaravelRestfulApi\Traits\ApiJson501Trait;
use Illuminate\Http\Request;
use Orchestra\Testbench\TestCase;

/**
 * ApiJson501TraitTest
 *
 * This test file tests the RESTful API routes generically.
 * This is a low level/internal functional test.
 *
 * @category  None
 * @package   Floor9design\LaravelRestfulApi\Tests\Functional
 * @author    Rick Morice <rick@floor9design.com>
 * @copyright Floor9design Ltd
 * @license   MIT
 * @version   1.0
 * @link      https://github.com/elb98rm/laravel-restful-api
 * @link      https://floor9design.com
 * @version   1.0
 * @since     File available since Release 1.0
 */
class ApiJson501TraitTest extends TestCase
{

    /**
     * Test the ApiJson501Trait.
     *
     * @return void
     */
    public function testApiJson501Trait()
    {
        $test = new class {
            use ApiJson501Trait;
        };

        // A correct json response:
        $json_api_response = json_encode([
            'data' => [],
            'errors' => [
                'status' => '501',
                'title' => 'Not Implemented',
                'detail' => 'This feature is not yet implemented.',
            ],
            'meta' => [
                'status' => null
            ]
        ]);

        // mock a request
        $request = $this->createMock(Request::class);

        // GET
        $this->assertEquals($json_api_response, $test->jsonIndex($request)->getContent());
        $this->assertEquals($json_api_response, $test->jsonDetails($request, 1)->getContent());

        // POST
        $this->assertEquals($json_api_response, $test->jsonCreate($request)->getContent());
        $this->assertEquals($json_api_response, $test->jsonCreateById($request, 1)->getContent());

        // PUT
        $this->assertEquals($json_api_response, $test->jsonCollectionReplace($request)->getContent());
        $this->assertEquals($json_api_response, $test->jsonElementReplace($request, 1)->getContent());

        // PATCH
        $this->assertEquals($json_api_response, $test->jsonCollectionUpdate($request)->getContent());
        $this->assertEquals($json_api_response, $test->jsonElementUpdate($request, 1)->getContent());

        // DELETE
        $this->assertEquals($json_api_response, $test->jsonCollectionDelete($request)->getContent());
        $this->assertEquals($json_api_response, $test->jsonElementDelete($request, 1)->getContent());
    }

}
