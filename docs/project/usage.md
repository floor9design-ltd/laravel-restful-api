# Usage

## A restful example

... how to set up auto routes

## Routes

To be completed

### Models

The software requires several default values to be established, as well as some basic functionality set up.

This can be included as follows:

```php
use Floor9design\LaravelRestfulApi\Traits\JsonApiFilterTrait;
use Illuminate\Database\Eloquent\Model;

class User extends Model  // or Authenticatable
{
    use JsonApiFilterTrait;

    // other functionality 

}
```

Many users add validation to models in order to sanitise inputs.

Each model can optionally have validation. This is done by adding the `getValidation` method:

```php
    use Floor9design\LaravelRestfulApi\Traits\JsonApiFilterTrait;
    use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use JsonApiFilterTrait;

    // other functionality 

    public function getValidation(Model $model): array
    {
        return [];
    }

}
```

The above code offers a validation array of `[]`, which is... no validation!
In this default case it is usually easier to use the optional trait:

```php
use Floor9design\LaravelRestfulApi\Traits\JsonApiFilterTrait;
use Floor9design\LaravelRestfulApi\Traits\ValidationTrait;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
   use JsonApiFilterTrait, ValidationTrait;

    // other functionality 
}
```

While this is not a validation tutorial, here is an example of the `getValidation` method which has been overridden.

```php
use Floor9design\LaravelRestfulApi\Traits\JsonApiFilterTrait;
use Floor9design\LaravelRestfulApi\Traits\ValidationTrait;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use JsonApiFilterTrait, ValidationTrait;

    public function getValidation(?User $user = null)
    {
        if (!$user) {
            $user = $this;
        }

        $validation = [
            'id' => ['sometimes', 'exists:users', 'integer'],
            'name' => ['required_without:id', 'max:255'],
            'email' => ['required_without:id', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['required_without:id', 'min:5'],
            'tel' => ['sometimes', 'max:20']
        ];

        return $validation;
    }
}
```

This contains some good example laravel validation techniques.  The only thing of note is that the following is 
required within the code:

```php
if (!$user) {
    $user = $this;
}
```

Finally, the properties of the model need to be exposed. Often it is not appropriate to expose all properties, for 
example `$user->password`. The `$api_array_filter` array can be added to expose these: 

```php
use Floor9design\LaravelRestfulApi\Traits\JsonApiFilterTrait;
use Floor9design\LaravelRestfulApi\Traits\ValidationTrait;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use JsonApiFilterTrait, ValidationTrait;

    protected $api_array_filter = [
        'name',
        'email'
    ];

    public function getValidation(?User $user = null)
    {
        if (!$user) {
            $user = $this;
        }

        $validation = [
            'id' => ['sometimes', 'exists:users', 'integer'],
            'name' => ['required_without:id', 'max:255'],
            'email' => ['required_without:id', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['required_without:id', 'min:5'],
            'tel' => ['sometimes', 'max:20']
        ];

        return $validation;
    }
}
```

## Controllers 

As discussed in [background](background.md), the main method to implement the classes is via the controller classes.

```php
namespace App\Http\Controllers;

use Floor9design\LaravelRestfulApi\Interfaces\JsonApiInterface;
use Floor9design\LaravelRestfulApi\Traits\JsonApiDefaultTrait;
use Floor9design\LaravelRestfulApi\Traits\JsonApiTrait;

class UsersController extends Controller implements JsonApiInterface
{
    use JsonApiDefaultTrait;
    use JsonApiTrait;

    public function __construct()
    {
        $this->setControllerModel('\App\Models\User');
        $this->setModelNameSingular('user');
        $this->setModelNamePlural('users');
        $this->setModel(new $this->controller_model);
        $this->setUrlBase(route('some.route'));
    }

}

```

Two core classes are shown:

* `JsonApiInterface` contracts the class into providing the correct methods
* `JsonApiTrait` provides basic functions

Finally:

* `JsonApiDefaultTrait` implements the default behaviour.

There are two classes that can be implemented here. 

* `JsonApiDefaultTrait` implements the default behaviour.
* `JsonApi501Trait` implements JsonApi compliant 501 responses ("not implemented")

```php
use JsonApi501Trait;
use JsonApiTrait;
```

The above use statements would result in a class that correctly responded with JsonApi compliant 501 for all methods.

These can be combined, as the following examples show:

```php
use JsonApi501Trait;
use JsonApiDefaultTrait {
    JsonApiDefaultTrait::jsonIndex insteadof JsonApi501Trait;
    JsonApiDefaultTrait::jsonDetails insteadof JsonApi501Trait;
    JsonApiDefaultTrait::jsonCreate insteadof JsonApi501Trait;
    JsonApi501Trait::jsonCreateById insteadof JsonApiDefaultTrait;
    JsonApi501Trait::jsonCollectionReplace insteadof JsonApiDefaultTrait;
    JsonApi501Trait::jsonElementReplace insteadof JsonApiDefaultTrait;
    JsonApi501Trait::jsonCollectionUpdate insteadof JsonApiDefaultTrait;
    JsonApi501Trait::jsonElementUpdate insteadof JsonApiDefaultTrait;
    JsonApi501Trait::jsonElementDelete insteadof JsonApiDefaultTrait;
    JsonApi501Trait::jsonCollectionDelete insteadof JsonApiDefaultTrait;
}
use JsonApiTrait;
```

Here, two clashing traits use the `insteadof` keyword to choose the correct method to apply.

## custom restful calls

* overwriting

