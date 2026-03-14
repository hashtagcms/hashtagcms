import { EditorHelper, PageManager, CollapsibleSection } from '@hashtagcms/admin-ui-kit/helpers';
window.EditorHelper = EditorHelper;
window.PageManager = PageManager;
window.CollapsibleSection = CollapsibleSection;

document.addEventListener('DOMContentLoaded', () => {
    CollapsibleSection.init();
});
