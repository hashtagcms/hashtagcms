<section class="mt-10 mb-10 p-10 bg-modern-bg">
    <div class="container mx-auto px-4 lg:px-12">
        <div class="modern-card p-10">
            <h2
                class="text-xl font-bold text-modern-blue bg-blue-50/50 px-5 py-2.5 rounded-xl mb-6 inline-block tracking-tight">
                {{$moduleInfo['dataType']}} {{ htcms_trans('hashtagcms::messages.module') }}</h2>
            <p class="text-lg text-slate-600 mb-8 leading-relaxed font-medium">
                {{ htcms_trans('hashtagcms::messages.service_later_desc') }} <br>
                <code
                    class="text-sm bg-blue-50 text-modern-blue px-3 py-1 rounded-lg border border-blue-100/50 mt-2 inline-block font-mono">{{$moduleInfo['dataHandler']}}</code>
            </p>

            <button id="serviceLater"
                class="modern-button !px-10 !py-4 shadow-lg shadow-modern-blue/10 hover:shadow-modern-blue/20">
                <i class="fa fa-cloud-download group-hover:-translate-y-1 transition-transform"></i>
                <span>{{ htcms_trans('hashtagcms::messages.click_to_load') }}</span>
            </button>
            {{-- Premium Code Window --}}
            <div id="serviceLaterWrapper" class="hidden mt-12 transition-all duration-700 transform translate-y-4 opacity-0">
                <div class="relative group bg-slate-900 rounded-[2rem] shadow-2xl overflow-hidden border border-slate-800">
                    {{-- Chrome/Terminal Header --}}
                    <div class="bg-slate-800/50 backdrop-blur-md px-6 py-4 border-b border-slate-700/50 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-3.5 h-3.5 rounded-full bg-rose-500/80 shadow-sm shadow-rose-500/20"></div>
                            <div class="w-3.5 h-3.5 rounded-full bg-amber-500/80 shadow-sm shadow-amber-500/20"></div>
                            <div class="w-3.5 h-3.5 rounded-full bg-emerald-500/80 shadow-sm shadow-emerald-500/20"></div>
                        </div>
                        <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] select-none">Module Response</div>
                        <div class="w-12"></div> {{-- Spacer for symmetry --}}
                    </div>
                    
                    {{-- Code Content Area --}}
                    <div class="relative p-8 lg:p-10 overflow-x-auto custom-scrollbar">
                        <pre class="relative focus:outline-none"><code id="serviceLaterContent" class="block text-slate-300 font-mono text-sm leading-relaxed whitespace-pre font-medium"></code></pre>
                        
                        {{-- Subtle background decoration --}}
                        <div class="absolute top-0 right-0 p-8 pointer-events-none opacity-10">
                            <i class="fa fa-code text-8xl text-blue-400 rotate-12"></i>
                        </div>
                    </div>
                    
                    {{-- Footer Info --}}
                    <div class="bg-slate-900/50 px-6 py-3 border-t border-slate-800/50 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Response from: {{ $moduleInfo["dataHandler"] }}</span>
                        </div>
                        <div id="contentStats" class="text-[9px] font-bold text-slate-600 uppercase tracking-widest"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(15, 23, 42, 0.1); }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(51, 65, 85, 0.5); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(51, 65, 85, 0.8); }
</style>

<script>
    let url = '{!! $moduleInfo["dataHandler"] ?? "" !!}';
    function loadServiceLater() {
        const btn = document.getElementById("serviceLater");
        const wrapper = document.getElementById("serviceLaterWrapper");
        const content = document.getElementById("serviceLaterContent");
        const stats = document.getElementById("contentStats");

        const originalHtml = btn.innerHTML;

        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i><span>{{ htcms_trans("hashtagcms::messages.please_wait") }}</span>';
        btn.classList.add('opacity-75', 'cursor-not-allowed', 'pointer-events-none');

        // Reset and show wrapper with loading state if needed
        content.innerHTML = "";
        
        function reqListener() {
            // Processing Response
            let response = this.responseText;
            
            // Try to format if it's JSON
            try {
                const jsonObj = JSON.parse(response);
                response = JSON.stringify(jsonObj, null, 4);
            } catch (e) {
                // Not JSON, keep as is
            }

            content.textContent = response;
            
            // Visual feedback
            wrapper.classList.remove('hidden', 'translate-y-4', 'opacity-0');
            wrapper.classList.add('translate-y-0', 'opacity-100');
            
            // Set Stats
            const size = (new Blob([response]).size / 1024).toFixed(2);
            stats.innerText = `Size: ${size} KB | Length: ${response.length}`;

            btn.innerHTML = '<i class="fa fa-check"></i><span>{{ htcms_trans("hashtagcms::messages.loaded_successfully") }}</span>';
            btn.classList.remove('opacity-75', 'cursor-not-allowed', 'pointer-events-none');
            btn.classList.add('bg-emerald-500', 'hover:bg-emerald-600');

            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.classList.remove('bg-emerald-500', 'hover:bg-emerald-600');
                const span = btn.querySelector('span');
                if (span) span.innerText = '{{ htcms_trans("hashtagcms::messages.click_to_load_again") }}';
            }, 3000);
        }

        const req = new XMLHttpRequest();
        req.addEventListener("load", reqListener);
        req.open("GET", url);
        req.send();
    }

    document.getElementById("serviceLater").addEventListener("click", loadServiceLater);
</script>