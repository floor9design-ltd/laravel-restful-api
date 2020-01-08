<?php
/**
 * ApiFilterTrait.php
 *
 * ApiFilterTrait trait
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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Trait ApiFilterTrait
 *
 * Trait to allow the base API o filter on object's structure, correctly parsing it into an array.
 * This catches elements, such as JSON, and correctly parses them for the returning.
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
trait ApiFilterTrait
{
    /**
     * Parse over an Object and convert it to an array suitable for the API to present
     *
     * @param Model $model
     * @return array
     */
    public function getApiFilter(Model $model): array
    {
        $return = [];

        foreach ($this->api_array_filter ?? [] as $property) {
            // if the object has "json" in it, automatically decode (to fix layout issues when it's re-encoded)
            if (Str::contains($property, 'json')) {
                $return[$property] = json_decode($model->$property);
            } else {
                $return[$property] = $model->$property;
            }
        }

        return $return;
    }
}
