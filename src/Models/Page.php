<?php

namespace HashtagCms\Models;

use Illuminate\Support\Facades\DB;
use HashtagCms\Core\Scopes\SiteScope;

class Page extends AdminBaseModel
{
    protected $guarded = [];

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
     * Get with lang
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lang()
    {
        return $this->hasOne(PageLang::class, 'page_id');
    }

    /**
     * With Langs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function langs()
    {
        return $this->hasMany(PageLang::class)->withoutGlobalScopes();
    }

    /**
     * Get content type
     *
     * @return array
     */
    public static function getContentTypes()
    {
        return static::getEnumValues('pages', 'content_type');
    }

    /**
     * Get menu placement
     *
     * @return array
     */
    public static function getMenuPlacements()
    {
        return ['top', 'top-right', 'left', 'right', 'bottom', 'bottom-right', 'other'];
    }

    /**
     * Get latest blog
     *
     * @param  null  $link_rewrite
     * @return mixed
     */
    public static function getLatestBlog($siteId, $langId, $link_rewrite = null, int $limit = 10)
    {

        $where = [
            ['pages.site_id', '=', $siteId],
            ['page_langs.lang_id', '=', $langId],
            ['pages.publish_status', '=', 1],
            ['pages.deleted_at', '=', null],
        ];

        if ($link_rewrite != null) {
            if (is_string($link_rewrite) && str_contains($link_rewrite, ',')) {
                $link_rewrite = array_map('trim', explode(',', $link_rewrite));
            }

            if (!is_array($link_rewrite)) {
                $where[] = ['categories.link_rewrite', '=', "$link_rewrite"];
            }
        }

        $query = DB::table('pages')
            ->join('page_langs', 'pages.id', '=', 'page_langs.page_id')
            ->join('categories', 'categories.id', '=', 'pages.category_id')
            ->join('users', 'users.id', '=', 'pages.insert_by')
            ->select('categories.link_rewrite as category_link_rewrite', 'users.name as user_name', 'pages.read_count',
                'pages.id', 'pages.site_id', 'pages.microsite_id', 'pages.platform_id', 'pages.category_id', 'pages.alias',
                'pages.exclude_in_listing', 'pages.content_type', 'pages.position', 'pages.link_rewrite', 'pages.menu_placement',
                'pages.enable_comments', 'pages.attachment', 'pages.img', 'pages.author', 'pages.created_at', 'pages.updated_at',
                'page_langs.name', 'page_langs.title', 'page_langs.description', 'page_langs.page_content',
                'page_langs.link_relation', 'page_langs.target', 'page_langs.active_key');

        if (is_array($link_rewrite)) {
            $query = $query->whereIn('categories.link_rewrite', $link_rewrite);
        }

        // Add scheduling Logic
        $now = now();
        $query->where(function ($q) use ($now) {
            $q->whereNull('pages.publish_at')
              ->orWhere('pages.publish_at', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('pages.expire_at')
              ->orWhere('pages.expire_at', '>=', $now);
        });

        $results = $query->where($where)->orderBy('pages.created_at', 'DESC')->paginate($limit);

        if (count($results) > 0) {
            foreach ($results as $result) {
                //dd($result);
                $whereComment = [
                    ['category_id', '=', $result->category_id],
                    ['page_id', '=', $result->id],
                ];
                $result->comments_count = Comment::where($whereComment)->count();
            }
        }

        return $results;
    }

    /**
     * Get with category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
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
