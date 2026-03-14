@if(isset($isBlogHome) && $isBlogHome == 1)
    <!-- No comments on home -->
@else
    @php
        $commentsCount = isset($data) ? sizeof($data) : 0;
        $hasComments = $commentsCount > 0;
    @endphp
    
    <section class="py-12 bg-white" id="comments">
        <div class="container mx-auto px-4">
            @if($hasComments)
                <div class="max-w-4xl mx-auto">
                    <div class="flex items-center justify-between mb-12 pb-6 border-b border-ocean-bg/30">
                        <h3 class="text-3xl font-bold text-ocean-navy flex items-center space-x-3">
                            <i class="fa fa-comments text-ocean-blue"></i>
                            <span>{{ htcms_trans('hashtagcms::messages.all_reflections') }}</span>
                        </h3>
                        <span class="bg-ocean-navy text-white px-5 py-1.5 rounded-full text-lg font-bold shadow-lg shadow-ocean-navy/20">
                            {{$commentsCount}}
                        </span>
                    </div>

                    <div class="space-y-12">
                        @foreach($data as $comment)
                            <div class="flex flex-col sm:flex-row gap-8 items-start group">
                                <!-- Author Side -->
                                <div class="sm:w-48 flex-shrink-0 text-center sm:text-left space-y-4">
                                    <div class="relative inline-block sm:block">
                                        <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto sm:mx-0 rounded-2xl overflow-hidden shadow-xl shadow-ocean-blue/10 transform transition-transform group-hover:rotate-3">
                                            @php
                                                $gravatarHash = md5(strtolower(trim($comment['email'])));
                                            @endphp
                                            <img src="https://www.gravatar.com/avatar/{{$gravatarHash}}?d=mp&s=150" alt="{{$comment['name']}}" class="w-full h-full object-cover" />
                                        </div>
                                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-ocean-accent rounded-full border-4 border-white flex items-center justify-center text-ocean-navy text-xs font-bold">
                                            <i class="fa fa-quote-right"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-ocean-navy text-lg leading-tight">{{$comment['name']}}</h4>
                                        <div class="flex items-center justify-center sm:justify-start space-x-2 text-ocean-blue/50 text-xs font-bold uppercase tracking-wider mt-1">
                                            <i class="fa fa-calendar-o"></i>
                                            <span>{{getFormattedDate($comment['created_at'])}}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Comment Body -->
                                <div class="flex-grow">
                                    <div class="modern-card bg-ocean-bg/10 p-8 relative rounded-tr-none sm:rounded-tl-none group-hover:bg-white transition-colors duration-500">
                                        <!-- Decorative notch -->
                                        <div class="absolute top-0 -left-2 w-0 h-0 border-t-[10px] border-t-transparent border-r-[10px] border-r-ocean-bg/20 hidden sm:block group-hover:border-r-white transition-colors"></div>
                                        
                                        <div class="text-lg text-ocean-navy/80 leading-relaxed italic">
                                            "{{$comment['comment']}}"
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="max-w-4xl mx-auto py-20 text-center border-2 border-dashed border-ocean-bg/40 rounded-3xl">
                    <div class="w-20 h-20 bg-ocean-bg/20 rounded-full flex items-center justify-center mx-auto mb-6 text-ocean-blue/40">
                        <i class="fa fa-pencil text-3xl"></i>
                    </div>
                    <p class="text-xl font-bold text-ocean-navy/40 uppercase tracking-widest">
                        {{ htcms_trans('hashtagcms::messages.silence_desc') }}
                    </p>
                </div>
            @endif
        </div>
    </section>
@endif
