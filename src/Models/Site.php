<?php

namespace HashtagCms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use HashtagCms\Core\Scopes\SiteScope;

class Site extends AdminBaseModel
{
    protected $guarded = [];

    protected $additionalColumns = ['site_id', 'microsite_id', 'platform_id', 'category_id',
        'hook_id', 'module_id', 'position', 'publish_status'];

    protected $currencyColumns = ['conversion_rate', 'markup', 'markup_type'];

    /**
     * With lang
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lang()
    {

        return $this->hasOne(SiteLang::class);

    }

    /**
     * With Langs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function langs()
    {
        return $this->hasMany(SiteLang::class)->withoutGlobalScopes();
    }

    /**
     * With Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function category()
    {

        return $this->hasManyThrough(CategoryLang::class, Category::class);

    }

    /**
     * With Category lang
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function categoryLang()
    {

        return $this->hasMany(Category::class)->with('lang');

    }

    /**
     * Theme
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function theme()
    {

        return $this->hasMany(Theme::class)->withoutGlobalScope(SiteScope::class);

    }

    /**
     * Supported Platform
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function platform()
    {

        return $this->belongsToMany(Platform::class); //, "platform_site", "platform_id"

    }

    /**
     * Supported Microsites
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function microsite()
    {
        return $this->hasMany(Microsite::class);
    }

    /**
     * Supported Language
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function language()
    {

        return $this->belongsToMany(Lang::class);

    }

    /**
     * Supported Country
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function country()
    {
        return $this->belongsToMany(Country::class)->with('lang');
    }

    /**
     * Supported Zone
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function zone()
    {
        return $this->belongsToMany(Zone::class);
    }

    /**
     * Suported hooks
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function hook()
    {
        return $this->belongsToMany(Hook::class);
    }

    /**
     * With module
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function module()
    {
        return $this->belongsToMany(Module::class)->withoutGlobalScopes()->withPivot($this->additionalColumns);
    }

    /**
     * For homepage
     *
     * @return Site[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function homepage()
    {
        return $this->with(['platform', 'category', 'hook'])->withoutGlobalScopes()->get(); //'module',

    }

    public function currency()
    {
        return $this->belongsToMany(Currency::class)->withPivot($this->currencyColumns);
    }

    /**
     * Get Site Info with minimum fields
     *
     * @param  int  $site_id
     * @return \Illuminate\Database\Eloquent\Collection|Model|null|static|static[]
     */
    public static function allInfo($site_id = 1)
    {
        $site = new self;

        return $site->with([
            'category:category_id,site_id,lang_id,name,title',
            'staticmodule:id,alias',
            'theme:id,site_id,name,directory',
            'platform:id,name',
            'language:id,name,iso_code',
            'microsite:id,site_id,name',
            'country:id',
            'zone',
            'hook',
            'prop',
        ])->withoutGlobalScopes()->find($site_id);
    }

    /**
     * Get Site Info with all fields
     *
     * @param  int  $site_id
     * @return \Illuminate\Database\Eloquent\Collection|Model|null|static|static[]
     */
    public static function allInfoFull($site_id = null)
    {
        $site = new self;

        if ($site_id == null) {
            return $site->with(['category', 'theme', 'platform', 'language', 'microsite', 'country'])->get();
        } else {
            return $site->with(['category', 'theme', 'platform', 'language', 'microsite', 'country'])->find($site_id);
        }

    }

    /**
     * Attach country, platform etc in site
     *
     * @param  $toAttachKey  - country, etc
     * @param  $toAttachIds  - array
     * @return mixed
     */
    public function attachThings($toAttachKey, $toAttachIds)
    {

        $key = Str::singular(strtolower($toAttachKey));

        $finder = Str::title($key);
        $finder = ($finder === 'Language') ? 'Lang' : $finder; //hack for language
        $needToUpdateLangCount = ($finder === 'Lang');
        $namespace = config('hashtagcms.namespace');
        $finder = resolve($namespace.'Models\\'.$finder);

        $data = $finder::find($toAttachIds); //Country::find([1,2]);

        //info(json_encode($data));

        $old = $this->$key()->get(); //$this->country()->get();

        if ($old->count() > 0) {
            $data = $data->merge($old); //fetch old data
        }

        //remove old
        $this->$key()->detach();

        $updated = $this->$key()->attach($data); //it doesn't return anything :)

        //info("needToUpdateLangCount: $needToUpdateLangCount, ".Str::title($key));

        if ($needToUpdateLangCount) {
            self::updateLangCount();
        }

        //attach now
        return $updated;
    }

