<?php
/**
 * ValidationTrait.php
 *
 * Validation trait
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

/**
 * Trait ValidationTrait
 *
 * Trait to offer the validation method to models easily. This SHOULD be over written as it offers no actual validation!
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
trait ValidationTrait
{
    /**
     * Return no validation!
     *
     * @param Model $model
     * @return array
     */
    public function getValidation(?Model $model = null): array
    {
        return [];
    }
}
