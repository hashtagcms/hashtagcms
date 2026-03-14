@php
    $results = $data['results'];
    $hasData = count($results) > 0;
    $firstStory = $hasData ? $results[0] : null;
    $restStories = $hasData ? array_slice($results, 1) : [];
@endphp

<section class="py-20 bg-ocean-surf/50" id="stories">
    <div class="container mx-auto px-4 lg:px-8">
        
        @if($hasData)
            <!-- Featured First Story -->
            <div class="mb-16">
                <article class="modern-card group hover:shadow-2xl transition-all duration-500 overflow-hidden relative border-t-4 border-t-ocean-accent">
                    <!-- Subtle background depth -->
                    <div class="absolute inset-0 bg-gradient-to-br from-ocean-surf/20 to-ocean-blue/5 pointer-events-none"></div>
                    
                    <div class="p-10 lg:p-16 relative z-10">
                        <div class="flex flex-wrap items-center justify-start mb-6 gap-4">
                            <div class="flex items-center space-x-2 text-sm font-bold text-ocean-accent bg-ocean-accent/10 px-4 py-1.5 rounded-full border border-ocean-accent/20 uppercase tracking-widest shadow-sm">
                                <i class="fa fa-star"></i>
                                <span>Featured Story</span>
                            </div>
                            <div class="flex items-center space-x-2 text-sm font-bold text-ocean-navy bg-white px-4 py-1.5 rounded-full border border-ocean-light/30 shadow-sm">
                                <i class="fa fa-calendar-o text-ocean-blue"></i>
                                <span>{{getFormattedDate($firstStory['createdAt'])}}</span>
                            </div>
                            <div class="flex items-center space-x-2 text-ocean-navy/80 text-sm font-bold bg-white px-4 py-1.5 rounded-full border border-ocean-light/30 shadow-sm">
                                <i class="fa fa-comment-o text-ocean-blue"></i>
                                <span>{{$firstStory['commentsCount'] ?? 0}} Comments</span>
                            </div>
                        </div>
                        
                        <h2 class="text-4xl lg:text-6xl font-black text-ocean-deep mb-8 leading-tight tracking-tight group-hover:text-ocean-blue transition-colors duration-300">
                            <a href="{{htcms_get_path($firstStory['categoryLinkRewrite'].'/'.$firstStory['linkRewrite'])}}">
                                {{$firstStory['title']}}
                            </a>
                        </h2>
                        
                        <div class="prose prose-xl text-ocean-deep/80 mb-12 line-clamp-3">
                            {!! $firstStory['description'] !!}
                        </div>

                        <div class="flex flex-col sm:flex-row items-center justify-between pt-8 border-t border-ocean-light/30 gap-6">
                            <div class="flex items-center space-x-4">
                                @if(!empty($firstStory['author']))
                                    <div class="w-14 h-14 bg-gradient-to-br from-ocean-blue to-ocean-light rounded-full flex items-center justify-center text-white shadow-md">
                                        <i class="fa fa-user text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-ocean-navy/60 uppercase font-bold tracking-widest mb-1">Written By</p>
                                        <p class="text-lg font-bold text-ocean-navy">
                                            {{$firstStory['author']}}
                                        </p>
                                    </div>
                                @endif
                            </div>
                            <a href="{{htcms_get_path($firstStory['categoryLinkRewrite'].'/'.$firstStory['linkRewrite'])}}" class="modern-button shadow-lg">
                                Read Full Story <i class="fa fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Grid of Remaining Stories -->
            @if(count($restStories) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($restStories as $story)
                        <article class="modern-card group hover:shadow-xl transition-all duration-500 flex flex-col h-full bg-white relative">
                            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-ocean-light to-ocean-blue opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="p-8 flex-1 flex flex-col">
                                <div class="flex items-center justify-between mb-6">
                                    <div class="flex items-center space-x-2 text-xs font-bold text-ocean-navy bg-ocean-surf px-3 py-1.5 rounded border border-ocean-light/30">
                                        <i class="fa fa-calendar-o text-ocean-blue"></i>
                                        <span>{{getFormattedDate($story['createdAt'])}}</span>
                                    </div>
                                    @if(($story['commentsCount'] ?? 0) > 0)
                                        <div class="flex items-center space-x-1 text-ocean-navy/60 text-xs font-bold">
                                            <i class="fa fa-comment"></i>
                                            <span>{{$story['commentsCount']}}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <h3 class="text-2xl font-black text-ocean-deep mb-4 group-hover:text-ocean-blue transition-colors leading-tight line-clamp-2">
                                    <a href="{{htcms_get_path($story['categoryLinkRewrite'].'/'.$story['linkRewrite'])}}">
                                        {{$story['title']}}
                                    </a>
                                </h3>
                                
                                <div class="prose prose-md text-ocean-navy/70 mb-8 line-clamp-3 flex-1 font-light">
                                    {!!  $story['description'] !!}
                                </div>

                                <div class="flex items-center justify-between pt-6 border-t border-ocean-surf mt-auto">
                                    <div class="flex items-center space-x-2">
                                        @if(!empty($story['author']))
                                            <span class="text-xs font-bold text-ocean-blue uppercase tracking-widest bg-ocean-blue/10 px-3 py-1 rounded">
                                                By {{$story['author']}}
                                            </span>
                                        @endif
                                    </div>
                                    <a href="{{htcms_get_path($story['categoryLinkRewrite'].'/'.$story['linkRewrite'])}}" class="text-ocean-accent font-bold hover:text-ocean-deep flex items-center transition-colors group/link px-2">
                                        Read <i class="fa fa-angle-right ml-2 group-hover/link:translate-x-1 transition-transform"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        @else
            <div class="modern-card p-20 text-center max-w-2xl mx-auto shadow-xl">
                <div class="w-24 h-24 bg-ocean-surf rounded-full flex items-center justify-center mx-auto mb-6 text-ocean-blue shadow-inner border-2 border-white">
                    <i class="fa fa-newspaper-o text-4xl"></i>
                </div>
                <h3 class="text-3xl font-black text-ocean-deep mb-3 tracking-tight">No stories found</h3>
                <p class="text-lg text-ocean-navy/60 font-light">The depths are quiet at the moment. Check back later for new insights.</p>
            </div>
        @endif
    </div>
</section>
