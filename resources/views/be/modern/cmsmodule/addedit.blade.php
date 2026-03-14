@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
               data-show-copy="false"
               data-show-paste="false"
    ></title-bar>

    @php

        $id = 0;
        $name = old('name');
        $controller_name = old('controller_name');
        $sub_title = old('sub_title');
        $parent_id = old('parent_id');
        $icon_css = old('icon_css');
        $list_view_name = old('icon_css', 'listing');
        $edit_view_name = old('icon_css', 'addedit');

        //print_r($results);

        if(isset($results)) {
            extract($results);
        }


    @endphp


    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden max-w-2xl mx-auto">
        <!-- Dashboard Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">System Module Definition</h3>
        </div>

        <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" id="addEditForm">
            <div class="p-8 lg:p-10 space-y-8">
                {{csrf_field()}}
                {!! FormHelper::input('hidden', 'id', $id) !!}
                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                <div class="space-y-12">
                    
                    <!-- Module Identity -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <i class="fa fa-cube text-blue-500"></i>
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-800">Module Identity & Presentation</h4>
                        </div>
                        <div class="space-y-6">
                            <div class="space-y-2">
                                {!! FormHelper::label('name', 'Module Name', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'name', $name , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Name')) !!}
                            </div>
                            <div class="space-y-2">
                                {!! FormHelper::label('sub_title', 'Sub-Title / Description', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'sub_title', $sub_title , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Sub Title')) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Technical Core -->
                    <div class="space-y-6 pt-10 border-t border-slate-50">
                        <div class="flex items-center gap-3 text-amber-600">
                             <i class="fa fa-code"></i>
                             <h4 class="text-[10px] font-black uppercase tracking-widest">Logic & Architecture</h4>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="space-y-2">
                                {!! FormHelper::label('controller_name', 'Controller Mapping', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'controller_name', $controller_name , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Controller Name')) !!}
                            </div>
                            <div class="p-4 bg-amber-50 rounded-xl border border-amber-100">
                                 <p class="text-[10px] leading-relaxed text-amber-700 font-bold uppercase tracking-tight">
                                    <i class="fa fa-exclamation-triangle mr-1"></i>
                                    Architectural Shift Warning: Changing the controller name requires manual file renaming in the backend source code.
                                 </p>
                            </div>

                            <div class="space-y-2">
                                {!! FormHelper::label('parent_id', 'Organizational Parent', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::select('parent_id', $cmsModules , array('class'=>'form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900'), $parent_id) !!}
                            </div>
                            <div class="space-y-2">
                                {!! FormHelper::label('icon_css', 'Dashboard Icon (FontAwesome)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'icon_css', $icon_css , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'fa fa-cube')) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Routing/Views -->
                     <div class="space-y-6 pt-10 border-t border-slate-50">
                        <div class="flex items-center gap-3 text-emerald-600">
                             <i class="fa fa-eye"></i>
                             <h4 class="text-[10px] font-black uppercase tracking-widest">Interface Routing</h4>
                        </div>
                        <div class="space-y-6">
                             <div class="space-y-2">
                                {!! FormHelper::label('list_view_name', 'Listing Template', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'list_view_name', $list_view_name, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter list view Name (Default list view is common/listing)')) !!}
                            </div>
                             <div class="space-y-2">
                                {!! FormHelper::label('edit_view_name', 'Editor Template', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                                {!! FormHelper::input('text', 'edit_view_name', $edit_view_name, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Edit list view Name (Default edit view is addedit)')) !!}
                            </div>
                        </div>
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

