@extends(htcms_admin_config('theme').'.index')

@section('content')

    <title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
               data-back-url="{{$backURL}}"></title-bar>
    @php


        $id = 0;
        $site_id = htcms_get_siteId_for_admin();
        $name = old('name', '');
        $image = old('image', '');
        $lottie = old('lottie', '');
        $body_css = old('body_css', '');
        $extra = old('extra', '');
        $start_date = old('start_date', date('Y-m-d'));
        $end_date = old('end_date', date('Y-m-d', strtotime("next month")));
        $publish_status = old('publish_status', 1);

        $width = old('width', '100%');
        $height = old('height', '100%');
        $background = old('background', 'transparent');
        $hide_on_complete = old('hide_on_complete', 1);

        $top = old('top', 0);
        $left = old('left', 0);
        $position_css = old('position_css', 'absolute');
        $z_index = old('z_index', 99999);

        $zIndex = old('zIndex', 99999);
        $play_mode = old('play_mode', 'normal'); //bounce
        $direction = old('direction', '1'); //backward

        $autoplay = old('autoplay', 1);
        $loop = old('loop', 1);
        $hover = old('hover', 0);
        $controls = old('controls', 0);

        //print_r($results);

        if(isset($results)) {
            extract($results);
            $start_date = date('Y-m-d', strtotime($start_date));
            $end_date = date('Y-m-d', strtotime($end_date));
        }

    @endphp


    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden max-w-4xl mx-auto">
        <!-- Card Header -->
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400">Festival Configuration</h3>
            <span class="text-[10px] font-black uppercase text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Interactive Elements</span>
        </div>

        <form action="{{htcms_get_save_path(request()->module_info->controller_name)}}" method="post" enctype="multipart/form-data" id="addEditForm">
            <div class="p-8 lg:p-10 space-y-10">
                {{csrf_field()}}
                {!! FormHelper::input('hidden', 'id', $id) !!}
                {!! FormHelper::input('hidden', 'backURL', $backURL) !!}
                {!! FormHelper::input('hidden', 'site_id', $site_id) !!}
                {!! FormHelper::input('hidden', 'actionPerformed', $actionPerformed) !!}

                <!-- Module Identity -->
                <section class="space-y-6">
                    <div class="flex items-center gap-3">
                        <i class="fa fa-star text-amber-500"></i>
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-800">General Properties</h4>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('name', 'Festival Name', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'name', $name, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required', 'placeholder' => 'Enter Name')) !!}
                        </div>
                        <div class="space-y-2">
                            {!! FormHelper::label('body_css', 'Body CSS Class', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'body_css', $body_css , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Body Css')) !!}
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('image', 'Background Image', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                                {!! FormHelper::file('image', $image, array('accept'=>'image/*', 'class' => 'text-xs'), TRUE, 100) !!}
                            </div>
                        </div>
                        <div class="space-y-2">
                            {!! FormHelper::label('lottie', 'Lottie Animation JSON', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                                {!! FormHelper::file('lottie', $lottie, array('class' => 'text-xs'), TRUE, 100) !!}
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Lottie Props -->
                <section class="p-8 bg-slate-50/50 rounded-2xl border border-slate-100 space-y-8">
                    <div class="flex items-center gap-3">
                        <i class="fa fa-sliders text-blue-500"></i>
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-800">Lottie Properties & Alignment</h4>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('width', 'Width', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'width', $width, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Width')) !!}
                        </div>
                        <div class="space-y-2">
                            {!! FormHelper::label('height', 'Height', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'height', $height, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Height')) !!}
                        </div>
                         <div class="space-y-2">
                            {!! FormHelper::label('top', 'Top (px/%)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'top', $top, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Top')) !!}
                        </div>
                        <div class="space-y-2">
                            {!! FormHelper::label('left', 'Left (px/%)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'left', $left, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Left')) !!}
                        </div>

                         <div class="space-y-2">
                            {!! FormHelper::label('position_css', 'CSS Position', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'position_css', $position_css, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Position Css')) !!}
                        </div>
                         <div class="space-y-2">
                            {!! FormHelper::label('z_index', 'Layer (z-index)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('text', 'z_index', $z_index, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'placeholder' => 'Enter Z Index')) !!}
                        </div>
                        <div class="space-y-2">
                            {!! FormHelper::label('background', 'Background Color', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            <div class="flex items-center gap-2">
                                {!! FormHelper::input('text', 'background', $background, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'id' => 'background', 'placeholder' => 'Enter Background')) !!}
                                {!! FormHelper::input('color', 'color_picker', null, array('oninput'=>'updateColor(this)', 'class' => 'w-10 h-10 rounded-xl border-0 bg-transparent p-0 cursor-pointer')) !!}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8 pt-4">
                        <div class="space-y-4">
                             <h5 class="text-[10px] font-black uppercase text-slate-500">Play Mode</h5>
                             <div class="flex flex-col gap-4">
                                <div class="flex items-center gap-2">
                                    {!! FormHelper::input('radio', 'play_mode', 'normal', array("id"=>"play_mode_normal", 'class'=>'w-4 h-4 text-blue-600 focus:ring-blue-500'), $play_mode) !!}
                                    {!! FormHelper::label('play_mode_normal', 'Normal', array('class' => 'text-sm font-medium text-slate-700')) !!}
                                </div>
                                <div class="flex items-center gap-2">
                                    {!! FormHelper::input('radio', 'play_mode', 'bounce', array("id"=>"play_mode_bounce", 'class'=>'w-4 h-4 text-blue-600 focus:ring-blue-500'), $play_mode) !!}
                                    {!! FormHelper::label('play_mode_bounce', 'Bounce', array('class' => 'text-sm font-medium text-slate-700')) !!}
                                </div>
                             </div>
                        </div>
                        <div class="space-y-4">
                             <h5 class="text-[10px] font-black uppercase text-slate-500">Direction</h5>
                             <div class="flex flex-col gap-4">
                                <div class="flex items-center gap-2">
                                    {!! FormHelper::input('radio', 'direction', '1', array("id"=>"direction_forward", 'class'=>'w-4 h-4 text-blue-600 focus:ring-blue-500'), $direction) !!}
                                    {!! FormHelper::label('direction_forward', 'Forward', array('class' => 'text-sm font-medium text-slate-700')) !!}
                                </div>
                                <div class="flex items-center gap-2">
                                    {!! FormHelper::input('radio', 'direction', '-1', array("id"=>"direction_backward", 'class'=>'w-4 h-4 text-blue-600 focus:ring-blue-500'), $direction) !!}
                                    {!! FormHelper::label('direction_backward', 'Backward', array('class' => 'text-sm font-medium text-slate-700')) !!}
                                </div>
                             </div>
                        </div>
                    </div>

                    <div class="space-y-6 pt-4">
                        <div class="flex flex-col gap-2">
                             <div class="flex items-center gap-2">
                                {!! FormHelper::checkbox('autoplay', $autoplay, array('class' => 'w-5 h-5 rounded text-blue-600 focus:ring-blue-500')) !!}
                                {!! FormHelper::label('autoplay', 'Autoplay', array('class' => 'text-sm font-medium text-slate-700')) !!}
                             </div>
                             <span class="text-[10px] text-slate-400 font-medium">Play on load</span>
                        </div>
                        <div class="flex flex-col gap-2">
                             <div class="flex items-center gap-2">
                                {!! FormHelper::checkbox('loop', $loop, array('class' => 'w-5 h-5 rounded text-blue-600 focus:ring-blue-500')) !!}
                                {!! FormHelper::label('loop', 'Loop', array('class' => 'text-sm font-medium text-slate-700')) !!}
                             </div>
                             <span class="text-[10px] text-slate-400 font-medium">Continuous play</span>
                        </div>
                         <div class="flex flex-col gap-2">
                             <div class="flex items-center gap-2">
                                {!! FormHelper::checkbox('hover', $hover, array('class' => 'w-5 h-5 rounded text-blue-600 focus:ring-blue-500')) !!}
                                {!! FormHelper::label('hover', 'Hover', array('class' => 'text-sm font-medium text-slate-700')) !!}
                             </div>
                             <span class="text-[10px] text-slate-400 font-medium">Play on mouseover</span>
                        </div>
                         <div class="flex flex-col gap-2">
                             <div class="flex items-center gap-2">
                                {!! FormHelper::checkbox('controls', $controls, array('class' => 'w-5 h-5 rounded text-blue-600 focus:ring-blue-500')) !!}
                                {!! FormHelper::label('controls', 'Show Controls', array('class' => 'text-sm font-medium text-slate-700')) !!}
                             </div>
                             <span class="text-[10px] text-slate-400 font-medium">Show player UI</span>
                        </div>
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                {!! FormHelper::checkbox('hide_on_complete', $hide_on_complete, array('class' => 'w-5 h-5 rounded text-blue-600 focus:ring-blue-500')) !!}
                                {!! FormHelper::label('hide_on_complete', 'Auto-hide Player', array('class' => 'text-sm font-medium text-slate-700')) !!}
                            </div>
                            <span class="text-[10px] text-slate-400 font-medium">Hide the player after animation completes</span>
                        </div>
                    </div>
                </section>

                <!-- Additional Data -->
                <section class="space-y-6">
                    <div class="space-y-2">
                        {!! FormHelper::label('extra', 'Additional Parameters (JSON/Strings)', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                        {!! FormHelper::textarea('extra', $extra, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'rows' => 3)) !!}
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            {!! FormHelper::label('start_date', 'Activation Date', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('date', 'start_date', $start_date , array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required')) !!}
                        </div>
                        <div class="space-y-2">
                            {!! FormHelper::label('end_date', 'Expiry Date', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                            {!! FormHelper::input('date', 'end_date', $end_date, array('class'=>'form-control w-full bg-white border transition-all duration-300 outline-none font-bold text-xs tracking-tight py-3.5 rounded-xl px-4 pl-3 hover:border-gray-300 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-900', 'required'=>'required')) !!}
                        </div>
                    </div>

                     <div class="space-y-2">
                         {!! FormHelper::label('publish_status', 'Launch Status', array('class' => 'text-sm font-medium text-slate-700 block')) !!}
                         <div class="flex items-center gap-3 bg-slate-50 p-4 rounded-xl border border-slate-100">
                             {!! FormHelper::input('checkbox', 'publish_status', $publish_status) !!}
                             <span class="text-xs font-black uppercase text-slate-600">Published</span>
                         </div>
                     </div>
                </section>
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

@push('scripts')
    <script>
        let backgroundInput;
        function updateColor(color) {
            if(!backgroundInput) {
                backgroundInput = document.getElementById("background");
            }
            backgroundInput.value = color.value;

        }

    </script>
@endpush
