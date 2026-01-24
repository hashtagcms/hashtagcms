<?php

namespace HashtagCms\Database\Seeds;

use Illuminate\Database\Seeder;

class HashtagCmsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Site
        $this->call(SitesTableSeeder::class);

        //Language
        $this->call(LangsTableSeeder::class);

        //Countries
        $this->call(CountriesTableSeeder::class);
        $this->call(CountryLangsTableSeeder::class);

        //Cities
        $this->call(CitiesTableSeeder::class);

        //Zone
        $this->call(ZonesTableSeeder::class);

        //Currencies
        $this->call(CurrenciesTableSeeder::class);

        //Cms Modules
        $this->call(CmsModulesTableSeeder::class);

        //User and Rights
        $this->call(UsersTableSeeder::class);
        //roles
        $this->call(RolesTableSeeder::class);
        //permission
        $this->call(PermissionsTableSeeder::class);
        //permission role
        $this->call(PermissionRoleTableSeeder::class);

        //Platforms
        $this->call(PlatformsTableSeeder::class);

        //Hooks
        $this->call(HooksTableSeeder::class);

        //Theme
        $this->call(ThemesTableSeeder::class);

        //category
        $this->call(CategoriesTableSeeder::class);

        //Modules
        $this->call(ModulesTableSeeder::class);

        //Site wise
        //country
        $this->call(CountrySiteTableSeeder::class); //done

        //currency
        $this->call(CurrencySiteTableSeeder::class); //done - one country

        //zone
        $this->call(ZoneSiteTableSeeder::class); //done

        //langs
        $this->call(LangSiteTableSeeder::class); //done

        //platforms
        $this->call(PlatformSiteTableSeeder::class); //done

        //hooks
        $this->call(HookSiteTableSeeder::class); //done

        //Cms permissions
        $this->call(CmsPermissionsTableSeeder::class);

        //Festival
        $this->call(FestivalsTableSeeder::class);

        //Logs
        //$this->call(LogsTableSeeder::class);

        //Microsites
        $this->call(MicrositesTableSeeder::class);

        //Page
        $this->call(PagesTableSeeder::class);

        //StaticModuleContent
        $this->call(StaticModuleContentsTableSeeder::class);

        //Installation check
        $this->call(SitePropsTableSeeder::class);

    }
}
