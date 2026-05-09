// Init Icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

// Translate.js Logic
window.initTranslate = function() {
    if (typeof translate !== 'undefined') {
        translate.selectLanguageTag.show = false; // Disable default select
        translate.service.use('client.edge'); // Use Edge client for better China access
        translate.execute();

        // Auto-detect browser language and switch if not manually set
        // Check if user has already selected a language (translate.js usually sets a cookie 'translate_language')
        if (!getCookie('translate_language')) {
            var userLang = navigator.language || navigator.userLanguage;
            var targetLang = null;

            // Simple mapping
            if (userLang.indexOf('en') === 0) targetLang = 'english';
            else if (userLang.indexOf('ja') === 0) targetLang = 'japanese';
            else if (userLang.indexOf('ko') === 0) targetLang = 'korean';
            else if (userLang.indexOf('fr') === 0) targetLang = 'french';
            else if (userLang.indexOf('de') === 0) targetLang = 'german';
            else if (userLang.indexOf('ru') === 0) targetLang = 'russian';
            else if (userLang.indexOf('es') === 0) targetLang = 'spanish';
            else if (userLang.indexOf('pt') === 0) targetLang = 'portuguese';
            else if (userLang.indexOf('vi') === 0) targetLang = 'vietnamese';
            else if (userLang.indexOf('id') === 0) targetLang = 'indonesian';
            else if (userLang.indexOf('th') === 0) targetLang = 'thai';
            else if (userLang.indexOf('ar') === 0) targetLang = 'arabic';
            else if (userLang.indexOf('zh') === 0) {
                if (userLang.toLowerCase().indexOf('tw') > -1 || userLang.toLowerCase().indexOf('hk') > -1) {
                    targetLang = 'chinese_traditional';
                } else {
                    // Default is simplified, no need to switch if already on it
                    targetLang = 'chinese_simplified'; 
                }
            }

            if (targetLang && targetLang !== 'chinese_simplified') {
                setTimeout(function() {
                    translate.changeLanguage(targetLang);
                }, 500);
            }
        }
    }
}

function getCookie(name) {
    var v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
    return v ? v[2] : null;
}

window.changeLang = function(lang) {
    if (typeof translate === 'undefined') return;
    
    // Map custom lang codes to translate.js codes
    const langMap = {
        'en': 'english',
        'zh-CN': 'chinese_simplified',
        'zh-TW': 'chinese_traditional'
    };
    
    const targetLang = langMap[lang] || lang;
    translate.changeLanguage(targetLang);
}

// Back to Top Logic
document.addEventListener('DOMContentLoaded', function() {
    const backToTopBtn = document.getElementById('backToTop');
    if (backToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopBtn.classList.remove('opacity-0', 'invisible');
                backToTopBtn.classList.add('opacity-100', 'visible');
            } else {
                backToTopBtn.classList.add('opacity-0', 'invisible');
                backToTopBtn.classList.remove('opacity-100', 'visible');
            }
        });
    }
});

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}