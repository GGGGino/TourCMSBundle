# TourCMSBundle
Symfony Bundle for the TourCMS library.

## Installation

CLI
```
composer require ggggino/tourcms-bundle
```

AppKernel.php
```php
array(
    ...,
    new GGGGino\TourCMSBundle\GGGGinoTourCMSBundle()
)
```

routing_dev.yml
```yml
ggggino_tourcms:
    resource: "@GGGGinoTourCMSBundle/Controller"
    prefix: /ggggino-tourcms
    type: annotation
```

## Configuration
```yml
ggggino_tourcms:
    marketplace_id: 10
    api_key: 'prova'
    timeout: 0
    channel_id: %tourcms_channel_id%
```
