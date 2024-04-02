# Installation

Make sure the following bundles are installed and set up:

1. `ohmediaorg/security-bundle`

Enable the backend bundle in `config/bundles.php`:

```php
return [
    // ...
    OHMedia\BackendBundle\OHMediaBackendBundle::class => ['all' => true],
];
```

Also run `npm install bootstrap bootstrap-icons sortablejs`

## Config

Update `config/routes/attributes.yml`:

```yaml
backend_controllers:
    resource: ../../src/Controller/Backend
    type: attribute
    prefix: admin
```

## JS/Styles

Add the following to your backend JS entry point:

```js
import '../../vendor/ohmediaorg/backend-bundle/src/Resources/js/index.js';
```

Add the following to your backend Sass file:

```scss
@import '../../../vendor/ohmediaorg/backend-bundle/src/Resources/scss/style';
```

This should typically be all that is needed for backend styles.

### Templates

All of your backend templates should ultimately extend `@OHMediaBackend/base.html.twig`.

The bundle also provides a simple form template `@OHMediaBackend/form.html.twig`.

Any styles/javascripts that need to be on every backend template can be set up
by overriding:
- `@OHMediaBackend/include/stylesheets_global.html.twig`
- `@OHMediaBackend/include/javascripts_global.html.twig`

### Logo

The logo can be similarly overridden via `@OHMediaBackend/include/logo.html.twig`
in combination with a Sass variable `$oh-logo` used for the mobile menu icon.

# Entities

Create entity classes using the boilerplate command:

```bash
php bin/console ohmedia:backend:boilerplate

 Class name of the entity:
 > Post
```

then add your custom fields using the maker command:

```bash
$ php bin/console make:entity

 Class name of the entity to create or update (e.g. TinyGnome):
 > Post
```

You may want to represent some of these custom fields in the
`App\Form\PostType` class that was auto-generated.
