# HashtagCMS

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]


Hashtag CMS, Use it as Headless CMS or Bundled, API enabled, 
Admin Panel, multisite/multi-tenant, multiplatform, multilingual with endless possibilities.
The Most powerful, fast, user-friendly and secured platform. Made with PHP Laravel Framework.

## Installation

Via Composer

``` bash
composer create-project laravel/laravel mysite
cd mysite
composer require hashtagcms/hashtagcms
```

## Hashtag CMS installation guidelines
- Open `config/app.php` and add below line in the array. (only needed less than v1.4.3 version)  
``` bash
HashtagCms\HashtagCmsServiceProvider::class
```
- Open `.env` file and update `APP_URL`, and make sure database information is correct.
- Change DB_CONNECTION to `mysql` and update database information. 
``` bash 
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password (leave it blank if no password)
```
- Open `app/Models/User.php` and remove or comment below lines.

``` bash
// use Illuminate\Foundation\Auth\User as Authenticatable
```
- Add below lines in the same file (`app/Models/User.php`)
``` bash
use HashtagCms\User as Authenticatable;
```
- You might want to comment/remove below route in `routes/web.php`

```bash 
/*Route::get('/', function () {
    return view('welcome');
});*/
```

###Install Command
``` bash
php artisan cms:install
```

### Configure site on browser. Open it with the appUrl prefix
```bash 
http://{APP_URL}/install
ie:
http://www.hashtagcms.org/install
```

- You are done :)

## Test
```bash 
php artisan test vendor/hashtagcms/hashtagcms
```

## Recent change logs [more...](changelog.md)

#v1.5.2 Changes
- General: When you edit a new site in the backend, the frontend breaks in the same browser due to a global scope issue. Fixed. 

#v1.5.1 Changes
- Sort module parse id to int. We don’t need to do that. Fixed.
- Improved routing and logging
- Added import export  data command
    - php atrisan cms:exportdata
    - php atrisan cms:importdata
- Unique link_rewrite update issue while updating the blog/content when you have multiple platforms enabled; is fixed now.

#v1.5.0 Changes
- Frontend: Added additional middleware support for frontend. You can add your own middleware in config/hashtagcms.php

#v1.4.9 Changes
- Frontend: Added directive support: app()->HashtagCms->layoutManager()->renderStack('scripts')

#v1.4.8 Changes
- General: Fixed route issue

#v1.4.7 Changes
- General: Moved to webpack from laravelmix

#See all change logs [more...](changelog.md)


### More logs [changelog](changelog.md) for more information on what has changed recently.


## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [Marghoob Suleman][https://www.marghoobsuleman.com]


## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/hashtagcms/hashtagcms.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/hashtagcms/hashtagcms.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/hashtagcms/hashtagcms/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/hashtagcms/hashtagcms
[link-downloads]: https://packagist.org/packages/hashtagcms/hashtagcms
[link-travis]: https://travis-ci.org/hashtagcms/hashtagcms
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/marghoobsuleman
[link-contributors]: ../../contributors
