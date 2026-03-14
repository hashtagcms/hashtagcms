<template>
    <div class="space-y-6">
        <!-- Site Configured Section -->
        <div v-if="isInstalled" class="bg-white shadow-xl rounded-xl overflow-hidden transition-all duration-300">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">{{title}}</h3>
                <a :href="domainName" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-2">
                    Visit Site
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
            <div class="p-8">
                <div class="text-gray-600 leading-relaxed" v-html="message"></div>
            </div>
        </div>

        <!-- Loading State -->
        <div class="bg-white shadow-xl rounded-xl overflow-hidden" v-show="loading===true">
            <div class="p-12 text-center">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent mb-4"></div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Please wait.</h3>
                <p class="text-gray-500" id="waitMessage">Saving Info...</p>
            </div>
        </div>

        <!-- Installation Form -->
        <div class="bg-white shadow-xl rounded-xl overflow-hidden transition-all duration-500" v-if="!loading && isInstalled===false">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-800">{{getTitle()}}</h3>
            </div>
            <div class="p-8">
                <form @submit.prevent>
                    <!-- Step 1: Site Info -->
                    <div v-show="currentStep===1" class="space-y-5 animate-fadeIn">
                        <div>
                            <label for="site_title" class="block text-sm font-semibold text-gray-700 mb-1">Site Title</label>
                            <input id="site_title" name="site_title" type="text" 
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all duration-200 outline-none" 
                                v-model="form.site_title" required @input="hideErrorMessage" placeholder="e.g. My Awesome Site" />
                            <p v-if="errors.site_title" class="mt-1 text-sm text-red-600 italic font-medium">{{errors.site_title}}</p>
                        </div>
                        <div>
                            <label for="site_name" class="block text-sm font-semibold text-gray-700 mb-1">Site Name</label>
                            <input id="site_name" name="site_name" type="text" 
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all duration-200 outline-none" 
                                v-model="form.site_name" required @input="hideErrorMessage" placeholder="e.g. AwesomeSite" />
                            <p v-if="errors.site_name" class="mt-1 text-sm text-red-600 italic font-medium">{{errors.site_name}}</p>
                        </div>
                        <div>
                            <label for="site_domain" class="block text-sm font-semibold text-gray-700 mb-1">Site Domain</label>
                            <input id="site_domain" name="site_domain" type="text" 
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all duration-200 outline-none" 
                                v-model="form.site_domain" required @input="hideErrorMessage" placeholder="e.g. www.awesomesite.com" />
                            <p v-if="errors.site_domain" class="mt-1 text-sm text-red-600 italic font-medium">{{errors.site_domain}}</p>
                        </div>
                        <div>
                            <label for="site_context" class="block text-sm font-semibold text-gray-700 mb-1">Site Context</label>
                            <input name="site_context" id="site_context" type="text" 
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all duration-200 outline-none" 
                                v-model="form.site_context" required @input="hideErrorMessage" />
                            <p class="mt-2 text-xs text-gray-500 bg-gray-50 p-2 rounded border-l-4 border-blue-400">
                                <strong>Note:</strong> If you change this here, please make sure to update your <code>.env</code> file accordingly.
                            </p>
                            <p v-if="errors.site_context" class="mt-1 text-sm text-red-600 italic font-medium">{{errors.site_context}}</p>
                        </div>
                    </div>

                    <!-- Step 2: User Info -->
                    <div v-show="currentStep===2" class="space-y-5 animate-fadeIn">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
                            <input id="name" name="full_name" type="text" 
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all duration-200 outline-none" 
                                v-model="form.name" required @input="hideErrorMessage" />
                            <p v-if="errors.name" class="mt-1 text-sm text-red-600 italic font-medium">{{errors.name}}</p>
                        </div>
                        <div>
                            <label for="user_email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                            <input id="user_email" name="user_email" type="email" 
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all duration-200 outline-none" 
                                v-model="form.user_email" required @input="hideErrorMessage" placeholder="admin@example.com" />
                            <p v-if="errors.user_email" class="mt-1 text-sm text-red-600 italic font-medium">{{errors.user_email}}</p>
                        </div>
                        <div>
                            <label for="user_password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                            <input id="user_password" name="user_password" type="password" 
                                class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all duration-200 outline-none" 
                                v-model="form.user_password" required @input="hideErrorMessage" />
                            <p v-if="errors.user_password" class="mt-1 text-sm text-red-600 italic font-medium">{{errors.user_password}}</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-10 flex items-center justify-between border-t border-gray-100 pt-6">
                        <div class="flex gap-4">
                            <button type="button" @click="goToPrevStep" v-if="currentStep === 2"
                                class="px-6 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-200">
                                Previous
                            </button>
                            
                            <button type="button" @click="goToNextStep" v-if="currentStep === 1"
                                class="px-8 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-lg shadow-blue-200 transition-all duration-200">
                                Next
                            </button>

                            <button type="button" @click="saveSite" v-if="currentStep === 2"
                                class="px-8 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-lg shadow-green-200 transition-all duration-200">
                                Complete Installation
                            </button>
                        </div>

                        <div class="text-sm font-semibold text-gray-400">
                            Step {{currentStep}} of 2
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
</template>

