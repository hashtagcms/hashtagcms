@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
               data-copy-paste-auto-init="true"
    ></title-bar>

    @php
        $id = 0;
        

        if(isset($results)) {
            extract($results);
        }

        // Lang fallback — ensures $lang is always an array even without a lang relation
        if(empty($lang)) {
            $lang = array();
            $lang["lang_id"] = session("lang_id");
        }
    @endphp

    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden max-w-3xl mx-auto">

        <!-- Card Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Moduletype Configuration</h3>
        </div>

        <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" id="addEditForm">
            <div class="p-8 lg:p-10 space-y-6">
                {{csrf_field()}}
                {!! FormHelper::input('hidden', 'id', $id) !!}
                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                <div class="space-y-6">
                    <!-- Type Name -->
                    <div class="space-y-2">
                        {!! FormHelper::label('name', 'Type Name', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::input('text', 'name', $name ?? '', array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder'=>'e.g. MenuService', 'required'=>'required')) !!}
                    </div>

                    <!-- Display Label -->
                    <div class="space-y-2">
                        {!! FormHelper::label('label', 'Display Label', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::input('text', 'label', $label ?? '', array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder'=>'e.g. Menu Service')) !!}
                    </div>

                    <!-- Icon Class -->
                    <div class="space-y-2">
                        {!! FormHelper::label('icon', 'Icon Class', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::input('text', 'icon', $icon ?? '', array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder'=>'fa fa-bars')) !!}
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        {!! FormHelper::label('description', 'Description', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::textarea('description', $description ?? '', array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900 min-h-[80px]', 'placeholder'=>'Describe the purpose of this data type')) !!}
                    </div>

                    <!-- Field Hint -->
                    <div class="space-y-2">
                        {!! FormHelper::label('field_hint', 'Field Hint (In UI)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::input('text', 'field_hint', $field_hint ?? '', array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder'=>'e.g. Data Handler → Service URL')) !!}
                    </div>

                    <!-- Placeholder -->
                    <div class="space-y-2">
                        {!! FormHelper::label('placeholder', 'Input Placeholder', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::input('text', 'placeholder', $placeholder ?? '', array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder'=>'e.g. https://api.example.com/...')) !!}
                    </div>

                    <!-- Publish Status -->
                    <div class="space-y-2">
                        {!! FormHelper::label('publish_status', 'Launch Status', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        <div class="flex items-center gap-3 bg-slate-50 p-4 rounded-xl border border-slate-100">
                            {!! FormHelper::input('checkbox', 'publish_status', $publish_status ?? 0) !!}
                            <span class="text-xs font-black uppercase text-slate-600">Published</span>
                        </div>
                    </div>
                </div>


            </div>

            <!-- Card Footer -->
            <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-4">
                <a href="{{$backURL ?? request()->headers->get('referer')}}"
                   class="w-full sm:w-auto text-center px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors order-2 sm:order-1">
                    Cancel
                </a>
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
