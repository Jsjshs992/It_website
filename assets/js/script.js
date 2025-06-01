// script.js - ملف JavaScript المخصص مع تأثيرات متطورة
document.addEventListener('DOMContentLoaded', function() {
    // 0. إخفاء المحتوى مؤقتًا حتى اكتمال التهيئة
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.5s ease';

    // 1. تهيئة أدوات Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            trigger: 'hover focus'
        });
    });

    // 2. تأثير التمرير السلس للروابط
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
                
                // إضافة فئة نشطة للقائمة
                if (targetId !== '#mainSlider') {
                    document.querySelectorAll('nav a').forEach(link => {
                        link.classList.remove('active');
                    });
                    this.classList.add('active');
                }
            }
        });
    });

    // 3. تأثير الظهور التدريجي للعناصر عند التمرير
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.animate-on-scroll');
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            const delay = element.dataset.delay || 0;
            
            if (elementPosition < windowHeight - 100) {
                setTimeout(() => {
                    element.classList.add('animated');
                }, delay);
            }
        });
    };

    // 4. تأثير التحويم على البطاقات
    document.querySelectorAll('.hover-scale').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.03)';
            this.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
            this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.05)';
        });
    });

    // 5. تأثير الأزرار المتدرجة
    document.querySelectorAll('.btn-hover-gradient').forEach(button => {
    // حفظ الحالة الأصلية للزر
    const originalStyles = {
        backgroundImage: button.style.backgroundImage,
        transition: button.style.transition
    };

    button.addEventListener('mouseenter', function() {
        this.style.backgroundImage = 'linear-gradient(to right, #4facfe 0%, #00f2fe 100%)';
        this.style.transition = 'all 0.3s ease';
    });
    
    button.addEventListener('mouseleave', function() {
        // إعادة الحالة الأصلية
        this.style.backgroundImage = originalStyles.backgroundImage;
        this.style.transition = originalStyles.transition;
    });
});

    // 6. تحسين السلايدر
    const mainSlider = document.getElementById('mainSlider');
    if (mainSlider) {
        const carousel = new bootstrap.Carousel(mainSlider, {
            interval: 5000,
            pause: 'hover'
        });

        mainSlider.addEventListener('slide.bs.carousel', function() {
            const activeItem = this.querySelector('.carousel-item.active');
            activeItem.style.transition = 'opacity 0.5s ease';
            activeItem.style.opacity = '0';
        });

        mainSlider.addEventListener('slid.bs.carousel', function() {
            const activeItem = this.querySelector('.carousel-item.active');
            activeItem.style.opacity = '1';
        });
    }

    // 7. تأثير تحميل الصور بشكل أنيق
    const lazyLoadImages = function() {
        const lazyImages = document.querySelectorAll('img[loading="lazy"]');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '200px'
        });

        lazyImages.forEach(img => {
            if(img.complete) {
                img.classList.add('loaded');
            } else {
                img.addEventListener('load', function() {
                    this.classList.add('loaded');
                });
                observer.observe(img);
            }
        });
    };

    // 8. تأثير النافبار عند التمرير
    const navbar = document.querySelector('.navbar-custom');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                navbar.classList.add('navbar-scrolled');
                navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
                navbar.style.transition = 'all 0.3s ease';
            } else {
                navbar.classList.remove('navbar-scrolled');
                navbar.style.boxShadow = 'none';
            }
        });
    }

    // 9. تأثيرات النصوص المتدرجة
    const textGradients = document.querySelectorAll('.text-gradient');
    textGradients.forEach(text => {
        text.style.backgroundImage = 'linear-gradient(to right, #4facfe, #00f2fe)';
        text.style.webkitBackgroundClip = 'text';
        text.style.backgroundClip = 'text';
        text.style.color = 'transparent';
    });

    // 10. إضافة رسوم متحركة للعناوين
    const animateHeadings = function() {
        const headings = document.querySelectorAll('h1, h2, h3, h4, h5, h6');
        headings.forEach((heading, index) => {
            heading.style.opacity = '0';
            heading.style.transform = 'translateY(20px)';
            heading.style.transition = `opacity 0.5s ease ${index * 0.1}s, transform 0.5s ease ${index * 0.1}s`;
            
            setTimeout(() => {
                heading.style.opacity = '1';
                heading.style.transform = 'translateY(0)';
            }, 100);
        });
    };

    // تهيئة جميع التأثيرات
    lazyLoadImages();
    animateHeadings();
    animateOnScroll();
    window.addEventListener('scroll', animateOnScroll);

    // إظهار المحتوى بعد التهيئة
    setTimeout(() => {
        document.body.style.opacity = '1';
    }, 100);
});

// 11. تأثيرات الصفحة عند التحميل
window.addEventListener('load', function() {
    // إضافة فئة تم التحميل للجسم
    document.body.classList.add('page-loaded');

    // إخفاء شاشة التحميل إذا كانت موجودة
    const loader = document.querySelector('.page-loader');
    if (loader) {
        setTimeout(() => {
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        }, 300);
    }
});

// 12. تأثيرات النماذج
document.querySelectorAll('input, textarea, select').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
        if (!this.value) {
            this.parentElement.classList.remove('focused');
        }
    });
});
