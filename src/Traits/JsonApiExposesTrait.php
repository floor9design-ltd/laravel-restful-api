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
    public function getApiFilter(): array
    {
        return $this->api_filter ?? [];
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

    /**
     * This attempts to get a model property ($api_model_name_singular).
     * If it cannot be found, the tablename will be returned, using the Str:singular() function to
     * singularise it.
     *
     * @return string
     */
    public function getApiModelNameSingular(): string
    {
        if($this->api_model_name_singular ?? false) {
        } else {
            $this->api_model_name_singular = Str::singular($this->getTable());
        }
        return $this->api_model_name_singular;
    }

    /**
     * This attempts to get a model property ($api_model_name_plural).
     * If it cannot be found, the tablename will be returned.
     *
     * @return string
     */
    public function getApiModelNamePlural(): string
    {
        if($this->api_model_name_plural ?? false) {
        } else {
            $this->api_model_name_plural = $this->getTable();
        }
        return $this->api_model_name_plural;
    }

    // Other functionality

    /**
     * Loads all the api_filter attributes into an array
     * These can be overwritten using the $overwrite variable.
     * If $overwrite is specified, $api_array_keys are filtered by $overwrite.
     * Note, you cannot show properties that are not in $api_array_filer
     *
     * @param array|null $overwrite
     * @return array
     */
    public function getApiAttributes(?array $overwrite = null): array
    {
        $attributes = [];

        foreach ($this->getApiFilter() as $api_key) {
            if(
                // Show if there was no overwrite:
                !$overwrite ||
                // or the overwrite matches the key
                in_array($api_key, $overwrite)
            ) {
                // if the object has "json" in it, automatically decode (to fix layout issues when it's re-encoded)
                if (Str::contains($api_key, 'json')) {
                    $attributes[$api_key] = json_decode($this->$api_key);
                } else {
                    $attributes[$api_key] = $this->$api_key;
                }
            }
        }

        return $attributes;
    }

    /**
     * Loads all the api_exposed_relationships attributes into an array suitable for inclusion
     * These can be overwritten using the $overwrite variable.
     * If $overwrite is specified, $api_array_keys are filtered by $overwrite.
     * Note, you cannot show properties that are not in $api_filer
     *
     * @param array|null $overwrite
     * @return array
     */
    public function processApiExposedRelationships(?array $overwrite = null): array
    {
        $relationships = [];

        foreach ($this->getApiExposedRelationships() as $relationship) {
            if(
                // Show if there was no overwrite:
                !$overwrite ||
                // or the overwrite matches the key
                in_array($relationship, $overwrite)
            ) {
                if ($this->$relationship ?? false) {
                    if ($this->$relationship instanceof Collection) {
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
        }

        if(count($this->generateIncludedStructure($this))) {
            $relationships['included'] = $this->generateIncludedStructure($this, $overwrite);
        }

        return $relationships;
    }

    /**
     * Creates a relationship array suitable for inclusion from a model
     *
     * @param Model $model
     * @return array
     * @todo update to include links
     *
     */
    public function generateRelationshipStructure(Model $model) : array
    {
        //$links = [];
        $data = [];
        $data['type'] = $model->getApiModelNameSingular();
        $data['id'] = (string)$model->id;

        return [
            //'links' => $links,
            'data' => $data
        ];
    }

    /**
     * Creates an included array suitable for inclusion from a model
     * These can be overwritten using the $overwrite variable.
     * If $overwrite is specified, $api_array_keys are filtered by $overwrite.
     * Note, you cannot show properties that are not in $api_filer
     *
     * @param Model $model
     * @param array|null $overwrite
     * @return array
     */
    public function generateIncludedStructure(Model $model, ?array $overwrite = null) : array
    {
        $included = [];

        // includes all are at the same level, so flatten all ojbects:
        $array_to_process = [];

        foreach ($model->getApiIncludedRelationships() as $relationship) {
            if(
                // Show if there was no overwrite:
                !$overwrite ||
                // or the overwrite matches the key
                in_array($relationship, $overwrite)
            ) {
                if ($model->$relationship ?? false) {
                    if ($model->$relationship instanceof Collection) {
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
        }

        foreach ($array_to_process as $relationship_object) {

            $processed_object = [];

            $processed_object['type'] = $relationship_object->getApiModelNameSingular();
            $processed_object['id'] = (string)$relationship_object->id;

            $processed_object['attributes'] = $relationship_object->getApiAttributes();

            $included[] = $processed_object;
        }

        return $included;
    }

}
