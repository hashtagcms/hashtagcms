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

/**
 * Lightweight Parallax Utility
 */
class Parallax {
    constructor() {
        this.elements = document.querySelectorAll('.js-parallax');
        if (this.elements.length === 0) return;
        
        this.onScroll = this.onScroll.bind(this);
        window.addEventListener('scroll', () => {
            requestAnimationFrame(this.onScroll);
        }, { passive: true });
        
        this.onScroll(); // Initial position
    }

    onScroll() {
        const scrollY = window.pageYOffset;
        this.elements.forEach(el => {
            const speed = parseFloat(el.getAttribute('data-parallax-speed')) || 0.1;
            const yPos = -(scrollY * speed);
            el.style.transform = `translate3d(0, ${yPos}px, 0)`;
        });
    }
}

// Initialize Parallax on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    window.HashtagCms.Parallax = new Parallax();
});
