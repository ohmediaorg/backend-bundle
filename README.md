# Installation

Update `composer.json` by adding this to the `repositories` array:

```json
{
    "type": "vcs",
    "url": "https://github.com/ohmediaorg/backend-bundle"
}
```

Then run `composer require ohmediaorg/backend-bundle:dev-main`.

Import the routes in `config/routes.yaml`:

```yaml
oh_media_backend:
    resource: '@OHMediaBackendBundle/config/routes.yaml'
```

Run `php bin/console make:migration` then run the subsequent migration.

Also run `npm install bootstrap bootstrap-icons sortablejs nice-select2`

# JS/Styles

Add the following to your backend JS entry point:

```js
import '../../vendor/ohmediaorg/backend-bundle/assets/js/index.js';
```

Add the following to your backend Sass file:

```scss
@import '../../../vendor/ohmediaorg/backend-bundle/assets/scss/style';
```

This should typically be all that is needed for backend styles.

## Templates

All of your backend templates should ultimately extend `@OHMediaBackend/base.html.twig`.

The bundle also provides a simple form template `@OHMediaBackend/form.html.twig`.

Any styles/javascripts that need to be on every backend template can be set up
by overriding:
- `@OHMediaBackend/include/stylesheets_global.html.twig`
- `@OHMediaBackend/include/javascripts_global.html.twig`

## Logo

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

# Navigation

## Sidebar

The sidebar nav is populated by extending `OHMedia\BackendBundle\Service\AbstractNavItemProvider`.

See [TestimonialNavItemProvider](https://github.com/ohmediaorg/testimonial-bundle/blob/main/src/Service/TestimonialNavItemProvider.php) for an example with just a link.

See [LogoNavItemProvider](https://github.com/ohmediaorg/logo-bundle/blob/main/src/Service/LogoNavItemProvider.php)
for an example with a dropdown.

### Developer Only

Links can be added to the Developer Only dropdown by extending
`OHMedia\BackendBundle\Service\AbstractDeveloperOnlyNavLinkProvider`.

See [EmailsNavLinkProvider](https://github.com/ohmediaorg/email-bundle/blob/main/src/Service/EmailsNavLinkProvider.php).

### Settings

Links can be added to the Settings dropdown by extending
`OHMedia\BackendBundle\Service\AbstractDeveloperOnlyNavLinkProvider`.
