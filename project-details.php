<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect(SITE_URL);
}

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ? AND is_active = 1");
$stmt->execute([$id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    redirect(SITE_URL);
}

$page_title = $project['title'] . ' - تفاصيل المشروع';
?>

<section class="project-details py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-5">
                    <div class="card-body">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">الرئيسية</a></li>
                                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/projects.php">المشاريع</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?= $project['title'] ?></li>
                            </ol>
                        </nav>
                        
                        <h1 class="text-gradient mb-4"><?= $project['title'] ?></h1>
                        
                        <div class="project-main-image mb-4">
                            <img src="<?= SITE_URL ?>/assets/images/<?= $project['image'] ?>" 
                                 alt="<?= $project['title'] ?>" 
                                 class="img-fluid rounded-3 shadow-sm w-100">
                        </div>
                        
                        <div class="project-meta d-flex flex-wrap gap-3 mb-4">
                            <span class="badge bg-primary">
                                <i class="fas fa-folder me-2"></i><?= $project['category'] ?>
                            </span>
                            <span class="badge bg-secondary">
                                <i class="fas fa-user-tie me-2"></i><?= $project['client'] ?>
                            </span>
                            <span class="badge bg-info">
                                <i class="far fa-calendar-alt me-2"></i><?= formatDate($project['project_date']) ?>
                            </span>
                        </div>
                        
                        <div class="project-content mb-5">
                            <h3 class="mb-3">وصف المشروع</h3>
                            <p class="lead"><?= nl2br($project['description']) ?></p>
                        </div>
                        
                        <?php if (!empty($project['features'])): ?>
                        <div class="project-features mb-5">
                            <h3 class="mb-3">الميزات الرئيسية</h3>
                            <ul class="list-group list-group-flush">
                                <?php 
                                $features = explode("\n", $project['features']);
                                foreach ($features as $feature): 
                                    if (trim($feature)): 
                                ?>
                                <li class="list-group-item bg-transparent d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span><?= $feature ?></span>
                                </li>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($project['technologies'])): ?>
                        <div class="project-technologies mb-5">
                            <h3 class="mb-3">التقنيات المستخدمة</h3>
                            <div class="d-flex flex-wrap gap-2">
                                <?php 
                                $techs = explode(",", $project['technologies']);
                                foreach ($techs as $tech): 
                                    if (trim($tech)): 
                                ?>
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-code me-2"></i><?= $tech ?>
                                </span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($project['challenges'])): ?>
                        <div class="project-challenges mb-5">
                            <h3 class="mb-3">التحديات والحلول</h3>
                            <div class="accordion" id="challengesAccordion">
                                <?php 
                                $challenges = explode("\n", $project['challenges']);
                                foreach ($challenges as $index => $challenge): 
                                    if (trim($challenge)): 
                                ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?= $index ?>">
                                        <button class="accordion-button collapsed" type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapse<?= $index ?>" 
                                                aria-expanded="false" 
                                                aria-controls="collapse<?= $index ?>">
                                            التحدي <?= $index + 1 ?>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $index ?>" class="accordion-collapse collapse" 
                                         aria-labelledby="heading<?= $index ?>" 
                                         data-bs-parent="#challengesAccordion">
                                        <div class="accordion-body">
                                            <?= $challenge ?>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php 
                        // عرض معرض الصور إذا وجد
                        if (!empty($project['gallery_images'])): 
                            $gallery_images = explode(",", $project['gallery_images']);
                        ?>
                        <div class="project-gallery mb-5">
                            <h3 class="mb-3">معرض الصور</h3>
                            <div class="row g-3">
                                <?php foreach ($gallery_images as $image): ?>
                                <div class="col-md-4 col-6">
                                    <a href="<?= SITE_URL ?>/assets/images/<?= $image ?>" 
                                       data-lightbox="project-gallery" 
                                       data-title="<?= $project['title'] ?>">
                                        <img src="<?= SITE_URL ?>/assets/images/<?= $image ?>" 
                                             alt="<?= $project['title'] ?>" 
                                             class="img-fluid rounded shadow-sm">
                                    </a>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($project['external_url'])): ?>
                        <div class="project-external-link mb-5">
                            <a href="<?= $project['external_url'] ?>" 
                               target="_blank" 
                               class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-external-link-alt me-2"></i> زيارة المشروع
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <div class="social-sharing mb-4">
                            <h5 class="mb-3">مشاركة المشروع:</h5>
                            <div class="d-flex gap-2">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(SITE_URL . '/project-details.php?id=' . $project['id']) ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?= urlencode(SITE_URL . '/project-details.php?id=' . $project['id']) ?>&text=<?= urlencode($project['title']) ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-info">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode(SITE_URL . '/project-details.php?id=' . $project['id']) ?>&title=<?= urlencode($project['title']) ?>&summary=<?= urlencode(substr($project['description'], 0, 200)) ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">معلومات المشروع</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-calendar-alt me-2 text-primary"></i>تاريخ النشر</span>
                                <span><?= formatDate($project['created_at']) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-tags me-2 text-primary"></i>الفئة</span>
                                <span><?= $project['category'] ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-user-tie me-2 text-primary"></i>العميل</span>
                                <span><?= $project['client'] ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-check-circle me-2 text-primary"></i>الحالة</span>
                                <span class="badge bg-<?= $project['is_active'] ? 'success' : 'secondary' ?>">
                                    <?= $project['is_active'] ? 'مكتمل' : 'قيد التنفيذ' ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <?php
                // عرض مشاريع ذات صلة
                $stmt = $pdo->prepare("SELECT id, title, image FROM projects 
                                      WHERE category = ? AND id != ? AND is_active = 1 
                                      ORDER BY created_at DESC LIMIT 3");
                $stmt->execute([$project['category'], $project['id']]);
                $related_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($related_projects)):
                ?>
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">مشاريع ذات صلة</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php foreach ($related_projects as $related): ?>
                            <a href="<?= SITE_URL ?>/project-details.php?id=<?= $related['id'] ?>" 
                               class="list-group-item list-group-item-action d-flex align-items-center">
                                <img src="<?= SITE_URL ?>/assets/images/<?= $related['image'] ?>" 
                                     alt="<?= $related['title'] ?>" 
                                     width="50" 
                                     height="50" 
                                     class="rounded me-3 object-fit-cover">
                                <span><?= $related['title'] ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>