<?php

namespace HashtagCms\Core\Traits;

use Illuminate\Support\Facades\DB;
use HashtagCms\Models\Site;

trait SiteManager
{
    public function sites()
    {

        return $this->belongsToMany(Site::class);

    }

    public function hasSite($site)
    {

        if (is_string($site)) {

            return $this->sites->contains('context', $site);

        }

        return (bool) $site->intersect($this->sites)->count();

    }

    /**
     * Save Role
     *
     * @param  $role
     */
    public function assignSite($site)
    {

        $this->sites()->save($site);

    }

    /**
     * Save multiple roles
     *
     * @param  $roles
     */
    public function assignMultipleSite($sites)
    {

        foreach ($sites as $site) {
            $this->sites()->save($site);
        }

    }

    /**
     * Delete all roles
     *
     * @return mixed
     */
    public function detachAllSites()
    {
        return DB::table('site_user')->where('user_id', $this->id)->delete();
    }

    public function supportedSites()
    {
        return DB::table('site_user')->where('user_id', $this->id)->get();
    }
}
