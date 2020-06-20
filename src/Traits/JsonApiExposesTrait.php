<?php
/**
 * JsonApiExposesTrait.php
 *
 * JsonApiExposesTrait trait
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
 * Trait JsonApiExposesTrait
 *
 * Trait to give models a way to expose core properties
 * Can be used to create responses that are still filtered according to the model rules, ensuring that nothing is
 * accidentally exposed.
 *
 * @category  None
 * @package   Floor9design\LaravelRestfulApi\Traits
 * @author    Rick Morice <rick@floor9design.com>
 * @copyright Floor9design Ltd
 * @license   MIT
 * @version   1.0
 * @link      https://www.floor9design.com
 * @link      https://laravel.com/
 * @since     File available since Release 1.0
 */
trait JsonApiExposesTrait
{
    // Properties

    /**
     * The attributes that are exposed to the API.
     *
     * @var array
     */
    protected $api_array_filter = [];

    // Accessors

    /**
     * @return array
     * @see $api_array_filter
     *
     */
    public function getApiArrayFilter(): array
    {
        return $this->api_array_filter;
    }

    // Other functionality

    /**
     * Loads all the api_array_filer attributes into an array
     *
     * @return array
     */
    public function getAttributes(): array
    {
        $attributes = [];

        foreach ($this->getApiArrayFilter() as $api_array_key) {
            $attributes[$api_array_key] = $this->$api_array_key;
        }

        return $attributes;
    }

}
