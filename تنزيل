<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$page_title = 'الرئيسية';
?>

<!-- السلايدر المحسّن -->
<section class="hero-section mb-5">
    <div id="mainSlider" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <?php
            $stmt = $pdo->query("SELECT * FROM slider WHERE is_active = 1 ORDER BY created_at DESC");
            $slides = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $active = 'active';
            
            foreach ($slides as $key => $slide) {
                echo '<button type="button" data-bs-target="#mainSlider" data-bs-slide-to="'.$key.'"
                      class="'.$active.'" aria-current="true" aria-label="Slide '.($key+1).'"></button>';
                $active = '';
            }
            ?>
        </div>
        <div class="carousel-inner rounded-3 overflow-hidden shadow-lg">
            <?php
            $active = 'active';
            foreach ($slides as $slide) {
                echo '<div class="carousel-item '.$active.'">
                    <img src="'.SITE_URL.'/assets/images/'.$slide['image'].'" class="d-block w-100" alt="'.$slide['title'].'" loading="lazy">
                    <div class="carousel-caption d-none d-md-block text-end">
                        <h2 class="display-4 fw-bold mb-3 text-white animate-on-scroll">'.$slide['title'].'</h2>
                        <p class="lead mb-4 text-white animate-on-scroll" data-delay="100">'.$slide['description'].'</p>';
                if (!empty($slide['button_text'])) {
                    echo '<a href="'.$slide['button_url'].'" class="btn btn-primary btn-lg px-4 btn-hover-gradient animate-on-scroll" data-delay="200">
                            <i class="fas fa-arrow-left me-2"></i>'.$slide['button_text'].'
                          </a>';
                }
                echo '</div>
                </div>';
                $active = '';
            }
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#mainSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">السابق</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">التالي</span>
        </button>
    </div>
</section>

<!-- الخدمات -->
<section class="services-section py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="display-5 fw-bold mb-3 text-gradient animate-on-scroll">خدماتنا</h2>
            <p class="lead text-muted animate-on-scroll" data-delay="100">نقدم حلولاً متكاملة تلبي جميع احتياجاتك</p>
        </div>
        <div class="row g-4">
            <?php
            $stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY created_at DESC LIMIT 4");
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($services as $service) {
                echo '<div class="col-lg-3 col-md-6 animate-on-scroll" data-delay="'.($service['id']*100).'">
                    <div class="card h-100 border-0 shadow-sm hover-scale">
                        <div class="card-body text-center p-4">
                            <div class="service-image-wrapper mb-4 mx-auto" style="width: 100%; height: 150px; overflow: hidden; border-radius: 8px;">';
                
                if (!empty($service['image'])) {
                    echo '<img src="'.SITE_URL.'/uploads/services/'.$service['image'].'" 
                          alt="'.$service['title'].'" 
                          class="img-fluid h-100 w-100 object-fit-cover">';
                } else {
                    // صورة افتراضية إذا لم توجد صورة
                    echo '<div class="bg-primary bg-opacity-10 d-flex align-items-center justify-content-center h-100">
                            <i class="fas fa-image fa-3x text-primary"></i>
                          </div>';
                }
                
                echo '</div>
                            <h3 class="h5 fw-bold mb-3 text-primary">'.$service['title'].'</h3>
                            <p class="text-muted mb-4">'.shortenText($service['short_description'], 100).'</p>
                            <a href="services.php?service_id='.$service['id'].'" class="btn btn-outline-primary btn-hover-gradient stretched-link">
                                <i class="fas fa-arrow-left me-2"></i>المزيد
                            </a>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
        <div class="text-center mt-5">
            <a href="services.php" class="btn btn-primary btn-lg px-4 btn-hover-gradient animate-on-scroll" data-delay="400">
                <i class="fas fa-list me-2"></i>عرض جميع الخدمات
            </a>
        </div>
    </div>
</section>

<!-- المشاريع -->
<section class="projects-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="display-5 fw-bold mb-3 text-gradient animate-on-scroll">أحدث المشاريع</h2>
            <p class="lead text-muted animate-on-scroll" data-delay="100">اكتشف إبداعاتنا وأعمالنا المميزة</p>
        </div>
        <div class="row g-4">
            <?php
            $stmt = $pdo->query("SELECT * FROM projects WHERE is_active = 1 ORDER BY created_at DESC LIMIT 3");
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($projects as $project) {
                echo '<div class="col-lg-4 col-md-6 animate-on-scroll" data-delay="'.($project['id']*100).'">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden hover-scale">
                        <div class="project-img-container">
                            <img src="'.SITE_URL.'/assets/images/'.$project['image'].'" class="card-img-top" alt="'.$project['title'].'" loading="lazy">
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h3 class="h5 fw-bold mb-0 text-primary">'.$project['title'].'</h3>
                                <span class="badge bg-primary bg-opacity-10 text-primary">'.$project['category'].'</span>
                            </div>
                            <p class="text-muted mb-4">'.shortenText($project['description'], 100).'</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="far fa-calendar-alt me-2"></i>'.formatDate($project['project_date']).'</span>
                                <a href="projects.php#project-'.$project['id'].'" class="btn btn-sm btn-outline-primary btn-hover-gradient stretched-link">
                                    <i class="fas fa-arrow-left me-2"></i>التفاصيل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
        <div class="text-center mt-5">
            <a href="projects.php" class="btn btn-primary btn-lg px-4 btn-hover-gradient animate-on-scroll" data-delay="300">
                <i class="fas fa-project-diagram me-2"></i>عرض جميع المشاريع
            </a>
        </div>
    </div>
</section>

<!-- المقالات -->
<section class="blog-section py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="display-5 fw-bold mb-3 text-gradient animate-on-scroll">أحدث المقالات</h2>
            <p class="lead text-muted animate-on-scroll" data-delay="100">آخر ما كتبناه في مدونتنا</p>
        </div>
        <div class="row g-4">
            <?php
            $stmt = $pdo->query("SELECT * FROM blog WHERE is_active = 1 ORDER BY created_at DESC LIMIT 3");
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($articles as $article) {
                echo '<div class="col-lg-4 col-md-6 animate-on-scroll" data-delay="'.($article['id']*100).'">
                    <div class="card h-100 border-0 shadow-sm hover-scale">
                        <div class="blog-img-container">
                            <img src="'.SITE_URL.'/assets/images/'.$article['image'].'" class="card-img-top" alt="'.$article['title'].'" loading="lazy">
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">'.$article['author'].'</span>
                                <span class="text-muted small"><i class="far fa-clock me-2"></i>'.formatDate($article['created_at']).'</span>
                            </div>
                            <h3 class="h5 fw-bold mb-3 text-primary">'.$article['title'].'</h3>
                            <p class="text-muted mb-4">'.shortenText($article['content'], 150).'</p>
                            <div class="d-flex align-items-center">
                                <a href="blog.php#article-'.$article['id'].'" class="btn btn-sm btn-outline-primary btn-hover-gradient stretched-link">
                                    <i class="fas fa-book-reader me-2"></i>قراءة المزيد
                                </a>
                                <span class="ms-auto small text-muted"><i class="far fa-eye me-2"></i>'.rand(50, 500).'</span>
                            </div>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
        <div class="text-center mt-5">
            <a href="blog.php" class="btn btn-primary btn-lg px-4 btn-hover-gradient animate-on-scroll" data-delay="300">
                <i class="fas fa-newspaper me-2"></i>عرض جميع المقالات
            </a>
        </div>
    </div>
</section>

<!-- شاشة التحميل -->
<div class="page-loader">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">جاري التحميل...</span>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>