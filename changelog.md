# Changelog

#v1.5.2 Changes
- General: When you edit a new site in the backend, the frontend breaks in the same browser due to a global scope issue. Fixed.

# Version 1.5.1
- Backend: Sort module issue. Id can be string too.
- General: Improved routing and logging
- General: Added import export data command. Example added below.

### Exporting Data help
```bash
# Export all tables
php artisan cms:exportdata

# Export specific tables
php artisan cms:exportdata --tables=users,roles,permissions

# Exclude certain tables
php artisan cms:exportdata --exclude=logs,cache

# Limit records per table
php artisan cms:exportdata --limit=500

# Specify output directory
php artisan cms:exportdata --output=database/custom-seeders
```

### Features

- **Memory-Efficient Processing**: Handles large tables in batches
- **Smart Table Organization**: Respects foreign key dependencies
- **Error Handling**: Gracefully skips empty tables and provides detailed error messages
- **Flexible Options**: Selective table export, exclusion, and record limiting

### Importing Data
After exporting, you can import the data using the standard Laravel seeder command:
```bash
php artisan db:seed
```
Or use our enhanced import command that automatically updates site domains to match your environment:

```bash
# Import with domain updating
php artisan cms:importdata --update-domain

# Import a specific seeder class
php artisan cms:importdata --class=CustomSeeder --update-domain

# Force import in production
php artisan cms:importdata --force --update-domain
```

The `--update-domain` flag ensures that the domain field in the sites table is updated to match your APP_URL environment variable, which is essential when moving between environments.

- Sort module parse id to int. We don’t need to do that. Fixed.
- Improved routing and logging
- Added import export  data command
  - php atrisan cms:exportdata
  - php atrisan cms:importdata
- Unique link_rewrite update issue while updating the blog/content when you have multiple platforms enabled; is fixed now.


#v1.5.0 Changes
- Frontend: Added additional middleware support for frontend. You can add your own middleware in config/hashtagcms.php

#v1.5.0 Changes
- Frontend: Added additional middleware support for frontend. You can add your own middleware in config/hashtagcms.php

#v1.4.9 Changes
- Frontend: Added directive support: app()->HashtagCms->layoutManager()->renderStack('scripts')

#v1.4.8 Changes
- General: Fixed route issue

#v1.4.7 Changes
- General: Moved to webpack from laravelmix 

#v1.4.6 Changes
- General: Laravel v12 support

#v1.4.5 Changes
- API: Return expiry and roles, sites in user info

#v1.4.4 Changes
- Backend: Module creator has a typo bug. x was added mistakenly. Fixed.
- Backend: Js module -> Site-wise component has been updated. It is now generic for similar purpose task. 
- General: API bug fixes.

#v1.4.3 Changes
- Laravel 11 support and cleanup

#v1.4.2 Changes
- Backend/Frontend/API: Added festival support. You can add lottie and schedule it.
- API: Added ServiceLater Support. Service url will be passed to view too.
- Core: Added module data modifier
- See how it works:
- Create class/method `App\Parser\ModuleDataModifier` -> `moduleAliasNameMethod($data, $moduleInfo)`
  and manipulate your data and return it. Works for API and views.
- Do not forget to run `php artisan migrate` if you are upgrading.

#1.4.1
- Backend: Export the correct folder (neo) while installing the package
- General: Code cleanup
- General: Added webpack.mix.js while exporting assets

#1.4.0
- Backend: Added command to export register and login controller
- General: Fixed autoload provider. 

#v1.3.9 Changes
- Backend: assets files are in lowercase
- Backend: Showing roles in user listing
- Backend: Fixed some bugs and cleanup

#v1.3.8 Changes
#### Major Update: Please note: This updates needs fresh installation.
- General: Compatible with laravel/framework": "^10.0"
- Backend: Moved from Vue 2 to Vue 3
- Backend: Added gallery module. You can now upload files/images.
- Backend: Added gallery module support with content editor
- Backend: Moved to bootstrap 5.0
- Backend: Improved Smart Copy Paste
- Backend: `direction` column in `hooks` table has default Null value now
- Backend: linked module and live edit bug while adding or editing frontend module
- Backend: Sitewise permission added
- Backend: Dashboard sub-modules data fetching issue on sitewise
- API: Refactored API. Used Resource to make key camelCase. 
- API: modules props are returns as key/value pair
- General: Move Passport to Sanctum for authentication
- Frontend: Frontend can partially work with API. Usefull for microservices architecture.
- Frontend: Moved to bootstrap 5.0

#v1.3.7 Changes
- Backend: Fixed issue while adding category
- Frontend: Minor bug fixes while loading after running a test

#v1.3.6 Changes
- Backend/Frontend: Corrected folder structure for js libraries for future re-usability.
- Backend: Handle multi database in query module.
- Backend: Handle 'resultType=html' in service type. In case you need html from a service.
- Backend: Fixed saving module in PageManager if there is only one platform.
- Backend: Fixed minor issue on image/icon upload in site and theme 

