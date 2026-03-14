@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}" data-back-url="{{$backURL}}"></title-bar>

    @php


        $id = 0;

        $name = old("name");
        $label = old("label");
        $description = old("description");

        $permissions = old("permissions", array());

        //print_r($results);

        if(isset($results)) {
            extract($results);
        }

    @endphp


    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden max-w-3xl mx-auto">
        <!-- Dashboard Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Security Role Definition</h3>
            <span class="text-[10px] font-black uppercase text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Primary Identity</span>
        </div>

        <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" id="addEditForm">
            <div class="p-8 lg:p-12 space-y-12">
                {{csrf_field()}}
                {!! FormHelper::input('hidden', 'id', $id) !!}
                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                <!-- Section 1: Core details -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa fa-shield-alt text-xs"></i>
                        </div>
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-700">Core Identity</h4>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('name', 'Name', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'name', $name , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Name')) !!}
                        </div>
                        <div class="space-y-2">
                             {!! FormHelper::label('label', 'Label', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                             {!! FormHelper::input('text', 'label', $label , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Label')) !!}
                        </div>

                        <div class="space-y-2">
                            {!! FormHelper::label('description', 'Role Description', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            <textarea name="description" id="description" rows="3" class="form-control w-full rounded-xl border border-slate-300 transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 pl-3 hover:border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900">{{ $description }}</textarea>
                            <p class="text-[10px] text-slate-400 italic mt-1 leading-relaxed">
                                <i class="fa fa-info-circle mr-1 text-blue-400"></i>
                                This description will be shown in the Author assignment screen to help admins choose the right role.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Permissions -->
                <div class="space-y-6 pt-10 border-t border-slate-50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fa fa-lock text-xs"></i>
                        </div>
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-700">Assigned Security Rights</h4>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] items-center gap-1 flex text-slate-400 font-bold uppercase tracking-widest bg-slate-50 px-2 py-1 rounded">Multi-Select Enabled</span>
                        </div>
                        <div class="relative group">
                            <input type="hidden" value="0" name="updatePermission" id="updatePermission" />
                            {!! FormHelper::select('permissions[]', $allPermissions , array('class'=>'form-multiselect w-full rounded-2xl border border-slate-300 focus:ring-8 focus:ring-blue-500/5 focus:border-blue-500 transition-all p-4 h-64', "id"=>"permissions", 'onChange'=>'document.getElementById("updatePermission").value = 1'), $permissions) !!}
                        </div>
                        <p class="text-[10px] text-slate-400 italic font-medium">
                            <i class="fa fa-keyboard-o mr-1"></i>
                            Hold Ctrl/Cmd to select multiple permissions</p>
                    </div>
                </div>

            </div>

            <!-- Card Footer -->
            <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-4">
                <a href="{{$backURL ?? request()->headers->get('referer')}}"
                    class="w-full sm:w-auto text-center px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors order-2 sm:order-1">Cancel</a>
                <button type="submit" name="submit"
                    class="w-full sm:w-auto px-12 py-4 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-xl shadow-blue-600/20 transition-all active:scale-95 flex items-center justify-center gap-2 order-1 sm:order-2">
                    <i class="fa fa-save opacity-50"></i>
                    Save
                </button>
            </div>
        </form>
    </div>

    @include(htcms_admin_get_view_path('common.validationerror-js'))
@endsection
