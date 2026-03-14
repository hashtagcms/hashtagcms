@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"
               data-copy-paste-auto-init="true"
    ></title-bar>
    @php


        $id = 0;
        $name = old("name");
        $iso_code = old("iso_code");
        $iso_code_num = old("iso_code_num");
        $sign = old("sign");
        $blank = old("blank", 1);
        $format = old("format", 1);
        $decimals = old("decimals", 1);
        $conversion_rate = old("conversion_rate", 1);

        if(isset($results)) {
            extract($results);
        }


    @endphp

    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden max-w-2xl mx-auto">
        <!-- Card Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Currency Definition</h3>
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
                        {!! FormHelper::label('name', 'Currency Name', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::input('text', 'name', $name , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Name')) !!}
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                {!! FormHelper::label('iso_code', 'ISO Code (Alpha)', array('class' => 'text-sm font-medium text-slate-700')) !!}
                                <a href="https://en.wikipedia.org/wiki/ISO_4217" target="_blank" class="text-[10px] text-blue-500 hover:underline">Reference</a>
                            </div>
                            {!! FormHelper::input('text', 'iso_code', $iso_code, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Iso Code')) !!}
                        </div>
                        <div class="space-y-2">
                            {!! FormHelper::label('iso_code_num', 'ISO Code (Numeric)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'iso_code_num', $iso_code_num, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Iso Code Num')) !!}
                        </div>
                    </div>
                </div>

                <!-- Display Settings Group -->
                <div class="pt-6 border-t border-slate-50 space-y-4">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Formatting & Display</h4>
                    
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                {!! FormHelper::label('sign', 'Currency Symbol', array('class' => 'text-sm font-medium text-slate-700')) !!}
                                <a href="https://en.wikipedia.org/wiki/Currency_symbol" target="_blank" class="text-[10px] text-blue-500 hover:underline">Reference</a>
                            </div>
                            {!! FormHelper::input('text', 'sign', $sign, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Sign')) !!}
                        </div>
                        <div class="space-y-2">
                            {!! FormHelper::label('conversion_rate', 'Conversion Rate', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'conversion_rate', $conversion_rate, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Conversion Rate')) !!}
                        </div>
                    </div>

                    <div class="space-y-4 pt-4">
                        <div class="flex items-center gap-3 bg-slate-50/50 p-4 rounded-xl border border-slate-100">
                            {!! FormHelper::checkbox('blank', $blank, array('class' => 'w-5 h-5 rounded text-blue-600 focus:ring-blue-500')) !!}
                            {!! FormHelper::label('blank', 'Include Spacing between symbol and amount', array('class' => 'text-sm font-medium text-slate-700')) !!}
                        </div>
                        <div class="flex items-center gap-3 bg-slate-50/50 p-4 rounded-xl border border-slate-100">
                            {!! FormHelper::checkbox('format', $format, array('class' => 'w-5 h-5 rounded text-blue-600 focus:ring-blue-500')) !!}
                            {!! FormHelper::label('format', 'Apply standard currency formatting', array('class' => 'text-sm font-medium text-slate-700')) !!}
                        </div>
                        <div class="flex items-center gap-3 bg-slate-50/50 p-4 rounded-xl border border-slate-100">
                            {!! FormHelper::checkbox('decimals', $decimals, array('class' => 'w-5 h-5 rounded text-blue-600 focus:ring-blue-500')) !!}
                            {!! FormHelper::label('decimals', 'Show decimal places for this currency', array('class' => 'text-sm font-medium text-slate-700')) !!}
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
