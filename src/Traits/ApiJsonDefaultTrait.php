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

        // remember: even if the ID is not called "id", JSON API format requires that it be called that:
        $id_name = $this->model->getKeyName();

        foreach ($objects as $object) {
            $this->json_api_response_array['data'][] = [
                'id' => (string)$object->$id_name,
                'type' => $this->model->getTable(),
                'attributes' => $object->getApiFilter($object),
                'links' => ['self' => $this->url_base . '/' . $object->$id_name],
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

            // remember: even if the ID is not called "id", JSON API format requires that it be called that:
            $id_name = $this->model->getKeyName();

            $this->json_api_response_array['data'] = [
                'id' => (string)$object->$id_name,
                'type' => $this->model->getTable(),
                'attributes' => $object->getApiFilter($object),
                'links' => ['self' => $this->url_base . '/' . $object->$id_name]
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
        // parse the request into an array:
        $re_encoded_array = $this->extractJsonApiAttributes($request->all());

        $object = new $this->controller_model();
        $validator = Validator::make(
            $re_encoded_array,
            $object->getValidation()
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $field_errors) {
                $this->json_api_response_array['errors'][] = [
                    'status' => '422',
                    'title' => 'Input validation has failed',
                    'detail' => $field_errors
                ];
            }

            unset($this->json_api_response_array['meta']);
            unset($this->json_api_response_array['data']);

            $status = $this->json_api_response_array['errors'][0]['status'];
        } else {
            unset($this->json_api_response_array['errors']);

            $single_object_name = Inflector::singularize($this->model->getTable());

            $object->fill($re_encoded_array);
            $object->save();

            // remember: even if the ID is not called "id", JSON API format requires that it be called that:
            $id_name = $this->model->getKeyName();

            $this->json_api_response_array['data'] = [
                'id' => (string)$object->$id_name,
                'type' => $this->model->getTable(),
                'attributes' => $object->getApiFilter($object),
                'links' => ['self' => $this->url_base . '/' . $object->$id_name]
            ];

            $this->json_api_response_array['data']['relationships'] = new \stdClass();

            $status = $this->json_api_response_array['meta']['status'] = "201";
            $this->json_api_response_array['meta']['detail'] = 'The ' . $single_object_name . ' was created.';
            $this->json_api_response_array['meta']['count'] = 1;
        }

        return Response::json($this->json_api_response_array, $status);
    }

    // jsonCreateById

    /**
     * The json version of the create feature, specified by id
     * "Create a new entry in the collection. The new entry's URI is assigned automatically."
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse json response
     */
    public function jsonCreateById(Request $request, int $id): JsonResponse
    {
        // Using an ID to specifically create an item is unusually, as people normally can't guarantee the database state
        // This causes validator issues. An often used validator rule is something like:
        // 'id' => 'sometimes|exists:objects|integer',
        // This fails when forcing a specific ID create, so overwrite with a sensible one for just the ID:

        // parse the request into an array:
        $re_encoded_array = $this->extractJsonApiAttributes($request->all());
        $re_encoded_array[$this->model->getKeyName()] = $id;
        $object = new $this->controller_model();

        $updated_validation_array = $object->getValidation();
        $updated_validation_array[$this->model->getKeyName()] = 'sometimes|unique:' . $this->model->getTable(
            ) . ',' . $this->model->getKeyName() . '|integer';

        $validator = Validator::make(
            $re_encoded_array,
            $updated_validation_array
        );

        $re_encoded_array[$this->model->getKeyName()] = (int)$id;

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $field_errors) {
                $this->json_api_response_array['errors'][] = [
                    'status' => '422',
                    'title' => 'Input validation has failed',
                    'detail' => $field_errors
                ];
            }

            unset($this->json_api_response_array['meta']);
            unset($this->json_api_response_array['data']);

            $status = $this->json_api_response_array['errors'][0]['status'];
        } else {
            unset($this->json_api_response_array['errors']);

            $single_object_name = Inflector::singularize($this->model->getTable());

            $object->fill($re_encoded_array);
            $object->save();

            // remember: even if the ID is not called "id", JSON API format requires that it be called that:
            $id_name = $this->model->getKeyName();

            $this->json_api_response_array['data'] = [
                'id' => (string)$object->$id_name,
                'type' => $this->model->getTable(),
                'attributes' => $object->getApiFilter($object),
                'links' => ['self' => $this->url_base . '/' . $object->$id_name]
            ];

            $this->json_api_response_array['data']['relationships'] = new \stdClass();

            $status = $this->json_api_response_array['meta']['status'] = "201";
            $this->json_api_response_array['meta']['detail'] = 'The ' . $single_object_name . ' was created.';
            $this->json_api_response_array['meta']['count'] = 1;
        }

        return Response::json($this->json_api_response_array, $status);
    }

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
        // parse the request into an array:
        $re_encoded_array = $this->extractJsonApiAttributes($request->all());

        // Need to validate twice: once for ID:
        $validator_id = Validator::make(
            [$this->model->getKeyName() => $id],
            [
                $this->model->getKeyName() => 'exists:' . $this->model->getTable() . ',' . $this->model->getKeyName(
                    ) . '|integer'
            ]
        );

        // Now we have that, load the model
        $object = $this->controller_model::find($id);

        // if there's no object, instantiate one to help with validation:
        if (!$object) {
            $object = new $this->model();
        }

        $validator = Validator::make(
            $re_encoded_array,
            $object->getValidation()
        );

        if ($validator_id->fails() || $validator->fails()) {
            foreach ($validator_id->errors()->messages() as $error) {
                foreach ($error as $message) {
                    $validator->errors()->add('id', $message);
                }
            }

            $this->json_api_response_array['status'] = '422';
            $this->json_api_response_array['detail'] = 'Input validation has failed.';
            $this->json_api_response_array['validator_errors'] = $validator->errors();
        } else {
            // Delete the old one : replace, not update, and use correct method to counteract soft deletes
            $object->forceDelete();
            // Write replacement
            $object = new $this->model();
            $object->fill($re_encoded_array);
            $object->id = $id;
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
        // parse the request into an array:
        $re_encoded_array = $this->extractJsonApiAttributes($request->all());
        $re_encoded_array['id'] = $id;

        // Now we have that, load the model
        $object = $this->controller_model::find($id);

        // if there's no object, instantiate one to help with validation:
        if (!$object) {
            $object = new $this->model();
        }

        $validator = Validator::make(
            $re_encoded_array,
            $object->getValidation()
        );

        if ($validator->fails()) {
            $this->json_api_response_array['status'] = '422';
            $this->json_api_response_array['detail'] = 'Input validation has failed.';
            $this->json_api_response_array['validator_errors'] = $validator->errors();
        } else {
            $object->fill($re_encoded_array);
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

    /**
     * Delete the entire collection.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse json response
     */
    public function jsonCollectionDelete(Request $request): JsonResponse
    {
        $this->model::query()->delete();

        $this->json_api_response_array['status'] = '200';
        $this->json_api_response_array['detail'] = 'The collection inside the ' . $this->model->getTable(
            ) . ' was deleted.';

        return Response::json($this->json_api_response_array, $this->json_api_response_array['status']);
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
     * Parses an input array in the form of a JSON API request, returning the attributes key.
     * Also prepares json fields for database writing
     *
     * @param array $array
     * @return array
     */
    public function extractJsonApiAttributes(array $array): array
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
