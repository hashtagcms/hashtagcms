<div class="mb-8">
    @if ($errors->any())
        <div class="bg-red-50 border border-red-100/50 text-red-600 px-6 py-4 rounded-xl text-sm font-semibold shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <i class="fa fa-exclamation-circle text-red-500"></i>
                <span class="uppercase tracking-widest text-[10px] font-black">Validation Errors</span>
            </div>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $key=>$error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
