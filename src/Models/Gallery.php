<?php

namespace HashtagCms\Models;

use HashtagCms\Core\Scopes\SiteScope;

class Gallery extends AdminBaseModel
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
     * Get Image Group
     *
     * @return array
     */
    public static function getTypeGroup()
    {

        $defaultMediaType = ['image', 'video', 'audio', 'document', 'files', 'other'];

        $siteGroup = self::all('media_type')->where('media_type', '!=', '')->groupBy('media_type')->toArray();
        $all = [];
        foreach ($siteGroup as $key => $menu) {
            $all[] = $key;
        }

        return array_unique(array_merge($all, $defaultMediaType));
    }

    /**
     * Get Image Group
     *
     * @return array
     */
    public static function getImageGroup()
    {
        $defaultMediaGroup = ['content'];
        $siteGroup = self::all('group_name')->where('group_name', '!=', '')->groupBy('group_name')->toArray();
        $all = [];
        foreach ($siteGroup as $key => $menu) {
            $all[] = $key;
        }

        return array_unique(array_merge($all, $defaultMediaGroup));
    }

    /**
     * with tags
     *
     * @return void
     */
    public function tag()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Save Tags
     *
     * @return void
     */
    public function saveTags(int $sourceId, array $tags)
    {
        $source = self::find($sourceId);
        $source->tag()->detach();
        foreach ($tags as $tag) {
            //Create Tag and relation
            $tag = strtolower(rtrim(ltrim($tag)));
            if ($tag != '') {
                $tagData = Tag::updateOrCreate(['name' => $tag]);
                $source->tag()->attach($tagData);
            }
        }
    }

    /**
     * Get all category attached to image
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function category()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Get all pages attached to image
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function page()
    {
        return $this->belongsToMany(Page::class);
    }

    /**
     * @return mixed
     */
    public static function getMedias($media_type = null, $media_group = null)
    {
        return static::where([['media_type', '=', $media_type], ['group_name', '=', $media_group]])->orderBy('position', 'asc')->get();
    }
}