    /**
     * @param  array  $toDetachArray
     * @return mixed
     */
    public function detachThings($toDetachKey, $toDetachArray = [])
    {

        $key = Str::singular(strtolower($toDetachKey));

        if (count($toDetachArray) > 0) {
            $old = $this->$key()->whereIn('id', $toDetachArray)->get(); //$this->country()->whereIn("id", [1,2])->get();

            $data = $this->$key()->detach($old);

            if ($key === 'language' || $toDetachKey === 'lang') {
                self::updateLangCount();
            }

            return $data;
        }

        $data = $this->$key()->detach();

        if ($key === 'language' || $toDetachKey === 'lang') {
            self::updateLangCount();
        }

        return $data;

    }

    /**
     * Get Supported platforms
     *
     * @param  null  $site_id
     * @return mixed
     */
    public static function getSupportedPlatforms($site_id = null)
    {
        $site_id = ($site_id === null) ? htcms_get_siteId_for_admin() : $site_id;
        $site = Site::with('platform')->where('id', $site_id)->first();

        return $site->platform;

    }

    /**
     * @return void
     */
    public static function getSupportedLangs($site_id = null)
    {
        $site_id = ($site_id === null) ? htcms_get_siteId_for_admin() : $site_id;
        $site = Site::with('language')->where('id', $site_id)->first();

        return $site->language;
    }

    public function staticmodule()
    {
        return $this->hasManyThrough(StaticModuleContentLang::class, StaticModuleContent::class);
        //return $this->hasMany(StaticModuleContent::class, "site_id","id");
    }

    /**
     * Suported props
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function prop()
    {
        return $this->hasMany(SiteProp::class);
    }

    /**
     * Suported props
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function siteproperty()
    {
        return $this->prop();
    }

    /**
     * Suported props where
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function propWhere($where = [])
    {
        return $this->hasMany(SiteProp::class)->where($where['column'], $where['operator'], $where['value']);
    }

    /**
     * Get module props
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function moduleprop()
    {
        return $this->hasMany(ModuleProp::class);
    }

    /**
     * @return void
     */
    public static function updateLangCount($site_id = null)
    {
        $id = $site_id === null ? htcms_get_siteId_for_admin() : $site_id;
        $count = self::getSupportedLangs($id)->count();
        self::find($id)->update(['lang_count' => $count]);
    }

    /**
     * Get defaults
     *
     * @param  string|null  $key
     * @return mixed
     */
    public function getDefaults(array|string|null $key = null)
    {
        $site_id = htcms_get_siteId_for_admin();
        $keys = ['category_id', 'theme_id', 'platform_id', 'lang_id', 'country_id', 'currency_id'];
        if (! empty($key)) {
            if (is_array($key)) {
                $keys = $key;
            } else {
                $key = str_replace(' ', '', $key);
                $keys = explode(',', $key);
            }

        }

        return $this->find($site_id, $keys);
    }

    /**
     * Get supported site for user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getSupportedSitesForUser(int $userId)
    {

        $user = User::find($userId);
        $isAdmin = $user->isSuperAdmin();
        $allSites = self::all();
        if ($isAdmin == 1) {
            return $allSites;
        }

        $supportedSites = $user->supportedSites();
        $supportedSites = collect($supportedSites)->pluck('site_id')->toArray();

        $allSites = self::find($supportedSites);

        return $allSites;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function festival()
    {
        return $this->hasMany(Festival::class)
            ->where([
                ['start_date', '<=', date('Y-m-d')],
                ['end_date', '>=', date('Y-m-d')],
                ['publish_status', '=', 1],
            ]
            )->orderBy('position', 'asc');
    }
}
