<div class="py-24 bg-slate-50 min-h-screen">
    <div class="container mx-auto px-4 lg:px-12">
        <div class="max-w-3xl mx-auto">
            @if(isset($data) && count($data) > 0)
                @php
                    $data = $data[0];
                    $categoryId = $data['category_id'];
                    $contentId = $data['id'];

                    $storyObj = htcms_get_shared_data('MODULE_STORY');
                    $title = isset($storyObj) ? $storyObj[0]['name'] : htcms_get_category_info('name');
                @endphp

                <article class="modern-card bg-white border-slate-100 shadow-sm mb-12 overflow-hidden">
                    <div class="p-10 lg:p-16 border-b border-slate-50">
                        <p class="uppercase tracking-[0.2em] text-blue-600 text-[10px] font-bold mb-4">{{ htcms_trans('hashtagcms::messages.article') }}</p>
                        <h1 class="text-3xl lg:text-5xl font-bold text-slate-900 leading-tight mb-8 tracking-tight">
                            {{$title}}
                        </h1>
                        <div class="flex items-center gap-6 text-slate-400 text-sm font-medium">
                            <span class="flex items-center gap-2">
                                <i class="fa fa-calendar-o"></i>
                                <span>{{getFormattedDate($data['createdAt'] ?? now())}}</span>
                            </span>
                            @if(!empty($data['author']))
                                <span class="flex items-center gap-2">
                                    <i class="fa fa-user-circle-o"></i>
                                    <span>{{$data['author']}}</span>
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-10 lg:p-16">
                        <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed italic-quote">
                            {!! $data['page_content'] !!}
                        </div>
                    </div>
                </article>

                @if($data['enable_comments'] == 1)
                    <div class="modern-card bg-white border-slate-100 shadow-sm p-10 lg:p-16" id="comment-section">
                        <div class="mb-10">
                            <h2 class="text-2xl font-bold text-slate-900 mb-2">{{ htcms_trans('hashtagcms::messages.post_comment') }}</h2>
                            <p class="text-slate-500 text-sm">{{ htcms_trans('hashtagcms::messages.join_discussion') }}</p>
                        </div>

                        <form class="space-y-6" action="/comment/saveComment" method="post">
                            @csrf
                            <input type="hidden" name="category_id" value="{{$categoryId}}" />
                            <input type="hidden" name="page_id" value="{{$contentId}}" />
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="_htcms_form_comment_name_" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">{{ htcms_trans('hashtagcms::modules.Name') }}</label>
                                    <input id="_htcms_form_comment_name_" name="name" type="text" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all text-sm" required placeholder="{{ htcms_trans('hashtagcms::modules.Please enter your full name') }}" value="{{old('name', auth()->user()->name ?? '')}}" />
                                </div>
                                <div class="space-y-2">
                                    <label for="_htcms_form_comment_email_" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">{{ htcms_trans('hashtagcms::modules.Email') }}</label>
                                    <input id="_htcms_form_comment_email_" type="email" name="email" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all text-sm" required placeholder="{{ htcms_trans('hashtagcms::modules.Please enter your email') }}" value="{{old('email', auth()->user()->email ?? '')}}" />
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <label for="_htcms_form_comment_comment_" class="block text-xs font-bold text-slate-700 tracking-wide uppercase">{{ htcms_trans('hashtagcms::messages.your_comment') }}</label>
                                <textarea id="_htcms_form_comment_comment_" name="comment" rows="6" class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-4 focus:ring-blue-50 focus:border-blue-500 outline-none transition-all resize-none text-sm" required placeholder="{{ htcms_trans('hashtagcms::messages.write_something') }}">{{old('comment')}}</textarea>
                            </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center justify-between gap-6 pt-2">
                                <button class="modern-button w-full sm:w-auto" type="submit">
                                    {{ htcms_trans('hashtagcms::messages.post_comment_btn') }}
                                </button>
                                
                                @if(session('results'))
                                    <div class="px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold border border-emerald-100 flex items-center gap-2">
                                        <i class="fa fa-check-circle"></i>
                                        <span>{{session('results')['message']}}</span>
                                    </div>
                                @endif
                            </div>

                            @if ($errors->any())
                                <div class="p-4 bg-red-50 border border-red-100 rounded-lg mt-6">
                                    <ul class="list-disc list-inside text-xs text-red-600 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </form>
                    </div>
                @endif
            @else
                <div class="modern-card bg-white border-slate-100 shadow-sm p-20 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 border border-slate-100 text-slate-300">
                        <i class="fa fa-search text-2xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">{{ htcms_trans('hashtagcms::messages.content_not_found') }}</h2>
                    <p class="text-slate-500 mt-2 text-sm">{{ htcms_trans('hashtagcms::messages.content_not_found_desc') }}</p>
                    <div class="mt-8">
                        <a href="/" class="text-sm font-bold text-blue-600 hover:text-blue-700 transition-colors">{{ htcms_trans('hashtagcms::messages.return_home') }}</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
