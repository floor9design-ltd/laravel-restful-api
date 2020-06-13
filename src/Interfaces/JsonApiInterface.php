<?php
/**
 * JsonApiInterface.php
 *
 * JsonApiInterface trait
 *
 * php 7.0+
 *
 * @category  None
 * @package   Floor9design\LaravelRestfulApi\Interfaces
 * @author    Rick Morice <rick@floor9design.com>
 * @copyright Floor9design Ltd
 * @license   MIT
 * @version   1.0
 * @link      https://www.floor9design.com
 * @version   1.0
 * @since     File available since Release 1.0
 *
 */

namespace Floor9design\LaravelRestfulApi\Interfaces;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Interface JsonApiInterface
 *
 * Interface to force all ApiJson requests to cover all methods.
 * Should be used with JsonApiTrait which implements these in a default (but not necessarily useful) way.
 *
 * These match the definitions on Wikipedia.
 *
 * @category  None
 * @package   Floor9design\LaravelRestfulApi\Interfaces
 * @author    Rick Morice <rick@floor9design.com>
 * @copyright Floor9design Ltd
 * @license   MIT
 * @version   1.0
 * @link      https://www.floor9design.com
 * @link      https://laravel.com/
 * @link      https://en.wikipedia.org/wiki/Representational_state_transfer#Relationship_between_URI_and_HTTP_methods
 * @since     File available since Release 1.0
 */
interface JsonApiInterface
{
    // GET

    /**
     * The json api version of the index screen
     * "List the URIs and perhaps other details of the collection's members"
     *
     * @param Request $request Laravel Request object
     * @return JsonResponse json response
     */
    public function jsonApiIndex(Request $request): JsonResponse;

    /**
     * The json api version of the detail screen
     * "Retrieve a representation of the addressed member of the collection"
     *
     * @param Request $request Laravel Request object
     * @param $id Object id;
     * @return JsonResponse json response
     */
    public function jsonApiDetails(Request $request, $id): JsonResponse;

    // POST

    /**
     * The json api version of the create feature
     * "Create a new entry in the collection. The new entry's URI is assigned automatically."
     *
     * @param Request $request
     * @return JsonResponse json response
     */
    public function jsonApiCreate(Request $request): JsonResponse;

    /**
     * The json api version of the create feature
     * "Create a member resource in the member resource using the instructions in the request body."
     *
     * This is interpreted as a "create with specified ID"
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse json response
     */
    public function jsonApiCreateById(Request $request,$id): JsonResponse;

    // PUT

    /**
     * Replaces the entire collection
     * "Replace the entire collection with another collection."
     *
     * @param Request $request
     * @return JsonResponse json response
     */
    public function jsonApiCollectionReplace(Request $request): JsonResponse;

    /**
     * Replaces an element
     * "Replace the addressed member of the collection, or if it does not exist, create it."
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse json response
     */
    public function jsonApiElementReplace(Request $request, $id): JsonResponse;

    // PATCH

    /**
     * Update an entire collection
     * "Update all the representations of the member resources of the collection resource."
     *
     * @param Request $request
     * @return JsonResponse json response
     */
    public function jsonApiCollectionUpdate(Request $request): JsonResponse;

    /**
     * Updates an element
     * "Update all the representations of the member resource, or may create the member resource if it does not exist."
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse json response
     */
    public function jsonApiElementUpdate(Request $request, $id): JsonResponse;

    // DELETE

    /**
     * Delete the entire collection.
     * "Delete the entire collection."
     *
     * @param Request $request
     * @return JsonResponse json response
     */
    public function jsonApiCollectionDelete(Request $request): JsonResponse;

    /**
     * Delete the element.
     * "Delete the addressed member of the collection."
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse json response
     */
    public function jsonApiElementDelete(Request $request, $id): JsonResponse;

}
