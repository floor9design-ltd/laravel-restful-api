<?php
/**
 * ValidationTraitTest.php
 *
 * ValidationTraitTest class
 *
 * php 7.1+
 *
 * @category  None
 * @package   Floor9design\LaravelRestfulApi\Tests\Unit
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

namespace Floor9design\LaravelRestfulApi\Tests\Unit;

use Floor9design\LaravelRestfulApi\Traits\JsonApiTrait;
use Floor9design\LaravelRestfulApi\Traits\ValidationTrait;
use Orchestra\Testbench\TestCase;

/**
 * ValidationTraitTest
 *
 * This tests the properties/methods implemented by the ValidationTrait.
 *
 * @category  None
 * @package   Floor9design\LaravelRestfulApi\Tests\Unit
 * @author    Rick Morice <rick@floor9design.com>
 * @copyright Floor9design Ltd
 * @license   MIT
 * @version   1.0
 * @link      https://github.com/floor9design-ltd/laravel-restful-api
 * @link      https://floor9design.com
 * @version   1.0
 * @since     File available since Release 1.0
 */
class ValidationTraitTest extends TestCase
{

    /**
     * Check the maximum response number.
     *
     * @return void
     */
    public function testGetValidation()
    {
        $test = new class {
            use ValidationTrait;
        };

        // maximum pagination amount
        $this->assertEquals([], $test->getValidation());
    }

}
