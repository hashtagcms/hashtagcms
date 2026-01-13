<?php

namespace HashtagCms\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use HashtagCms\Core\Traits\Admin\Common;
use HashtagCms\Core\Traits\RoleManager;
use HashtagCms\Core\Traits\SiteManager;

class User extends AdminBaseModel
{
    use Common, Notifiable, RoleManager, SiteManager;

    protected $guarded = [];

    protected $hidden = [
        'password', 'remember_token',
    ];

    //@override

    /**
     * @param  int  $id
     * @param  string  $with
     * @return array
     */
    public static function getById($id = 0, $with = '')
    {

        $data = parent::getById($id, ['profile', 'roles', 'sites']);
        $data['roles'] = self::pivotToArray($data['roles'], 'role_id');
        $data['sites'] = self::pivotToArray($data['sites'], 'site_id');

        return $data;
    }

    /**
     * Make Password
     *
     * @return string
     */
    public static function makePassword($str)
    {
        return Hash::make($str);
    }

    /**
     * Get CMS Modules
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cmsmodules()
    {
        return $this->hasMany(CmsPermission::class);
    }

    /**
     * with profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }
}
