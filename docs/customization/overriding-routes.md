# Overriding Routes

You can override any Cruid routes by writing the routes you want to overwrite below `Cruid::routes()`. For example if you want to override your post LoginController:

```php
Route::group(['prefix' => 'admin'], function () {
   Cruid::routes();

   // Your overwrites here
   Route::post('login', ['uses' => 'MyAuthController@postLogin', 'as' => 'postlogin']);
});
```

