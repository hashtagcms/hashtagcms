@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
               data-copy-paste-auto-init="true"
    ></title-bar>

    @php

        $id = 0;
        $name = old('name');
        $country_id = old('country_id');
        $iso_code = old('iso_code');
        $tax_behavior = old('tax_behavior');
        $airport_name = old('airport_name');
        $airport_code = old('airport_code');
        $latitude = old('latitude');
        $longitude = old('longitude');

        if(isset($results)) {
            extract($results);
        }

        //dd($countries[0]);


    @endphp

    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden max-w-2xl mx-auto">
        <!-- Card Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">City Details</h3>
        </div>

        <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" id="addEditForm">
            <div class="p-8 lg:p-10 space-y-6">
                {{csrf_field()}}
                {!! FormHelper::input('hidden', 'id', $id) !!}
                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                <!-- Main Info -->
                <div class="space-y-6">
                    <div class="space-y-2">
                        {!! FormHelper::label('name', 'City Name', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::input('text', 'name', $name , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Name')) !!}
                    </div>

                    <div class="space-y-2">
                        {!! FormHelper::label('country_id', 'Country', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::select('country_id', $countries,
                                                array('required'=>'required', 'class'=>'form-select w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900'),
                                                $country_id,
                                                array("value"=>"id", "label"=>"lang.name")) !!}
                    </div>

                    <div class="space-y-2">
                        {!! FormHelper::label('iso_code', 'ISO Code', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::input('text', 'iso_code', $iso_code , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Iso Code')) !!}
                    </div>
                </div>

                <!-- Airport Information Group -->
                <div class="pt-6 border-t border-slate-50 space-y-4">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Airport Information</h4>
                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('airport_code', 'Airport Code', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'airport_code', $airport_code, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Airport Code')) !!}
                        </div>
                        <div class="space-y-2">
                            {!! FormHelper::label('airport_name', 'Airport Name', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'airport_name', $airport_name, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Airport Name')) !!}
                        </div>
                    </div>
                </div>

                <!-- Geographic Coordinates Group -->
                <div class="pt-6 border-t border-slate-50 space-y-4">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Geographic Coordinates</h4>
                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('latitude', 'Latitude', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'latitude', $latitude, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Latitude')) !!}
                        </div>
                        <div class="space-y-2">
                            {!! FormHelper::label('longitude', 'Longitude', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'longitude', $longitude, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Longitude')) !!}
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <div class="flex items-center gap-3 bg-slate-50 p-4 rounded-xl border border-slate-100 italic">
                        {!! FormHelper::checkbox('tax_behavior', $tax_behavior, array('class' => 'w-5 h-5 rounded text-blue-600 focus:ring-blue-500')) !!}
                        {!! FormHelper::label('tax_behavior', 'Apply specific Tax Behavior for this city', array('class' => 'text-sm font-medium text-slate-600')) !!}
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-4">
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
