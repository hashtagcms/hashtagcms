<?php

namespace HashtagCms\Models;

use Illuminate\Support\Facades\DB;
use HashtagCms\Core\Scopes\SiteScope;
use HashtagCms\Core\Traits\Admin\Common;

class Category extends AdminBaseModel
{
    use Common;

    protected $guarded = [];

    protected $categorySiteTable = 'category_site';

    protected $additionalColumns = ['site_id', 'platform_id', 'theme_id', 'icon', 'icon_css',
        'header_content', 'footer_content', 'exclude_in_listing', 'cache_category', 'position'];

    /**
     * @override
     * boot
     */
    protected static function boot()
    {

        parent::boot();
        static::addGlobalScope(new SiteScope);
    }

    /**
     * With Lang
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lang()
    {
        return $this->hasOne(CategoryLang::class);
    }

    /**
     * With Langs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function langs()
    {
        return $this->hasMany(CategoryLang::class)->withoutGlobalScopes();
    }

    /**
     * Not in use anywhere
     * With theme
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function theme()
    {
        return $this->belongsTo(Theme::class, 'category_id', 'theme_id');
    }

    /**
     * With Platform
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function platform()
    {

        return $this->belongsToMany(Platform::class, 'category_site')->withPivot($this->additionalColumns);
    }

    /**
     * With Site
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    //one category should only with one site
    public function site()
    {

        return $this->belongsToMany(Site::class)->withPivot($this->additionalColumns)->orderBy('position', 'ASC');
        //return $this->belongsToMany(Site::class);

    }

    /**
     * Get parent only
     *
     * @return mixed
     */
    public static function parentOnly()
    {
        return Category::with(['site', 'lang'])->where('parent_id', null)->get();
    }

    /**
     * Get parent only which has dynamic pattern
     *
     * @return mixed
     */
    public static function parentOnlyDynamic()
    {
        return Category::with(['site', 'lang'])->where('link_rewrite_pattern', '!=', '')->get();
    }

    /**
     * @return array
     */
    public static function getTargetType()
    {
        return static::getEnumValues('category_langs', 'target');
    }

    /**
     * @return array
     */
    public static function getLinkRelationType()
    {

        return static::getEnumValues('category_langs', 'link_relation');

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'insert_by');
    }

    /**
     * @override
     * Find By Id
     *
     * @param  int  $id
     * @param  string  $with
     * @param  int  $platform_id
     * @return array
     */
    public static function getById($id = 0, $with = '', $platform_id = 1)
    {

        //$category = Category::find($id, $with);

        $obj = ($with != '') ? static::with($with) : new static;

        $data = $obj->findOrFail($id);

        $where = [['site_id', $data->site_id],
            ['platform_id', $platform_id]];

        $platform_data = $data->platform()->where($where)->get()->toArray();
        $data = $data->toArray();
        $data['platform_wise'] = self::pivotToArray($platform_data);
        $data['platforms'] = Site::with('platform')->where('id', $data['site_id'])->get(['id']);
        $data['platforms'] = ($data['platforms']->count() > 0) ? $data['platforms'][0]->platform : [];
        $data['platform_id'] = $platform_id;

        return $data;
    }

    /**
     * @param  int  $category_id
     * @param  null  $platform_id
     * @param  int  $site_id
     * @param  null  $microsite_id
     * @return mixed
     */
    public static function getModules($category_id = 1, $platform_id = null, $site_id = 1, $microsite_id = null)
    {

        $where = ['category_id' => $category_id, 'site_id' => $site_id];
        if ($platform_id !== null) {
            $where['platform_id'] = $platform_id;
        }
        if ($microsite_id !== null) {
            $where['microsite_id'] = $microsite_id;
        }

        //working here
        return DB::table('module_site')->select('*')->where($where)->orderBy('position', 'ASC')->get();
    }

    /**
     * Get pivot data with relation of site
     *
     * @param  null  $tenent_id
     * @param  null  $key
     * @return array|mixed|null
     */
    public function getFromSitePivot($tenent_id = null, $key = null)
    {

        if ($tenent_id !== null) {
            $data = $this->site()->wherePivot('platform_id', $tenent_id)->get()->toArray();
            $data = (count($data) > 0) ? $data[0] : [];
            if ($key === null) {

                return isset($data['pivot']) ? $data['pivot'] : [];

            }

            //If there is a key
            return (isset($data['pivot']) && isset($data['pivot'][$key])) ? $data['pivot'][$key] : null;
        }

        //get all pivot
        $all = $this->site()->get()->toArray();
        $data = [];
        foreach ($all as $key => $val) {
            $data[] = (isset($val['pivot'])) ? $val['pivot'] : [];
        }

        return $data;

    }

    /**
     * just for testing
     * Get category info by link_rewrite
     *
     * @param  string  $link_rewrite
     * @param  int  $lang_id
     * @param  int  $platform_id
     * @param  int  $site_id
     * @return mixed|null
     */
    public static function getByLinkrewrite($link_rewrite = '', $lang_id = 1, $platform_id = 1, $site_id = 1)
    {

        $where = [
            ['category_site.platform_id', '=', $platform_id],
            ['category_site.site_id', '=', $site_id],
            ['categories.publish_status', '=', 1],
            ['category_langs.lang_id', '=', $lang_id],
            ['categories.link_rewrite', '=', $link_rewrite],
        ];

        $data = DB::table('category_site')
            ->where($where)
            ->join('categories', 'categories.id', '=', 'category_site.category_id')
            ->join('category_langs', 'category_langs.category_id', '=', 'category_site.category_id')
            ->select('category_site.*', 'categories.*', 'category_langs.*')
            ->get();

        if ($data->count() == 0) {
            return null;
        }

        return $data[0];
    }

    /**
     * @param  int  $limit
     * @return mixed
     */
    public static function getReadCounts($limit = 10)
    {
        return self::orderBy('read_count', 'DESC')->take($limit)->get(['read_count', 'link_rewrite']);
    }

    /**
     * @param  int  $limit
     * @return mixed
     */
    public static function getContentReadCounts($limit = 10)
    {
        return Page::orderBy('read_count', 'DESC')->take($limit)->get(['read_count', 'link_rewrite']);
    }

    /**
     * with gallery
     *
     * @return void
     */
    public function gallery()
    {
        return $this->belongsToMany(Gallery::class)->withPivot('position');
    }
}
