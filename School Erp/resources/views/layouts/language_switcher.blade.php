<!-- Google Translate Element (Hidden) -->
<div id="google_translate_element" style="position: absolute !important; top: -9999px !important; left: -9999px !important; width: 1px !important; height: 1px !important; overflow: hidden !important; opacity: 0 !important; pointer-events: none !important;"></div>

<script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'en',
            includedLanguages: 'en,hi',
            autoDisplay: false
        }, 'google_translate_element');
    }
</script>
<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<style>
    /* Aggressive hiding of all Google Translate injected bars, frames, wrappers and tooltips */
    body > .skiptranslate:not(#custom-lang-switcher):not(.lang-switch-container) {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0 !important;
        width: 0 !important;
        overflow: hidden !important;
    }
    
    iframe.goog-te-banner-frame,
    .goog-te-banner-frame,
    .goog-te-banner-frame.skiptranslate,
    #goog-gt-tt,
    .goog-tooltip,
    .goog-tooltip:hover,
    .goog-te-balloon-frame {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0 !important;
        width: 0 !important;
        border: none !important;
        box-shadow: none !important;
    }
    
    /* Prevent Google Translate from pushing page content down */
    html {
        margin-top: 0px !important;
        padding-top: 0px !important;
    }
    body {
        top: 0 !important;
        position: static !important;
    }
    
    /* Remove high-light border on translation hover */
    .goog-text-highlight {
        background: none !important;
        box-shadow: none !important;
        box-sizing: border-box !important;
    }
    iframe.goog-te-menu-frame {
        display: none !important;
        visibility: hidden !important;
    }
    
    /* Custom sliding pill language switcher styling */
    .lang-switch-container {
        display: inline-flex !important;
        visibility: visible !important;
        align-items: center;
        background: rgba(15, 23, 42, 0.06);
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 20px;
        padding: 2px;
        position: relative;
        user-select: none;
        vertical-align: middle;
        width: 106px;
        height: 32px;
        box-sizing: border-box;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Dark mode support */
    body.dark-mode .lang-switch-container,
    .dark-mode .lang-switch-container,
    .hold-transition.dark-mode .lang-switch-container {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.12);
    }
    
    /* Sliding Indicator */
    .lang-switch-slider {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 49px;
        height: 26px;
        background: #3b82f6; /* Modern Blue */
        border-radius: 18px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1;
        box-shadow: 0 2px 5px rgba(59, 130, 246, 0.3);
    }
    
    body.dark-mode .lang-switch-slider,
    .dark-mode .lang-switch-slider,
    .hold-transition.dark-mode .lang-switch-slider {
        background: #3b82f6;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.5);
    }
    
    /* Active slide translation indicator */
    .lang-switch-container.lang-active-hi .lang-switch-slider {
        left: 53px;
    }
    
    .lang-switch-btn {
        flex: 1;
        background: transparent;
        border: none;
        color: #64748b;
        cursor: pointer;
        font-size: 11px;
        font-weight: 800;
        text-align: center;
        transition: color 0.25s ease;
        outline: none;
        z-index: 2;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 3px;
        line-height: 1;
    }
    
    body.dark-mode .lang-switch-btn,
    .dark-mode .lang-switch-btn,
    .hold-transition.dark-mode .lang-switch-btn {
        color: #94a3b8;
    }
    
    .lang-switch-btn.active {
        color: #ffffff !important;
    }
    
    .lang-switch-btn:hover:not(.active) {
        color: #0f172a;
    }
    
    body.dark-mode .lang-switch-btn:hover:not(.active),
    .dark-mode .lang-switch-btn:hover:not(.active),
    .hold-transition.dark-mode .lang-switch-btn:hover:not(.active) {
        color: #f1f5f9;
    }

    /* Fixed floating style for login/auth/standalone fallback pages */
    .lang-switch-container.lang-floating {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 99999;
        background: rgba(15, 23, 42, 0.8) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        backdrop-filter: blur(12px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3) !important;
        padding: 3px;
        height: 38px;
        width: 112px;
    }
    .lang-switch-container.lang-floating .lang-switch-slider {
        width: 52px;
        height: 30px;
        top: 3px;
        left: 3px;
    }
    .lang-switch-container.lang-floating.lang-active-hi .lang-switch-slider {
        left: 55px;
    }
    .lang-switch-container.lang-floating .lang-switch-btn {
        color: #cbd5e1;
        font-size: 11.5px;
    }