<script setup>
    import { ref, reactive, computed, onMounted } from 'vue';
    import Form from "../helpers/form";

    const props = defineProps({
        dataSiteInfo: String,
        dataIsInstalled: [String, Number]
    });

    const currentStep = ref(1);
    const message = ref(`Your site is configured.<br />
                    <code>Powered By <a target="_blank" href='https://www.hashtagcms.org/?utm_source=${window.location.href}'>HashtagCMS</a></code>
                    `);

    const siteInfo = ref((typeof props.dataSiteInfo === "undefined" || props.dataSiteInfo === "") ? [] : JSON.parse(props.dataSiteInfo));
    const isInstalled = ref((typeof props.dataIsInstalled === "undefined" || props.dataIsInstalled === "") ? false : !!parseInt(props.dataIsInstalled));

    const form = ref(new Form({
        site_name: "",
        site_title:"",
        site_context:"",
        site_domain:"",
        user_email:"",
        user_password:"",
        name:""
    }));

    const errors = reactive({});
    const errorMessage = ref('');
    const saveURL = "/install/save";
    const loading = ref(false);
    const title = ref('Congratulations!');

    const domainName = computed(() => {
        let protocol = window.location.protocol;
        // let port = window.location.port;
        let domain = (siteInfo.value.domain && siteInfo.value.domain.indexOf("http") >= 0) ? siteInfo.value.domain : protocol+"//"+siteInfo.value.domain;
        //domain = (domain+"/example").replace("//example", "/example");
        return domain;
    });

    const getTitle = () => {
        return currentStep.value === 1 ? "Site Info" : "User Info";
    };

    const showLoader = (show) => {
        loading.value = show;
    };

    const goToPrevStep = () => {
        currentStep.value = currentStep.value - 1;
    };

    const goToNextStep = () => {
        currentStep.value = currentStep.value + 1;
    };

    const init = () => {
        if (siteInfo.value) {
            form.value.site_name = siteInfo.value.name;
            form.value.site_title = siteInfo.value.lang ? siteInfo.value.lang.title : "";
            form.value.site_context = siteInfo.value.context;
            form.value.site_domain = siteInfo.value.domain;
        }
    };

    const showError = (res) => {
        showLoader(false);
        if (res.errors) {
            for(let i in res.errors) {
                if(Object.prototype.hasOwnProperty.call(res.errors, i)) {
                    errors[i] = res.errors[i][0];
                    if(i.indexOf("site_")>=0) {
                        currentStep.value = 1;
                    }
                }
            }
        }
        errorMessage.value = res.message || '';
    };

    const hideErrorMessage = (event) => {
        let name = event.target.getAttribute("name");
        if (name && errors[name]) {
            errors[name] = "";
        }
        if(errorMessage.value !== '') {
            errorMessage.value = '';
        }
    };

    const saveSite = () => {
        showLoader(true);
        form.value.submit("post", saveURL)
            .then(function (response) {
                if(response['error']) {
                    showLoader(false);
                    message.value = response.error;
                    title.value = response.title;
                    isInstalled.value = true;
                } else {
                    isInstalled.value = !!response.isInstalled;
                    siteInfo.value = response.siteInfo;
                    showLoader(false);
                }
            })
            .catch(response => showError(response));
    };

    onMounted(() => {
        init();
    });
</script>
