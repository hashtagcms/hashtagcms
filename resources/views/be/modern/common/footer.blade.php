<footer class="shrink-0 bg-white border-t border-slate-200 py-5 px-10">
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                &copy; {{ date('Y') }} HashtagCms v{{ config('hashtagcmscommon.version') }}
            </span>
            <div class="h-4 w-[1px] bg-slate-200 hidden md:block"></div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 flex items-center gap-1">
                Made with <i class="fa fa-heart text-rose-500 animate-pulse"></i> by 
                <a href="https://www.hashtagcms.org?utm_source=adminpanel&utm_medium=footer&utm_campaign=hashtagcms" target="_blank" class="text-blue-600 hover:text-blue-700 transition-colors">HashtagCms</a>
            </span>
        </div>
        
        <div class="flex items-center gap-6">
            <a href="https://github.com/hashtagcms/hashtagcms/blob/master/readme.md?utm_source=adminpanel&utm_medium=footer&utm_campaign=hashtagcms" target="_blank" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-slate-600">Documentation</a>
            <a href="https://github.com/hashtagcms/hashtagcms/issues?utm_source=adminpanel&utm_medium=footer&utm_campaign=hashtagcms" target="_blank" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-slate-600">Support</a>
        </div>
    </div>
</footer>
