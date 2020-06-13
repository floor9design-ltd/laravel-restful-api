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

    // Background functionality tests
    public function testAccessors()
    {
        // Set up a mock trait in a class
        $test_controller = new class {
            use ApiJsonDefaultTrait;
            use ApiJsonTrait;
        };

        $model = new User();
        $test_controller->setModel($model);
        $this->assertSame($model, $test_controller->getModel());

        $controller_model = '\Floor9design\LaravelRestfulApi\Models\User';
        $test_controller->setControllerModel($controller_model);
        $this->assertSame($controller_model, $test_controller->getControllerModel());

        $model_name_singular = 'user';
        $test_controller->setModelNameSingular($model_name_singular);
        $this->assertSame($model_name_singular, $test_controller->getModelNameSingular());

        $model_name_plural = 'users';
        $test_controller->setModelNamePlural($model_name_plural);
        $this->assertSame($model_name_plural, $test_controller->getModelNamePlural());

        $url_base = 'users';
        $test_controller->setUrlBase($url_base);
        $this->assertSame($url_base, $test_controller->getUrlBase());
    }



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
        $test_controller = $this->setUpUserClass();

        // mock a request
        $request_user = $this->createMock(Request::class);

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
        $test_controller = $this->setUpUserClass();

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
        $test_controller = $this->setUpUserClass();

        // mock a request
        $request_user = $this->createMock(Request::class);

        // Expected user object response
        $api_user_response = json_encode(
            [
                'data' => [
                    'id' => "1",
                    'type' => 'user',
                    'attributes' => [
                        'name' => 'Rick',
                        'email' => 'rick@floor9design.com'
                    ],
                    'links' => [
                        'self' => 'https://laravel-restful-api.local/user/1'
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

    // Other functionality

    /**
     * Create an anonymous class configured as a User
     */
    private function setUpUserClass()
    {
        return new class {
            use ApiJsonDefaultTrait;
            use ApiJsonTrait;

            public function __construct()
            {
                $this->controller_model = '\Floor9design\LaravelRestfulApi\Models\User';
                $this->setModelNameSingular('user');
                $this->setModelNamePlural('users');
                $this->setModel(new $this->controller_model);
                $this->setUrlBase('https://laravel-restful-api.local/' . $this->getModelNamePlural());
            }
        };
    }
}

