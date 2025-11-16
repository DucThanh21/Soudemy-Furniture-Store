<?php
$pageTitle = "About Us - Soudemy";
include 'includes/header.php';
?>

<!-- About Hero Section with Carousel -->
<section class="hero about-hero">
    <div class="hero-carousel">
        <!-- Slide 1 -->
        <div class="hero-slide active" style="background-image: url('images/Sofa/sofa13.png');">
            <div class="container">
                <div class="hero-content">
                    <h1>About Us</h1>
                    <p>Discover our story of craftsmanship, quality, and commitment to modern living.</p>
                </div>
            </div>
        </div>

        <!-- Navigation Dots -->
        <div class="carousel-dots">
            <span class="dot active" onclick="currentSlide(0)"></span>
        </div>
    </div>
</section>

<style>
/* Hero Carousel Styles - Same as Homepage */
.hero {
    position: relative;
    width: 100%;
    height: 600px;
    overflow: hidden;
    background: #f5f5f5;
}

.hero-carousel {
    position: relative;
    width: 100%;
    height: 100%;
}

.hero-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0;
    transition: opacity 0.8s ease-in-out;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-slide.active {
    opacity: 1;
    z-index: 10;
}

.hero-slide::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
    z-index: 1;
}

.hero-slide .container {
    position: relative;
    z-index: 2;
    text-align: center;
}

.hero-content {
    color: white;
    max-width: 600px;
    margin: 0 auto;
}

.hero-content h1 {
    font-size: 3.5rem;
    font-weight: 800;
    margin: 0 0 20px 0;
    text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
    letter-spacing: 1px;
    text-transform: uppercase;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.hero-content p {
    font-size: 1.2rem;
    margin: 0 0 30px 0;
    font-weight: 300;
    text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
    line-height: 1.6;
}

.carousel-dots {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 15px;
    z-index: 20;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255,255,255,0.6);
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.dot.active {
    background: #d4a574;
    width: 14px;
    height: 14px;
    box-shadow: 0 0 8px rgba(212,165,116,0.8);
}

.dot:hover {
    background: rgba(255,255,255,0.9);
}

@media (max-width: 768px) {
    .hero {
        height: 400px;
    }
    .hero-content h1 {
        font-size: 2rem;
    }
    .hero-content p {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .hero {
        height: 300px;
    }
    .hero-content h1 {
        font-size: 1.5rem;
    }
    .hero-content p {
        font-size: 0.9rem;
    }
    .carousel-dots {
        bottom: 15px;
        gap: 10px;
    }
}
</style>

<!-- Carousel Script -->
<script>
let currentSlideIndex = 0;
const autoSlideInterval = 5000; // 5 seconds

function showSlide(index) {
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.dot');
    
    if (index >= slides.length) {
        currentSlideIndex = 0;
    } else if (index < 0) {
        currentSlideIndex = slides.length - 1;
    } else {
        currentSlideIndex = index;
    }
    
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    slides[currentSlideIndex].classList.add('active');
    dots[currentSlideIndex].classList.add('active');
}

function currentSlide(index) {
    showSlide(index);
    resetAutoSlide();
}

function nextSlide() {
    showSlide(currentSlideIndex + 1);
}

function resetAutoSlide() {
    clearInterval(window.autoSlideTimer);
    // Chỉ auto-slide nếu có nhiều hơn 1 slide
    if (document.querySelectorAll('.hero-slide').length > 1) {
        window.autoSlideTimer = setInterval(nextSlide, autoSlideInterval);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    showSlide(0);
    resetAutoSlide();
});
</script>
</style>
    </style>

<!-- Features -->
<section class="features">
    <div class="container">
        <div class="feature-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa fa-clock"></i>
                </div>
                <h3>Shop online</h3>
                <p>Browse our complete collection from the comfort of your home with easy online ordering</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa fa-shipping-fast"></i>
                </div>
                <h3>Free shipping</h3>
                <p>Enjoy complimentary delivery on all orders over $500 to your doorstep</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa fa-undo"></i>
                </div>
                <h3>Return policy</h3>
                <p>30-day hassle-free returns with full money-back guarantee</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa fa-credit-card"></i>
                </div>
                <h3>PAYMENT</h3>
                <p>Secure payment processing with multiple options including credit cards and PayPal</p>
            </div>
        </div>
    </div>
</section>

<!-- Auto-play Video Section - Full Width với Scroll Animation -->
<section class="video-hero-section">
    <div class="video-container">
        <video autoplay muted loop playsinline poster="images/video-preview.jpg">
            <source src="images/video/4685376-uhd_4096_2160_30fps.mp4" type="video/mp4">
            <source src="images/video/about-us.webm" type="video/webm">
            Your browser does not support the video tag.
        </video>
        <div class="video-overlay">
            <!-- Hero panel Ở GÓC DƯỚI - CHỈ CÓ TEXT -->
            <div class="hero-panel" id="heroPanel">
                <div class="panel-content">
                    <h2 class="panel-title">Where Comfort Meets Style</h2>
                    <p class="panel-text">Premium furniture crafted with passion and precision</p>
                </div>
            </div>

            <!-- play/pause button -->
            <button id="bannerToggle" class="video-btn" aria-label="Play/Pause video">❚❚</button>
        </div>
    </div>
</section>

<style>
/* Video Hero Section với Scroll Animation */
.video-hero-section {
    position: relative;
    width: 100vw;
    margin-left: calc(-50vw + 50%);
    height: 90vh;
    overflow: hidden;
    background: #000;
}

.video-hero-section .video-container {
    width: 100%;
    height: 100%;
    position: relative;
}

.video-hero-section video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: brightness(1) contrast(1.05) saturate(1.1);
    display: block;
}

/* Video Overlay */
.video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    padding: 40px;
    z-index: 3;
}

