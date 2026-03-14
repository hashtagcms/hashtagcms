@extends(htcms_admin_config('theme') . '.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}" data-back-url="{{$backURL}}"></title-bar>

    @php


        $id = 0;

        $name = old('name');
        $alias = old('alias');
        ;
        $directory = old('directory');
        ;
        $skeleton = old('skeleton');
        ;
        $body_class = old('body_class');
        ;
        $header_content = old('header_content');
        ;
        $footer_content = old('footer_content');
        ;
        $site_id = old('site_id', htcms_get_siteId_for_admin());
        ;
        $img_preview = old('img_preview');

        //print_r($results);

        if (isset($results)) {
            extract($results);
        }


    @endphp


    <div
        class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden max-w-2xl mx-auto">
        <!-- Card Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Theme Engine Configuration</h3>
        </div>

        <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post"
            enctype="multipart/form-data" id="addEditForm">
            <div class="p-8 lg:p-10 space-y-10">
                {{csrf_field()}}
                {!! FormHelper::input('hidden', 'id', $id) !!}
                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}
                {!! FormHelper::input('hidden', 'site_id', $site_id) !!}

                <!-- Primary Identity -->
                <div class="space-y-6">
                    <div class="space-y-2">
                        {!! FormHelper::label('name', 'Theme Display Name', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::input('text', 'name', $name, array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required' => 'required', 'placeholder' => 'Enter Name')) !!}
                    </div>
                    <div class="space-y-2">
                        {!! FormHelper::label('alias', 'Theme Alias (Internal Key)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::input('text', 'alias', $alias, array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required' => 'required', 'placeholder' => 'Enter Alias')) !!}
                    </div>
                    <div class="space-y-2">
                        {!! FormHelper::label('directory', 'Template Directory Path', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::input('text', 'directory', $directory, array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required' => 'required', 'placeholder' => 'Enter Directory')) !!}
                    </div>
                    <div class="space-y-2">
                        {!! FormHelper::label('body_class', 'Default Body CSS Classes', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::input('text', 'body_class', $body_class, array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Body Class')) !!}
                    </div>
                </div>

                <!-- Template Structure -->
                <div class="space-y-8 border-t border-slate-50">

                    <div class="space-y-2">
                        {!! FormHelper::label('skeleton', 'HTML Structure (Skeleton)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        <p class="text-[10px] text-slate-400 mb-2 italic">Main layout structure for all pages using this
                            theme</p>

                        <!-- Developer Cheat Sheet (Interactive) -->
                        <div
                            class="cheat-sheet-container bg-blue-50/20 hover:bg-blue-50/50 rounded-xl p-3 border border-blue-100/30 hover:border-blue-100/50 mb-4 transition-all duration-300 cursor-help">
                            <h4
                                class="text-[10px] font-black uppercase tracking-widest text-blue-400 flex items-center gap-2">
                                <i class="fa fa-info-circle"></i>
                                Developer Cheat Sheet
                                <span class="text-[9px] font-medium lowercase tracking-normal text-slate-300 ml-auto">Hover
                                    to show placeholders</span>
                            </h4>

                            <div class="cheat-sheet-content pt-4 mt-3 border-t border-blue-100/30">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <section>
                                        <h5 class="text-[9px] font-bold text-slate-500 uppercase mb-1.5">Structure</h5>
                                        <div class="flex flex-wrap gap-2">
                                            <code
                                                class="text-[10px] bg-white px-2 py-1 rounded border border-blue-100 font-bold"
                                                title="Inject Hooks">%{cms.hook.ALIAS}%</code>
                                            <code
                                                class="text-[10px] bg-white px-2 py-1 rounded border border-blue-100 font-bold"
                                                title="Embed Module">%{cms.module.ALIAS}%</code>
                                        </div>
                                    </section>
                                    <section>
                                        <h5 class="text-[9px] font-bold text-slate-500 uppercase mb-1.5">Assets</h5>
                                        <div class="flex flex-wrap gap-2">
                                            <code
                                                class="text-[10px] bg-white px-2 py-1 rounded border border-blue-100 font-bold">%{css_path}%</code>
                                            <code
                                                class="text-[10px] bg-white px-2 py-1 rounded border border-blue-100 font-bold">%{js_path}%</code>
                                            <code
                                                class="text-[10px] bg-white px-2 py-1 rounded border border-blue-100 font-bold">%{image_path}%</code>
                                            <code
                                                class="text-[10px] bg-white px-2 py-1 rounded border border-blue-100 font-bold">%{resource_path}%</code>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>

                        {!! FormHelper::textarea('skeleton', $skeleton, array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'rows' => '12', 'required' => 'required', 'placeholder' => '<div id="app">
    <div>
        %{cms.hook.HOOK_HEADER}%
    </div>
    <main class="basic-theme">
        %{cms.hook.HOOK_ONE_COLUMN}%        
    </main>    
    <div>
        %{cms.hook.HOOK_FOOTER}%
    </div>
</div>')) !!}
                    </div>

                    <div class="space-y-2">
                        {!! FormHelper::label('header_content', 'Global Header Injection', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::textarea('header_content', $header_content, array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'rows' => '6', 'placeholder' => '<link rel="stylesheet" href="%{css_path}%/app.css" />')) !!}
                    </div>
                    <div class="space-y-2">
                        {!! FormHelper::label('footer_content', 'Global Footer Injection', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::textarea('footer_content', $footer_content, array('class' => 'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'rows' => '6', 'placeholder' => '<script src="%{js_path}%/app.js"></script>')) !!}
                    </div>
                </div>

                <!-- Visual Preview -->
                <div class="space-y-4 border-t border-slate-50">
                    {!!  FormHelper::label('img_preview', 'Theme Snapshot / Preview', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                    <div
                        class="p-6 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 hover:border-blue-400 transition-colors">
                        <div class="max-h-40 overflow-hidden">
                            {!! FormHelper::file('img_preview', $img_preview, array('class' => 'text-xs'), true, 200) !!}
                        </div>
                    </div>
                    @error('upload_error')
                        <div
                            class="mt-3 flex items-start gap-2.5 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold px-4 py-3 rounded-xl">
                            <i class="fa fa-exclamation-circle mt-0.5 shrink-0"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Card Footer -->
            <div
                class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-4">
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

@push('styles')
    <style>
        .cheat-sheet-content {
            display: none;
        }

        .cheat-sheet-container:hover .cheat-sheet-content {
            display: block;
        }
    </style>
@endpush