</style>

<script type="text/javascript">
    // Global language target state
    window.customLangActive = 'en';

    // Helper to read cookies
    function getGoogleTranslateCookie(name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length == 2) return parts.pop().split(";").shift();
        return "";
    }

    // Set cookie correctly for google translate API to pick up
    function setGoogleTranslateCookie(langCode) {
        var cookieVal = "/en/" + langCode;
        var domain = window.location.hostname;
        var domainParts = domain.split('.');
        
        // Remove existing cookies to clean up duplicate path/domain conflicts
        document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        if (domainParts.length > 1) {
            document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=." + domain;
            var mainDomain = domainParts.slice(-2).join('.');
            document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=." + mainDomain;
        }
        
        // Use /en/en for English to restore original state, and /en/hi for Hindi
        if (langCode === 'en') {
            cookieVal = "/en/en";
        }
        
        // Write cookie for paths
        document.cookie = "googtrans=" + cookieVal + "; path=/; expires=Fri, 31 Dec 9999 23:59:59 GMT";
        if (domainParts.length > 1) {
            var mainDomain = domainParts.slice(-2).join('.');
            document.cookie = "googtrans=" + cookieVal + "; path=/; domain=." + mainDomain + "; expires=Fri, 31 Dec 9999 23:59:59 GMT";
            document.cookie = "googtrans=" + cookieVal + "; path=/; domain=." + domain + "; expires=Fri, 31 Dec 9999 23:59:59 GMT";
        }
    }

    // Number Translation System (English numerals 0-9 <-> Hindi numerals ०-९)
    var englishDigits = ['0','1','2','3','4','5','6','7','8','9'];
    var hindiDigits = ['०','१','२','३','४','५','६','७','८','९'];
    
    function convertTextDigits(text, toHindi) {
        var from = toHindi ? englishDigits : hindiDigits;
        var to = toHindi ? hindiDigits : englishDigits;
        var result = text;
        for (var i = 0; i < 10; i++) {
            var reg = new RegExp(from[i], 'g');
            result = result.replace(reg, to[i]);
        }
        return result;
    }

    function walkTextNodes(node, toHindi) {
        if (!node) return;
        
        // Ignore style sheets, scripts, inputs, templates and code snippets
        var tagName = node.tagName ? node.tagName.toLowerCase() : '';
        if (tagName === 'script' || tagName === 'style' || tagName === 'textarea' || 
            tagName === 'noscript' || tagName === 'code' || tagName === 'pre' || tagName === 'template') {
            return;
        }
        
        // Ignore language switcher itself
        if (node.id === 'custom-lang-switcher' || (node.classList && node.classList.contains('lang-switch-container'))) {
            return;
        }

        if (node.nodeType === 3) { // TEXT_NODE
            var orig = node.nodeValue;
            var converted = convertTextDigits(orig, toHindi);
            if (orig !== converted) {
                node.nodeValue = converted;
            }
        } else {
            var child = node.firstChild;
            while (child) {
                var next = child.nextSibling;
                walkTextNodes(child, toHindi);
                child = next;
            }
        }
    }

    var numberObserver = null;
    
    function startNumberObserver() {
        if (numberObserver) return;
        
        // Perform initial conversion sweep based on the active language
        walkTextNodes(document.body, window.customLangActive === 'hi');
        
        // Setup observer to continuously sync numbers (handles dynamic loads & Google Translate restores)
        numberObserver = new MutationObserver(function(mutations) {
            numberObserver.disconnect();
            
            try {
                var toHindi = (window.customLangActive === 'hi');
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'characterData') {
                        var orig = mutation.target.nodeValue;
                        var converted = convertTextDigits(orig, toHindi);
                        if (orig !== converted) {
                            mutation.target.nodeValue = converted;
                        }
                    } else if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach(function(node) {
                            walkTextNodes(node, toHindi);
                        });
                    }
                });
            } finally {
                numberObserver.observe(document.body, {
                    childList: true,
                    subtree: true,
                    characterData: true
                });
            }
        });
        
        numberObserver.observe(document.body, {
            childList: true,
            subtree: true,
            characterData: true
        });
    }

    // Programmatically trigger Google Translate
    function changeLanguage(langCode) {
        setGoogleTranslateCookie(langCode);
        localStorage.setItem('preferred_language', langCode);
        window.customLangActive = langCode;
        
        // Sweep page immediately to transition numbers
        walkTextNodes(document.body, langCode === 'hi');
        
        var select = document.querySelector('.goog-te-combo');
        if (select) {
            // Trigger value change only if it differs
            if (select.value !== langCode) {
                select.value = langCode;
                
                // Dispatch native change event
                var event = document.createEvent('HTMLEvents');
                event.initEvent('change', true, true);
                select.dispatchEvent(event);
                
                // Dispatch bubbling change event
                var changeEvent = new Event('change', { bubbles: true, cancelable: true });
                select.dispatchEvent(changeEvent);
                
                // Dispatch jQuery change event if jQuery is loaded
                if (window.jQuery) {
                    window.jQuery(select).val(langCode).trigger('change');
                }
            }
            updateToggleButtonsUI(langCode);
        } else {
            // Retry if Google Translate isn't loaded yet
            setTimeout(function() {
                changeLanguage(langCode);
            }, 100);
        }
    }

    // Update active states of all switchers on the current page
    function updateToggleButtonsUI(langCode) {
        document.querySelectorAll('.lang-switch-container').forEach(function(container) {
            var enBtn = container.querySelector('.lang-en');
            var hiBtn = container.querySelector('.lang-hi');
            if (langCode === 'hi') {
                if (enBtn) enBtn.classList.remove('active');
                if (hiBtn) hiBtn.classList.add('active');
                container.classList.add('lang-active-hi');
            } else {
                if (hiBtn) hiBtn.classList.remove('active');
                if (enBtn) enBtn.classList.add('active');
                container.classList.remove('lang-active-hi');
            }
        });
    }

    // Dynamically insert switchers into active views depending on what elements exist
    function mountLanguageSwitcher() {
        if (document.getElementById('custom-lang-switcher')) return;

        var container = document.createElement('div');
        container.id = 'custom-lang-switcher';
        // Add notranslate and skiptranslate classes to prevent translation of the EN/HI buttons
        container.className = 'lang-switch-container notranslate skiptranslate';
        container.innerHTML = `
            <div class="lang-switch-slider"></div>
            <button class="lang-switch-btn lang-en active" onclick="changeLanguage('en')" title="Switch to English">
                EN
            </button>
            <button class="lang-switch-btn lang-hi" onclick="changeLanguage('hi')" title="हिन्दी में अनुवाद करें">
                HI
            </button>
        `;

        var mounted = false;

        // 1. Super Admin Navbar (Bootstrap/AdminLTE style)
        var superAdminNav = document.querySelector('.navbar-nav.ml-auto');
        if (superAdminNav) {
            var li = document.createElement('li');
            li.className = 'nav-item d-flex align-items-center';
            li.style.display = 'flex';
            li.style.alignItems = 'center';
            li.style.marginRight = '10px';
            li.appendChild(container);
            
            var themeToggle = document.getElementById('superadminThemeToggleBtn');
            if (themeToggle) {
                superAdminNav.insertBefore(li, themeToggle.closest('li'));
            } else {
                superAdminNav.appendChild(li);
            }
            mounted = true;
        }

        // 2. School/Parent Dashboard Header (.topbar-right)
        if (!mounted) {
            var topbarRight = document.querySelector('.topbar-right');
            if (topbarRight) {
                var wrap = document.createElement('div');
                wrap.className = 'lang-switch-wrap';
                wrap.style.marginRight = '10px';
                wrap.style.display = 'flex';
                wrap.style.alignItems = 'center';
                wrap.appendChild(container);
                
                var themeToggleWrap = topbarRight.querySelector('.theme-toggle-wrap') || 
                                      topbarRight.querySelector('.notif-wrap') || 
                                      topbarRight.querySelector('.user-wrap');
                if (themeToggleWrap) {
                    topbarRight.insertBefore(wrap, themeToggleWrap);
                } else {
                    topbarRight.appendChild(wrap);
                }
                mounted = true;
            }
        }

        // 3. Teacher Dashboard Top Header Header (.th-actions)
        if (!mounted) {
            var thActions = document.querySelector('.th-actions');
            if (thActions) {
                var wrap = document.createElement('div');
                wrap.className = 'lang-switch-wrap';
                wrap.style.marginRight = '10px';
                wrap.style.display = 'flex';
                wrap.style.alignItems = 'center';
                wrap.appendChild(container);
                
                var userPill = thActions.querySelector('.th-user-pill') || thActions.querySelector('.th-icon-btn');
                if (userPill) {
                    thActions.insertBefore(wrap, userPill);
                } else {
                    thActions.appendChild(wrap);
                }
                mounted = true;
            }
        }

        // 4. Welcome Landing Page Navigation (.navbar-collapse .d-flex)
        if (!mounted) {
            var welcomeNav = document.querySelector('.navbar-custom .navbar-collapse .d-flex');
            if (welcomeNav) {
                container.style.marginRight = '15px';
                welcomeNav.insertBefore(container, welcomeNav.firstChild);
                mounted = true;
            }
        }

        // 5. Fallback: Floating Corner Switcher for Auth/Standalone/Error Pages
        if (!mounted) {
            container.classList.add('lang-floating');
            document.body.appendChild(container);
        }

        // Read cookies and sync values
        var currentLang = 'en';
        var cookieVal = getGoogleTranslateCookie('googtrans');
        if (cookieVal && cookieVal.indexOf('/hi') !== -1) {
            currentLang = 'hi';
        } else {
            var localLang = localStorage.getItem('preferred_language');
            if (localLang === 'hi') {
                currentLang = 'hi';
            }
        }
        
        window.customLangActive = currentLang;
        updateToggleButtonsUI(currentLang);

        // Start dynamic observer to watch & convert digits
        startNumberObserver();

        // Apply translation if Hindi is requested
        if (currentLang === 'hi') {
            var attempts = 0;
            var interval = setInterval(function() {
                var select = document.querySelector('.goog-te-combo');
                attempts++;
                if (select) {
                    if (select.value !== 'hi') {
                        select.value = 'hi';
                        
                        // Dispatch native events
                        var event = document.createEvent('HTMLEvents');
                        event.initEvent('change', true, true);
                        select.dispatchEvent(event);
                        
                        var changeEvent = new Event('change', { bubbles: true, cancelable: true });
                        select.dispatchEvent(changeEvent);
                    }
                    clearInterval(interval);
                }
                if (attempts > 50) {
                    clearInterval(interval);
                }
            }, 100);
        }
    }

    // Force hide Google Translate's banner frame and reset body/html styles periodically
    function forceHideGoogleTranslateElements() {
        // 1. Target injected wrappers directly appended to body (Google appends its skiptranslate iframe here)
        var bodyChildren = document.body ? document.body.children : [];
        for (var i = 0; i < bodyChildren.length; i++) {
            var child = bodyChildren[i];
            if (child.classList.contains('skiptranslate') && 
                child.id !== 'custom-lang-switcher' && 
                !child.classList.contains('lang-switch-container')) {
                child.style.setProperty('display', 'none', 'important');
                child.style.setProperty('visibility', 'hidden', 'important');
                child.style.setProperty('height', '0px', 'important');
                child.style.setProperty('opacity', '0', 'important');
            }
        }

        // 2. Target specific frames
        var iframe = document.querySelector('iframe.goog-te-banner-frame') || 
                     document.querySelector('iframe[class*="goog-te-banner-frame"]');
        if (iframe) {
            iframe.style.setProperty('display', 'none', 'important');
            iframe.style.setProperty('visibility', 'hidden', 'important');
            iframe.style.setProperty('height', '0px', 'important');
            iframe.style.setProperty('opacity', '0', 'important');
            
            // If hiding the iframe's parent works better
            var parent = iframe.parentElement;
            if (parent && parent.id !== 'custom-lang-switcher' && !parent.classList.contains('lang-switch-container')) {
                parent.style.setProperty('display', 'none', 'important');
                parent.style.setProperty('visibility', 'hidden', 'important');
                parent.style.setProperty('height', '0px', 'important');
                parent.style.setProperty('opacity', '0', 'important');
            }
        }
        
        // 3. Remove high-light border and margins from html/body
        document.documentElement.style.setProperty('margin-top', '0px', 'important');
        document.documentElement.style.setProperty('padding-top', '0px', 'important');
        
        if (document.body) {
            document.body.style.setProperty('top', '0px', 'important');
            document.body.style.setProperty('position', 'static', 'important');
        }
    }

    // Initialize switcher
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mountLanguageSwitcher);
    } else {
        mountLanguageSwitcher();
    }
    
    // Register the force hide loop
    setInterval(forceHideGoogleTranslateElements, 100);
</script>
