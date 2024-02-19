# Symfony Deprecated Routes Bundle

Symfony Deprecated Routes Bundle offers to mark some api routes as deprecated.

## Installation

Use Composer to install the bundle:

```shell
composer require macpaw/symfony-messenger-bundle
```

## Setup bundle

Enable the bundle by adding it to the list of registered bundles in config/bundles.php

```php
// config/bundles.php

return [
            Macpaw\SymfonyDeprecatedRoutes\DeprecatedRoutesBundle::class => ['all' => true],
        // ...
    ];
```

## Extend bundle options

This bundle provide configuration for marking routes

| Option          | Type  | Description                                                                                     | Default value |
|-----------------|-------|-------------------------------------------------------------------------------------------------|---------------|
| headers         | array | Deprecation headers names                                                                       |               |
| isDisabled      | bool  | Disable add marks for routes                                                                    | false         |
| headers         | array | Deprecation headers names                                                                       |               |
| isSinceRequired | bool  | If true enable validation for set [route attribute](src/Routing/Attribute/DeprecatedRoute.php)  | false         |

### Headers names options

| Name                  | Description                         |
|-----------------------|-------------------------------------|
| deprecatedMessageName | Deprecated message info header name |
| deprecatedFromName    | Start deprecation date              |
| deprecatedSinceName   | The date of the removal route       |

## Full config example with default values

`config/packages/deprecated-routes.yaml` 

```shell
deprecated-routes:
    isSinceRequired: false
    isDisabled: false

    headers:
      deprecatedMessageName: 'X-DEPRECATED'
      deprecatedFromName: 'X-DEPRECATED-FROM'
      deprecatedSinceName: 'X-DEPRECATED-SINCE'    
```
