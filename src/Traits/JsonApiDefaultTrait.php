<?php
/**
 * JsonApiDefaultTrait.php
 *
 * JsonApiDefaultTrait trait
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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

/**
 * Trait JsonApiDefaultTrait
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
trait JsonApiDefaultTrait
{
    use JsonApiTrait;

    /**
     * @var Object the model exposed by the controller
     */
    protected object $model;

    /**
     * @var string the model exposed by the controller
     */
    protected string $controller_model;

    /**
     * @var string the singular name of the model
     */
    protected string $model_name_singular;

    /**
     * @var string the plural name of the model
     */
    protected string $model_name_plural;

    /**
     * @var string the base url for the model; this should ideally be overwritten
     */
    protected $url_base = '/';

    // Accessors

    /**
     * @return Object
     * @see $model
     *
     */
    public function getModel(): object
    {
        return $this->model;
    }

    /**
     * @param Object $model
     * @return JsonApiDefaultTrait
     * @see $model
     *
     */
    public function setModel(object $model): object
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return string
     * @see $controller_model
     *
     */
    public function getControllerModel(): string
    {
        return $this->controller_model;
    }

    /**
     * @param string $controller_model
     * @return JsonApiDefaultTrait
     * @see $controller_model
     *
     */
    public function setControllerModel(string $controller_model): object
    {
        $this->controller_model = $controller_model;
        return $this;
    }

    /**
     * @return string
     * @see $url_base
     *
     */
    public function getUrlBase(): string
    {
        return $this->url_base;
    }

    /**
     * @param string $url_base
     * @return JsonApiDefaultTrait
     * @see $url_base
     *
     */
    public function setUrlBase(string $url_base): object
    {
        $this->url_base = $url_base;
        return $this;
    }

    // Main functions

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
        $objects = $this->getControllerModel()::paginate($this->getJsonApiMaximumResponseNumber());

        // This always returns 200 if it's got this far... an empty response set is still "OK".
        $this->json_api_response_array['meta']['status'] = "200";
        $this->json_api_response_array['meta']['count'] = $objects->count();

        unset($this->json_api_response_array['errors']);

        $this->json_api_response_array['links'] = [
            'collection' => $this->getUrlBase(),
            'self' => $this->getUrlBase() . '?page=' . $objects->currentPage(),
            'first' => $this->getUrlBase() . '?page=1',
            'last' => $this->getUrlBase() . '?page=' . $objects->lastPage(),
            'prev' => null,
            'next' => null
        ];

        $this->json_api_response_array['links']['next'] = $objects->nextPageUrl() ?? null;

        $this->json_api_response_array['links']['prev'] = $objects->previousPageUrl() ?? null;

        // remember: even if the ID is not called "id", JSON API format requires that it be called that:
        $id_name = $this->model->getKeyName();

        foreach ($objects as $object) {
            $this->json_api_response_array['data'][] = [
                'id' => (string)$object->$id_name,
                'type' => $this->getModel()->getApiModelNameSingular(),
                'attributes' => $object->getApiAttributes(),
                'links' => ['self' => $this->singularizeUrl() . '/' . $object->$id_name],
                'relationships' => new \stdClass()
            ];
        }

        return Response::json($this->json_api_response_array, $this->json_api_response_array['meta']['status']);
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
        $object = $this->getControllerModel()::find($id ?? 0);
        $status = null;

        if (!$object) {
            $this->json_api_response_array['errors'] = [
                [
                    'status' => '404',
                    'title' => 'Resource could not found',
                    'detail' => 'The ' . $this->getModel()->getApiModelNameSingular() . ' could not be found.'
                ]
            ];
            unset($this->json_api_response_array['meta']);
            unset($this->json_api_response_array['data']);

            $status = $this->json_api_response_array['errors'][0]['status'];
        } else {
            $this->json_api_response_array['meta']['status'] = '200';
            $this->json_api_response_array['meta']['count'] = 1;

            unset($this->json_api_response_array['errors']);

            $this->json_api_response_array['links'] = ['collection' => $this->getUrlBase()];

            // remember: even if the ID is not called "id", JSON API format requires that it be called that:
            $id_name = $this->model->getKeyName();

            if (count($object->getApiAttributes()) == 0) {
                $relationships = $object->getApiAttributes();
            } else {
                $relationships = new StdClass();
            }

            $this->json_api_response_array['data'] = [
                'id' => (string)$object->$id_name,
                'type' => $this->getModel()->getApiModelNameSingular(),
                'attributes' => $object->getApiAttributes(),
                'relationships' => $relationships,
                'links' => ['self' => $this->singularizeUrl() . '/' . $object->$id_name]
            ];

            $status = $this->json_api_response_array['meta']['status'];
        }

        return Response::json($this->getJsonApiResponseArray(), $status);
    }

    // CREATE

    /**
     * The json api version of the create feature
     * "Create a new entry in the collection. The new entry's URI is assigned automatically."
     *
     * @param Request $request
     * @return JsonResponse json response
     */
    public function jsonApiCreate(Request $request): JsonResponse
    {
        // parse the request into an array:
        $re_encoded_array = $this->extractJsonApiAttributes($request->all());

        $object = new $this->controller_model();

        // remember: even if the ID is not called "id", JSON API format requires that it be called that:
        $id_name = $this->model->getKeyName();

        // manually unset the id: this needs to be a new model so force a create
        unset($re_encoded_array[$id_name]);

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

            $this->json_api_response_array['meta']['status'] = '422';
            unset($this->json_api_response_array['data']);

            $status = $this->json_api_response_array['errors'][0]['status'];
        } else {
            unset($this->json_api_response_array['errors']);

            $object->fill($re_encoded_array);
            $object->save();

            $this->json_api_response_array['data'] = [
                'id' => (string)$object->$id_name,
                'type' => $this->getModel()->getApiModelNameSingular(),
                'attributes' => $object->getApiAttributes(),
                'links' => ['self' => $this->singularizeUrl() . '/' . $object->$id_name]
            ];

            $this->json_api_response_array['data']['relationships'] = new \stdClass();

            $status = $this->json_api_response_array['meta']['status'] = "201";
            $this->json_api_response_array['meta']['detail'] = 'The ' . $this->getModel()->getApiModelNameSingular(
                ) . ' was created.';
            $this->json_api_response_array['meta']['count'] = 1;
        }

        return Response::json($this->json_api_response_array, $status);
    }

    /**
     * The json api version of the create feature, specified by id
     * "Create a new entry in the collection. The new entry's URI is assigned automatically."
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse json response
     */
    public function jsonApiCreateById(Request $request, $id): JsonResponse
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

            $this->json_api_response_array['meta']['status'] = '422';
            unset($this->json_api_response_array['data']);

            $status = $this->json_api_response_array['errors'][0]['status'];
        } else {
            unset($this->json_api_response_array['errors']);

            $object->fill($re_encoded_array);
            $object->save();

            // remember: even if the ID is not called "id", JSON API format requires that it be called that:
            $id_name = $this->model->getKeyName();

            $this->json_api_response_array['data'] = [
                'id' => (string)$object->$id_name,
                'type' => $this->getModel()->getApiModelNameSingular(),
                'attributes' => $object->getApiAttributes(),
                'links' => ['self' => $this->singularizeUrl() . '/' . $object->$id_name]
            ];

            $this->json_api_response_array['data']['relationships'] = new \stdClass();

            $status = $this->json_api_response_array['meta']['status'] = "201";
            $this->json_api_response_array['meta']['detail'] = 'The ' . $this->getModel()->getApiModelNameSingular(
                ) . ' was created.';
            $this->json_api_response_array['meta']['count'] = 1;
        }

        return Response::json($this->getJsonApiResponseArray(), $status);
    }

    // PUT

    /**
     * Replaces the entire collection
     * "Replace the entire collection with another collection."
     *
     * @return JsonResponse json response
     */
    public function jsonApiCollectionReplace(Request $request): JsonResponse
    {
        // parse the request into an array:
        $re_encoded_array = $this->extractJsonApiAttributes($request->all());

        // instantiate object to help with validation:
        $object = new $this->model();

        // drop the existing collections: has to be a force to ensure no validation errors
        $this->model::query()->forceDelete();

        // Cycle over the array and check validation:
        foreach ($re_encoded_array as $collection_item) {
            $validator = Validator::make(
                $collection_item,
                $object->getValidation()
            );

            $failed = false;
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $field_errors) {
                    $this->json_api_response_array['errors'][] = [
                        'status' => '422',
                        'title' => 'Input validation has failed',
                        'detail' => $field_errors,
                        'data' => $collection_item
                    ];
                }

                $this->json_api_response_array['meta']['status'] = '422';
                unset($this->json_api_response_array['data']);

                $status = $this->json_api_response_array['errors'][0]['status'];
                $failed = true;

                break;
            }
        }

        // if there's no errors:
        if (!$failed) {
            unset($this->json_api_response_array['errors']);

            foreach ($re_encoded_array as $collection_item) {
                $item = new $this->model();

                $item->fill($collection_item);
                $item->save();

                $processed_collection[] = $item;
            }

            $this->json_api_response_array['meta']['count'] = 1;

            $status = $this->json_api_response_array['meta']['status'] = '201';
            $this->json_api_response_array['meta']['detail'] = 'The ' . $this->getModelNameSingular(
                ) . ' collection was replaced.';
            $this->json_api_response_array['meta']['count'] = count($re_encoded_array);
        }

        return Response::json($this->json_api_response_array, $status);
    }

    /**
     * Replaces an element
     * "Replace the usered member of the collection, or if it does not exist, create it."
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse json response
     */
    public function jsonApiElementReplace(Request $request, $id): JsonResponse
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
        $object = $this->getControllerModel()::find($id);

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

            // Delete the old one : replace, not update, and use correct method to counteract soft deletes
            $object->forceDelete();

            // Write replacement
            $object = new $this->model();
            $object->fill($re_encoded_array);
            $object->id = $id;
            $object->save();

            // remember: even if the ID is not called "id", JSON API format requires that it be called that:
            $id_name = $this->model->getKeyName();

            $this->json_api_response_array['data'] = [
                'id' => (string)$object->$id_name,
                'type' => $this->getModel()->getApiModelNameSingular(),
                'attributes' => $object->getApiAttributes(),
                'links' => ['self' => $this->singularizeUrl() . '/' . $object->$id_name]
            ];

            $this->json_api_response_array['data']['relationships'] = new \stdClass();

            $status = $this->json_api_response_array['meta']['status'] = "201";
            $this->json_api_response_array['meta']['detail'] = 'The ' . $this->getModelNameSingular(
                ) . ' was replaced.';
            $this->json_api_response_array['meta']['count'] = 1;
        }

        return Response::json($this->getJsonApiResponseArray(), $status);
    }

    // PATCH

    /**
     * Update an entire collection
     * "Update all the representations of the member resources of the collection resource."
     *
     * @param Request $request
     * @return JsonResponse json response
     */
    public function jsonApiCollectionUpdate(Request $request): JsonResponse
    {
        // parse the request into an array:
        $re_encoded_array = $this->extractJsonApiAttributes($request->all());

        // to be efficient, load all items now and then use them this collection to parse over:
        $object_ids = [];
        foreach ($re_encoded_array as $collection_item) {
            if ($collection_item['id'] ?? false) {
                $object_ids[] = $collection_item['id'];
            }
        }

        $object_collection = $this->model::whereIn('id', $object_ids)->get();

        // instantiate object to help with validation:
        $object = new $this->model();

        // Cycle over the array and check validation:
        foreach ($re_encoded_array as $collection_item) {
            // if there's an existing object, find it from the $object_collection, else pass null as its a create
            if ($collection_item['id'] ?? false) {
                $validation_id = $object_collection->where('id', '=', $collection_item['id'])->find(
                        $collection_item['id']
                    ) ?? null;
            } else {
                $validation_id = null;
            }

            $validator = Validator::make(
                $collection_item,
                $object->getValidation($validation_id)
            );

            $failed = false;
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $field_errors) {
                    $this->json_api_response_array['errors'][] = [
                        'status' => '422',
                        'title' => 'Input validation has failed',
                        'detail' => $field_errors,
                        'data' => $collection_item
                    ];
                }

                $this->json_api_response_array['meta']['status'] = '422';
                unset($this->json_api_response_array['data']);

                $status = $this->json_api_response_array['errors'][0]['status'];
                $failed = true;

                break;
            }
        }

        // if there's no errors:
        if (!$failed) {
            unset($this->json_api_response_array['errors']);

            foreach ($re_encoded_array as $collection_item) {
                // if there's an existing object, find it from the $object_collection, else its a create

                if ($collection_item['id'] ?? false) {
                    $object = $object_collection->where('id', '=', $collection_item['id'])->find(
                            $collection_item['id']
                        ) ?? null;
                } else {
                    $object = new $this->model();
                }

                if (!$object) {
                    $object = new $this->model();
                }

                $object->fill($collection_item);
                $object->save();
            }

            $this->json_api_response_array['meta']['count'] = 1;

            $status = $this->json_api_response_array['meta']['status'] = '200';
            $this->json_api_response_array['meta']['detail'] = 'The ' . $this->getModelNameSingular(
                ) . ' collection was updated.';
            $this->json_api_response_array['meta']['count'] = count($re_encoded_array);
        }

        return Response::json($this->json_api_response_array, $status);
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
        // parse the request into an array:
        $re_encoded_array = $this->extractJsonApiAttributes($request->all());
        $re_encoded_array['id'] = $id;

        // Now we have that, load the model
        $object = $this->model::find($id);

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

            $this->json_api_response_array['meta']['status'] = '422';
            unset($this->json_api_response_array['data']);

        } else {
            $object->fill($re_encoded_array);
            $object->save();
            $this->json_api_response_array['status'] = '200';
            $this->json_api_response_array['detail'] = 'The ' . $this->getModel()->getApiModelNameSingular(
                ) . ' was replaced.';
            $this->json_api_response_array[$this->getModel()->getApiModelNameSingular()] = $object->getJsonFilter(
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
     * @param $id
     * @return JsonResponse json response
     */
    public function jsonApiCollectionDelete(Request $request): JsonResponse
    {
        unset($this->json_api_response_array['errors']);

        $objects = $this->getControllerModel()::all();
        $this->json_api_response_array['meta']['count'] = $objects->count();

        // remember: even if the ID is not called "id", JSON API format requires that it be called that:
        $id_name = $this->model->getKeyName();

        foreach ($objects as $object) {
            $this->json_api_response_array['data'][] = [
                'id' => (string)$object->$id_name,
                'type' => $this->getModel()->getApiModelNameSingular(),
                'attributes' => $object->getApiAttributes()
            ];
        }

        $this->model::query()->delete();

        $status = $this->json_api_response_array['meta']['status'] = 200;
        $this->json_api_response_array['meta']['detail'] = 'The collection inside the ' . $this->getModel(
            )->getApiModelNamePlural() . ' table was deleted.';
        $this->json_api_response_array['links'] = [
            'collection' => $this->getUrlBase()
        ];


        return Response::json($this->json_api_response_array, $status);
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
        $object = $this->getControllerModel()::find($id ?? 0);

        if (!$object) {
            $this->json_api_response_array['errors'] = [
                [
                    'status' => '404',
                    'title' => 'Resource could not found',
                    'detail' => 'The ' . $this->getModel()->getApiModelNameSingular() . ' could not be found.'
                ]
            ];
            unset($this->json_api_response_array['meta']);
            unset($this->json_api_response_array['data']);

            $status = $this->json_api_response_array['errors'][0]['status'];
        } else {
            $object->delete();
            unset($this->json_api_response_array['errors']);

            $this->json_api_response_array['meta']['status'] = '200';
            $this->json_api_response_array['meta']['count'] = 1;
            $this->json_api_response_array['meta']['detail'] = 'The ' . $this->getModel()->getApiModelNameSingular(
                ) . ' was deleted.';
            $this->json_api_response_array['links'] = ['collection' => $this->getUrlBase()];

            // remember: even if the ID is not called "id", JSON API format requires that it be called that:
            $id_name = $this->model->getKeyName();

            $this->json_api_response_array['data'] = [
                'id' => (string)$object->$id_name,
                'type' => $this->getModel()->getApiModelNameSingular(),
                'attributes' => $object->getApiAttributes()
            ];

            $status = $this->json_api_response_array['meta']['status'];
        }

        return Response::json($this->json_api_response_array, $status);
    }

    // Other functionality

    /**
     * Parses an input array in the form of a JSON API request, returning the attributes key.
     * Also:
     *     prepares json fields for database writing
     *     reintroduces id so that this can be parsed for create/updates
     *
     * @param array $array
     * @return array An item (in array form), OR an array of items (in array form)
     */
    public function extractJsonApiAttributes(array $array): array
    {
        $re_encoded_array = [];

        if ($array['data']['attributes'] ?? false) {
            // single object

            // note, an ID of 0 will not be found, thus still generates a create when required (which is desired functionality)
            $re_encoded_array['id'] = $array['data']['id'];

            foreach ($array['data']['attributes'] as $key => $value) {
                if (Str::contains($key, 'json')) {
                    // convert the escaped string into a json string
                    $re_encoded_array[$key] = json_encode($value);
                } else {
                    $re_encoded_array[$key] = $value;
                }
            }
        } elseif (
            is_array($array['data'] ?? false) &&
            array_values($array['data'])[0]['attributes'] ?? false
        ) {
            // array object - parse over each item
            foreach ($array['data'] as $data_item) {
                $data_item_array = [];
                if ($data_item['id'] ?? false) {
                    $data_item_array['id'] = $data_item['id'];
                }

                foreach ($data_item['attributes'] ?? [] as $key => $value) {
                    if (Str::contains($key, 'json')) {
                        $data_item_array[$key] = json_encode($value);
                    } else {
                        $data_item_array[$key] = $value;
                    }
                }

                // only include it if correct formatting items were found
                if (count($data_item_array)) {
                    $re_encoded_array[] = $data_item_array;
                }
            }
        }

        return $re_encoded_array;
    }

    /**
     * Singularizes the url, defaulting to $this->getUrlBase() if not supplied
     *
     * @param string|null $url
     * @return string
     */
    private function singularizeUrl(?string $url = null): string
    {
        if (!$url) {
            $url = $this->getUrlBase();
        }

        return str_replace(
            $this->getModel()->getApiModelNamePlural(),
            $this->getModel()->getApiModelNameSingular(),
            $url
        );
    }
}
