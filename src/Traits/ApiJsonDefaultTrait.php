<?php
/**
 * ApiJsonDefaultTrait.php
 *
 * ApiJsonDefaultTrait trait
 *
 * php 7.4+
 *
 * @category  None
 * @package   App\Traits
 * @author    Rick Morice <rick@floor9design.com>
 * @copyright Intrica Ltd
 * @license   Private software
 * @version   1.0
 * @link      https://www.intrica.net/
 * @link      https://www.smartsearchsecure.com/
 * @version   1.0
 * @since     File available since Release 1.0
 *
 */

namespace App\Traits;

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
 * @package   App\Traits
 * @author    Rick Morice <rick@floor9design.com>
 * @copyright Intrica Ltd
 * @license   Private software
 * @version   1.0
 * @link      https://www.intrica.net/
 * @link      https://www.smartsearchsecure.com/
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

        $response['http_response_code'] = '200';
        $response[$this->model->getTable()] = [];

        foreach ($objects as $object) {
            $response[$this->model->getTable()][] = $object->getApiFilter($object);
        }

        return Response::json($response, $response['http_response_code']);

    }

    /**
     * The json version of the detail screen
     * "Retrieve a representation of the usered member of the collection"
     *
     * @param Request $request Laravel Request object
     * @param int $id Object id
     * @return JsonResponse json response
     */
    public function jsonDetails(Request $request, int $id): JsonResponse
    {
        $object = $this->controller_model::find($id ?? 0);

        if (!$object) {
            $response['http_response_code'] = '404';
            $response['detail'] = 'The ' . $this->model->getTable() . ' could not be found.';
        } else {
            $response['http_response_code'] = '200';
            $response[Inflector::singularize($this->model->getTable())] = $object->getApiFilter($object);
        }

        return Response::json($response, $response['http_response_code']);
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
            $response['http_response_code'] = '422';
            $response['detail'] = 'Input validation has failed.';
            $response['validator_errors'] = $validator->errors();
        } else {
            $object = new $this->controller_model($request->all());
            $object->save();
            $response['http_response_code'] = '201';
            $response['detail'] = 'The ' . $this->model->getTable() . ' was created.';
            $response[Inflector::singularize($this->model->getTable())] = $object->getApiFilter($object);
        }

        return Response::json($response, $response['http_response_code']);
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
            $response['http_response_code'] = '422';
            $response['detail'] = 'Input validation has failed.';
            $response['validator_errors'] = $validator->errors();
        } else {
            // Clear old object
            $old = $this->controller_model::find($id);
            $old->forceDelete();
            // Write replacement
            $object->fill($filtered);
            $object->save();
            $response['http_response_code'] = '200';
            $response['detail'] = 'The ' . Inflector::singularize($this->model->getTable()) . ' was replaced.';
            $response[Inflector::singularize($this->model->getTable())] = $object->getApiFilter($object);
        }

        return Response::json($response, $response['http_response_code']);
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
            $response['http_response_code'] = '422';
            $response['detail'] = 'Input validation has failed.';
            $response['validator_errors'] = $validator->errors();
        } else {
            $object = $this->controller_model::find($id);

            $object->fill($filtered);
            $object->save();
            $response['http_response_code'] = '200';
            $response['detail'] = 'The ' . Inflector::singularize($this->model->getTable()) . ' was replaced.';
            $response[Inflector::singularize($this->model->getTable())] = $object->getApiFilter($object);
        }

        return Response::json($response, $response['http_response_code']);
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
            $response['http_response_code'] = '404';
            $response['detail'] = 'The ' . $this->model->getTable() . ' could not be found.';
        } else {

            $object->delete();

            $response['http_response_code'] = '200';
            $response['detail'] = 'The ' . $this->model->getTable() . ' was deleted.';
        }

        return Response::json($response, $response['http_response_code']);
    }

}
