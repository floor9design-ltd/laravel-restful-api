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
use Illuminate\Support\Str;

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

        // This always returns 200 if it's got this far... an empty response set is still "OK".
        $this->json_api_response_array['meta']['status'] = "200";
        $this->json_api_response_array['meta']['count'] = $objects->count();

        unset($this->json_api_response_array['errors']);

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
            $this->json_api_response_array['data'][] = [
                'id' => (string)$object->id,
                'type' => $this->model->getTable(),
                'attributes' => $object->getApiFilter($object),
                'links' => ['self' => $this->url_base . '/' . $object->id],
                'relationships' => new \stdClass()
            ];
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
                [
                    'status' => '404',
                    'title' => 'Resource could not found',
                    'detail' => 'The ' . Inflector::singularize($this->model->getTable()) . ' could not be found.'
                ]
            ];
            unset($this->json_api_response_array['meta']);
            unset($this->json_api_response_array['data']);

            $status = $this->json_api_response_array['errors'][0]['status'];
        } else {
            $this->json_api_response_array['meta']['status'] = '200';
            $this->json_api_response_array['meta']['count'] = 1;

            unset($this->json_api_response_array['errors']);

            $this->json_api_response_array['links'] = ['collection' => $this->url_base];

            $this->json_api_response_array['data'] = [
                'id' => (string)$object->id,
                'type' => $this->model->getTable(),
                'attributes' => $object->getApiFilter($object),
                'links' => ['self' => $this->url_base . '/' . $object->id]
            ];

            $this->json_api_response_array['data']['relationships'] = new \stdClass();

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
        // laravel parse the request into an array: reset this to be json where valid array (move this into a private function later)
        $re_encoded_array = $this->extractJsonApiAttributes($request->all());

        $validator = Validator::make(
            $re_encoded_array,
            $this->controller_model::getValidation()
        );

        if ($validator->fails()) {
            $this->json_api_response_array['status'] = '422';
            $this->json_api_response_array['detail'] = 'Input validation has failed.';
            $this->json_api_response_array['validator_errors'] = $validator->errors();
        } else {
            $single_object_name = Inflector::singularize($this->model->getTable());

            $object = new $this->controller_model($re_encoded_array);
            $object->save();
            $this->json_api_response_array['status'] = '201';
            $this->json_api_response_array['detail'] = 'The ' . $single_object_name . ' was created.';
            $this->json_api_response_array[$single_object_name] = $object->getApiFilter(
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

    /**
     * Parses an input array in the form of a JSON API request, returning thr attributes key.
     * Also prepares json fields for database writing
     *
     * @param array $array
     * @return array
     */
    public function extractJsonApiAttributes(array $array): Array
    {
        $re_encoded_array = [];

        if ($array['data']['attributes'] ?? 0) {
            foreach ($array['data']['attributes'] as $key => $value) {
                if (Str::contains($key, 'json')) {
                    $re_encoded_array[$key] = json_encode($value);
                } else {
                    $re_encoded_array[$key] = $value;
                }
            }
        }

        return $re_encoded_array;
    }

}
