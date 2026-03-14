@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
               data-copy-paste-auto-init="true"
    ></title-bar>

    @php

        $id = 0;
        $site_id = old('site_id', htcms_get_siteId_for_admin());
        $path = old('path', '');
        $media_type = old('media_type', 'image');
        $group_name = old('group_name');
        $media_key = old('media_key');
        $tags = old('tags', "");

        $isMultiple = array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'multiple'=>'multiple');
        $fileField = 'image[]';
        $tag = [];

        if(isset($results)) {
            extract($results);
        }
        foreach ($tag as $row) {
            $tags .= $row['name'].",";
        }

        $tags = rtrim($tags, ",");

        if ($id > 0) {
            $isMultiple = array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900');
            $fileField = 'image';
        }



    @endphp

    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden max-w-2xl mx-auto">
        <!-- Card Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Media Asset Management</h3>
        </div>

        <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" enctype="multipart/form-data" id="addEditForm">
            <div class="p-8 lg:p-10 space-y-8">
                {{csrf_field()}}
                {!! FormHelper::input('hidden', 'id', $id) !!}
                {!! FormHelper::input('hidden', 'site_id', $site_id) !!}
                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                <!-- Media Type Selection -->
                <div class="p-6 bg-slate-50/50 rounded-2xl border border-slate-100 space-y-4">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Classification</h4>
                    
                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('media_type', 'Media Type (Category)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            <hc-auto-suggest value="{{ $media_type }}" name="media_type"
                                            placeholder="Enter or search Media Type..."
                                            :data='{{ json_encode(array_values($typeGroups)) }}'
                                            display-field="media_type" :min-chars="0">
                                            <template #icon-left>
                                                <i class="fa fa-folder-open"></i>
                                            </template>
                            </hc-auto-suggest>
                        </div>
                    </div>
                </div>

                <!-- Grouping Selection -->
                <div class="p-6 bg-slate-50/50 rounded-2xl border border-slate-100 space-y-4">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Organization</h4>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('group_name', 'Group Name', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            <hc-auto-suggest value="{{ $group_name }}" name="group_name"
                                            placeholder="Enter or search Group Name..."
                                            endpoint="{{ htcms_admin_path('gallery/getGalleryGroup') }}"
                                            display-field="group_name" :min-chars="0">
                                            <template #icon-left>
                                                <i class="fa fa-layer-group"></i>
                                            </template>
                            </hc-auto-suggest>
                        </div>
                    </div>
                </div>

                <!-- Metadata info -->
                <div class="space-y-6">
                    <div class="space-y-2">
                        {!! FormHelper::label('tags', 'Search Tags (Comma separated)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::input('text', 'tags', $tags, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder'=>'Please enter keyword to search later. ie: flowers, decoration')) !!}
                    </div>

                    <div class="space-y-2">
                        {!! FormHelper::label('media_key', 'Internal Identification Key (Optional)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::input('text', 'media_key', $media_key, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder'=>'Any key for the media? (optional)')) !!}
                    </div>

                    <div class="space-y-2 pt-4">
                        <div class="flex items-center justify-between mb-2">
                            {!! FormHelper::label('image', 'Select File Assets', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            @if($id === 0)
                            <span class="text-[10px] text-blue-500 font-medium bg-blue-50 px-2 py-0.5 rounded">Bulk upload supported</span>
                            @endif
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border-2 border-dashed border-slate-200 hover:border-blue-400 transition-colors">
                             {!! FormHelper::file($fileField, $path, $isMultiple, TRUE, 100, NULL, "text-xs file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer", FALSE) !!}
                        </div>
                        @if($id === 0)
                        <p class="text-[10px] text-slate-400 italic mt-2">Each selected file will be created as a unique record.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-4">
                <a href="{{$backURL ?? request()->headers->get('referer')}}" class="w-full sm:w-auto text-center px-6 py-4 text-xs font-black uppercase tracking-widest text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors order-2 sm:order-1">Cancel</a>
                <button type="submit" name="submit" class="w-full sm:w-auto px-12 py-4 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-xl shadow-blue-600/20 transition-all active:scale-95 flex items-center justify-center gap-2 order-1 sm:order-2">
                    <i class="fa fa-save opacity-50"></i>
                    Save
                </button>
            </div>
        </form>
    </div>
    @include(htcms_admin_get_view_path('common.validationerror-js'))

@endsection
