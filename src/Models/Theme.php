<?php

namespace HashtagCms\Models;

use HashtagCms\Core\Scopes\SiteScope;

class Theme extends AdminBaseModel
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SiteScope);
    }

    /**
     * Find theme of other site id via current site theme id.
     * current site theme id -> 1, find -> alias -> search with alias in target site
     * then return theme id
     */
    public static function getThemeIdThroughSite(int $source_theme_id, int $target_site_id)
    {
        $themeInfo = Theme::withoutGlobalScopes()->where('id', '=', $source_theme_id)->first();

        if ($themeInfo && $themeInfo->alias) {
            $themeWhere = [['alias', '=', $themeInfo->alias], ['site_id', '=', $target_site_id]];
            $theme = Theme::withoutGlobalScopes()->where($themeWhere)->first();

            return $theme->id;
        }

        return null;
    }
}
