<?php

namespace HashtagCms\Core\Utils;

class CacheKeys
{
    // API Keys
    public const SITE_CONFIGS = 'site_configs'; //plural
    public const LOAD_DATA = 'load_data';
    public const BLOG_LATESTS = 'blog_latests';

    // Internal Keys
    public const SITE_CONFIG = 'site_config'; // Note: singular for internal site config resolver
    public const EXTERNAL_CONFIG = 'external_config';
    public const EXTERNAL_DATA = 'external_data';
    public const EXTERNAL_BLOG = 'external_blog';
    public const CLONE_JOB = 'clone_job';
    public const CMS_PERMISSIONS_BOOT = 'cms_permissions_boot';
}
