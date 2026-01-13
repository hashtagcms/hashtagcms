<?php
namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CmsModulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'cms_modules';
        $date = date('Y-m-d H:i:s');
        $cms_modules = array(
            array('id' => '1','name' => 'Sites','controller_name' => 'site','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manage sites','icon_css' => 'fa fa-globe','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '9','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '2','name' => 'Localization','controller_name' => 'localization','display_name' => NULL,'parent_id' => '0','sub_title' => 'Localization','icon_css' => 'fa fa-star-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '2','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '3','name' => 'Countries','controller_name' => 'country','display_name' => NULL,'parent_id' => '2','sub_title' => 'Manage countries','icon_css' => 'fa fa-map-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '4','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '4','name' => 'Cities','controller_name' => 'city','display_name' => NULL,'parent_id' => '2','sub_title' => 'Manage cities','icon_css' => 'fa fa-building-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '5','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '5','name' => 'Settings','controller_name' => 'setting','display_name' => NULL,'parent_id' => '0','sub_title' => 'Settings','icon_css' => 'fa fa-gear','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '21','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '6','name' => 'CMS Modules','controller_name' => 'cmsmodule','display_name' => NULL,'parent_id' => '5','sub_title' => 'Manage CMS modules','icon_css' => 'fa fa-cogs','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '22','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '7','name' => 'Dashboard','controller_name' => 'dashboard','display_name' => NULL,'parent_id' => '0','sub_title' => 'Dashboard','icon_css' => 'fa fa-dashboard','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '1','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '8','name' => 'Zones','controller_name' => 'zone','display_name' => NULL,'parent_id' => '2','sub_title' => 'Manage Zones','icon_css' => 'fa fa-map-marker','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '3','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '9','name' => 'Currencies','controller_name' => 'currency','display_name' => NULL,'parent_id' => '2','sub_title' => 'Manage currencies','icon_css' => 'fa fa-dollar','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '7','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '10','name' => 'Authors','controller_name' => 'author','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manage authors','icon_css' => 'fa fa-user','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '8','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            //1 space for future use
            /*array('id' => '11','name' => 'Permission','controller_name' => 'permission','display_name' => NULL,'parent_id' => '5','sub_title' => 'Manage users permission','icon_css' => 'fa fa-lock','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '11','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),*/
            array('id' => '12','name' => 'Roles','controller_name' => 'role','display_name' => NULL,'parent_id' => '5','sub_title' => 'Manage roles','icon_css' => 'fa fa-user-secret','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '23','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '13','name' => 'Layout','controller_name' => 'layout','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manage layout, themes etc','icon_css' => 'fa fa-paperclip','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '12','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '14','name' => 'Contents','controller_name' => 'page','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manager pages','icon_css' => 'fa fa-newspaper-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '18','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),

            //added with v1.3.8
            array('id' => '15','name' => 'CMS Logs','controller_name' => 'cmslog','display_name' => NULL,'parent_id' => '5','sub_title' => 'View CMS logs','icon_css' => 'fa fa-list-ul','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '31','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),

            array('id' => '16','name' => 'Themes','controller_name' => 'theme','display_name' => NULL,'parent_id' => '13','sub_title' => 'Mange themes','icon_css' => 'fa fa-object-group','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '13','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '17','name' => 'Frontend Modules','controller_name' => 'module','display_name' => NULL,'parent_id' => '13','sub_title' => 'Manage frontend modules','icon_css' => 'fa fa-cogs','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '14','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '18','name' => 'Categories','controller_name' => 'category','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manage categories','icon_css' => 'fa fa-folder-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '17','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '19','name' => 'Platforms','controller_name' => 'platform','display_name' => NULL,'parent_id' => '1','sub_title' => 'Manage platform','icon_css' => 'fa fa-superpowers','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '10','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),

            //Changed oAuth to gallery (v1.3.8)
            array('id' => '20','name' => 'Festivals','controller_name' => 'festival','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manage Festivals','icon_css' => 'fa fa-snowflake-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '24','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),

            array('id' => '21','name' => 'Hooks','controller_name' => 'hook','display_name' => NULL,'parent_id' => '13','sub_title' => 'Manage hooks','icon_css' => 'fa fa-anchor','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '15','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '22','name' => 'Roles Rights','controller_name' => 'rolesright','display_name' => NULL,'parent_id' => '5','sub_title' => 'Manage roles rights','icon_css' => 'fa fa-key','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '24','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '23','name' => 'Static Modules','controller_name' => 'staticmodule','display_name' => NULL,'parent_id' => '13','sub_title' => 'Manage static module','icon_css' => 'fa fa-puzzle-piece','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '16','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),

            //9 more module space for future use
            array('id' => '32','name' => 'Pages Manager','controller_name' => 'homepage','display_name' => 'Homepage Manager','parent_id' => '0','sub_title' => 'Page manager','icon_css' => 'fa fa-file-code-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '20','created_at' => NULL,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '33','name' => 'Language','controller_name' => 'language','display_name' => NULL,'parent_id' => '2','sub_title' => 'Manage languages','icon_css' => 'fa fa-language','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '6','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),

            //5 more module space for future use
            array('id' => '38','name' => 'Site Properties','controller_name' => 'siteprop','display_name' => NULL,'parent_id' => '1','sub_title' => 'Manage site properties','icon_css' => 'fa fa-cogs','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '11','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),

            //Changed oAuth to gallery (v1.3.8)
            array('id' => '39','name' => 'Galleries','controller_name' => 'gallery','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manage gallery and files','icon_css' => 'fa fa-picture-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '25','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '40','name' => 'Contacts','controller_name' => 'contact','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manage contacts','icon_css' => 'fa fa-telegram','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '26','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '41','name' => 'Subscribers','controller_name' => 'subscriber','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manage subscribers','icon_css' => 'fa fa-handshake-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '27','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '42','name' => 'Blogs','controller_name' => 'blog','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manage blogs','icon_css' => 'fa fa-quote-right','list_view_name' => NULL,'edit_view_name' => 'page/addedit','position' => '19','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),
            array('id' => '43','name' => 'Comments','controller_name' => 'comment','display_name' => NULL,'parent_id' => '0','sub_title' => 'Manage comments','icon_css' => 'fa fa-comments-o','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '29','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL),

            //more module space for future use
            array('id' => '5000','name' => 'Module Properties','controller_name' => 'moduleproperty','display_name' => NULL,'parent_id' => '13','sub_title' => 'Manage module properties','icon_css' => 'fa fa-cog','list_view_name' => NULL,'edit_view_name' => NULL,'position' => '30','created_at' => $date,'updated_at' => $date,'deleted_at' => NULL)
        );

        if(DB::table($table_name)->get()->count() === 0) {
            DB::table($table_name)->insert($cms_modules);
        } else {
            echo "SeedingError: `$table_name` table is not empty\n";
        }

    }
}
