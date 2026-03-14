<section class="py-12 bg-white">
    <div class="container mx-auto px-4 lg:px-8">
        <article class="modern-card group overflow-hidden max-w-4xl mx-auto transition-all duration-500 hover:shadow-2xl hover:shadow-ocean-blue/10">
            <div class="flex flex-col md:flex-row">
                <!-- Thumbnail -->
                <div class="md:w-1/3 relative overflow-hidden">
                    <a href="single-post.html" class="block h-full">
                        <img src="%{resource_path}%/img/blog/standard/blog_post_1.jpg" class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-110" alt="Blog Post">
                        <div class="absolute inset-0 bg-ocean-navy/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    </a>
                </div>
                
                <!-- Content -->
                <div class="md:w-2/3 p-8 lg:p-10 flex flex-col justify-center">
                    <div class="flex items-center space-x-3 mb-4">
                        <span class="px-3 py-1 bg-ocean-blue/10 text-ocean-blue text-xs font-bold uppercase tracking-wider rounded-lg">{{ htcms_trans('hashtagcms::messages.finance') }}</span>
                        <span class="px-3 py-1 bg-ocean-accent/20 text-ocean-navy text-xs font-bold uppercase tracking-wider rounded-lg">{{ htcms_trans('hashtagcms::messages.management') }}</span>
                    </div>
                    
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-ocean-navy mb-4 group-hover:text-ocean-blue transition-colors">
                        <a href="single-post.html">{{ htcms_trans('hashtagcms::messages.blog_title_placeholder') }}</a>
                    </h2>
                    
                    <div class="flex items-center text-ocean-navy/40 text-sm font-medium mb-6">
                        <i class="fa fa-calendar-o mr-2"></i>
                        <span>{{ htcms_trans('hashtagcms::messages.blog_date_placeholder') }}</span>
                    </div>
                    
                    <p class="text-ocean-navy/60 leading-relaxed mb-8 line-clamp-3">
                        {{ htcms_trans('hashtagcms::messages.blog_desc_placeholder') }}
                    </p>

                    <div>
                        <a href="single-post.html" class="inline-flex items-center font-bold text-ocean-blue hover:text-ocean-navy transition-colors space-x-2 group/btn">
                            <span>{{ htcms_trans('hashtagcms::messages.read_deeply') }}</span>
                            <i class="fa fa-arrow-right transform transition-transform group-hover/btn:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </article>
    </div>
</section>
