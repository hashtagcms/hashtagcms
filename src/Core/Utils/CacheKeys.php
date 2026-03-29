<?php

namespace HashtagCms\Core\Utils;

/**
 * Cache key constants for HashtagCMS.
 *
 * Key naming convention
 * ---------------------
 * All Redis/cache keys are composed as:
 *
 *   {prefix}{constant}_{discriminators}
 *
 * {prefix}       — returned by RedisCacheManager::get*Prefix().
 *                  Includes the project namespace automatically.
 * {constant}     — one of the constants in this class (entity name).
 * {discriminators} — runtime values joined with "_" (context, lang, id, hash…).
 *
 * Namespace
 * ---------
 * HASHTAGCMS_CACHE_NAMESPACE env var (defaults to APP_NAME) is slugified and
 * prepended to every prefix, isolating keys per project on a shared Redis server.
 *
 * Examples (APP_NAME="My Project" → namespace "my_project"):
 *   my_project:cms:permissions_boot
 *   my_project:cms:admin_modules_master_list
 *   my_project:cms:clone_job_{jobId}
 *   my_project:db:site_config_{context}
 *   my_project:ex:site_config_{context}
 *   my_project:ex:external_config_{context}_{lang}_{platform}
 *   my_project:ex:external_data_{context}_{lang}_{platform}_{cat}_{micro}_{hash}
 *   my_project:ex:external_blog_{context}_{categoryKey}_{limit}
 *   my_project:cms:module_path_cache_{md5_path}
 *   my_project:cms:permissions_menu:allowed_{userId}
 *
 * Legacy format (no namespace / empty APP_NAME):
 *   cms_permissions_boot
 *   db_site_config_{context}
 *   ex_external_data_{context}_{lang}_{platform}_{cat}_{micro}_{hash}
 *
 * Source-type prefix reference
 * ----------------------------
 *   cms  (getInternalPrefix)      — internal, admin, permissions, boot
 *   db   (getDatabasePrefix)      — data loaded from local DB
 *   ex   (getExternalSourcePrefix)— data fetched from external CMS API
 *   api  (getApiPrefix)           — raw external API response cache
 *
 * Redis debugging
 * ---------------
 * Use RedisCacheManager::getAllKeys() — it handles Laravel's store prefix for you.
 *
 * Alternatively via redis-cli (you must include Laravel's store prefix manually,
 * e.g. "my_project_" from config/cache.php):
 *   redis-cli KEYS "*my_project:*"           — all keys for this project
 *   redis-cli KEYS "*my_project:ex:*"        — all external-source cache
 *   redis-cli KEYS "*my_project:db:site_config*" — all DB site-config rows
 *
 * Rules for constants
 * -------------------
 * - Constants used with get*Prefix() must NOT embed a source-type prefix
 *   in their value (e.g. value must be "permissions_boot", NOT "cms_permissions_boot").
 * - Session-only keys (PHP session, not Redis) are exempt — they are never
 *   passed to a cache store, so the prefix rules do not apply to them.
 */
class CacheKeys
{
    // API Keys
    public const SITE_CONFIGS = 'site_configs'; //plural
    public const LOAD_DATA = 'load_data';
    public const BLOG_LATESTS = 'blog_latests';

    // Internal Keys — always use with RedisCacheManager::getInternalPrefix() / getDatabasePrefix() / getExternalSourcePrefix()
    public const SITE_CONFIG = 'site_config'; // singular — used by SiteConfigResolver & Site model
    public const EXTERNAL_CONFIG = 'external_config';
    public const EXTERNAL_DATA = 'external_data';
    public const EXTERNAL_BLOG = 'external_blog';
    public const CLONE_JOB = 'clone_job';
    // Previously 'cms_permissions_boot' — prefix now supplied by getInternalPrefix(), value must not repeat it
    public const CMS_PERMISSIONS_BOOT = 'permissions_boot';
    public const MODULE_PATH_CACHE = 'module_path_cache';
    // Previously 'cms_admin_modules_master_list' — prefix now supplied by getInternalPrefix()
    public const CMS_ADMIN_MODULES_MASTER_LIST = 'admin_modules_master_list';

    // Admin Session Keys (stored in PHP session, not Redis)
    public const CMS_API_TOKEN = 'hashtagcms_api_token';
    public const CMS_API_USER = 'hashtagcms_api_user';
    public const CMS_LANG_ID = 'lang_id';
    public const CMS_SITE_ID = 'site_id';
    public const CMS_MESSAGE = '__hashtagcms_message__';
    public const CMS_MESSAGE_ERROR = '__hashtagcms_message_error__';
    public const CMS_PERMISSIONS_MENU_ALLOWED = 'cms_permissions_menu_allowed_';
    public const CMS_PERMISSIONS_CACHE_KEY_PREFIX = 'cms_permissions_';
    public const CMS_LAYOUT = 'layout';

    public const CMS_RECORDS_PER_PAGE = 'cms_records_per_page';
}
