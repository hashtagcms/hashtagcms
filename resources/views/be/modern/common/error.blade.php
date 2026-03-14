@extends(htcms_admin_config('theme').'.index')

@section('content')
<title-bar data-title="{!! htcms_get_module_name(request()->module_info) !!}"
           data-back-url="{{$backUrl ?? ''}}"
           data-show-copy="false"
           data-show-paste="false"
></title-bar>

<div class="bg-white rounded-2xl shadow-xl shadow-red-500/5 border border-red-100 overflow-hidden">
    <div class="px-8 py-5 border-b border-red-50 bg-red-50/50">
        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-red-800">{{$title ?? "Whoops!"}}</h3>
    </div>
    <div class="p-10 text-center">
        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fa fa-exclamation-triangle text-2xl"></i>
        </div>
        <p class="text-red-600 font-bold text-lg mb-2">{{$message ?? "There is some error. Please check the logs"}}</p>
        <p class="text-slate-400 text-sm">You can go back to the previous page or try again later.</p>
    </div>
</div>

@endsection

