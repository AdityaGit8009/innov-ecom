<?php
include 'config/db.php';

$technologies = [];
$techQ = mysqli_query($conn, "SELECT * FROM training_technologies 
    WHERE status=1 
    ORDER BY sort_order ASC, id ASC");

while ($row = mysqli_fetch_assoc($techQ)) {
    $technologies[] = $row;
}
?>
<?php include 'header.php'; ?>

<?php
$page = [
    "title" => "TRAINING/ <span class='pink'>INTERNSHIP</span>",
    "breadcrumb" => "Training/Internship"
];

$leftContent = [
    "heading" => "Summer Training in Lucknow",
    "description" => "We provide the best B.Tech training in Lucknow with industry-oriented Summer Training programs in Embedded Systems, IoT, and Robotics. Our courses focus on practical learning with real-time projects, covering Embedded C, Microcontrollers, Arduino, Raspberry Pi, Sensors, IoT Applications, Python, and Industrial Automation.",
    "training_contact" => [
        "title" => "Course Counsellor",
        "phone" => "7080102007"
    ],
    "technical_contact" => [
        "name" => "Er. Akash Maurya",
        "phone" => "7080102008"
    ]
];

$whySoftpro = [
    "39 Practicals, 23 Minor Projects & 4 Major Projects within 45 days",
    "Professionally designed syllabus for Basic & Advance Embedded Systems",
    "Daily theory + practical sessions",
    "New Practicals & Projects every day",
    "Regular practical assignments",
    "Revision & query sessions",
    "Exams after Basic & Advance modules",
    "Dedicated project development time"
];

$eligibility = [
    "Diploma (EC)",
    "Diploma (EE)",
    "B.Tech (EC)",
    "B.Tech (EE)",
];
?>

<style>
    :root {
        --tp-primary: #f05a28;
        --tp-primary-light: #ff7a1a;
        --tp-primary-glow: rgba(240, 90, 40, 0.25);
        --tp-dark: #0f172a;
        --tp-text: #334155;
        --tp-muted: #64748b;
        --tp-bg: #f8fafc;
        --tp-card-bg: rgba(255, 255, 255, 0.85);
        --tp-card-border: rgba(255, 255, 255, 0.5);
    }

    body {
        background-color: var(--tp-bg);
    }

    /* Keyframes for micro-animations */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }

    .training-page-wrap {
        background: radial-gradient(circle at top left, rgba(240, 90, 40, 0.08) 0%, transparent 40%),
                    radial-gradient(circle at bottom right, rgba(240, 90, 40, 0.05) 0%, transparent 40%);
        min-height: 100vh;
        padding-bottom: 60px;
    }

    .training-hero-box {
        width: min(1200px, 92%);
        margin: 50px auto 40px;
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 32px;
        align-items: start;
        animation: fadeUp 0.8s ease-out forwards;
    }

    /* Glassmorphic Cards */
    .training-info-card,
    .training-main-card {
        background: var(--tp-card-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--tp-card-border);
        border-radius: 28px;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .training-info-card {
        position: sticky;
        top: 100px;
    }

    .training-info-head {
        padding: 32px;
        background: linear-gradient(135deg, rgba(255,255,255,1) 0%, rgba(255,245,240,0.4) 100%);
        border-bottom: 1px solid rgba(0,0,0,0.03);
    }

    .training-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--tp-primary);
        background: #fff;
        border: 1px solid #ffe4d6;
        padding: 8px 16px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 16px;
        box-shadow: 0 4px 12px rgba(240, 90, 40, 0.08);
    }

    .training-info-card h5 {
        color: var(--tp-dark);
        font-size: 24px;
        font-weight: 900;
        line-height: 1.3;
        margin-bottom: 12px;
    }

    .training-info-card p,
    .training-main-card p {
        color: var(--tp-muted);
        line-height: 1.8;
        font-size: 16px;
        margin-bottom: 0;
    }

    .training-info-body {
        padding: 32px;
    }

    .training-cta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 30px;
    }

    .tp-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 48px;
        padding: 12px 16px;
        border-radius: 14px;
        text-decoration: none !important;
        font-weight: 800;
        font-size: 14px;
        border: 1px solid transparent;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        white-space: nowrap;
    }

    .tp-btn-primary {
        color: #fff !important;
        background: linear-gradient(135deg, var(--tp-primary), var(--tp-primary-light));
        box-shadow: 0 12px 24px var(--tp-primary-glow);
        flex: 1;
    }

    .tp-btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 32px rgba(240, 90, 40, 0.35);
    }

    .tp-btn-outline {
        color: var(--tp-primary) !important;
        background: #fff;
        border-color: #ffd4c2;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        flex: 1;
    }

    .tp-btn-outline:hover {
        background: #fff5ef;
        transform: translateY(-2px);
    }

    .training-contact-box {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .training-contact-item {
        display: flex;
        gap: 16px;
        align-items: center;
        padding: 16px;
        border: 1px solid rgba(0,0,0,0.04);
        border-radius: 20px;
        background: #fff;
        transition: transform 0.2s ease;
    }

    .training-contact-item:hover {
        transform: translateX(5px);
        border-color: #ffe4d6;
    }

    .training-contact-icon {
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        background: linear-gradient(135deg, var(--tp-primary), var(--tp-primary-light));
        font-size: 20px;
        box-shadow: 0 8px 16px var(--tp-primary-glow);
    }

    .training-contact-item h6 {
        margin: 0 0 4px;
        font-weight: 850;
        color: var(--tp-dark);
        font-size: 15px;
    }

    .training-contact-item small,
    .training-contact-item a {
        color: var(--tp-muted);
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
        display: block;
    }

    .training-contact-item a {
        color: var(--tp-primary);
        margin-top: 4px;
        font-weight: 700;
    }

    /* Main Card */
    .training-main-card {
        padding: 40px;
        position: relative;
    }

    .training-main-card::before {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, var(--tp-primary-glow) 0%, transparent 60%);
        pointer-events: none;
        opacity: 0.6;
    }

    .training-main-card h4 {
        font-size: 32px;
        font-weight: 900;
        color: var(--tp-dark);
        margin-bottom: 20px;
        line-height: 1.2;
    }

    .training-main-card h4 .pink {
        background: linear-gradient(135deg, var(--tp-primary), var(--tp-primary-light));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .training-highlight-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin: 32px 0 40px;
    }

    .training-point {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 16px;
        background: #fff;
        border: 1px solid rgba(0,0,0,0.03);
        border-radius: 16px;
        color: var(--tp-text);
        font-size: 15px;
        line-height: 1.5;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }

    .training-point:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
        border-color: #ffe4d6;
    }

    .training-point i {
        color: var(--tp-primary);
        font-size: 18px;
        margin-top: 2px;
    }

    .training-eligibility {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 20px;
    }

    .training-eligibility span {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        color: var(--tp-dark);
        background: #fff;
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 999px;
        font-weight: 800;
        font-size: 14px;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
        transition: all 0.3s ease;
    }

    .training-eligibility span:hover {
        border-color: var(--tp-primary);
        color: var(--tp-primary);
        transform: translateY(-2px);
    }

    .training-eligibility span i {
        color: var(--tp-primary);
    }

    /* Technology Section */
    .training-tech-section {
        width: min(1200px, 92%);
        margin: 0 auto;
        animation: fadeUp 0.8s ease-out 0.2s forwards;
        opacity: 0;
    }

    .training-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px 32px;
        background: var(--tp-card-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--tp-card-border);
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
        margin-bottom: 32px;
        transition: transform 0.3s ease;
    }

    .training-section-header:hover {
        transform: translateY(-2px);
    }

    .training-section-title small {
        display: block;
        color: var(--tp-primary);
        font-weight: 900;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-bottom: 6px;
        font-size: 12px;
    }

    .training-section-title h3 {
        margin: 0;
        font-size: 28px;
        font-weight: 900;
        color: var(--tp-dark);
    }

    .training-toggle-btn {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        border: 0;
        color: #fff;
        background: linear-gradient(135deg, var(--tp-primary), var(--tp-primary-light));
        font-size: 24px;
        font-weight: 400;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 20px var(--tp-primary-glow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .training-section-header[aria-expanded="true"] .training-toggle-btn {
        background: var(--tp-dark);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.2);
    }

    /* Technology Grid */
    .training-tech-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 32px;
    }

    .training-tech-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.04);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.04);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .training-tech-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 30px 60px rgba(240, 90, 40, 0.12);
        border-color: #ffe4d6;
    }

    .training-tech-img {
        position: relative;
        aspect-ratio: 16/9;
        overflow: hidden;
        background: #f1f5f9;
    }

    .training-tech-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .training-tech-card:hover .training-tech-img img {
        transform: scale(1.08);
    }

    .training-tech-img::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.4) 0%, transparent 50%);
        opacity: 0.5;
        transition: opacity 0.4s ease;
    }

    .training-tech-card:hover .training-tech-img::after {
        opacity: 0.2;
    }

    .training-tech-badge {
        position: absolute;
        left: 16px;
        top: 16px;
        padding: 6px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        color: var(--tp-primary);
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        z-index: 2;
    }

    .training-tech-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        flex: 1;
        background: #fff;
    }

    .training-tech-body h6 {
        font-size: 18px;
        color: var(--tp-dark);
        line-height: 1.4;
        font-weight: 900;
        margin-bottom: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .training-tech-body p {
        color: var(--tp-muted);
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 20px;
        flex: 1;
    }

    .training-stars {
        color: #fbbf24;
        font-size: 14px;
        letter-spacing: 2px;
        margin-bottom: 20px;
    }

    .training-read-btn {
        width: 100%;
        border-radius: 14px;
        padding: 14px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        color: #fff !important;
        text-decoration: none !important;
        background: var(--tp-dark);
        font-weight: 800;
        font-size: 14px;
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }

    .training-read-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: left 0.5s ease;
    }

    .training-tech-card:hover .training-read-btn {
        background: linear-gradient(135deg, var(--tp-primary), var(--tp-primary-light));
        box-shadow: 0 8px 20px var(--tp-primary-glow);
    }

    .training-tech-card:hover .training-read-btn::before {
        left: 100%;
    }

    .training-read-btn i {
        transition: transform 0.3s ease;
    }

    .training-tech-card:hover .training-read-btn i {
        transform: translateX(4px);
    }

    /* Responsive */
    @media (max-width: 1100px) {
        .training-hero-box {
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .training-info-card {
            position: static;
        }

        .training-tech-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }
    }

    @media (max-width: 768px) {
        .training-highlight-grid {
            grid-template-columns: 1fr;
        }

        .training-tech-grid {
            grid-template-columns: 1fr;
        }

        .training-main-card {
            padding: 24px;
        }

        .training-main-card h4 {
            font-size: 26px;
        }

        .training-section-header {
            padding: 20px;
        }

        .training-section-title h3 {
            font-size: 22px;
        }
    }