/* Hero Panel - ĐẶT Ở GÓC DƯỚI BÊN TRÁI - CHỈ TEXT */
.hero-panel {
    position: absolute;
    bottom: 50px;
    left: 50px;
    background: rgba(0,0,0,0.75);
    border: 1px solid rgba(212,165,116,0.5);
    padding: 24px 32px;
    border-radius: 12px;
    max-width: 550px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.5), inset 0 1px 2px rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Khi scroll vào view */
.hero-panel.visible {
    opacity: 1;
    transform: translateY(0);
}

.panel-content {
    width: 100%;
}

.hero-panel h2 {
    margin: 0 0 10px 0;
    font-family: 'Playfair Display', 'Georgia', serif;
    font-size: 1.9rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    font-weight: 700;
    color: #fff;
    transform: translateX(-30px);
    opacity: 0;
    transition: all 0.8s ease 0.2s;
}

.hero-panel.visible h2 {
    transform: translateX(0);
    opacity: 1;
}

.hero-panel p {
    margin: 0;
    font-family: 'Montserrat', 'Arial', sans-serif;
    color: rgba(255,255,255,0.92);
    font-weight: 300;
    font-size: 1rem;
    line-height: 1.6;
    transform: translateY(15px);
    opacity: 0;
    transition: all 0.8s ease 0.4s;
}

.hero-panel.visible p {
    transform: translateY(0);
    opacity: 1;
}

/* Play/Pause Button */
.video-btn {
    position: absolute;
    bottom: 30px;
    right: 30px;
    width: 48px;
    height: 48px;
    background: rgba(212,165,116,0.85);
    border: none;
    border-radius: 50%;
    color: #fff;
    font-size: 1.1rem;
    cursor: pointer;
    z-index: 5;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
}

.video-btn:hover {
    background: rgba(212,165,116,1);
    transform: scale(1.08);
}

/* Responsive */
@media (max-width: 1024px) {
    .video-hero-section { height: 80vh; }
    .hero-panel {
        bottom: 40px;
        left: 40px;
        padding: 22px 30px;
        max-width: 500px;
    }
    .hero-panel h2 { 
        font-size: 1.7rem;
    }
    .hero-panel p { 
        font-size: 0.95rem; 
    }
}