#v1.3.5 Changes
- Major changes (Need fresh installation)
- 'Tenants' will be called as 'Platforms' from now on. Since this CMS is built for corporates;
  multiple feedback received for 'tenant'. In corporate world site is a tenant and tenant is a platform.
- all tenant_id will be referred as platform_id.
- some tables have been removed and added few to make sense. index and primary keys are also fixed/added.
- shipped with two languages by default
- fixed site's lang count issue
- Added test cases
```bash
php artisan config:cache 
php artisan test vendor/marghoobsuleman/hashtagcms
```

#v1.3.4 Changes
- API: Send api header with modules

#v1.3.3 Changes
- How to update to v1.3.2 to v1.3.3:
```bash 
  > composer upgrade 
  > php artisan migrate
  > php artisan make:seed CmsModuleTableSeeder
    open CmsModuleTableSeeder and paste below content in run()
     
        $table_name = 'cms_modules';
        $date = date('Y-m-d H:i:s');
        $cms_modules = array(
            array('name' => 'Module Properties','controller_name' => 'moduleproperty','display_name' => NULL,'parent_id' => '13','sub_title' => 'Manage Module Propeties','icon_css' => 'fa fa-cog','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '30','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );

        $res = DB::selectOne("select * from $table_name where controller_name='moduleproperty'");
        if (!$res) {
            DB::table($table_name)->insert($cms_modules);
        }
    
  > php artisan db:seed CmsModuleTableSeeder
```
- Frontend: Separate assets path support for different domain. you can configure that in config/hashtagcms.php
- Backend: Added Module Props and Module Props langs with view and controller
- Backend: Added support for module props copy in site config and in site clone too.
- Backend: Fixed in create frontend module.
- Backend: added 'headers' column in module table. Will use this field to send header with each module. (implement in next version)
- API: Module props added in api results
- API: Site props key value is similar to module props

## Version 1.3.2
- Backend: Site copy support
- Backend: Logo height can be set via data-props
- Frontend: Analytics controller bug fixed on multisite. 
- Frontend: Asset path fetching based on current site. 
- Frontend: Fixed 404 issue while working with multsite. 

## Version 1.3.1
- Frontend: Fixed few multisite issue.

## Version 1.3.0
- Optimization
- Frontend: Major Code Refactoring
- Frontend: Api Code refactor
- Frontend: ModuleParser Hook. If you are adding a new module type. You need to add parser for that. Create a class Parser\ModuleParser in app folder and need to create a method to parse module. Say module type is "MenuService"; you need to create a method called "getMenuServiceModule(mixed $module):?array"
- Backend: Frontend Module type can be pass through config
- Backend: Menu Sorter changes, change id and name field


## Version 1.2.9
- Backend: Fixed content auto update url issue

## Version 1.2.8
- Backend: ModifyHooks Migration table issue fixed

## Version 1.2.7
- Backend: ModifyCategory Migration table issue fixed

## Version 1.2.6
- Frontend: moduleInfo is now passed with each view template.
- Frontend: hooks table has now 'direction' column. It can be useful for mobile layout handling.  

## Version 1.2.5
- Blog read_count is now displayed
- You can define a controller name for a category
- Code refactoring. InfoKeeper is now accessible from app()->HashtagCms->getInfoKeeper();


## Version 1.2.4
- User Model for controller fill-able is now `protected $guarded = array()`;
- Backend: Code refactoring in editor.js


## Version 1.2.3
- Added Rich text editor (tinyMCE)

## Version 1.2.2
- Backend: Added graph for Top categories and Top content in dashboard
- Change TEXT to LONGTEXT of page_content table 
  - (ALTER TABLE `page_langs` MODIFY `page_content` LONGTEXT)
- Change TEXT to LONGTEXT of category_langs table 
  - (ALTER TABLE `category_langs` MODIFY `content` LONGTEXT)
- Showing read count in category and page listing module.  


## Version 1.2.1
- Backend: Add "shared" field in module add/edit
- Blog::getLatestBlog is now generic. Blog can read multiple categories now. look into config/hashtagcms.php 
- page addedit.blade limit fix

## Version 1.2.0
- Added content_source field in page table

## Version 1.1.9
- Added image upload feature in Content/Blog module
- Code refactor in Content/Blog modules
- Added message if unable to connect mysql

## Version 1.1.8
- Fixed Site settings issue

## Version 1.1.7
- Remove VueJs dependency from frontend 
- Update security bugs

## Version 1.1.6
- Fixed Analytic log error
- Font load error fixed 

## Version 1.1.5
- Added Analytical support - read count for each story

## Version 1.0

### Added
- Headless CMS, Use it as Headless CMS or Bundled, API enabled, 
  Admin Panel, Multisite, Multitenant, Multilingual, and oAuth 2.0 enabled.
  The Most powerful, fast, user-friendly and secured platform.
