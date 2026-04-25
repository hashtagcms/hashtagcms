<?php

namespace HashtagCms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\CmsModule;
use HashtagCms\Models\CmsPermission;
use HashtagCms\Models\Role;
use HashtagCms\Models\Site;
use HashtagCms\Models\User;

class AuthorController extends BaseAdminController
{
    protected $dataFields = [
        'id',
        'name',
        'user_type',
        ['label' => 'Roles', 'key' => 'roles.name', 'showAllScopes' => true],
        'email',
        'updated_at'
    ];

    protected $dataSource = User::class;

    protected $dataWith = 'roles';

    //protected $dataWhere = array(array("field"=>"user_type", "operator"=>"=", "value"=>"Staff"));

    protected $actionFields = ['edit', 'delete']; //This is last column of the row

    protected $minResults = 1;

    protected $moreActionFields = [
        [
            'label' => 'Permission',
            'icon_css' => 'fa fa-lock',
            'action' => 'permission',
            'action_append_field' => 'id'
        ],
    ];

    protected $bindDataWithAddEdit = [
        'allRoles' => ['dataSource' => Role::class, 'method' => 'all'],
        'allSites' => ['dataSource' => Site::class, 'method' => 'all'],
    ];

    /**
     * Inject a role → permissions + access-level map so the view can show
     * what each role allows (and whether sites are auto-assigned) without AJAX.
     */
    protected function getExtraDataForEdit($bindData = null, $useBoth = false)
    {
        $data = parent::getExtraDataForEdit($bindData, $useBoth);

        // Super-admin role slugs — must match RoleManager::isSuperAdmin()
        $superAdminSlugs = ['super-admin', 'super-duper-admin'];

        // Eager-load permissions for every role in a single query
        $rolesWithPermissions = Role::with('permissions')->get();

        $rolePermissionsMap = [];
        foreach ($rolesWithPermissions as $role) {
            $isSuperAdmin = in_array(strtolower($role->name), $superAdminSlugs);

            $rolePermissionsMap[$role->id] = [
                'name'          => $role->name,
                'description'   => $role->description ?? '',
                'is_super_admin'=> $isSuperAdmin,
                // site_access tells the view what to display for the Sites field
                'site_access'   => $isSuperAdmin
                    ? 'all'        // super-admin: all sites automatically
                    : 'manual',    // admin/others: must be assigned specific sites
                'site_access_label' => $isSuperAdmin
                    ? 'All sites (automatic — no site assignment needed)'
                    : 'Specific sites only — must be manually assigned below',
                'permissions'   => $role->permissions->map(fn($p) => [
                    'id'   => $p->id,
                    'name' => $p->name,
                ])->values()->toArray(),
            ];
        }

        $data['rolePermissionsMap'] = json_encode($rolePermissionsMap);

        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (!$this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        $rules = [
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->whereNull('deleted_at')->ignore($request->input('id', 0))],
            'name' => 'required|max:255|string',
            'password' => 'nullable|max:255|string',
            'facebook_user_id' => 'nullable|max:255|string',
            'google_user_id' => 'nullable|max:255|string',
            'remember_token' => 'nullable|max:100|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data = request()->all();

        $saveData['name'] = $data['name'];
        $saveData['email'] = $data['email'];
        $saveData['user_type'] = 'Staff';

        $roles = $data['roles'] ?? [];  
        $updateRoles = ($data['updateRoles'] ?? 0) == 1;

        $sites = $data['sites'] ?? []; 
        $updateSites = ($data['updateSites'] ?? 0) == 1;

        if (!empty($data['password'])) {
            $saveData['password'] = User::makePassword($data['password']);
        }

        //date
        $saveData['updated_at'] = htcms_get_current_date();
        if ($data['actionPerformed'] !== 'edit') {
            $saveData['created_at'] = htcms_get_current_date();
        }

        $arrSaveData = ['model' => $this->dataSource, 'data' => $saveData];

        if ($data['actionPerformed'] == 'edit') {

            $where = $data['id'];
            //This is in base controller
            $savedData = $this->saveData($arrSaveData, $where);

            $id = $savedData['id'];

        } else {
            //This is in base controller
            $savedData = $this->saveData($arrSaveData);

            $id = $savedData['id'];
        }

        $user = User::find($id);

        //Insert/Update Roles
        if (!empty($roles) && $updateRoles == true) {            
            $superAdminSlugs = ['super-admin', 'super-duper-admin'];
            $requestingRoles = Role::whereIn('id', $roles)->pluck('name')->map(fn($n) => strtolower($n))->toArray();
            $isAssigningSuperAdmin = !empty(array_intersect($requestingRoles, $superAdminSlugs));

            if ($isAssigningSuperAdmin && !auth()->user()->isSuperAdmin()) {
                return htcms_admin_view('common.error', ['message' => 'Only a super-admin can grant the super-admin role.']);
            }

            $user->detachAllRoles(); //remove old roles

            //Get Roles
            $allRoles = Role::find($roles);
            $user->assignMultipleRole($allRoles); //Assign new roles
        }

        //Insert/Update Site relation
        if (!empty($sites) && $updateSites == true) {
            $user->detachAllSites(); //remove old sites

            //Get Sites
            $allSites = Site::find($sites);
            $user->assignMultipleSite($allSites); //Assign new sites

        }

        $viewData['id'] = $savedData['id'];
        $viewData['saveData'] = $data;
        $viewData['backURL'] = $data['backURL'];
        $viewData['isSaved'] = $savedData['isSaved'];

        return htcms_admin_view('common.saveinfo', $viewData);
    }

    /**
     * Save Permission
     *
     * @param  $id
     * @return mixed
     */
    public function permission($user_id = 0)
    {

        if (!$this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError(), \request()->ajax()); 
        }

        if ($user_id == 0) {
            return ['error' => "Unable to read data for userId: $user_id"];
        }

        $allModules = CmsModule::getAdminModules();

        $userWithModules = User::with('cmsmodules')->find($user_id);

        $viewData['results'] = ['id' => $user_id];
        $viewData['allModules'] = $allModules;
        $viewData['isSuperAdmin'] = $userWithModules->isSuperAdmin() || $userWithModules->isAdmin();
        $viewData['userModules'] = $userWithModules;
        $viewData['backURL'] = $this->getBackURL();
        $viewData['actionPerformed'] = 'edit';

        return htcms_admin_view('author.permission', $viewData);

    }

