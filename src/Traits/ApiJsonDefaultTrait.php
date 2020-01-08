<?php
/**
 * ApiJsonDefaultTrait.php
 *
 * ApiJsonDefaultTrait trait
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

use Doctrine\Common\Inflector\Inflector;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

/**
 * Trait ApiJsonDefaultTrait
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
trait ApiJsonDefaultTrait
{

    /**
     * @var Object the model exposed by the controller
     */
    protected $model;

    /**
     * @var string the model exposed by the controller
     */
    protected $controller_model;

    /**
     * @var string the base url for the model; this should ideally be overwritten
     */
    protected $url_base = '/';

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
        $objects = $this->controller_model::paginate($this->maximum_response_number);

        $this->json_api_response_array['meta']['status'] = 200;

        $this->json_api_response_array['links'] = [
            'collection' => $this->url_base,
            'self' => $this->url_base . '?page=' . $objects->currentPage(),
            'first' => $this->url_base . '?page=1',
            'last' => $this->url_base . '?page=' . $objects->lastPage(),
            'prev' => null,
            'next' => null
        ];


        if ($objects->nextPageUrl()) {
            $this->json_api_response_array['links']['next'] = $objects->nextPageUrl();
        }

        if ($objects->previousPageUrl()) {
            $this->json_api_response_array['links']['prev'] = $objects->previousPageUrl();
        }

        foreach ($objects as $object) {
            $this->json_api_response_array['data'][$object->id] = [
                'id' => $object->id,
                'type' => $this->model->getTable(),
                'attributes' => $object->getApiFilter($object)
            ];

            $this->json_api_response_array['relationships'] = [];
        }

        return Response::json($this->json_api_response_array, $this->json_api_response_array['meta']['status']);
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
        $object = $this->controller_model::find($id ?? 0);
        $status = null;

        if (!$object) {
            $this->json_api_response_array['errors'] = [
                'status' => '404',
                'title' => 'Resource could not found',
                'detail' => 'The ' . Inflector::singularize($this->model->getTable()) . ' could not be found.'
            ];

            $status = $this->json_api_response_array['errors']['status'];
        } else {
            $this->json_api_response_array['meta']['status'] = 200;
            $this->json_api_response_array['meta']['count'] = 1;

            $this->json_api_response_array['links'] = [
                'collection' => $this->url_base,
                'self' => $this->url_base . '/' . $object->id
            ];

            $this->json_api_response_array['data'] = [
                'id' => $object->id,
                'type' => $this->model->getTable(),
                'attributes' => $object->getApiFilter($object)
            ];

            $this->json_api_response_array['relationships'] = [];

            $status = $this->json_api_response_array['meta']['status'];
        }

        return Response::json($this->json_api_response_array, $status);
    }

    // CREATE

    /**
     * The json version of the create feature
     * "Create a new entry in the collection. The new entry's URI is assigned automatically."
     *
     * @param Request $request
     * @return JsonResponse json response
     */
    public function jsonCreate(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            $this->controller_model::getValidation()
        );
        if ($validator->fails()) {
            $this->json_api_response_array['status'] = '422';
            $this->json_api_response_array['detail'] = 'Input validation has failed.';
            $this->json_api_response_array['validator_errors'] = $validator->errors();
        } else {
            $object = new $this->controller_model($request->all());
            $object->save();
            $this->json_api_response_array['status'] = '201';
            $this->json_api_response_array['detail'] = 'The ' . $this->model->getTable() . ' was created.';
            $this->json_api_response_array[Inflector::singularize($this->model->getTable())] = $object->getApiFilter(
                $object
            );
        }

        return Response::json($this->json_api_response_array, $this->json_api_response_array['status']);
    }

    // jsonCreateById

    // PUT

    // jsonCollectionReplace

    /**
     * Replaces an element
     * "Replace the usered member of the collection, or if it does not exist, create it."
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse json response
     */
    public function jsonElementReplace(Request $request, int $id): JsonResponse
    {
        // laravel issue with PUT, so create the array manually:
        $object = new $this->controller_model();
        foreach ($object->getFillable() as $fillable) {
            $filtered[$fillable] = $request->get($fillable);
        }
        $filtered['id'] = $id;

        $validator = Validator::make(
            $filtered,
            $this->controller_model::getValidation($object->getDefaultIgnoredUniques(), $id)
        );

        if ($validator->fails()) {
            $this->json_api_response_array['status'] = '422';
            $this->json_api_response_array['detail'] = 'Input validation has failed.';
            $this->json_api_response_array['validator_errors'] = $validator->errors();
        } else {
            // Clear old object
            $old = $this->controller_model::find($id);
            $old->forceDelete();
            // Write replacement
            $object->fill($filtered);
            $object->save();
            $this->json_api_response_array['status'] = '200';
            $this->json_api_response_array['detail'] = 'The ' . Inflector::singularize(
                    $this->model->getTable()
                ) . ' was replaced.';
            $this->json_api_response_array[Inflector::singularize($this->model->getTable())] = $object->getApiFilter(
                $object
            );
        }

        return Response::json($this->json_api_response_array, $this->json_api_response_array['status']);
    }

    // PATCH

    // jsonCollectionUpdate

    /**
     * Replaces an element
     * "Replace the usered member of the collection, or if it does not exist, create it."
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse json response
     */
    public function jsonElementUpdate(Request $request, int $id): JsonResponse
    {
        // laravel issue with PATCH, so create the array manually:
        $object = new $this->controller_model();
        foreach ($object->getFillable() as $fillable) {
            $filtered[$fillable] = $request->get($fillable);
        }
        $filtered['id'] = $id;

        $validator = Validator::make(
            $filtered,
            $this->controller_model::getValidation($object->getDefaultIgnoredUniques(), $id)
        );

        if ($validator->fails()) {
            $this->json_api_response_array['status'] = '422';
            $this->json_api_response_array['detail'] = 'Input validation has failed.';
            $this->json_api_response_array['validator_errors'] = $validator->errors();
        } else {
            $object = $this->controller_model::find($id);

            $object->fill($filtered);
            $object->save();
            $this->json_api_response_array['status'] = '200';
            $this->json_api_response_array['detail'] = 'The ' . Inflector::singularize(
                    $this->model->getTable()
                ) . ' was replaced.';
            $this->json_api_response_array[Inflector::singularize($this->model->getTable())] = $object->getApiFilter(
                $object
            );
        }

        return Response::json($this->json_api_response_array, $this->json_api_response_array['status']);
    }

    // DELETE

    // jsonCollectionDelete

    /**
     * Delete the element.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse json response
     */
    public function jsonElementDelete(Request $request, int $id): JsonResponse
    {
        $object = $this->controller_model::find($id ?? 0);

        if (!$object) {
            $this->json_api_response_array['status'] = '404';
            $this->json_api_response_array['detail'] = 'The ' . $this->model->getTable() . ' could not be found.';
        } else {
            $object->delete();

            $this->json_api_response_array['status'] = '200';
            $this->json_api_response_array['detail'] = 'The ' . $this->model->getTable() . ' was deleted.';
        }

        return Response::json($this->json_api_response_array, $this->json_api_response_array['status']);
    }

}
