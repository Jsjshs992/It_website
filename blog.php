<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$page_title = 'المدونة';
?>

<section class="mb-5 mt-4">
    <div class="container">
        <h1 class="text-center mb-5 text-gradient">المدونة</h1>
        
        <div class="row">
            <div class="col-lg-8">
                <?php
                // عرض مقال مفرد إذا تم تحديد معرف المقال
                if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                    $stmt = $pdo->prepare("SELECT * FROM blog WHERE id = ? AND is_active = 1");
                    $stmt->execute([$_GET['id']]);
                    $article = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($article) {
                        echo '<article class="card mb-5 hover-scale" id="article-'.$article['id'].'">
                            <div class="card-body">
                                <img src="'.SITE_URL.'/assets/images/'.$article['image'].'" class="img-fluid rounded mb-4 shadow-sm" alt="'.$article['title'].'">
                                <h2 class="text-primary">'.$article['title'].'</h2>
                                <p class="text-muted d-flex align-items-center">
                                    <i class="far fa-calendar-alt me-2"></i> نشر في '.formatDate($article['created_at']).'
                                    <i class="far fa-user me-2 ms-3"></i> بواسطة '.$article['author'].'
                                </p>
                                <div class="article-content">'.$article['content'].'</div>';
                        
                        if (!empty($article['tags'])) {
                            echo '<div class="mt-4 pt-3 border-top">
                                <strong class="d-block mb-2"><i class="fas fa-tags me-2"></i>الكلمات المفتاحية:</strong>
                                <div class="d-flex flex-wrap gap-2">';
                            $tags = explode(',', $article['tags']);
                            foreach ($tags as $tag) {
                                echo '<span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">'.trim($tag).'</span>';
                            }
                            echo '</div></div>';
                        }
                        
                        echo '</div></article>';
                        
                        // رابط العودة لصفحة المدونة
                        echo '<a href="blog.php" class="btn btn-primary btn-hover-gradient mb-5">
                            <i class="fas fa-arrow-right me-2"></i>العودة للمدونة
                        </a>';
                    } else {
                        echo '<div class="alert alert-warning fade-in">
                            <i class="fas fa-exclamation-triangle me-2"></i>المقال المطلوب غير موجود.
                        </div>';
                    }
                } else {
                    // عرض جميع المقالات
                    $stmt = $pdo->query("SELECT * FROM blog WHERE is_active = 1 ORDER BY created_at DESC");
                    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($articles as $article) {
                        echo '<article class="card mb-5 hover-scale" id="article-'.$article['id'].'">
                            <div class="card-body">
                                <img src="'.SITE_URL.'/assets/images/'.$article['image'].'" class="img-fluid rounded mb-4 shadow-sm" alt="'.$article['title'].'">
                                <h2 class="text-primary">'.$article['title'].'</h2>
                                <p class="text-muted d-flex align-items-center">
                                    <i class="far fa-calendar-alt me-2"></i> نشر في '.formatDate($article['created_at']).'
                                    <i class="far fa-user me-2 ms-3"></i> بواسطة '.$article['author'].'
                                </p>
                                <p>'.shortenText($article['content'], 300).'</p>
                                <a href="blog.php?id='.$article['id'].'" class="btn btn-primary btn-hover-gradient">
                                    <i class="fas fa-book-reader me-2"></i>قراءة المزيد
                                </a>
                            </div>
                        </article>';
                    }
                }
                ?>
            </div>
            
            <div class="col-lg-4">
                <div class="card mb-4 shadow-sm hover-scale">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-search me-2"></i>البحث في المدونة</h5>
                    </div>
                    <div class="card-body">
                        <form action="blog.php" method="get">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control shadow-sm" placeholder="ابحث هنا...">
                                <button class="btn btn-primary btn-hover-gradient" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card mb-4 shadow-sm hover-scale">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="far fa-newspaper me-2"></i>أحدث المقالات</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <?php
                            $stmt = $pdo->query("SELECT id, title FROM blog WHERE is_active = 1 ORDER BY created_at DESC LIMIT 5");
                            $recent_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach ($recent_articles as $article) {
                                echo '<li class="mb-3 pb-2 border-bottom">
                                    <a href="blog.php?id='.$article['id'].'" class="d-flex align-items-center text-decoration-none">
                                        <i class="fas fa-chevron-left text-primary me-2"></i>
                                        <span>'.$article['title'].'</span>
                                    </a>
                                </li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>