    /**
     * Save Module Permission
     *
     * @return mixed
     */
    public function saveModulePermissions()
    {

        if (!$this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }
        try {
            $data = request()->all();

            $cmsModuleData = $data['cmsModuleData'];

            $userId = $data['userId'];

            $saveData = array();

            $savedData = array();

            foreach ($cmsModuleData as $cmsModule) {

                $isReadOnly = (isset($cmsModule['readonly']) && $cmsModule['readonly'] === true) ? 1 : 0;
                $selected = $cmsModule['selected'] ?? 0;

                if ($selected) {
                    $saveData[] = array('module_id' => $cmsModule['id'], 'user_id' => $userId, 'readonly' => $isReadOnly);
                }

                if (!empty($cmsModule['child'])) {

                    foreach ($cmsModule['child'] as $child) {

                        $isReadOnly = (isset($child['readonly']) && $child['readonly'] === true) ? 1 : 0;
                        $selected = $child['selected'] ?? 0;

                        if ($selected) {
                            $saveData[] = array('module_id' => $child['id'], 'user_id' => $userId, 'readonly' => $isReadOnly);
                        }
                    }

                }
            }

            //Delete old
            CmsPermission::detachOldModules($userId);

            //return $arrSaveData = array('model' => CmsPermission::class, 'data' => $saveData);
            $this->rawInsert('cms_permissions', $saveData);
            $savedData = ['isSaved' => true];

        } catch (\Exception $exception) {
            $savedData = array('message' => $exception->getMessage());
        }

        return $savedData;

    }
}
