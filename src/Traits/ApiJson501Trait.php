<?php
/**
 * ApiJson501Trait.php
 *
 * ApiJson501Trait trait
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

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

/**
 * Trait ApiJson501Trait
 *
 * Trait to give the base responses for all classes.
 *
 * These match the definitions on Wikipedia.
 *
 * Currently returns 501 (not implemented) on all methods: the point is to overwrite them where needed, while these
 * methods provide useful feedback for any interaction in the meantime.
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
trait ApiJson501Trait
{

    // GET

    /**
     * The json version of the index screen
     * "List the URIs and perhaps other details of the collection's members"
     *
     * @param Request $request Laravel Request object
     * @return JsonResponse json response
     */
    public function jsonIndex(Request $request): JsonResponse
    {
        $response['http_response_code'] = '501';
        $response['detail'] = 'This feature is not yet implemented.';
        return Response::json($response, $response['http_response_code']);
    }

    /**
     * The json version of the detail screen
     * "Retrieve a representation of the addressed member of the collection"
     *
     * @param Request $request Laravel Request object
     * @param int $id Object id
     * @return JsonResponse json response
     */
    public function jsonDetails(Request $request, int $id): JsonResponse
    {
        $response['http_response_code'] = '501';
        $response['detail'] = 'This feature is not yet implemented.';
        return Response::json($response, $response['http_response_code']);
    }

    // POST

    /**
     * The json version of the create feature
     * "Create a new entry in the collection. The new entry's URI is assigned automatically."
     *
     * @param Request $request
     * @return JsonResponse json response
     */
    public function jsonCreate(Request $request): JsonResponse
    {
        $response['http_response_code'] = '501';
        $response['detail'] = 'This feature is not yet implemented.';
        return Response::json($response, $response['http_response_code']);
    }

    /**
     * The json version of the create feature
     * "Create a new entry in the collection. The new entry's URI is assigned automatically."
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse json response
     */
    public function jsonCreateById(Request $request, int $id): JsonResponse
    {
        $response['http_response_code'] = '501';
        $response['detail'] = 'This feature is not yet implemented.';
        return Response::json($response, $response['http_response_code']);
    }

    // PUT

    /**
     * Replaces the entire collection
     * "Replace the entire collection with another collection."
     *
     * @return JsonResponse json response
     */
    public function jsonCollectionReplace(): JsonResponse
    {
        $response['http_response_code'] = '501';
        $response['detail'] = 'This feature is not yet implemented.';
        return Response::json($response, $response['http_response_code']);
    }

    /**
     * Replaces an element
     * "Replace the addressed member of the collection, or if it does not exist, create it."
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse json response
     */
    public function jsonElementReplace(Request $request, int $id): JsonResponse
    {
        $response['http_response_code'] = '501';
        $response['detail'] = 'This feature is not yet implemented.';
        return Response::json($response, $response['http_response_code']);
    }

    // PATCH

    /**
     * Updates the entire collection
     * "Update all the representations of the member resources of the collection resource."
     *
     * @return JsonResponse json response
     */
    public function jsonCollectionUpdate(): JsonResponse
    {
        $response['http_response_code'] = '501';
        $response['detail'] = 'This feature is not yet implemented.';
        return Response::json($response, $response['http_response_code']);
    }

    /**
     * Replaces an element
     * "Update all the representations of the member resource, or may create the member resource if it does not exist."
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse json response
     */
    public function jsonElementUpdate(Request $request, int $id): JsonResponse
    {
        $response['http_response_code'] = '501';
        $response['detail'] = 'This feature is not yet implemented.';
        return Response::json($response, $response['http_response_code']);
    }

    // DELETE

    /**
     * Delete the entire collection.
     * "Delete the entire collection."
     *
     * @param Request $request
     * @return JsonResponse json response
     */
    public function jsonCollectionDelete(Request $request): JsonResponse
    {
        $response['http_response_code'] = '501';
        $response['detail'] = 'This feature is not yet implemented.';
        return Response::json($response, $response['http_response_code']);
    }

    /**
     * Delete the element.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse json response
     */
    public function jsonElementDelete(Request $request, int $id): JsonResponse
    {
        $response['http_response_code'] = '501';
        $response['detail'] = 'This feature is not yet implemented.';
        return Response::json($response, $response['http_response_code']);
    }

}
