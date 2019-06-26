# wp-responsive-thumbnail

Responsive image sizes for Wordpress

This plugin enables theme developers configure media breakpoints for different images sizes.
It works by wrapping the image inside a html5 `picture`-tag and providing the appropriate source elements.

## Usage

Add custom image sizes

```php
add_image_size( 'stage', 1440, 560, true );
add_image_size( 'stage_md', 768, 360, true );
add_image_size( 'stage_sm', 480, 480, true );
```

Configure image-sizes at certain breakpoints by using the `add_responsive_thumbnail`-method:

```php
if (function_exists('add_responsive_thumbnail')) {
  add_responsive_thumbnail('stage', array(
    480 => 'stage_sm',
    768 => 'stage_md'
  ));
}
```

Call `the_post_thumbnail` as usual:

```
the_post_thumbnail('large_wide');
```

The generated output looks like this:

```html
<picture>
  <source media="(max-width: 480px)" srcset="http://127.0.0.1:9090/wp-content/uploads/2016/04/image-480x480.jpg 480w"/>
  <source media="(max-width: 768px)" srcset="http://127.0.0.1:9090/wp-content/uploads/2016/04/image-768x360.jpg 768w"/>
  <img class="attachment-stage size-stage wp-post-image" alt="image" src="http://127.0.0.1:9090/wp-content/uploads/2016/04/image-1440x560.jpg">
</picture>
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
