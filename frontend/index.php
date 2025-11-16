<?php
$pageTitle = "Soudemy - Modern Furniture Store";
include 'includes/header.php';

// Dữ liệu sản phẩm featured với ID chính xác
$featuredProducts = [
    [
        'id' => 4,
        'name' => 'Modern Bed', 
        'price' => 899.00, 
        'image' => 'images/bed/giuong1.png', 
        'category' => 'bed'
    ],
    [
        'id' => 3,
        'name' => 'Contemporary Lamp', 
        'price' => 156.00, 
        'image' => 'images/lamp/lamp1.png', 
        'category' => 'lamp'
    ],
    [
        'id' => 1,
        'name' => 'Comfort Sofa', 
        'price' => 750.00, 
        'image' => 'images/Sofa/sofa1.png', 
        'category' => 'sofa'
    ]
];
?>

<!-- Hero Section with Carousel -->
<section class="hero">
    <div class="hero-carousel">
        <!-- Slide 1 -->
        <div class="hero-slide active" style="background-image: url('images/design/homepage1.jpg');">
            <div class="container">
                <div class="hero-content">
                    <h1>ALL FOR YOUR HOME</h1>
                    <p>Discover our premium collection of modern furniture designed for your comfort and style.</p>
                    <a href="shop.php" class="btn btn-primary">VIEW MORE</a>
                </div>
            </div>
        </div>

        <!-- Navigation Dots -->
        <div class="carousel-dots">
            <span class="dot active" onclick="currentSlide(0)"></span>
        </div>
    </div>
</section>

<!-- Products of the Week -->
<section class="products-week">
    <div class="container">
        <h2 class="section-title">PRODUCTS OF THE WEEK</h2>
        <p class="section-desc">Explore our carefully selected furniture pieces that combine style, comfort, and quality craftsmanship.</p>
        
        <div class="product-grid">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="product-card">
                <div class="product-image">
                    <a href="product.php?id=<?php echo $product['id']; ?>">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    </a>
                </div>
                <h3><?php echo $product['name']; ?></h3>
                <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Full-width Banner Section - Tràn viền toàn bộ -->
<div class="section-banner">
    <!-- Video Background Section -->
    <div class="banner-video-container">
        <video autoplay muted loop playsinline class="video-banner">
            <source src="images/video/7533210-uhd_3840_2160_30fps.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    
    <!-- Text Content Overlay với Container -->
    <div class="banner-content">
        <div class="container">
            <h2 class="text-line-1">Experience Luxury & Comfort</h2>
            <p class="text-line-2">Premium furniture crafted with attention to detail</p>
        </div>
    </div>
</div>

<!-- Furniture Categories -->
<section class="furniture-categories">
    <div class="container">
        <div class="category-item">
            <div class="category-content">
                <h2>STYLISH CHAIRS</h2>
                <p>Comfortable and elegant seating solutions that blend perfectly with modern interior design.</p>
                <a href="shop.php?category=sofa" class="btn btn-outline">View more</a>
            </div>
            <div class="category-image">
                <img src="images/Sofa/sofa2.png" alt="Stylish Chair">
            </div>
        </div>
        
        <div class="category-item">
            <div class="category-content">
                <h2>ELEGANT TABLES</h2>
                <p>Functional and beautiful tables that serve as the centerpiece of your dining and living spaces.</p>
                <a href="shop.php?category=table" class="btn btn-outline">View more</a>
            </div>
            <div class="category-image">
                <img src="images/table/table2.png" alt="Elegant Table">
            </div>
        </div>
        
        <div class="category-item">
            <div class="category-content">
                <h2>CONTEMPORARY LAMPS</h2>
                <p>Illuminate your space with our modern lighting solutions that add warmth and ambiance.</p>
                <a href="shop.php?category=lamp" class="btn btn-outline">View more</a>
            </div>
            <div class="category-image">
                <img src="images/lamp/lamp3.png" alt="Contemporary Lamp">
            </div>
        </div>
    </div>
</section>

<!-- Express Delivery Banner -->
<section class="express-delivery">
    <div class="container">
        <div class="delivery-content">
            <p>order now for an express delivery in 24h !</p>
            <a href="shop.php" class="btn btn-outline">View more</a>
        </div>
    </div>
</section>

<!-- Features -->
<section class="features">
    <div class="container">
        <div class="feature-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa fa-clock"></i>
                </div>
                <h3>Shop online</h3>
                <p>Browse our complete collection from the comfort of your home</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa fa-shipping-fast"></i>
                </div>
                <h3>Free shipping</h3>
                <p>Enjoy free delivery on all orders over $500</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa fa-undo"></i>
                </div>
                <h3>Return policy</h3>
                <p>30-day return guarantee for your peace of mind</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fa fa-credit-card"></i>
                </div>
                <h3>PAYMENT</h3>
                <p>Secure payment options including credit cards and PayPal</p>
            </div>
        </div>
    </div>
</section>

