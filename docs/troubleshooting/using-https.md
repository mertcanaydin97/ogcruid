# Using HTTPS on yours site

**Symptom:** When visiting your site with HTTPS, images within Cruid fail to load.

**Cause:** Images urls are generated by Cruid using

```php
Storage::disk(config('cruid.storage.disk'))->url($file);
```

If cruid.storage.disk is set to public, and the public disk is the default from Laravel, then the url property in the disk configuration is set to

```php
'url' => env('APP_URL').'/storage',
```

which uses the non-HTTPS APP\_URL value to build an absolute url to the image.

**Solution:** If you remove env\('APP\_URL'\). from the public disk configuration, then it will render a domain-relative url, which will always use the current domain and protocol.

As an aside, if you need a fully-qualified URL, you could wrap the call to `Cruid::image('...')` with `asset()`, so it would be

```php
asset(Cruid::image('...'))
```

This will return the current protocol, domain, and correct path to that image.
