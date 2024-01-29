# wp-responsive-thumbnail

Responsive image sizes for Wordpress

This plugin enables theme developers to specify media breakpoints for different images sizes.
It works by wrapping the image inside a html5 `picture`-tag and providing the appropriate source elements.

## Usage

Add custom image sizes

```php
add_image_size( 'stage', 1440, 560, true );
add_image_size( 'stage-md', 768, 360, true );
add_image_size( 'stage-sm', 480, 480, true );
```

Configure image-sizes at certain breakpoints by using the `add_responsive_thumbnail`-method:

```php
add_action( 'after_setup_theme', function() {
  add_image_size( 'stage', 1440, 560, true );
  add_image_size( 'stage-md', 768, 360, true );
  add_image_size( 'stage-sm', 480, 480, true );

  add_theme_support('responsive-thumbnails', [
    'stage' => [
      480 => 'stage-sm',
      768 => 'stage-md'
    ]
  ]);
});

```

## Development

Download [Docker CE](https://www.docker.com/get-docker) for your OS.
Download [NodeJS](https://nodejs.org) for your OS.

### Install

#### Install wordpress

```cli
docker-compose run --rm wp wp-install.sh
```

After installation you can log in with user `wordpress` and password `wordpress`.

#### Install front-end dependencies

```cli
npm i
```

### Development Server

Point terminal to your project root and start up the container.

```cli
docker-compose up -d
```

Point your browser to [http://localhost:8030](http://localhost:8030).


#### Watch front-end dependencies

```cli
npm run watch
```

### Docker

##### Update composer dependencies

```cli
docker-compose run composer update
```

##### Globally stop all running docker containers

```cli
docker stop $(docker ps -a -q)
```

##### Update Wordpress

Due to some permission issues, you need to chmod your container's web-root prior to running the updater:

```cli
docker-compose exec wordpress bash
```

From the container shell, change permissons all down the tree.
```cli
chmod -R 777 .
```

After `CTRL+D`, you're ready to update Wordpress, either from the admin-interface or using wp-cli:

```
docker-compose run wp core update
```