<!-- Blog Posts Preview -->
<section class="blog-preview">
    <div class="container">
        <h2 class="section-title">last blog post</h2>
        
        <div class="blog-grid">
            <div class="blog-card">
                <div class="blog-image">
                    <img src="images/table/table3.png" alt="Interior Design Tips">
                </div>
                <div class="blog-date">Oct 15, 2024</div>
                <h3>How to choose the perfect dining table</h3>
                <a href="#" class="read-more">Read more</a>
            </div>
            
            <div class="blog-card">
                <div class="blog-image">
                    <img src="images/bed/giuong2.png" alt="Bedroom Design">
                </div>
                <div class="blog-date">Oct 12, 2024</div>
                <h3>Creating a cozy bedroom sanctuary</h3>
                <a href="#" class="read-more">Read more</a>
            </div>
            
            <div class="blog-card">
                <div class="blog-image">
                    <img src="images/bookshelf/ke1.png" alt="Storage Solutions">
                </div>
                <div class="blog-date">Oct 10, 2024</div>
                <h3>Smart storage solutions for small spaces</h3>
                <a href="#" class="read-more">Read more</a>
            </div>
        </div>
    </div>
</section>
<style>
.product-price {
    color: #000000 !important;
}

/* Video Banner Styles - Full width với scroll animation */
.section-banner {
    position: relative;
    width: 100vw;
    height: 500px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: calc(-50vw + 50%);
    margin-right: calc(-50vw + 50%);
    margin-top: 60px;
    margin-bottom: 60px;
}

/* Video background - full coverage */
.section-banner .banner-video-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.section-banner .video-banner {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    display: block;
}

/* Text content overlay - BAN ĐẦU ẨN */
.section-banner .banner-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    max-width: 900px;
    text-align: center;
    z-index: 10;
    opacity: 0;
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Khi scroll vào view - TEXT XUẤT HIỆN */
.section-banner .banner-content.visible {
    opacity: 1;
}

/* Heading với font đẹp hơn */
.section-banner .banner-content h2 {
    font-family: 'Playfair Display', 'Georgia', serif;
    font-size: 3.5rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 20px 0;
    text-shadow: 3px 3px 15px rgba(0, 0, 0, 0.9);
    text-transform: uppercase;
    letter-spacing: 3px;
    line-height: 1.2;
    transform: translateY(50px);
    opacity: 0;
    transition: all 0.8s ease 0.2s;
}

.section-banner .banner-content.visible h2 {
    transform: translateY(0);
    opacity: 1;
}

/* Paragraph với font hiện đại */
.section-banner .banner-content p {
    font-family: 'Montserrat', 'Arial', sans-serif;
    font-size: 1.4rem;
    font-weight: 300;
    color: #f5f5f5;
    margin: 0;
    text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.8);
    letter-spacing: 1px;
    line-height: 1.6;
    transform: translateY(50px);
    opacity: 0;
    transition: all 0.8s ease 0.4s;
}

.section-banner .banner-content.visible p {
    transform: translateY(0);
    opacity: 1;
}

/* Responsive */
@media (max-width: 768px) {
    .section-banner {
        height: 350px;
    }
    
    .section-banner .banner-content h2 {
        font-size: 2rem;
        letter-spacing: 1px;
    }
    
    .section-banner .banner-content p {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .section-banner .banner-content h2 {
        font-size: 1.5rem;
    }
    
    .section-banner .banner-content p {
        font-size: 0.9rem;
    }
}
</style>

<!-- Scroll Animation JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bannerContent = document.querySelector('.section-banner .banner-content');
    const sectionBanner = document.querySelector('.section-banner');
    
    // Intersection Observer để phát hiện khi banner vào viewport
    const observerOptions = {
        threshold: 0.3, // Kích hoạt khi 30% banner hiện ra
        rootMargin: '0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Khi scroll TỚI banner -> Text chạy VÀO
                bannerContent.classList.add('visible');
            } else {
                // Khi scroll QUA banner -> Text chạy RA
                bannerContent.classList.remove('visible');
            }
        });
    }, observerOptions);
    
    // Bắt đầu quan sát section banner
    observer.observe(sectionBanner);
});
</script>

<!-- Google Fonts Import (Thêm vào head nếu chưa có) -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@300;400&display=swap" rel="stylesheet">

<!-- Carousel JavaScript -->
<script>
    let currentSlideIndex = 0;
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.dot');
    const totalSlides = slides.length;

    // Auto-rotate slides every 5 seconds (only if more than 1 slide)
    if (totalSlides > 1) {
        setInterval(function() {
            currentSlideIndex = (currentSlideIndex + 1) % totalSlides;
            showSlide(currentSlideIndex);
        }, 5000);
    }

    // Function to show specific slide
    function showSlide(index) {
        // Remove active class from all slides and dots
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        // Add active class to current slide and dot
        slides[index].classList.add('active');
        dots[index].classList.add('active');
    }

    // Function called by dot clicks
    function currentSlide(index) {
        currentSlideIndex = index;
        showSlide(currentSlideIndex);
    }

    // Initialize first slide
    showSlide(0);
</script>

<?php include 'includes/footer.php'; ?>