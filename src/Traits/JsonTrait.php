<?php
/**
 * JsonTrait.php
 *
 * JsonTrait trait
 *
 * php 7.0+
 *
 * @category  None
 * @package   Floor9design\LaravelRestfulApi\Traits
 * @author    Rick Morice <rick@floor9design.com>
 * @copyright Floor9design Ltd
 * @license   MIT
 * @version   1.0
 * @link      https://www.floor9design.com
 * @version   1.0
 * @since     File available since Release 1.0
 *
 */

namespace Floor9design\LaravelRestfulApi\Traits;

/**
 * Trait JsonTrait
 *
 * Trait to offer methods/properties for all Api requests.
 *
 * @category  None
 * @package   Floor9design\LaravelRestfulApi\Traits
 * @author    Rick Morice <rick@floor9design.com>
 * @copyright Floor9design Ltd
 * @license   MIT
 * @version   1.0
 * @link      https://www.floor9design.com
 * @link      https://laravel.com/
 * @link      https://en.wikipedia.org/wiki/Representational_state_transfer#Relationship_between_URI_and_HTTP_methods
 * @since     File available since Release 1.0
 */
trait JsonTrait
{
    /**
     * A clean array to populate, including the main required elements
     *
     * @var array
     */
    protected $json_api_response_array = [
        'data' => [],
        'errors' => [],
        'meta' => [
            'status' => null
        ]
    ];

    /**
     * @var int Maximum number of responses that the api will give if pagination is set up
     */
    protected $maximum_response_number = 200;

    /**
     * @return array
     * @see $json_api_response_array
     *
     */
    public function getJsonApiResponseArray(): array
    {
        return $this->json_api_response_array;
    }

    /**
     * @return int
     * @see $maximum_response_number
     *
     */
    public function getMaximumResponseNumber(): int
    {
        return $this->maximum_response_number;
    }


}
