# User Management & Permissions

HashtagCMS comes with a robust RBAC (Role-Based Access Control) system.

## Roles
-   **Super Admin**: Has access to everything.
-   **Site Admin**: Can manage 1 specific site.
-   **Editor**: Can edit content but not change configuration.

## Permissions (`roles_rights`)
You can define granular permissions.
-   `category_view`, `category_add`, `category_edit`, `category_delete`
-   `module_publish`

## Managing Users
1.  **Users** (table `users`): The authentication record.
2.  **Profiles** (table `user_profiles`): Extended data (avatar, bio).

## Site-Specific Access
A user can be assigned to **Site A** but blocked from **Site B**.
This is controlled in the `site_user` pivot table.

## Frontend Members vs Backend Authors
HashtagCMS distinguishes between:
1.  **Authors/Admins**: People who login to `/admin`.
2.  **Staff/Customers**: People who register on the frontend website.
    -   They use the same `users` table.
    -   Differentiation is usually handled by **Roles** (e.g., Role ID 2 = "Subscriber").
    -   Frontend registration APIs (`/api/v1/register`) assign the default "Subscriber" role.
3. **Staff**: Can only be created via the Admin Panel for security reasons. 
