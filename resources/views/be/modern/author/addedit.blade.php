@extends(htcms_admin_config('theme').'.index')

@section('content')
    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
    ></title-bar>

    @php


        $id = 0;


        $name = old('name');
        $password = "";
        $email = old('email');
        $roles = old('roles', []);
        $sites = old('sites', []);


        if(isset($results)) {
            extract($results);
        }

    @endphp


    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden max-w-2xl mx-auto">
        <!-- Card Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Author Authentication & Roles</h3>
        </div>

        <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" enctype="multipart/form-data" id="addEditForm">
            <div class="p-8 lg:p-10 space-y-6">
                {{csrf_field()}}
                {!! FormHelper::input('hidden', 'id', $id) !!}
                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                <!-- Name Field -->
                <div class="space-y-2">
                    {!! FormHelper::label('name', 'Full Name', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                    {!! FormHelper::input('text', 'name', $name, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-sm tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Name')) !!}
                </div>

                <!-- Email Field -->
                <div class="space-y-2">
                    {!! FormHelper::label('email', 'Email Address', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                    {!! FormHelper::input('text', 'email', $email, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-sm tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Email')) !!}
                </div>

                <!-- Roles Selection -->
                <div class="space-y-2">
                    {!! FormHelper::label('roles', 'Assigned Roles', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                    <input type="hidden" value="0" name="updateRoles" id="updateRoles" />
                    {!! FormHelper::select('roles[]', $allRoles , array('id' => 'rolesSelect', 'class'=>'w-full bg-white border transition-all duration-300 outline-none font-bold text-sm tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'multiple'=>'multiple', 'onChange'=>'document.getElementById("updateRoles").value = 1; updateRolePermissions()'), $roles) !!}
                    <p class="text-[10px] text-slate-400 italic">Hold Ctrl/Cmd to select multiple roles</p>

                    {{-- Live Role Permissions Preview --}}
                    <div id="rolePermissionsPreview" class="mt-3 hidden">
                        <div class="rounded-xl border border-indigo-100 bg-indigo-50/40 p-4 space-y-3">

                            {{-- Super-admin special badge --}}
                            <div id="roleSuperAdminBadge" class="hidden flex items-center gap-2 px-3 py-2 rounded-lg bg-amber-50 border border-amber-200">
                                <i class="fa fa-star text-amber-500 text-xs"></i>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-amber-600">Super Admin — Unrestricted Access</p>
                                    <p class="text-[10px] text-amber-500">All permissions on all sites are granted automatically. No site assignment needed.</p>
                                </div>
                            </div>

                            <div id="roleDescriptionsBox" class="space-y-2 mb-4 hidden border-b border-indigo-100 pb-3">
                                <p class="text-[10px] font-black uppercase tracking-widest text-indigo-400">
                                    <i class="fa fa-info-circle mr-1"></i> Role Definitions
                                </p>
                                <div id="roleDescriptionsList" class="space-y-1"></div>
                            </div>

                            <p class="text-[10px] font-black uppercase tracking-widest text-indigo-400">
                                <i class="fa fa-shield mr-1"></i> Permissions granted by selected role(s)
                            </p>
                            <div id="rolePermissionsList" class="flex flex-wrap gap-2"></div>
                            <p id="rolePermissionsEmpty" class="text-[10px] text-slate-400 italic hidden">
                                No permissions assigned to the selected role(s) yet.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Sites Selection -->
                {{-- Auto-notice shown when super-admin role is selected --}}
                <div id="sitesAutoNotice" class="hidden rounded-xl border border-emerald-100 bg-emerald-50 p-4 flex items-start gap-3">
                    <i class="fa fa-check-circle text-emerald-500 mt-0.5"></i>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600">All Sites — Access Automatic</p>
                        <p class="text-[10px] text-emerald-500 mt-0.5">Super Admin role has access to all sites automatically. No manual site assignment is required.</p>
                    </div>
                </div>

                <div id="sitesSectionWrapper" class="space-y-2">
                    {!! FormHelper::label('Sites', 'Authorized Sites', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                    <input type="hidden" value="0" name="updateSites" id="updateSites" />
                    {!! FormHelper::select('sites[]', $allSites, array('class'=>'form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-sm tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'onChange'=>'document.getElementById("updateSites").value = 1'), $sites) !!}
                    <p class="text-[10px] text-slate-400 italic">Select sites this author can manage</p>
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    {!! FormHelper::label('password', 'Security Password', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                    @if($id == 0)
                        {!! FormHelper::input('text', 'password', $password, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-sm tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Password')) !!}
                    @else
                        {!! FormHelper::input('text', 'password', $password, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-sm tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Password')) !!}
                    @endif
                </div>
            </div>

            <!-- Card Footer -->
            <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-4">
                <a href="{{$backURL ?? request()->headers->get('referer')}}" class="w-full sm:w-auto text-center px-6 py-4 text-sm font-black uppercase tracking-widest text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors order-2 sm:order-1">Cancel</a>
                <button type="submit" name="submit" class="w-full sm:w-auto px-12 py-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-black uppercase tracking-widest rounded-xl shadow-xl shadow-blue-600/20 transition-all active:scale-95 flex items-center justify-center gap-2 order-1 sm:order-2">
                    <i class="fa fa-save opacity-50"></i>
                    Save
                </button>
            </div>
        </form>
    </div>
    @include(htcms_admin_get_view_path('common.validationerror-js'))
@endsection

@push('scripts')
    <script>
                        var __rolePermissionsMap = {!! $rolePermissionsMap ?? '{}' !!};

                        function updateRolePermissions() {
                            var select      = document.getElementById('rolesSelect');
                            var preview     = document.getElementById('rolePermissionsPreview');
                            var list        = document.getElementById('rolePermissionsList');
                            var empty       = document.getElementById('rolePermissionsEmpty');
                            var superBadge  = document.getElementById('roleSuperAdminBadge');
                            var sitesSection= document.getElementById('sitesSectionWrapper');
                            var sitesNotice = document.getElementById('sitesAutoNotice');
                            var descBox     = document.getElementById('roleDescriptionsBox');
                            var descList    = document.getElementById('roleDescriptionsList');

                            var selectedIds = Array.from(select.selectedOptions).map(function(o) { return o.value; });

                            if (selectedIds.length === 0) {
                                preview.classList.add('hidden');
                                if (sitesSection)  sitesSection.style.display = '';
                                if (sitesNotice)   sitesNotice.classList.add('hidden');
                                return;
                            }

                            // Check if ANY selected role is super-admin
                            var hasSuperAdmin = selectedIds.some(function(id) {
                                return __rolePermissionsMap[id] && __rolePermissionsMap[id].is_super_admin;
                            });

                            // Collect unique permissions across all selected roles
                            var seen = {};
                            var perms = [];
                            selectedIds.forEach(function(id) {
                                var role = __rolePermissionsMap[id];
                                if (role && role.permissions) {
                                    role.permissions.forEach(function(p) {
                                        if (!seen[p.id]) {
                                            seen[p.id] = true;
                                            perms.push(p);
                                        }
                                    });
                                }
                            });

                            // --- Permissions panel ---
                            list.innerHTML = '';
                            superBadge.classList.toggle('hidden', !hasSuperAdmin);

                            if (!hasSuperAdmin) {
                                if (perms.length === 0) {
                                    empty.classList.remove('hidden');
                                } else {
                                    empty.classList.add('hidden');
                                    perms.forEach(function(p) {
                                        var tag = document.createElement('span');
                                        tag.className = 'inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white border border-indigo-200 text-indigo-700 shadow-sm';
                                        tag.textContent = p.name;
                                        list.appendChild(tag);
                                    });
                                }
                            }

                             // --- Descriptions panel ---
                            descList.innerHTML = '';
                            var hasAnyDesc = false;

                            selectedIds.forEach(function(id) {
                                var role = __rolePermissionsMap[id];
                                if (role && role.description) {
                                    hasAnyDesc = true;
                                    var item = document.createElement('div');
                                    item.className = 'text-[10px] text-indigo-600 font-medium leading-relaxed';
                                    item.innerHTML = '<span class="font-black uppercase tracking-tighter mr-1">' + role.name + ':</span> ' + role.description;
                                    descList.appendChild(item);
                                }
                            });
                            descBox.classList.toggle('hidden', !hasAnyDesc);

                            preview.classList.remove('hidden');

                            // --- Sites section ---
                            if (hasSuperAdmin) {
                                // Super-admin: hide the sites selector, show auto-notice
                                if (sitesSection)  sitesSection.style.display = 'none';
                                if (sitesNotice)   sitesNotice.classList.remove('hidden');
                            } else {
                                // Other roles: show sites selector, hide auto-notice
                                if (sitesSection)  sitesSection.style.display = '';
                                if (sitesNotice)   sitesNotice.classList.add('hidden');
                            }
                        }

                        // Run on page load if roles are already selected (edit mode)
                        document.addEventListener('DOMContentLoaded', function() {
                            updateRolePermissions();
                        });
                    </script>
@endpush