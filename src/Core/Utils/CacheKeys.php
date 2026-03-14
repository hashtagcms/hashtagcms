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
    public const MODULE_PATH_CACHE = 'module_path_cache';

    // Admin Session / Cache Keys
    public const CMS_API_TOKEN = 'hashtagcms_api_token';
    public const CMS_API_USER = 'hashtagcms_api_user';
    public const CMS_LANG_ID = 'lang_id';
    public const CMS_SITE_ID = 'site_id';
    public const CMS_MESSAGE = '__hashtagcms_message__';
    public const CMS_MESSAGE_ERROR = '__hashtagcms_message_error__';
    public const CMS_PERMISSIONS_MENU_ALLOWED = 'cms_permissions_menu_allowed_';
    public const CMS_PERMISSIONS_CACHE_KEY_PREFIX = 'cms_permissions_';
    public const CMS_LAYOUT = 'layout';
    public const CMS_ADMIN_MODULES_MASTER_LIST = 'cms_admin_modules_master_list';

    public const CMS_RECORDS_PER_PAGE = 'cms_records_per_page';
}
