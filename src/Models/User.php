<?php
/**
 * User.php
 *
 * User class
 *
 * php 7.2+
 *
 * @category  None
 * @package   Floor9design\LaravelRestfulApi\Tests\Unit
 * @author    Rick Morice <rick@floor9design.com>
 * @copyright Floor9design Ltd
 * @license   MIT
 * @version   1.0
 * @link      https://github.com/floor9design-ltd/laravel-restful-api
 * @link      https://floor9design.com
 * @since     File available since Release 1.0
 *
 */

namespace Floor9design\LaravelRestfulApi\Models;

use Floor9design\LaravelRestfulApi\Traits\JsonApiExposesTrait;
use Floor9design\LaravelRestfulApi\Traits\ValidationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Class User
 *
 * An essentially empty class designed to allow for testing. It returns test data only.
 * Used in Floor9design\LaravelRestfulApi\Tests\Functional\ApiJsonDefaultTraitTest
 *
 * Rather than mess about mocking internally, this class just simulates the results, allowing meaningful simple tests
 * of the controller level trait functions.
 *
 * @category  None
 * @package   Floor9design\LaravelRestfulApi\Tests\Unit
 * @author    Rick Morice <rick@floor9design.com>
 * @copyright Floor9design Ltd
 * @license   MIT
 * @version   1.0
 * @link      https://github.com/floor9design-ltd/laravel-restful-api
 * @link      https://floor9design.com
 * @see       \Floor9design\LaravelRestfulApi\Tests\Functional\ApiJsonDefaultTraitTest
 * @since     File available since Release 1.0
 */
class User extends Model
{
    use JsonApiExposesTrait, ValidationTrait;

    /**
     * Example test data
     *
     * @var int
     */
    public $id = 1;

    /**
     * Example test data
     *
     * @var string
     */
    public $name = 'Rick';

    /**
     * Example test data
     *
     * @var string
     */
    public $email = 'rick@floor9design.com';

    /**
     * The attributes that are exposed to the API.
     *
     * @var array
     */
    protected $api_filter = [
        'name',
        'email'
    ];

    protected $api_model_name_singular = 'user';

    protected $api_model_name_plural = 'users';

    /**
     * Simulate the find by returning a user.
     *
     * @param int $id
     * @return mixed
     */
    public static function find(int $id)
    {
        // the user of 0 returns null
        if ($id == 0) {
            return null;
        } else {
            return new User();
        }
    }

    /**
     * Simulate a paginate() by returning a paginated list.
     *
     * @return LengthAwarePaginator
     */
    public static function paginate(int $maximum_response_number)
    {
        // make an array of 250 users
        $users = [];

        $i = 1;
        while ($i <= 250) {
            // Update the ID to make them unique
            $user = new User();
            $user->id = $i;

            $users[] = $user;

            $i++;
        }

        // Have to manually slice as it's a manually created paginator
        $paginated_users = array_slice($users, 0, 200);

        // turn into a length aware paginator
        return new LengthAwarePaginator($paginated_users, count($users), $maximum_response_number, 1);
    }

    /**
     * Simulate an all().
     *
     * @return Collection
     */
    public static function all($columns = ['*'])
    {
        // make an array of 250 users
        $users = [];

        $i = 1;
        while ($i <= 3) {
            // Update the ID to make them unique
            $user = new User();
            $user->id = $i;

            $users[] = $user;

            $i++;
        }

        $collection = collect($users);

        return $collection;
    }

    /**
     * Simulate a create, responding with either a validation fail or pass.
     *
     * @param array $input
     * @return string
     */
    public static function create(array $input): string
    {
        $response = null;

        // our first test will simulate a failed a duplicate email test, so return the correct array:
        if ($input['email'] == 'a@b.com') {
            $response = json_encode(
                [
                    "errors" => [
                        [
                            "status" => "422",
                            "title" => "Input validation has failed",
                            "detail" => "The email has already been taken."
                        ]
                    ]
                ]
            );
        }

        return $response;
    }

    /**
     * Simulate a delete, responding with either a  fail or pass.
     *
     * @return bool
     */
    public function delete(): bool
    {
        // just going to return true: the mocked delete is successful
        return true;
    }

    /**
     *
     * @return object anonymous class mocking the delete function
     */
    public static function query()
    {
        return new class {
            public function delete()
            {
                return true;
            }
        };
    }

    /**
     * Simulate the users table
     *
     * @return string
     */
    public function getTable()
    {
        return 'users';
    }

}
