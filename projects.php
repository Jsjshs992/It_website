<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$page_title = 'المشاريع';

// معالجة معاملات البحث والتصفية
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';
$technology = isset($_GET['technology']) ? sanitizeInput($_GET['technology']) : '';

// بناء استعلام SQL مع عوامل التصفية
$sql = "SELECT * FROM projects WHERE is_active = 1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR description LIKE ? OR client LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

if (!empty($technology)) {
    $sql .= " AND technologies LIKE ?";
    $params[] = "%$technology%";
}

// الترتيب
$sql .= " ORDER BY created_at DESC";

// الترقيم
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 6;
$total = $pdo->prepare(str_replace('*', 'COUNT(*)', $sql));
$total->execute($params);
$total_projects = $total->fetchColumn();

$total_pages = ceil($total_projects / $per_page);
$offset = ($page - 1) * $per_page;

$sql .= " LIMIT $offset, $per_page";

// تنفيذ الاستعلام
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// الحصول على الفئات والتقنيات المتاحة للتصفية
$categories = $pdo->query("SELECT DISTINCT category FROM projects WHERE is_active = 1 ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
$technologies = $pdo->query("SELECT DISTINCT technologies FROM projects WHERE is_active = 1")->fetchAll(PDO::FETCH_COLUMN);

// استخراج جميع التقنيات الفريدة
$all_techs = [];
foreach ($technologies as $tech_string) {
    $techs = explode(',', $tech_string);
    foreach ($techs as $tech) {
        $tech = trim($tech);
        if ($tech && !in_array($tech, $all_techs)) {
            $all_techs[] = $tech;
        }
    }
}
sort($all_techs);
?>

<section class="projects-page py-5">
    <div class="container">
        <h1 class="text-center mb-5 text-gradient">مشاريعنا</h1>
        
        <!-- فلترة المشاريع -->
        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <form id="filter-form" method="get" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">بحث</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="ابحث عن مشروع..." value="<?= $search ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label">الفئة</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">جميع الفئات</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat ?>" <?= $category == $cat ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="technology" class="form-label">التقنية</label>
                        <select class="form-select" id="technology" name="technology">
                            <option value="">جميع التقنيات</option>
                            <?php foreach ($all_techs as $tech): ?>
                            <option value="<?= $tech ?>" <?= $technology == $tech ? 'selected' : '' ?>><?= $tech ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>تصفية
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- نتائج البحث -->
        <div class="row g-4 mb-5">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                <div class="col-lg-4 col-md-6 animate-on-scroll">
                    <div class="card h-100 hover-scale shadow-sm">
                        <img src="<?= SITE_URL ?>/assets/images/<?= $project['image'] ?>" 
                             class="card-img-top img-fluid rounded-top object-fit-cover" 
                             alt="<?= $project['title'] ?>"
                             style="height: 200px;">
                        <div class="card-body">
                            <h3 class="card-title text-primary"><?= $project['title'] ?></h3>
                            <p class="card-text text-muted"><?= substr($project['description'], 0, 100) ?>...</p>
                            
                            <?php if (!empty($project['technologies'])): ?>
                            <div class="mb-3">
                                <?php 
                                $techs = explode(",", $project['technologies']);
                                foreach ($techs as $tech): 
                                    if (trim($tech)):
                                ?>
                                <span class="badge bg-light text-dark border me-1 mb-1"><?= $tech ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fas fa-folder-open text-primary me-2"></i>
                                <span><strong>الفئة:</strong> <?= $project['category'] ?></span>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="far fa-calendar-alt text-primary me-2"></i>
                                <span><strong>التاريخ:</strong> <?= formatDate($project['project_date']) ?></span>
                            </li>
                        </ul>
                        <div class="card-footer bg-transparent border-top-0">
                            <a href="<?= SITE_URL ?>/project-details.php?id=<?= $project['id'] ?>" 
                               class="btn btn-primary btn-hover-gradient w-100">
                                <i class="fas fa-eye me-2"></i>عرض التفاصيل
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center fade-in">
                        <i class="fas fa-info-circle me-2"></i>لا توجد مشاريع متطابقة مع معايير البحث.
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- الترقيم -->
        <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" 
                       href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
                       aria-label="السابق">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" 
                       href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" 
                       aria-label="التالي">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</section>

<script>
// تأثير التمرير للعناصر
document.addEventListener('DOMContentLoaded', function() {
    const animateElements = document.querySelectorAll('.animate-on-scroll');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });
    
    animateElements.forEach(element => {
        observer.observe(element);
    });
});
</script>

<?php
require_once 'includes/footer.php';
?>