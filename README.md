This is a simple bundle built on top of
[Httpful](http://phphttpclient.com/) that performs GET or POST
requests to an url, and parses the JSON response to a PHP array.
It supports sending custom headers, as well as attached files
using the Symfony `FileUploaded` class.

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require rubenrubiob/simple-api-caller-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new rubenrubiob\SimpleApiCallerBundle\rubenrubiobSimpleApiCallerBundle(),
        );

        // ...
    }

    // ...
}
```


Usage
=====

The caller is provided as a service, so you may use it wherever you
want. Below there are examples of use within a controller.

GET requests
------------

```php
$url = 'http://your/complete/url';
$headers = array(
    'header-1-name'     => 'header-1-value',
    'header-2-name'     => 'header-2-value',
);

$response = $this->get('rrb.simple_api_caller')->get($url, $headers);
```

POST requests
-------------

```php
use Symfony\Component\HttpFoundation\File\UploadedFile;

// ...

$url = 'http://your/complete/url';
$data = array(
    'field-1'       => 'field-1-value',
    'field-2'       => new UploadedFile('/path/to/file', 'file-name'),
    'field-3'       => array(
        'subfield-3.1'   => 'subvalue-3.1',
        'subfield-3.2'   => 'subvalue-3.2',
    ),
);
$headers = array(
    'header-1-name'     => 'header-1-value',
    'header-2-name'     => 'header-2-value',
);

$response = $this->get('rrb.simple_api_caller')->post($url, $data, $headers);
```

PUT requests
------------

```php
use Symfony\Component\HttpFoundation\File\UploadedFile;

// ...

$url = 'http://your/complete/url';
$data = array(
    'field-1'       => 'field-1-value',
    'field-2'       => new UploadedFile('/path/to/file', 'file-name'),
    'field-3'       => array(
        'subfield-3.1'   => 'subvalue-3.1',
        'subfield-3.2'   => 'subvalue-3.2',
    ),
);
$headers = array(
    'header-1-name'     => 'header-1-value',
    'header-2-name'     => 'header-2-value',
);

$response = $this->get('rrb.simple_api_caller')->put($url, $data, $headers);
```

PATCH requests
--------------

```php
use Symfony\Component\HttpFoundation\File\UploadedFile;

// ...

$url = 'http://your/complete/url';
$data = array(
    'field-1'       => 'field-1-value',
    'field-2'       => new UploadedFile('/path/to/file', 'file-name'),
    'field-3'       => array(
        'subfield-3.1'   => 'subvalue-3.1',
        'subfield-3.2'   => 'subvalue-3.2',
    ),
);
$headers = array(
    'header-1-name'     => 'header-1-value',
    'header-2-name'     => 'header-2-value',
);

$response = $this->get('rrb.simple_api_caller')->patch($url, $data, $headers);
```

Expects
-------

By default, the service expects a JSON response and parses it as an array. But
it is possible to specify the MIME type to expect:

```php
$url = 'http://your/complete/url';
$headers = array(
    'header-1-name'     => 'header-1-value',
    'header-2-name'     => 'header-2-value',
);

$response = $this->get('rrb.simple_api_caller')->expects('html')->get($url, $headers);
```

The MIME types supported are the ones of [Httpful](http://phphttpclient.com/), so check
it for further information.
