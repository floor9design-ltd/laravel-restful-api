<?php
/**
 * JsonApi501Trait.php
 *
 * JsonApi501Trait trait
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
 * Trait JsonApi501Trait
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
trait JsonApi501Trait
{

    /**
     * A clean array to populate, including the main required elements
     *
     * @var array
     */
    protected $response = [
        'errors' => [
            [
                'status' => '501',
                'title' => 'Not Implemented',
                'detail' => 'This feature is not yet implemented.',
            ]
        ],
        'meta' => [
            'status' => '501',
            'title' => 'Not Implemented',
            'detail' => 'This feature is not yet implemented.',
        ]
    ];

    // GET

    /**
     * The json api version of the index screen
     * "List the URIs and perhaps other details of the collection's members"
     *
     * @param Request $request Laravel Request object
     * @return JsonResponse json response
     */
    public function jsonApiIndex(Request $request): JsonResponse
    {
        return Response::json($this->response, $this->response['errors'][0]['status']);
    }

    /**
     * The json api version of the detail screen
     * "Retrieve a representation of the addressed member of the collection"
     *
     * @param Request $request Laravel Request object
     * @param $id Object id
     * @return JsonResponse json response
     */
    public function jsonApiDetails(Request $request, $id): JsonResponse
    {
        return Response::json($this->response, $this->response['errors'][0]['status']);
    }

    // POST

    /**
     * The json api version of the create feature
     * "Create a new entry in the collection. The new entry's URI is assigned automatically."
     *
     * @param Request $request
     * @return JsonResponse json response
     */
    public function jsonApiCreate(Request $request): JsonResponse
    {
        return Response::json($this->response, $this->response['errors'][0]['status']);
    }

    /**
     * The json api version of the create feature
     * "Create a new entry in the collection. The new entry's URI is assigned automatically."
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse json response
     */
    public function jsonApiCreateById(Request $request, $id): JsonResponse
    {
        return Response::json($this->response, $this->response['errors'][0]['status']);
    }

    // PUT

    /**
     * Replaces the entire collection
     * "Replace the entire collection with another collection."
     *
     * @param Request $request
     * @return JsonResponse json response
     */
    public function jsonApiCollectionReplace(Request $request): JsonResponse
    {
        return Response::json($this->response, $this->response['errors'][0]['status']);
    }

    /**
     * Replaces an element
     * "Replace the addressed member of the collection, or if it does not exist, create it."
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse json response
     */
    public function jsonApiElementReplace(Request $request, $id): JsonResponse
    {
        return Response::json($this->response, $this->response['errors'][0]['status']);
    }

    // PATCH

    /**
     * Updates the entire collection
     * "Update all the representations of the member resources of the collection resource."
     *
     * @param Request $request
     * @return JsonResponse json response
     */
    public function jsonApiCollectionUpdate(Request $request): JsonResponse
    {
        return Response::json($this->response, $this->response['errors'][0]['status']);
    }

    /**
     * Updates an element
     * "Update all the representations of the member resource, or may create the member resource if it does not exist."
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse json response
     */
    public function jsonApiElementUpdate(Request $request, $id): JsonResponse
    {
        return Response::json($this->response, $this->response['errors'][0]['status']);
    }

    // DELETE

    /**
     * Delete the entire collection.
     * "Delete the entire collection."
     *
     * @param Request $request
     * @return JsonResponse json response
     */
    public function jsonApiCollectionDelete(Request $request): JsonResponse
    {
        return Response::json($this->response, $this->response['errors'][0]['status']);
    }

    /**
     * Delete the element.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse json response
     */
    public function jsonApiElementDelete(Request $request, $id): JsonResponse
    {
        return Response::json($this->response, $this->response['errors'][0]['status']);
    }

}