@media (max-width: 768px) {
    .video-hero-section { height: 70vh; }
    .video-overlay { padding: 20px; }
    .hero-panel {
        bottom: 30px;
        left: 20px;
        right: 20px;
        max-width: calc(100% - 40px);
        padding: 20px 26px;
        border-radius: 10px;
    }
    .hero-panel h2 { 
        font-size: 1.5rem; 
        letter-spacing: 1px;
        margin-bottom: 8px;
    }
    .hero-panel p { 
        font-size: 0.9rem;
        line-height: 1.5;
    }
}

@media (max-width: 480px) {
    .video-hero-section { height: 60vh; }
    .video-overlay { padding: 15px; }
    .hero-panel {
        bottom: 20px;
        left: 15px;
        right: 15px;
        padding: 16px 20px;
        border-radius: 8px;
    }
    .hero-panel h2 { 
        font-size: 1.2rem; 
        letter-spacing: 0.8px;
    }
    .hero-panel p { 
        font-size: 0.85rem;
    }
    .video-btn {
        width: 42px;
        height: 42px;
        bottom: 20px;
        right: 20px;
        font-size: 1rem;
    }
}
</style>

<!-- Google Fonts Import -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">

<!-- Scroll Animation Script CHỈ CHO VIDEO BANNER -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const heroPanel = document.querySelector('.hero-panel');
    const videoSection = document.querySelector('.video-hero-section');
    
    if (heroPanel && videoSection) {
        const observerOptions = {
            threshold: 0.3,
            rootMargin: '0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    heroPanel.classList.add('visible');
                } else {
                    heroPanel.classList.remove('visible');
                }
            });
        }, observerOptions);
        
        observer.observe(videoSection);
    }
    
    // Video play/pause control
    const video = document.querySelector('.video-hero-section video');
    const toggleBtn = document.getElementById('bannerToggle');
    
    if (video && toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            if (video.paused) {
                video.play();
                toggleBtn.textContent = '❚❚';
            } else {
                video.pause();
                toggleBtn.textContent = '▶';
            }
        });
    }
});
</script>

<!-- Functionality Section -->
<section class="functionality-section">
    <div class="container">
        <div class="functionality-layout">
            <div class="functionality-content">
                <h2>Functionality<br>meets perfection</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse massa libero, mattis vulputat id. Egestas adipiscing placerat eleifend a nascetur. Mattis proin enim, nam porttitor vitae.</p>
            </div>
            
            <div class="functionality-stats">
                <div class="stat-item">
                    <div class="stat-header">
                        <h3>Creativity</h3>
                        <span class="progress-percent">72 %</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress" style="width: 72%"></div>
                    </div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-header">
                        <h3>Advertising</h3>
                        <span class="progress-percent">84 %</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress" style="width: 84%"></div>
                    </div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-header">
                        <h3>Design</h3>
                        <span class="progress-percent">72 %</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress" style="width: 72%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Blog Posts Preview -->
<section class="blog-preview about-blog">
    <div class="container">
        <div class="blog-header">
            <h2 class="section-title">last blog post</h2>
            <div class="blog-nav">
                <button class="blog-nav-btn"><i class="fa fa-chevron-left"></i></button>
                <button class="blog-nav-btn"><i class="fa fa-chevron-right"></i></button>
            </div>
        </div>
        
        <div class="blog-grid">
            <div class="blog-card">
                <div class="blog-image">
                    <img src="images/Sofa/sofa5.png" alt="Living Room Design">
                </div>
                <div class="blog-date">Sep 26, 2022</div>
                <h3>Paint your office in natural colors only</h3>
                <a href="#" class="read-more">Read more</a>
            </div>
            
            <div class="blog-card">
                <div class="blog-image">
                    <img src="images/Sofa/sofa6.png" alt="Office Design">
                </div>
                <div class="blog-date">Sep 26, 2022</div>
                <h3>Paint your office in natural colors only</h3>
                <a href="#" class="read-more">Read more</a>
            </div>
            
            <div class="blog-card">
                <div class="blog-image">
                    <img src="images/Sofa/sofa8.png" alt="Interior Design">
                </div>
                <div class="blog-date">Sep 26, 2022</div>
                <h3>Paint your office in natural colors only</h3>
                <a href="#" class="read-more">Read more</a>
            </div>
        </div>
    </div>
</section>

<script>
</script>

<?php include 'includes/footer.php'; ?>