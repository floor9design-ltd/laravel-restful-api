<?php
/**
 * ApiJsonDefaultTraitTest.php
 *
 * ApiJsonDefaultTraitTest class
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

use Floor9design\LaravelRestfulApi\Models\User;
use Floor9design\LaravelRestfulApi\Traits\ApiJsonDefaultTrait;
use Floor9design\LaravelRestfulApi\Traits\ApiJsonTrait;
use Illuminate\Http\Request;
use Orchestra\Testbench\TestCase;

/**
 * ApiJsonDefaultTraitTest
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
 * @see       \Floor9design\LaravelRestfulApi\Models\User
 * @since     File available since Release 1.0
 */
class ApiJsonDefaultTraitTest extends TestCase
{

    // GET

    //jsonIndex

    /**
     * Test ApiJsonDefaultTrait:JsonIndex.
     *
     * @return void
     */
    public function testJsonIndex()
    {
        // Set up a mock trait in a class
        $test_controller = new class {
            use ApiJsonDefaultTrait;
            use ApiJsonTrait;

            public function __construct()
            {
                $this->controller_model = '\Floor9design\LaravelRestfulApi\Models\User';
                $this->model = new User();
                $this->url_base = 'https://laravel-restful-api.local/' . $this->model->getTable();
            }
        };

        // mock a request
        $request_user = $this->createMock(Request::class);

        // make an array of 200 users
        $users = [];

        $i = 1;
        while ($i <= 200) {
            $users[] = [
                'id' => (string)$i,
                'type' => 'users',
                'attributes' => [
                    'name' => 'Rick',
                    'email' => 'rick@floor9design.com'
                ],
                'links' => [
                    'self' => 'https://laravel-restful-api.local/users/' . $i
                ],
                'relationships' => new \stdClass(),
            ];
            $i++;
        }

        // Expected user object response
        $api_user_response = json_encode(
            [
                'data' => $users,
                'meta' => [
                    'status' => "200",
                    'count' => 200
                ],
                'links' => [
                    'collection' => 'https://laravel-restful-api.local/users',
                    "self" => "https://laravel-restful-api.local/users?page=1",
                    "first" => "https://laravel-restful-api.local/users?page=1",
                    "last" => "https://laravel-restful-api.local/users?page=2",
                    "prev" => null,
                    // Note: as it's mocked, Users::path is not set, so it returns a "/". This works and is tested in reality.
                    "next" => "/?page=2"
                ]
            ]
        );

        $user_response = $test_controller->jsonIndex($request_user);
        $this->assertEquals($api_user_response, $user_response->getContent());

    }

    // jsonDetails

    /**
     * Test ApiJsonDefaultTrait:JsonDetails.
     *
     * @return void
     */
    public function testJsonDetails404()
    {
        // Set up a mock trait in a class
        $test_controller = new class {
            use ApiJsonDefaultTrait;

            public function __construct()
            {
                $this->controller_model = '\Floor9design\LaravelRestfulApi\Models\User';
                $this->model = new User();
                $this->url_base = 'https://laravel-restful-api.local/' . $this->model->getTable();
            }
        };

        // mock a request
        $request_404 = $this->createMock(Request::class);

        // empty response:
        $api_404_response = json_encode(
            [
                'errors' => [
                    [
                        'status' => '404',
                        'title' => 'Resource could not found',
                        'detail' => 'The user could not be found.'
                    ]
                ]
            ]
        );

        // 404
        $response_404 = $test_controller->jsonDetails($request_404, 0);
        $this->assertEquals($api_404_response, $response_404->getContent());
    }

    /**
     * Test ApiJsonDefaultTrait:JsonDetails.
     *
     * @return void
     */
    public function testJsonDetailsUser()
    {
        // Set up a mock trait in a class
        $test_controller = new class {
            use ApiJsonDefaultTrait;

            public function __construct()
            {
                $this->controller_model = '\Floor9design\LaravelRestfulApi\Models\User';
                $this->model = new User();
                $this->url_base = 'https://laravel-restful-api.local/' . $this->model->getTable();
            }
        };

        // mock a request
        $request_user = $this->createMock(Request::class);

        // Expected user object response
        $api_user_response = json_encode(
            [
                'data' => [
                    'id' => "1",
                    'type' => 'users',
                    'attributes' => [
                        'name' => 'Rick',
                        'email' => 'rick@floor9design.com'
                    ],
                    'links' => [
                        'self' => 'https://laravel-restful-api.local/users/1'
                    ],
                    'relationships' => new \stdClass(),
                ],
                'meta' => [
                    'status' => "200",
                    'count' => 1
                ],
                'links' => [
                    'collection' => 'https://laravel-restful-api.local/users'
                ]
            ]
        );

        $user_response = $test_controller->jsonDetails($request_user, 1);
        $this->assertEquals($api_user_response, $user_response->getContent());

        // Note: this only tests page1, but there's no real need to check if laravel pagination works... it does!
    }

    // CREATE

    // jsonCreate

    // jsonCreateById

    // PUT

    // jsonCollectionReplace

    // jsonElementReplace

    // PATCH

    // jsonCollectionUpdate

    // jsonElementUpdate

    // DELETE

    // jsonCollectionDelete

    // jsonElementDelete
}