</style>


<section class="breadcrumb-section">
    <h1><?= $page['title']; ?></h1>
    <p class="breadcrumb">
        <a href="index.php">Home</a> <span>| <?= htmlspecialchars($page['breadcrumb']); ?></span>
    </p>
</section>

<main class="training-page-wrap">
    <section class="training-hero-box">
        <aside class="training-info-card">
            <div class="training-info-head">
                <span class="training-eyebrow">
                    <i class="fa-solid fa-graduation-cap"></i> Practical Training
                </span>

                <h5><?= htmlspecialchars($leftContent['heading']); ?></h5>
                <p><?= htmlspecialchars($leftContent['description']); ?></p>
            </div>

            <div class="training-info-body" id="register">
                <div class="training-cta-row">
                    <a href="tel:+91<?= htmlspecialchars($leftContent['training_contact']['phone']); ?>" class="tp-btn tp-btn-primary">
                        <i class="fa-solid fa-phone"></i> Register Now
                    </a>

                    <a href="https://www.trainingatsoftpro.com/" target="_blank" class="tp-btn tp-btn-outline">
                        <i class="fa-solid fa-circle-info"></i> Know More
                    </a>
                </div>

                <div class="training-contact-box">
                    <div class="training-contact-item">
                        <div class="training-contact-icon">
                            <i class="fa-solid fa-headset"></i>
                        </div>

                        <div>
                            <h6>For Training Queries</h6>
                            <small><?= htmlspecialchars($leftContent['training_contact']['title']); ?></small><br>
                            <a href="tel:+91<?= htmlspecialchars($leftContent['training_contact']['phone']); ?>">
                                <i class="fa-solid fa-mobile-screen-button"></i> /
                                <i class="fa-brands fa-whatsapp"></i>
                                <?= htmlspecialchars($leftContent['training_contact']['phone']); ?>
                            </a>
                        </div>
                    </div>

                    <div class="training-contact-item">
                        <div class="training-contact-icon">
                            <i class="fa-solid fa-screwdriver-wrench"></i>
                        </div>

                        <div>
                            <h6>For Technical Queries</h6>
                            <small><?= htmlspecialchars($leftContent['technical_contact']['name']); ?></small><br>
                            <a href="tel:+91<?= htmlspecialchars($leftContent['technical_contact']['phone']); ?>">
                                <i class="fa-solid fa-mobile-screen-button"></i> /
                                <i class="fa-brands fa-whatsapp"></i>
                                <?= htmlspecialchars($leftContent['technical_contact']['phone']); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <section class="training-main-card">
            <h4><b>About B.Tech Elx</b> <b class="pink">Summer Training in Lucknow</b></h4>

            <p>
                Electronics has become an integral part of everyday life—from mobile phones to satellites.
                Softpro Innovations provides industry-oriented training to bridge the gap between academics
                and real-world applications.
            </p>

            <div class="training-highlight-grid">
                <?php foreach ($whySoftpro as $point): ?>
                    <div class="training-point">
                        <i class="fa-solid fa-circle-check"></i>
                        <span><?= htmlspecialchars($point); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <h4>Eligibility for <b class="pink">Summer Training Program</b></h4>

            <div class="training-eligibility">
                <?php foreach ($eligibility as $course): ?>
                    <span>
                        <i class="fa-solid fa-user-graduate"></i>
                        <?= htmlspecialchars($course); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </section>
    </section>

    <section class="training-tech-section">
        <div class="training-section-header"
            data-bs-toggle="collapse"
            data-bs-target="#trendingTechCollapse"
            aria-expanded="true"
            aria-controls="trendingTechCollapse"
            style="cursor:pointer;">

            <div class="training-section-title">
                <small>Explore Courses</small>
                <h3>Our Trending Technologies</h3>
            </div>

            <button class="training-toggle-btn" type="button" aria-label="Toggle technologies">−</button>
        </div>

        <div class="collapse show" id="trendingTechCollapse">
            <?php if (!empty($technologies)): ?>
                <div class="training-tech-grid">
                    <?php foreach ($technologies as $tech): ?>
                        <?php
                        $img = !empty($tech['image']) ? $tech['image'] : './assets/images/embededtraing.png';
                        $rating = isset($tech['rating']) ? max(0, min(5, (int)$tech['rating'])) : 5;
                        ?>

                        <article class="training-tech-card">
                            <div class="training-tech-img">
                                <img src="<?= htmlspecialchars($img); ?>" alt="<?= htmlspecialchars($tech['title']); ?>">
                                <span class="training-tech-badge">Certification</span>
                            </div>

                            <div class="training-tech-body">
                                <h6><?= htmlspecialchars($tech['title']); ?></h6>

                                <p><?= htmlspecialchars($tech['short_text'] ?? 'Industry based practical training'); ?></p>

                                <div class="training-stars">
                                    <?php for ($i = 1; $i <= $rating; $i++): ?>
                                        <i class="fa-solid fa-star"></i>
                                    <?php endfor; ?>
                                </div>

                                <a href="technology.php?slug=<?= urlencode($tech['slug']); ?>" class="training-read-btn">
                                    Read More <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center text-muted bg-white p-4 rounded-4 border">
                    No technology available.
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const collapseEl = document.getElementById('trendingTechCollapse');
        const btn = document.querySelector('.training-toggle-btn');

        if (collapseEl && btn) {
            collapseEl.addEventListener('shown.bs.collapse', function() {
                btn.textContent = '−';
            });

            collapseEl.addEventListener('hidden.bs.collapse', function() {
                btn.textContent = '+';
            });
        }
    });
</script>

<?php include 'footer.php'; ?>