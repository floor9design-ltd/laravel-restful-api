<?php
/**
 * JsonDefaultTraitTest.php
 *
 * JsonDefaultTraitTest class
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
use Floor9design\LaravelRestfulApi\Traits\JsonApiDefaultTrait;
use Floor9design\LaravelRestfulApi\Traits\JsonApiTrait;
use Illuminate\Http\Request;
use Orchestra\Testbench\TestCase;

/**
 * JsonDefaultTraitTest
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
class JsonDefaultTraitTest extends TestCase
{

    // Background functionality tests
    public function testAccessors()
    {
        // Set up a mock trait in a class
        $test_controller = new class {
            use JsonApiDefaultTrait;
            use JsonApiTrait;
        };

        $model = new User();
        $test_controller->setModel($model);
        $this->assertSame($model, $test_controller->getModel());

        $controller_model = '\Floor9design\LaravelRestfulApi\Models\User';
        $test_controller->setControllerModel($controller_model);
        $this->assertSame($controller_model, $test_controller->getControllerModel());

        $url_base = 'users';
        $test_controller->setUrlBase($url_base);
        $this->assertSame($url_base, $test_controller->getUrlBase());
    }

    // GET

    //jsonApiIndex

    /**
     * Test JsonApiDefaultTrait:jsonIndex.
     *
     * @return void
     */
    public function testJsonIndex()
    {
        // Set up a mock trait in a class
        $test_controller = $this->setUpUserClass();

        // mock a request
        $mock_request = $this->createMock(Request::class);

        // make an array of 200 users
        $users = [];

        $i = 1;
        while ($i <= 200) {
            $users[] = [
                'id' => (string)$i,
                'type' => 'user',
                'attributes' => [
                    'name' => 'Rick',
                    'email' => 'rick@floor9design.com'
                ],
                'links' => [
                    'self' => 'https://laravel-restful-api.local/user/' . $i
                ],
                'relationships' => new \stdClass(),
            ];
            $i++;
        }

        // Expected user object response
        $expected_response = json_encode(
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

        $users_response = $test_controller->jsonApiIndex($mock_request);
        $this->assertEquals($users_response->getStatusCode(), 200);
        $this->assertEquals($expected_response, $users_response->getContent());
    }

    // jsonApiDetails

    /**
     * Test JsonApiDefaultTrait:jsonDetails.
     * Tests the response where no details are found
     *
     * @return void
     */
    public function testJsonDetails404()
    {
        // Set up a mock trait in a class
        $test_controller = $this->setUpUserClass();

        // mock a request
        $mock_request = $this->createMock(Request::class);

        // empty response:
        $expected_response = json_encode(
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
        $response_404 = $test_controller->jsonApiDetails($mock_request, 0);
        $this->assertEquals($response_404->getStatusCode(), 404);
        $this->assertEquals($expected_response, $response_404->getContent());
    }

    /**
     * Test JsonApiDefaultTrait:jsonDetails.
     *
     * @return void
     */
    public function testJsonDetailsUser()
    {
        // Set up a mock trait in a class
        $test_controller = $this->setUpUserClass();

        // mock a request
        $mock_request = $this->createMock(Request::class);

        // Expected user object response
        $expected_response = json_encode(
            [
                'data' => [
                    'id' => "1",
                    'type' => 'user',
                    'attributes' => [
                        'name' => 'Rick',
                        'email' => 'rick@floor9design.com'
                    ],
                    'relationships' => new \stdClass(),
                    'links' => [
                        'self' => 'https://laravel-restful-api.local/user/1'
                    ],
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

        $user_response = $test_controller->jsonApiDetails($mock_request, 1);
        $this->assertEquals($user_response->getStatusCode(), 200);
        $this->assertEquals($expected_response, $user_response->getContent());
    }

    // CREATE

    // jsonApiCreate

    // jsonApiCreateById

    // PUT

    // jsonApiCollectionReplace

    // jsonApiElementReplace

    // PATCH

    // jsonApiCollectionUpdate

    // jsonApiElementUpdate

    // DELETE

    /**
     * Test JsonApiDefaultTrait:jsonApiCollectionDelete.
     *
     * @return void
     */
    public function testJsonApiCollectionDelete()
    {
        // Set up a mock trait in a class
        $test_controller = $this->setUpUserClass();

        // mock a request
        $mock_request = $this->createMock(Request::class);

        // make an array of 200 users
        $users = [];

        $total = 3;

        $i = 1;
        while ($i <= $total) {
            $users[] = [
                'id' => (string)$i,
                'type' => 'user',
                'attributes' => [
                    'name' => 'Rick',
                    'email' => 'rick@floor9design.com'
                ]
            ];
            $i++;
        }

        // Expected user object response
        $expected_response = json_encode(
            [
                'data' => $users,
                'meta' => [
                    'status' => 200,
                    'count' => $total,
                    'detail' => 'The collection inside the users table was deleted.'
                ],
                'links' => [
                    'collection' => 'https://laravel-restful-api.local/users'
                ]
            ]
        );

        $delete_response = $test_controller->jsonApiCollectionDelete($mock_request, 1);
        $this->assertEquals($delete_response->getStatusCode(), 200);
        $this->assertEquals($expected_response, $delete_response->getContent());
    }

    /**
     * Test JsonApiDefaultTrait:jsonApiElementDelete.
     *
     * @return void
     */
    public function testJsonApiElementDelete404()
    {
        // Set up a mock trait in a class
        $test_controller = $this->setUpUserClass();

        // mock a request
        $mock_request = $this->createMock(Request::class);

        // Expected user object response
        $expected_response = json_encode(
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

        $response_404 = $test_controller->jsonApiElementDelete($mock_request, 0);
        $this->assertEquals($response_404->getStatusCode(), 404);
        $this->assertEquals($expected_response, $response_404->getContent());
    }

    /**
     * Test JsonApiDefaultTrait:jsonApiElementDelete.
     *
     * @return void
     */
    public function testJsonApiElementDelete()
    {
        // Set up a mock trait in a class
        $test_controller = $this->setUpUserClass();

        // mock a request
        $mock_request = $this->createMock(Request::class);

        // Expected user object response
        $expected_response = json_encode(
            [
                'data' => [
                    'id' => "1",
                    'type' => 'user',
                    'attributes' => [
                        'name' => 'Rick',
                        'email' => 'rick@floor9design.com'
                    ]
                ],
                'meta' => [
                    'status' => "200",
                    'count' => 1,
                    'detail' => 'The user was deleted.'
                ],
                'links' => [
                    'collection' => 'https://laravel-restful-api.local/users'
                ]
            ]
        );

        $delete_response = $test_controller->jsonApiElementDelete($mock_request, 1);
        $this->assertEquals($delete_response->getStatusCode(), 200);
        $this->assertEquals($expected_response, $delete_response->getContent());
    }

    // Other functionality

    /**
     * Create an anonymous class configured as a User
     */
    private function setUpUserClass()
    {
        return new class {
            use JsonApiDefaultTrait;
            use JsonApiTrait;

            public function __construct()
            {
                $this->controller_model = '\Floor9design\LaravelRestfulApi\Models\User';
                $this->setModel(new $this->controller_model);
                $this->setUrlBase('https://laravel-restful-api.local/' . $this->getModel()->getApiModelNamePlural());
            }
        };
    }
}

