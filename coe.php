<?php
include 'header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

<style>
  /* ================= Center of Excellence Light Theme ================= */

  .coe-page {
    background: #ffffff;
  }

  .coe-hero {
    position: relative;
    padding: 75px 0 65px;
    background:
      linear-gradient(135deg, #fff7f4 0%, #ffffff 48%, #f8fafc 100%);
    overflow: hidden;
  }

  .coe-hero::before {
    content: "";
    position: absolute;
    width: 280px;
    height: 280px;
    background: rgba(255, 91, 46, 0.10);
    border-radius: 50%;
    top: -80px;
    right: -70px;
  }

  .coe-hero::after {
    content: "";
    position: absolute;
    width: 180px;
    height: 180px;
    background: rgba(255, 91, 46, 0.08);
    border-radius: 50%;
    bottom: -60px;
    left: -50px;
  }

  .coe-hero .container {
    position: relative;
    z-index: 2;
  }

  .coe-hero-box {
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
  }

  .coe-subtitle {
    display: inline-block;
    color: #ff5b2e;
    background: #fff0ea;
    border: 1px solid #ffded2;
    padding: 8px 18px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 16px;
  }

  .coe-hero h1 {
    color: #2d3b4f;
    font-size: 42px;
    line-height: 1.25;
    font-weight: 800;
    margin-bottom: 18px;
  }

  .coe-hero p {
    color: #6b7280;
    font-size: 16px;
    line-height: 1.8;
    margin-bottom: 0 !important;
  }

  .coe-section {
    padding: 65px 0;
    background: #ffffff;
  }

  .coe-section-light {
    padding: 65px 0;
    background: #f8fafc;
  }

  .coe-heading {
    text-align: center;
    max-width: 850px;
    margin: 0 auto 38px;
  }

  .coe-heading span {
    color: #ff5b2e;
    font-size: 13px;
    font-weight: 900;
    letter-spacing: 1px;
    text-transform: uppercase;
    display: block;
    margin-bottom: 8px;
  }

  .coe-heading h2 {
    color: #2d3b4f;
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 12px;
  }

  .coe-heading p {
    color: #6b7280;
    font-size: 15px;
    line-height: 1.8;
    margin-bottom: 0 !important;
  }

  .coe-card {
    height: 100%;
    border-radius: 16px;
    overflow: hidden;
    background: #ffffff;
    border: 1px solid #edf0f5;
    box-shadow: 0 8px 24px rgba(30, 45, 70, 0.08);
    transition: all 0.3s ease;
  }

  .coe-card:hover {
    transform: translateY(-6px);
    border-color: #ffded2;
    box-shadow: 0 16px 35px rgba(255, 91, 46, 0.14);
  }

  .coe-img {
    width: 100%;
    height: 185px;
    overflow: hidden;
    background: #f8fafc;
    padding: 12px;
    border-bottom: 1px solid #f1f3f6;
  }

  .coe-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    border-radius: 12px;
    transition: all 0.35s ease;
  }

  .coe-card:hover .coe-img img {
    transform: scale(1.05);
  }

  .coe-title {
    min-height: 62px;
    padding: 15px 14px;
    text-align: center;
    background: #ffffff;
    color: #2d3b4f;
    font-size: 14px;
    font-weight: 800;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .coe-info-card {
    height: 100%;
    background: #ffffff;
    border-radius: 18px;
    padding: 26px;
    border: 1px solid #edf0f5;
    box-shadow: 0 8px 24px rgba(30, 45, 70, 0.08);
    transition: all 0.3s ease;
  }

  .coe-info-card:hover {
    transform: translateY(-5px);
    border-color: #ffded2;
    box-shadow: 0 16px 35px rgba(255, 91, 46, 0.12);
  }

  .coe-icon {
    width: 58px;
    height: 58px;
    border-radius: 16px;
    background: #fff0ea;
    color: #ff5b2e;
    display: grid;
    place-items: center;
    font-size: 25px;
    margin-bottom: 18px;
  }

  .coe-info-card h3 {
    font-size: 20px;
    font-weight: 800;
    color: #2d3b4f;
    margin-bottom: 10px;
  }

  .coe-info-card p {
    color: #6b7280;
    font-size: 14.5px;
    line-height: 1.8;
    margin-bottom: 0 !important;
  }

  .benefit-box {
    display: flex;
    gap: 15px;
    background: #ffffff;
    border: 1px solid #edf0f5;
    border-radius: 18px;
    padding: 22px;
    height: 100%;
    box-shadow: 0 8px 24px rgba(30, 45, 70, 0.06);
    transition: all 0.3s ease;
  }

  .benefit-box:hover {
    transform: translateY(-4px);
    border-color: #ffded2;
    box-shadow: 0 14px 30px rgba(255, 91, 46, 0.11);
  }

  .benefit-box i {
    width: 42px;
    height: 42px;
    background: #ff5b2e;
    color: #ffffff;
    border-radius: 12px;
    display: grid;
    place-items: center;
    flex-shrink: 0;
  }

  .benefit-box h4 {
    font-size: 17px;
    font-weight: 800;
    color: #2d3b4f;
    margin-bottom: 7px;
  }

  .benefit-box p {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.7;
    margin-bottom: 0 !important;
  }

  .coe-cta {
    padding: 62px 0;
    background: #fff7f4;
    border-top: 1px solid #ffded2;
    text-align: center;
  }

  .coe-cta h2 {
    color: #2d3b4f;
    font-size: 34px;
    font-weight: 800;
    margin-bottom: 14px;
  }

  .coe-cta p {
    max-width: 780px;
    margin: 0 auto 26px !important;
    color: #6b7280;
    line-height: 1.8;
  }

  .coe-cta a {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #ff5b2e;
    color: #ffffff;
    padding: 13px 28px;
    border-radius: 10px;
    font-weight: 800;
    text-decoration: none;
    transition: 0.25s ease;
  }

  .coe-cta a:hover {
    background: #e94d23;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(255, 91, 46, 0.28);
    text-decoration: none;
  }

  @media (max-width: 991px) {
    .coe-hero h1 {
      font-size: 34px;
    }

    .coe-img {
      height: 190px;
    }
  }

  @media (max-width: 768px) {
    .coe-hero {
      padding: 55px 0;
    }

    .coe-hero h1 {
      font-size: 28px;
    }

    .coe-heading h2,
    .coe-cta h2 {
      font-size: 26px;
    }

    .coe-section,
    .coe-section-light {
      padding: 50px 0;
    }

    .coe-img {
      height: 210px;
    }
  }

  @media (max-width: 480px) {
    .coe-img {
      height: 185px;
    }

    .coe-title {
      font-size: 12px;
      padding: 12px 8px;
    }

    .benefit-box {
      flex-direction: column;
    }
  }
</style>

<main class="coe-page">

  <!-- Hero Section -->
  <section class="coe-hero">
    <div class="container">
      <div class="coe-hero-box">
        <span class="coe-subtitle">Center of Excellence</span>
        <h1>Building Industry-Ready Technical Skills Through Institutional Collaboration</h1>
        <p>
          Softpro Innovations collaborates with premier engineering institutions to establish
          Centers of Excellence focused on practical learning, emerging technologies, skill development,
          project-based training and innovation-driven academic growth.
        </p>
      </div>
    </div>
  </section>

  <!-- Collaboration Section -->
  <section class="coe-section">
    <div class="container">
      <div class="coe-heading">
        <span>Our Collaborations</span>
        <h2>India’s Premier Engineering Institutions</h2>
        <p>
          We work with reputed academic institutions to build a strong technical ecosystem
          through labs, training, mentoring and project-based implementation.
        </p>
      </div>

      <div class="row g-4 justify-content-center">

        <div class="col-lg-4 col-md-6">
          <div class="coe-card">
            <div class="coe-img"><a href="./assets/images/coe/1.jpeg" class="glightbox" data-gallery="lab-setup-gallery"><img src="./assets/images/coe/1.jpeg" alt="SRMU Chennai Delhi-NCR Campus" style="cursor: zoom-in;"></a></div>
            <div class="coe-title">
              SRMU Chennai, Delhi-NCR Campus
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="coe-card">
            <div class="coe-img"><a href="./assets/images/coe/2.jpeg" class="glightbox" data-gallery="lab-setup-gallery"><img src="./assets/images/coe/2.jpeg" alt="Galgotias University Greater Noida" style="cursor: zoom-in;"></a></div>
            <div class="coe-title">
              Galgotias University, Greater Noida
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="coe-card">
            <div class="coe-img"><a href="./assets/images/coe/3.jpeg" class="glightbox" data-gallery="lab-setup-gallery"><img src="./assets/images/coe/3.jpeg" alt="GNIOT Greater Noida" style="cursor: zoom-in;"></a></div>
            <div class="coe-title">
              GNIOT, Greater Noida
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="coe-card">
            <div class="coe-img"><a href="./assets/images/coe/4.jpeg" class="glightbox" data-gallery="lab-setup-gallery"><img src="./assets/images/coe/4.jpeg" alt="ITS Engineering College" style="cursor: zoom-in;"></a></div>
            <div class="coe-title">
              ITS Engineering College
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="coe-card">
            <div class="coe-img"><a href="./assets/images/coe/5.jpeg" class="glightbox" data-gallery="lab-setup-gallery"><img src="./assets/images/coe/5.jpeg" alt="RKGIT Ghaziabad" style="cursor: zoom-in;"></a></div>
            <div class="coe-title">
              RKGIT, Ghaziabad
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="coe-card">
            <div class="coe-img"><a href="./assets/images/coe/6.jpeg" class="glightbox" data-gallery="lab-setup-gallery"><img src="./assets/images/coe/6.jpeg" alt="Dr. K.N. Modi Institute Modinagar" style="cursor: zoom-in;"></a></div>
            <div class="coe-title">
              Dr. K.N. Modi Institute, Modinagar
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- About COE -->
  <section class="coe-section-light">
    <div class="container">
      <div class="coe-heading">
        <span>About COE</span>
        <h2>Empowering Students with Practical Technology Exposure</h2>
        <p>
          Our Center of Excellence initiative bridges the gap between academic curriculum and
          industry expectations with structured training, live projects and technical mentoring.
        </p>
      </div>

      <div class="row g-4">
        <div class="col-lg-4 col-md-6">
          <div class="coe-info-card">
            <div class="coe-icon">
              <i class="fa-solid fa-microchip"></i>
            </div>
            <h3>Emerging Technology Labs</h3>
            <p>
              Practical labs for IoT, embedded systems, robotics, automation, AI-enabled solutions
              and smart engineering applications.
            </p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="coe-info-card">
            <div class="coe-icon">
              <i class="fa-solid fa-chalkboard-user"></i>
            </div>
            <h3>Skill-Based Training</h3>
            <p>
              Industry-oriented programs to improve technical fundamentals, project confidence
              and employability.
            </p>
          </div>
        </div>

        <div class="col-lg-4 col-md-6">
          <div class="coe-info-card">
            <div class="coe-icon">
              <i class="fa-solid fa-diagram-project"></i>
            </div>
            <h3>Project Development Support</h3>
            <p>
              Complete guidance for minor projects, major projects, prototypes, research models
              and real-time demonstrations.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Benefits -->
  <section class="coe-section">
    <div class="container">
      <div class="coe-heading">
        <span>Key Benefits</span>
        <h2>What Institutions Get Through COE</h2>
        <p>
          The Center of Excellence model strengthens academic delivery by combining traditional
          engineering discipline with modern practical implementation.
        </p>
      </div>

      <div class="row g-4">
        <div class="col-lg-6">
          <div class="benefit-box">
            <i class="fa-solid fa-circle-check"></i>
            <div>
              <h4>Hands-on Technical Learning</h4>
              <p>
                Students learn by working on real hardware, software, tools, modules and
                industry-relevant project workflows.
              </p>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="benefit-box">
            <i class="fa-solid fa-users-gear"></i>
            <div>
              <h4>Faculty & Student Enablement</h4>
              <p>
                Structured sessions, demonstrations, technical mentoring and lab-based guidance
                help both faculty and students.
              </p>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="benefit-box">
            <i class="fa-solid fa-lightbulb"></i>
            <div>
              <h4>Innovation & Prototype Culture</h4>
              <p>
                Encourages students to develop innovative solutions, working models and practical
                prototypes for academic and industry use.
              </p>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="benefit-box">
            <i class="fa-solid fa-chart-line"></i>
            <div>
              <h4>Industry-Ready Skill Development</h4>
              <p>
                Focuses on implementation, documentation, presentation, debugging and deployment-oriented
                learning outcomes.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="coe-cta">
    <div class="container">
      <h2>Want to Establish a Center of Excellence?</h2>
      <p>
        Partner with Softpro Innovations to create a practical technology ecosystem inside your
        institution and prepare students for future-ready technical careers.
      </p>
      <a href="contactus.php">
        Contact Us Now
        <i class="fa-solid fa-arrow-right"></i>
      </a>
    </div>
  </section>

</main>

<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>document.addEventListener('DOMContentLoaded',function(){if(typeof GLightbox!=='undefined'){GLightbox({selector:'.glightbox',touchNavigation:true,loop:true});}});</script>
<?php
include 'footer.php';
?>