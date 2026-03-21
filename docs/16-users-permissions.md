# User Management & Permissions

HashtagCMS comes with a robust RBAC (Role-Based Access Control) system.

## Roles

HashtagCMS uses a predefined set of roles to manage administrative access:

- **Super-admin**: Has absolute access to every part of the system, including all sites, global configurations, and license management.
- **Admin**: Has full access (Read, Edit, Delete, Approve, Publish) to all enabled cms modules within their assigned sites.
- **Editor**: Can manage content (Read, Edit, Delete, Approve, Publish) created by **any user** within their assigned sites and cms modules.
- **Contributor**: Can manage content (Read, Edit, Delete, Approve, Publish) but **only their own content**.
- **Approver**: Focused on the publication workflow; has Read, Approve, and Publish permissions.
- **ReadOnly**: Has view-only (Read) access to the cms modules they are authorized to see.


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
