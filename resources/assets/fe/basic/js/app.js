/**
 * HashtagCms Frontend SDK Setup
 * Import core SDK modules from @hashtagcms/web-sdk
 */
import { Newsletter, Analytics, AppConfig, FormValidator, validateForm } from '@hashtagcms/web-sdk';

// Initialize and expose SDK modules globally on window.HashtagCms
// This maintains consistency with the UMD/CDN build where window.HashtagCms is used
window.HashtagCms = {
    ...(window.HashtagCms || {}),
    // Form Components
    Newsletter: Newsletter,
    FormSubmitter: Newsletter,  // Alias for backward compatibility
    FormValidator: FormValidator,
    validateForm: validateForm,
    // Analytics - instantiated for method calls
    Analytics: new Analytics(),
    // Configuration - instantiated with existing configData if available
    AppConfig: new AppConfig(window.HashtagCms?.configData || {})
};
