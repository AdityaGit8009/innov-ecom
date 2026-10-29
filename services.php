<?php
include 'header.php';
?>
<style>
  .services-hero {
    position: relative;
    padding: 90px 0;
    background:
      linear-gradient(135deg, rgba(253, 246, 243, 0.98), rgba(255, 255, 255, 0.95)),
      url('./assets/images/banner/banner-1.jpg');
    background-size: cover;
    background-position: center;
    color: #111827;
    overflow: hidden;
  }

  .services-hero::after {
    content: "";
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top right, rgba(239, 102, 47, 0.05), transparent 35%);
    pointer-events: none;
  }

  .services-hero .container {
    position: relative;
    z-index: 2;
  }

  .services-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 50px;
    background: rgba(239, 102, 47, 0.12);
    color: #ef662f;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 18px;
  }

  .services-hero h1 {
    font-size: 48px;
    line-height: 1.15;
    font-weight: 800;
    margin-bottom: 18px;
    color: #0f172a;
  }

  .services-hero p {
    max-width: 850px;
    font-size: 17px;
    line-height: 1.8;
    color: #64748b;
    margin-bottom: 0 !important;
  }

  .services-section {
    padding: 70px 0;
    background: #f8fafc;
  }

  .section-title-wrap {
    text-align: center;
    max-width: 850px;
    margin: 0 auto 45px;
  }

  .section-title-wrap span {
    color: #ef662f;
    font-size: 13px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .section-title-wrap h2 {
    margin-top: 10px;
    margin-bottom: 14px;
    font-size: 36px;
    font-weight: 800;
    color: #0f172a;
  }

  .section-title-wrap p {
    color: #64748b;
    font-size: 15.5px;
    line-height: 1.8;
  }

  .service-card {
    height: 100%;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 22px;
    padding: 30px;
    box-shadow: 0 12px 35px rgba(15, 23, 42, 0.06);
    transition: 0.25s ease;
    position: relative;
    overflow: hidden;
  }

  .service-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: linear-gradient(90deg, #ef662f, #ff9a5c);
  }

  .service-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
  }

  .service-icon {
    width: 62px;
    height: 62px;
    border-radius: 18px;
    background: #fff0ea;
    color: #ef662f;
    display: grid;
    place-items: center;
    font-size: 26px;
    margin-bottom: 22px;
  }

  .service-card h3 {
    font-size: 22px;
    font-weight: 800;
    color: #111827;
    margin-bottom: 12px;
  }

  .service-card p {
    color: #64748b;
    font-size: 14.5px;
    line-height: 1.8;
    margin-bottom: 16px !important;
  }

  .service-card ul {
    padding-left: 0 !important;
    margin-bottom: 0 !important;
  }

  .service-card ul li {
    display: flex;
    gap: 10px;
    color: #475569;
    font-size: 14px;
    line-height: 1.7;
    margin-bottom: 9px;
  }

  .service-card ul li i {
    color: #ef662f;
    margin-top: 5px;
    font-size: 12px;
  }

  .process-section {
    padding: 70px 0;
    background: #fff;
  }

  .process-box {
    position: relative;
    background: #fff;
    border-radius: 22px;
    border: 1px solid #e5e7eb;
    padding: 28px;
    height: 100%;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
  }

  .process-number {
    width: 46px;
    height: 46px;
    border-radius: 14px;
    background: #ef662f;
    color: #fff;
    font-weight: 800;
    display: grid;
    place-items: center;
    margin-bottom: 18px;
  }

  .process-box h4 {
    font-size: 19px;
    font-weight: 800;
    color: #111827;
    margin-bottom: 10px;
  }

  .process-box p {
    color: #64748b;
    font-size: 14.5px;
    line-height: 1.8;
  }

  .why-services {
    padding: 70px 0;
    background: #ffffff;
    color: #111827;
  }

  .why-services h2 {
    color: #0f172a;
    font-size: 36px;
    font-weight: 800;
    margin-bottom: 16px;
  }

  .why-services p {
    color: #64748b;
    line-height: 1.8;
    font-size: 15.5px;
  }

  .why-list {
    margin-top: 28px;
  }

  .why-item {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
  }

  .why-item i {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: rgba(239, 102, 47, 0.12);
    color: #ef662f;
    display: grid;
    place-items: center;
    flex-shrink: 0;
  }

  .why-item h5 {
    color: #1e293b;
    font-size: 17px;
    font-weight: 800;
    margin-bottom: 5px;
  }

  .why-item span {
    color: #64748b;
    font-size: 14px;
    line-height: 1.7;
  }

  .service-stats-card {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    padding: 30px;
    height: 100%;
  }

  .stat-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
  }

  .stat-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 22px;
    text-align: center;
  }

  .stat-box h3 {
    color: #0f172a;
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 6px;
  }

  .stat-box p {
    font-size: 13px;
    color: #64748b;
    margin: 0 !important;
  }

  .industries-section {
    padding: 70px 0;
    background: #f8fafc;
  }

  .industry-chip {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 700;
    color: #334155;
    box-shadow: 0 8px 25px rgba(15, 23, 42, 0.05);
  }

  .industry-chip i {
    color: #ef662f;
    font-size: 20px;
  }

  .service-cta {
    padding: 70px 0;
    background: #fff7f4;
    color: #111827;
    text-align: center;
    border-top: 1px solid #ffded2;
  }

  .service-cta h2 {
    color: #0f172a;
    font-size: 38px;
    font-weight: 800;
    margin-bottom: 14px;
  }

  .service-cta p {
    max-width: 760px;
    margin: 0 auto 26px !important;
    color: #64748b;
    line-height: 1.8;
  }

  .service-cta .btn-main {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #ef662f;
    color: #fff;
    padding: 14px 28px;
    border-radius: 50px;
    font-weight: 800;
    transition: 0.2s ease;
  }

  .service-cta .btn-main:hover {
    transform: translateY(-2px);
    background: #d94f18;
    color: #fff;
    box-shadow: 0 14px 30px rgba(239, 102, 47, 0.3);
  }

  @media (max-width: 768px) {
    .services-hero {
      padding: 65px 0;
    }

    .services-hero h1 {
      font-size: 34px;
    }

    .section-title-wrap h2,
    .why-services h2,
    .service-cta h2 {
      font-size: 28px;
    }

    .stat-row {
      grid-template-columns: 1fr;
    }
  }
