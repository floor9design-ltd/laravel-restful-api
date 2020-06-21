<?php
/**
 * JsonApiExposesTrait.php
 *
 * JsonApiExposesTrait trait
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
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Trait JsonApiExposesTrait
 *
 * Trait to give models a way to expose core properties
 * Can be used to create responses that are still filtered according to the model rules, ensuring that nothing is
 * accidentally exposed.
 *
 * @category  None
 * @package   Floor9design\LaravelRestfulApi\Traits
 * @author    Rick Morice <rick@floor9design.com>
 * @copyright Floor9design Ltd
 * @license   MIT
 * @version   1.0
 * @link      https://www.floor9design.com
 * @link      https://laravel.com/
 * @since     File available since Release 1.0
 */
trait JsonApiExposesTrait
{
    // Accessors

    /**
     * @return array
     * @see $api_array_filter
     *
     */
    public function getApiArrayFilter(): array
    {
        return $this->api_array_filter ?? [];
    }

    /**
     * @return array
     * @see $api_exposed_relationships
     *
     */
    public function getApiExposedRelationships(): array
    {
        return $this->api_exposed_relationships ?? [];
    }

    /**
     * @return array
     * @see $api_included_relationships
     *
     */
    public function getApiIncludedRelationships(): array
    {
        return $this->api_included_relationships ?? [];
    }

    // Other functionality

    /**
     * Loads all the api_array_filer attributes into an array
     *
     * @return array
     */
    public function getApiAttributes(): array
    {
        $attributes = [];

        foreach ($this->getApiArrayFilter() as $api_array_key) {

            // if the object has "json" in it, automatically decode (to fix layout issues when it's re-encoded)
            if (Str::contains($api_array_key, 'json')) {
                $attributes[$api_array_key] = json_decode($this->$api_array_key);
            } else {
                $attributes[$api_array_key] = $this->$api_array_key;
            }

        }

        return $attributes;
    }

    /**
     * Loads all the api_exposed_relationships attributes into an array suitable for inclusion
     *
     * @return array
     */
    public function processApiExposedRelationships(): array
    {
        $relationships = [];

        foreach ($this->getApiExposedRelationships() as $relationship) {

            if($this->$relationship ?? false) {

                if($this->$relationship instanceof Collection) {
                    // catch collections
                    foreach ($this->$relationship as $object) {
                        $relationships[$relationship][] = $this->generateRelationshipStructure($object);
                    }
                } else {
                    // process single objects
                    $relationships[$relationship] = $this->generateRelationshipStructure($this->$relationship);
                }

            }

        }

        if(count($this->generateIncludedStructure($this))) {
            $relationships['included'] = $this->generateIncludedStructure($this);
        }

        return $relationships;
    }

    /**
     * Creates a relationship array suitable for inclusion from a model
     *
     * @param Model $model
     * @return array
     * @todo update to include links
     * @todo update to not use getTable()
     *
     */
    public function generateRelationshipStructure(Model $model) : array
    {
        //$links = [];
        $data = [];

        foreach ($model->getApiArrayFilter() as $api_array_key) {

            $data['type'] = $model->getTable();
            $data['id'] = (string)$model->id;

        }

        return [
            //'links' => $links,
            'data' => $data
        ];
    }

    /**
     * Creates an included array suitable for inclusion from a model
     *
     * @param Model $model
     * @return array
     */
    public function generateIncludedStructure(Model $model) : array
    {
        $included = [];

        // includes all are at the same level, so flatten all ojbects:
        $array_to_process = [];

        foreach ($model->getApiIncludedRelationships() as $relationship) {

            if($model->$relationship ?? false) {
                if($model->$relationship instanceof Collection) {
                    // catch collections
                    foreach ($model->$relationship as $object) {
                        $array_to_process[] = $object;
                    }
                } else {
                    // process single objects
                    $array_to_process[] = $model->$relationship;
                }
            }

        }

        foreach ($array_to_process as $relationship_object) {

            $processed_object = [];

            $processed_object['type'] = $relationship_object->getTable();
            $processed_object['id'] = (string)$relationship_object->id;

            $processed_object['attributes'] = $relationship_object->getApiAttributes();

            $included[] = $processed_object;
        }

        return $included;
    }

}
