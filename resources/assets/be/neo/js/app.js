/** Axios setup **/
import axios from "axios";
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + token.content;
    axios.defaults.withCredentials = true;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
window.axios = axios;


// Helpers - Imported from @hashtagcms/admin-sdk (Pure Logic)
import { AdminConfig, Storage, Fetcher } from '@hashtagcms/admin-sdk';
// Helpers - Imported from @hashtagcms/admin-ui-kit (UI Bound)
import { Toast } from '@hashtagcms/admin-ui-kit';

window.AdminConfig = new AdminConfig();
window.ToastGloabl = Toast;
window.log = console.log;
window.Store = new Storage();
window.Fetcher = new Fetcher();

import { createApp } from 'vue';

// Import all components from the new package
import {
    TopNav, LeftNav, TitleBar, ToastBox, Loader, CopyPaste, TimerButton, LeftMenuShowHide,
    Homepage, InfoBoxes, InfoBox, ModalBox,
    TabularView, ActionBar, SearchBar,
    Pagination,
    ModulePermission, ModuleCreator, FrontendModuleCreator,
    MenuSorter,
    SitewiseData, SitewiseCopier, SiteCloner,
    LanguageCopier,
    CategoryPlatform, CategorySettings,
    ImageGallery
} from '@hashtagcms/admin-ui-kit';

// Alias resolving if components had different names in import
const PaginationView = Pagination;
const SiteWiseData = SitewiseData;
const SiteWiseCopier = SitewiseCopier;

const app = createApp({
    components: {
        'top-nav': TopNav,
        'title-bar': TitleBar,
        'admin-modules': LeftNav,
        'info-box': InfoBox,
        'info-boxes': InfoBoxes,
        'table-view': TabularView,
        'search-bar': SearchBar,
        'action-bar': ActionBar,
        'pagination-view': PaginationView,
        'toast-box': ToastBox,
        'admin-loader': Loader,
        'modal-box': ModalBox,
        'module-permission': ModulePermission,
        'menu-sorter': MenuSorter,
        'site-wise': SiteWiseData,
        'site-wise-copier': SiteWiseCopier,
        'site-cloner': SiteCloner,
        'language-copier': LanguageCopier,
        'module-creator': ModuleCreator,
        'front-module-creator': FrontendModuleCreator,
        'copy-paste': CopyPaste,
        'timer-button': TimerButton,
        'category-platform': CategoryPlatform,
        'category-settings': CategorySettings,
        'left-menu-toggle': LeftMenuShowHide,
        'page-manager': Homepage,
        'image-gallery': ImageGallery
    }
}).mount('#app');

window.Vue = app;