</style>

<section class="breadcrumb-section">
    <h1>OUR SERVICES</h1>
    <p class="breadcrumb">
        <a href="index.php">Home</a> <span>| Services</span>
    </p>
</section>

<section class="services-section">
  <div class="container">
    <div class="section-title-wrap">
      <span>Our Core Services</span>
      <h2>Complete Technical Support Under One Roof</h2>
      <p>
        We work with a traditional engineering discipline and a forward-looking technology mindset.
        Every service is planned with proper requirement analysis, practical execution, testing,
        documentation and support.
      </p>
    </div>

    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <div class="service-card">
          <div class="service-icon">
            <i class="fa-solid fa-microchip"></i>
          </div>
          <h3>Embedded Systems Development</h3>
          <p>
            We design and develop embedded solutions using microcontrollers, sensors,
            communication modules and real-time control logic. These solutions are useful for
            academic projects, automation prototypes, industrial demonstrations and research-based work.
          </p>
          <ul>
            <li><i class="fa-solid fa-check"></i> Arduino, ESP32, Raspberry Pi and microcontroller-based systems</li>
            <li><i class="fa-solid fa-check"></i> Sensor integration, actuator control and automation logic</li>
            <li><i class="fa-solid fa-check"></i> Circuit planning, wiring guidance and testing support</li>
            <li><i class="fa-solid fa-check"></i> Project report, block diagram and working explanation</li>
          </ul>
        </div>
      </div>

      <div class="col-lg-4 col-md-6">
        <div class="service-card">
          <div class="service-icon">
            <i class="fa-solid fa-wifi"></i>
          </div>
          <h3>IoT Project Solutions</h3>
          <p>
            Our IoT services help connect hardware devices with cloud platforms, dashboards
            and mobile/web interfaces. We build solutions where devices can collect data,
            send alerts, monitor status and control equipment remotely.
          </p>
          <ul>
            <li><i class="fa-solid fa-check"></i> IoT-based monitoring and controlling systems</li>
            <li><i class="fa-solid fa-check"></i> Wi-Fi, Bluetooth, GSM and cloud connectivity</li>
            <li><i class="fa-solid fa-check"></i> Realtime data display and device control interface</li>
            <li><i class="fa-solid fa-check"></i> Smart home, smart agriculture and smart industry projects</li>
          </ul>
        </div>
      </div>

      <div class="col-lg-4 col-md-6">
        <div class="service-card">
          <div class="service-icon">
            <i class="fa-solid fa-laptop-code"></i>
          </div>
          <h3>Software & Web Development</h3>
          <p>
            We develop user-friendly websites, portals, admin panels and business applications.
            The focus is on clean UI, secure backend logic, database-driven modules and scalable
            code structure that can be maintained easily.
          </p>
          <ul>
            <li><i class="fa-solid fa-check"></i> Business websites, portfolio sites and landing pages</li>
            <li><i class="fa-solid fa-check"></i> Admin panels, dashboards and data management systems</li>
            <li><i class="fa-solid fa-check"></i> PHP, Laravel, CodeIgniter, Java, JSP and MySQL solutions</li>
            <li><i class="fa-solid fa-check"></i> Authentication, reports, filters and export features</li>
          </ul>
        </div>
      </div>

      <div class="col-lg-4 col-md-6">
        <div class="service-card">
          <div class="service-icon">
            <i class="fa-solid fa-graduation-cap"></i>
          </div>
          <h3>Training & Internship Programs</h3>
          <p>
            Our training programs are designed for students who want practical exposure
            beyond theory. We focus on fundamentals, hands-on implementation, project practice,
            interview preparation and industry-oriented workflow.
          </p>
          <ul>
            <li><i class="fa-solid fa-check"></i> Summer training, winter training and industrial training</li>
            <li><i class="fa-solid fa-check"></i> Web development, Java, Python, IoT and embedded systems</li>
            <li><i class="fa-solid fa-check"></i> Project-based learning with real implementation</li>
            <li><i class="fa-solid fa-check"></i> Guidance for reports, presentation and viva preparation</li>
          </ul>
        </div>
      </div>

      <div class="col-lg-4 col-md-6">
        <div class="service-card">
          <div class="service-icon">
            <i class="fa-solid fa-diagram-project"></i>
          </div>
          <h3>Academic Project Development</h3>
          <p>
            We provide complete support for mini projects, major projects and final year projects.
            Students get hardware/software implementation, proper explanation, report material and
            demonstration guidance.
          </p>
          <ul>
            <li><i class="fa-solid fa-check"></i> Mini project and major project development</li>
            <li><i class="fa-solid fa-check"></i> Final year project support for diploma and engineering students</li>
            <li><i class="fa-solid fa-check"></i> Synopsis, report, PPT and working model explanation</li>
            <li><i class="fa-solid fa-check"></i> Project customization as per college requirements</li>
          </ul>
        </div>
      </div>

      <div class="col-lg-4 col-md-6">
        <div class="service-card">
          <div class="service-icon">
            <i class="fa-solid fa-flask-vial"></i>
          </div>
          <h3>Lab Setup & Institutional Support</h3>
          <p>
            We support colleges, training centers and institutions in setting up practical labs.
            This includes equipment planning, project kits, demonstration modules, lab manuals and
            technical guidance for smooth operation.
          </p>
          <ul>
            <li><i class="fa-solid fa-check"></i> Electronics, IoT, robotics and computer lab setup</li>
            <li><i class="fa-solid fa-check"></i> Project kits, trainer kits and demo models</li>
            <li><i class="fa-solid fa-check"></i> Lab manuals, experiment structure and faculty support</li>
            <li><i class="fa-solid fa-check"></i> Customized setup according to syllabus and budget</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="process-section">
  <div class="container">
    <div class="section-title-wrap">
      <span>Our Working Process</span>
      <h2>How We Deliver Reliable Services</h2>
      <p>
        A good technical solution needs more than coding or assembly. It needs requirement clarity,
        design planning, disciplined execution, testing and proper handover. Our process keeps the
        work transparent and result-oriented.
      </p>
    </div>

    <div class="row g-4">
      <div class="col-lg-3 col-md-6">
        <div class="process-box">
          <div class="process-number">01</div>
          <h4>Requirement Analysis</h4>
          <p>
            We first understand the exact need, objective, user flow, technical scope,
            deadline, budget and expected output. This prevents confusion and avoids rework.
          </p>
        </div>
      </div>

      <div class="col-lg-3 col-md-6">
        <div class="process-box">
          <div class="process-number">02</div>
          <h4>Planning & Design</h4>
          <p>
            We prepare the system structure, module flow, hardware/software architecture,
            database planning and implementation roadmap before starting final development.
          </p>
        </div>
      </div>

      <div class="col-lg-3 col-md-6">
        <div class="process-box">
          <div class="process-number">03</div>
          <h4>Development & Testing</h4>
          <p>
            The solution is developed step by step and tested for functionality, usability,
            security, performance and real-time working conditions.
          </p>
        </div>
      </div>

      <div class="col-lg-3 col-md-6">
        <div class="process-box">
          <div class="process-number">04</div>
          <h4>Delivery & Support</h4>
          <p>
            We provide final files, documentation, explanation and support so that the client
            or student can use, present and maintain the solution confidently.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="why-services">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-7">
        <h2>Why Choose Softpro Innovations?</h2>
        <p>
          We believe that technology should not only look impressive, it should work correctly
          in real conditions. Our services are built with practical understanding, proper testing
          and a commitment to long-term reliability. Whether it is a student project, business
          software, IoT model or institutional lab setup, we maintain a structured and professional
          delivery approach.
        </p>

        <div class="why-list">
          <div class="why-item">
            <i class="fa-solid fa-circle-check"></i>
            <div>
              <h5>Practical Engineering Approach</h5>
              <span>
                We focus on working implementation, not just theoretical design. Every module
                is planned so it can be demonstrated, explained and improved.
              </span>
            </div>
          </div>

          <div class="why-item">
            <i class="fa-solid fa-shield-halved"></i>
            <div>
              <h5>Reliable and Maintainable Work</h5>
              <span>
                We prefer clean structure, understandable coding, proper wiring, safe database
                flow and documentation so future updates become easier.
              </span>
            </div>
          </div>

          <div class="why-item">
            <i class="fa-solid fa-users-gear"></i>
            <div>
              <h5>Student and Institution Friendly Support</h5>
              <span>
                We provide explanation support, report support, presentation guidance and
                technical clarification for smooth academic and institutional usage.
              </span>
            </div>
          </div>

          <div class="why-item">
            <i class="fa-solid fa-chart-line"></i>
            <div>
              <h5>Future Ready Technology Mindset</h5>
              <span>
                We work with current technologies like IoT, automation, web platforms,
                dashboards and smart devices while keeping the foundation strong.
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="service-stats-card">
          <div class="stat-row">
            <div class="stat-box">
              <h3>10+</h3>
              <p>Technology Domains</p>
            </div>
            <div class="stat-box">
              <h3>100+</h3>
              <p>Project Categories</p>
            </div>
            <div class="stat-box">
              <h3>24/7</h3>
              <p>Support Mindset</p>
            </div>
            <div class="stat-box">
              <h3>100%</h3>
              <p>Practical Focus</p>
            </div>
          </div>

          <p class="mt-4">
            Our focus is not only to deliver a service, but to create a dependable solution
            that solves the actual requirement and gives confidence to the user.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="industries-section">
  <div class="container">
    <div class="section-title-wrap">
      <span>Where We Help</span>
      <h2>Service Areas We Cover</h2>
      <p>
        Our services are suitable for multiple users and sectors, especially where technical
        implementation, product demonstration, student learning and digital automation are required.
      </p>
    </div>

    <div class="row g-3">
      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="industry-chip">
          <i class="fa-solid fa-user-graduate"></i>
          Engineering Students
        </div>
      </div>

      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="industry-chip">
          <i class="fa-solid fa-school"></i>
          Colleges & Institutes
        </div>
      </div>

      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="industry-chip">
          <i class="fa-solid fa-building"></i>
          Small Businesses
        </div>
      </div>

      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="industry-chip">
          <i class="fa-solid fa-seedling"></i>
          Smart Agriculture
        </div>
      </div>

      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="industry-chip">
          <i class="fa-solid fa-house-signal"></i>
          Smart Home Systems
        </div>
      </div>

      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="industry-chip">
          <i class="fa-solid fa-industry"></i>
          Industrial Automation
        </div>
      </div>

      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="industry-chip">
          <i class="fa-solid fa-cart-shopping"></i>
          E-Commerce Support
        </div>
      </div>

      <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="industry-chip">
          <i class="fa-solid fa-chalkboard-user"></i>
          Training Centers
        </div>
      </div>
    </div>
  </div>
</section>

<section class="service-cta">
  <div class="container">
    <h2>Need a Custom Technical Solution?</h2>
    <p>
      Share your requirement with us. Whether you need a project, website, IoT model,
      training support or lab setup, our team can guide you with the right technical approach
      and practical execution plan.
    </p>
    <a href="contactus.php" class="btn-main">
      Contact Us Now
      <i class="fa-solid fa-arrow-right"></i>
    </a>
  </div>
</section>

<?php
include 'footer.php';
?>