# Overriding files

## Overriding BREAD Views

You can override any of the BREAD views for a **single** BREAD by creating a new folder in `resources/views/vendor/cruid/slug-name` where _slug-name_ is the _slug_ that you have assigned for that table. There are 4 files that you can override:

* browse.blade.php
* edit-add.blade.php
* read.blade.php
* order.blade.php

Alternatively you can override the views for **all** BREADs by creating any of the above files under `resources/views/vendor/cruid/bread`

## Overriding submit button:
You can override the submit button without the need to override the whole `edit-add.blade.php` by extending the `submit-buttons` section:  
```blade
@extends('cruid::bread.edit-add')
@section('submit-buttons')
    @parent
    <button type="submit" class="btn btn-primary save">Save And Publish</button>
@endsection
```

## Using custom Controllers

You can override the controller for a single BREAD by creating a controller which extends Cruids controller, for example:

```php
<?php

namespace App\Http\Controllers;

class CruidCategoriesController extends \Og\Cruid\Http\Controllers\CruidBaseController
{
    //...
}
```

After that go to the BREAD-settings and fill in the Controller Name with your fully-qualified class-name:

![](../.gitbook/assets/bread_controller.png)

You can now override all methods from the [CruidBaseController](https://github.com/the-control-group/cruid/blob/1.6/src/Http/Controllers/CruidBaseController.php)

## Overriding Cruids Controllers

{% hint style="danger" %}
**Only use this method if you know what you are doing**  
We don't recommend or support overriding all controllers as you won't get any code-changes made in future updates.
{% endhint %}

If you want to override any of Cruids core controllers you first have to change your config file `config/cruid.php`:

```php
/*
|--------------------------------------------------------------------------
| Controllers config
|--------------------------------------------------------------------------
|
| Here you can specify cruid controller settings
|
*/

'controllers' => [
    'namespace' => 'App\\Http\\Controllers\\Cruid',
],
```

Then run `php artisan cruid:controllers`, Cruid will now use the child controllers which will be created at `App/Http/Controllers/Cruid`

## Overriding Cruid-Models

You are also able to override Cruids models if you need to.  
To do so, you need to add the following to your AppServiceProviders register method:

```php
Cruid::useModel($name, $object);
```

Where **name** is the class-name of the model and **object** the fully-qualified name of your custom model. For example:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Events\Dispatcher;
use Og\Cruid\Facades\Cruid;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Cruid::useModel('DataRow', \App\DataRow::class);
    }
    // ...
}
```

The next step is to create your model and make it extend the original model. In case of `DataRow`:

```php
<?php

namespace App;

class DataRow extends \Og\Cruid\Models\DataRow
{
    // ...
}
```

If the model you are overriding has an associated BREAD, go to the BREAD settings for the model you are overriding
and replace the Model Name with your fully-qualified class-name. For example, if you are overriding the Cruid `Menu`
model with your own `App\Menu` model:

![](../.gitbook/assets/bread_override_cruid_models.png)

