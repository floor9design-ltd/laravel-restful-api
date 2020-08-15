<?php
/**
 * JsonApi501TraitTest.php
 *
 * JsonApi501TraitTest class
 *
 * php 7.1+
 *
 * @category  None
 * @package   Floor9design\LaravelRestfulApi\Tests\Functional
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

namespace Floor9design\LaravelRestfulApi\Tests\Functional;

use Floor9design\LaravelRestfulApi\Traits\JsonApi501Trait;
use Illuminate\Http\Request;
use Orchestra\Testbench\TestCase;

/**
 * JsonApi501TraitTest
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
 * @link      https://github.com/floor9design-ltd/laravel-restful-api
 * @link      https://floor9design.com
 * @version   1.0
 * @since     File available since Release 1.0
 */
class JsonApi501TraitTest extends TestCase
{

    /**
     * Test the responses for all methods.
     *
     * @return void
     */
    public function testJsonApi501Trait()
    {
        $test = new class {
            use JsonApi501Trait;
        };

        // A correct json response:
        $json_api_response = json_encode(
            [
                'errors' => [
                    [
                        'status' => '501',
                        'title' => 'Not Implemented',
                        'detail' => 'This feature is not yet implemented.',
                    ]
                ],
                'meta' => [
                    'status' => '501',
                    'title' => 'Not Implemented',
                    'detail' => 'This feature is not yet implemented.',
                ]
            ]
        );

        // mock a request
        $request = $this->createMock(Request::class);

        // GET
        $this->assertEquals($json_api_response, $test->jsonApiIndex($request)->getContent());
        $this->assertEquals($json_api_response, $test->jsonApiDetails($request, 1)->getContent());

        // POST
        $this->assertEquals($json_api_response, $test->jsonApiCreate($request)->getContent());
        $this->assertEquals($json_api_response, $test->jsonApiCreateById($request, 1)->getContent());

        // PUT
        $this->assertEquals($json_api_response, $test->jsonApiCollectionReplace($request)->getContent());
        $this->assertEquals($json_api_response, $test->jsonApiElementReplace($request, 1)->getContent());

        // PATCH
        $this->assertEquals($json_api_response, $test->jsonApiCollectionUpdate($request)->getContent());
        $this->assertEquals($json_api_response, $test->jsonApiElementUpdate($request, 1)->getContent());

        // DELETE
        $this->assertEquals($json_api_response, $test->jsonApiCollectionDelete($request)->getContent());
        $this->assertEquals($json_api_response, $test->jsonApiElementDelete($request, 1)->getContent());
    }

}
