url
===

[![Build Status](https://travis-ci.org/krixon/url.svg?branch=master)](https://travis-ci.org/krixon/url)
[![Coverage Status](https://coveralls.io/repos/github/krixon/url/badge.svg?branch=master)](https://coveralls.io/github/krixon/url?branch=master)

PHP7 URL value object library.

# Prerequisites

- PHP 5.6+

# Installation


## Install via composer

To install url with Composer, run the following command:

```sh
$ composer require krixon/url
```

You can see this library on [Packagist](https://packagist.org/packages/krixon/url).

## Install from source

```sh
# HTTP
$ git clone https://github.com/krixon/url.git
# SSH
$ git clone git@github.com:krixon/url.git
```
## Development

### Build Image and Run Container

Note: If your host machine's user does not have an ID of 1000, run the following command from the project root
directory:

```bash
echo "DEV_UID=$(id -u)" > .env
```
This ensures that any files created in mounted directories have the correct permissions. It will also cause the host
user's SSH keys and Composer cache to be used inside the container.

Build image:

`$ docker-compose build`

Install dependencies:

`$ docker-compose run --rm library composer install`

### Run the tests

`$ docker-compose run --rm library composer test`