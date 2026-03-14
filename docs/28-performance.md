# Performance Optimization

## 1. Module Caching (Critical)
HashtagCMS allows you to cache individual modules to drastically reduce database queries.

**Where is it?**
Go to **Admin > Site > Site Settings (Module Assignment)**. When you assign a module to a Category/Hook, you will see a field for **Cache Group** (or Cache).

**When to use it?**
-   **✅ Cache It**: For content that looks the same for everyone (e.g., **Footer**, **Header**, **Hero Banner**, **Static HTML**). Use a group like `global` or `static`.
-   **❌ Don't Cache**: For content that changes per user or per second (e.g., **Cart**, **User Profile**, **Randomized Lists**).

**Why?**
If a module is cached, the CMS skips the database queries and PHP logic for that module and serves the pre-rendered HTML instantly.
For full-page API caching details, see [38-caching.md](./38-caching.md).

## 2. CDN Configuration
To serve assets and media from a CDN:
1.  Open `config/hashtagcms.php`.
2.  Update `media.http_path` for uploaded files.
3.  Update `info.assets_path.base_url` for theme assets (CSS/JS/Images).

---

## 3. Bulk Database Operations

When reordering / sorting records, the `HasRawDatabaseOps` trait (available on all controllers) provides two methods that replace N individual `UPDATE` round-trips with a **single parameterised SQL statement**.

### `bulkUpdateIndex()` — single PK sort

```php
// N=20 sorted records → still 1 query
$table    = (new $this->dataSource)->getTable();
$affected = $this->bulkUpdateIndex($table, $rows);
// rows: [['id' => 5, 'position' => 1], ['id' => 3, 'position' => 2], ...]
```

Generated SQL:
```sql
UPDATE `cms_modules` SET `position` = CASE `id`
    WHEN 5 THEN 1  WHEN 3 THEN 2  ...
END WHERE `id` IN (5, 3, ...)
```

### `bulkRawUpdate()` — composite key sort (e.g. pivot tables)

```php
$this->bulkRawUpdate('category_site', [
    ['where' => ['category_id' => 5, 'site_id' => 1], 'data' => ['position' => 1]],
    ...
]);
```

Generated SQL: single `UPDATE ... SET position = CASE WHEN ... END WHERE (col=? AND col=?) OR ...`

**Performance gain**: For a page with 50 sortable items, this reduces 50 DB round-trips to 1.

See [24-backend-dev.md](./24-backend-dev.md#bulk-database-operations-hasrawdatabaseops-trait) for full API details.

---

## 4. Sorting — Frontend Fix

The `sorter.vue` and `menu-sorter.vue` components use `data-id` attributes stamped on each draggable `<li>` to identify records after drag. This avoids fragile DOM text-matching and ensures the correct IDs are always sent to `updateIndex`.

```html
<!-- Correct: ID is read from attribute, not text -->
<li :data-id="current.id" class="parent ...">
```

The selector used to read back order after dragging:
```js
el.querySelectorAll(":scope > li[data-id]").forEach((item, index) => {
    const id = parseInt(item.dataset.id, 10); // reliable, no text matching
    ...
});
```
