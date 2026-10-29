<?php include 'header.php' ?>

<?php
/* =======================
   BASIC PAGE DATA
======================= */
$page = [
    "title" => "EMBEDDED SYSTEM & EMBEDDED PROGRAMMING",
    "breadcrumb" => [
        "link" => "training.php",
        "name" => "Training/Internship",
        "current" => "Embedded System"
    ],
    "image" => "./assets/images/embeded-training2.png",
    "description" => "Embedded Systems & Programming focuses on building a strong link
        between hardware and software. It covers microcontrollers, sensors, and real-time
        systems with hands-on coding practice. You will learn low-level programming using
        C/C++ for system control and optimization. The course includes practical projects
        to develop real-world embedded applications. It provides a solid foundation for
        careers in electronics, IoT, and automation."
];

/* =======================
   FEATURE CARDS
======================= */
$features = [
    ["icon" => "fa-building", "title" => "Industry Experts", "desc" => "Learn by Professionals"],
    ["icon" => "fa-handshake", "title" => "Job Assistance", "desc" => "Placement Guidance"],
    ["icon" => "fa-clock", "title" => "6 Months"],
    ["icon" => "fa-book", "title" => "Study Material", "desc" => "Access Anytime"],
    ["icon" => "fa-laptop-code", "title" => "Live Projects", "desc" => "Real-World Tasks"]
];

/* =======================
   PLACEMENT COMPANIES
======================= */
$companies = [
    "1.png",
    "2.png",
    "3.png",
    "4.svg",
    "5.png",
    "7.png",
    "8.webp",
    "9.png",
    "1.png",
    "10.png",
    "2.png",
    "3.png"
];

/* =======================
   COURSE PHASES & MODULES
======================= */
$course = [

    "PHASE 1: ORIENTATION, PD & COMPUTER BASICS (Day 1–5)" => [
        ["id" => "module1", "title" => "Day 1 – Program Orientation & Embedded Overview", "topics" => [
            "Training structure & evaluation method",
            "Introduction to Embedded Systems",
            "Real-world embedded applications"
        ]],
        ["id" => "module2", "title" => "Day 2 – Personality Development (PD-I)", "topics" => [
            "Communication skills for engineers",
            "Technical discipline & lab ethics",
            "Presentation basics"
        ]],
        ["id" => "module3", "title" => "Day 3: Basic Computer Fundamentals", "topics" => [
            "Computer hardware overview",
            "Operating systems basics",
            "File & folder management"
        ]],
        ["id" => "module4", "title" => "Day 4: Software & Embedded Tools Introduction", "topics" => [
            "Editors, compilers & IDEs",
            "Embedded workflow overview",
            "Introduction to simulators"
        ]],
        ["id" => "module5", "title" => "Day 5: PD-II & Technical Skills", "topics" => [
            "Logical thinking",
            "Problem-solving techniques",
            "Task-based learning approach"
        ]]
    ],

    "PHASE 2: ELECTRONICS & EMBEDDED FOUNDATIONS (Day 6–10)" => [
        ["id" => "module6", "title" => "Day 6: Electronics Fundamentals", "topics" => [
            "Voltage, current & resistance",
            "Ohm’s law",
            "Passive components"
        ]],
        ["id" => "module7", "title" => "Day 7: Digital Electronics Basics", "topics" => [
            "Number systems",
            "Logic gates",
            "Truth tables"
        ]],
        ["id" => "module8", "title" => "Day 8: Embedded System Architecture", "topics" => [
            "Embedded hardware blocks",
            "Memory types",
            "Clock & reset"
        ]],
        ["id" => "module9", "title" => "Day 9: Microcontroller Fundamentals", "topics" => [
            "8051, AVR & ARM overview",
            "Pin functions",
            "Internal peripherals"
        ]],
        ["id" => "module10", "title" => "Day 10: Embedded Development Cycle", "topics" => [
            "Flowchart & algorithm",
            "Embedded coding process",
            "Simple logic design"
        ]]
    ],

    "PHASE 3: EMBEDDED C PROGRAMMING (Day 11–17)" => [
        ["id" => "module11", "title" => "Day 11: Introduction to C for Embedded", "topics" => [
            "C structure",
            "Data types & variables"
        ]],
        ["id" => "module12", "title" => "Day 12: Operators & Control Statements", "topics" => [
            "Conditional statements",
            "Looping concepts",
            "Logic-based programs"
        ]],
        ["id" => "module13", "title" => "Day 13: Functions & Arrays", "topics" => [
            "Function creation",
            "Array handling"
        ]],
        ["id" => "module14", "title" => "Day 14: Pointers & Memory", "topics" => [
            "Pointer basics",
            "Memory addressing"
        ]],
        ["id" => "module15", "title" => "Day 15: Bitwise Operations", "topics" => [
            "Bit masking",
            "Register-level logic"
        ]],
        ["id" => "module16", "title" => "Day 16: Embedded C Optimization", "topics" => [
            "Efficient coding",
            "Memory optimization"
        ]],
        ["id" => "module17", "title" => "Day 17: Mini Project-1 (C Based)", "topics" => [
            "Embedded logic mini project",
            "Code testing & review"
        ]]
    ],

    "PHASE 4: EMBEDDED SIMULATORS & TOOLS (Day 18–22)" => [
        ["id" => "module18", "title" => "Day 18: Embedded Simulation Tools", "topics" => [
            "Proteus software overview",
            "Circuit simulation basics"
        ]],
        ["id" => "module19", "title" => "Day 19: Microcontroller Simulation", "topics" => [
            "Simulating microcontrollers",
            "Loading hex files"
        ]],
        ["id" => "module20", "title" => "Day 20: Circuit Design & Testing", "topics" => [
            "Schematic design",
            "Error detection"
        ]],
        ["id" => "module21", "title" => "Day 21: Debugging Using Simulator", "topics" => [
            "Breakpoints",
            "Real-time variable monitoring"
        ]],
        ["id" => "module22", "title" => "Day 22: Mini Project-2 (Simulation Based)", "topics" => [
            "Simulator-based project",
            "Verification & analysis"
        ]]
    ],

    "PHASE 5: ARDUINO PLATFORM (Day 23–30)" => [
        ["id" => "module23", "title" => "Day 23: Arduino Hardware Architecture", "topics" => [
            "Microcontroller on Arduino",
            "Pin configuration"
        ]],
        ["id" => "module24", "title" => "Day 24: Arduino Embedded Programming", "topics" => [
            "Arduino sketch structure",
            "Digital I/O control"
        ]],
        ["id" => "module25", "title" => "Day 25: LED & Buzzer Interfacing", "topics" => [
            "Output device control",
            "Pattern generation"
        ]],
        ["id" => "module26", "title" => "Day 26: Switch & Relay Interfacing", "topics" => [
            "Input handling",
            "Debouncing logic"
        ]],
        ["id" => "module27", "title" => "Day 27: Motor Interfacing", "topics" => [
            "DC motor",
            "Motor driver ICs"
        ]],
        ["id" => "module28", "title" => "Day 28: Servo Motor Control", "topics" => [
            "PWM fundamentals",
            "Servo positioning"
        ]],
        ["id" => "module29", "title" => "Day 29: Sensor Interfacing", "topics" => [
            "IR sensor",
            "Ultrasonic sensor"
        ]],
        ["id" => "module30", "title" => "Day 30: Mini Project-3 (Arduino Based)", "topics" => [
            "Sensor-based project",
            "Hardware testing"
        ]]
    ],

    "PHASE 6: ADVANCED EMBEDDED INTERFACING (Day 31–35)" => [
        ["id" => "module31", "title" => "Day 31: LCD & Display Interfacing", "topics" => [
            "16×2 LCD",
            "Display formatting"
        ]],
        ["id" => "module32", "title" => "Day 32: Keypad & Input Devices", "topics" => [
            "Matrix keypad",
            "User input logic"
        ]],
        ["id" => "module33", "title" => "Day 33: Timers & Delays", "topics" => [
            "Timer concepts",
            "Accurate delay generation"
        ]],
        ["id" => "module34", "title" => "Day 34: Interrupts", "topics" => [
            "External interrupts",
            "Priority handling"
        ]],
        ["id" => "module35", "title" => "Day 35: Mini Project-4", "topics" => [
            "Interrupt-based project",
            "Performance testing"
        ]]
    ],

    "PHASE 7: ARM & STM32 MICROCONTROLLER (Day 36–40)" => [
        ["id" => "module36", "title" => "Day 36: Introduction to ARM Architecture", "topics" => [
            "ARM core basics",
            "STM32 family overview"
        ]],
        ["id" => "module37", "title" => "Day 37: STM32 Hardware Architecture", "topics" => [
            "GPIO registers",
            "Clock system"
        ]],
        ["id" => "module38", "title" => "Day 38: STM32 Programming Environment", "topics" => [
            "STM32CubeIDE",
            "Project creation"
        ]],
        ["id" => "module39", "title" => "Day 39: GPIO & Peripheral Programming", "topics" => [
            "LED & switch interfacing",
            "Register-level programming"
        ]],
        ["id" => "module40", "title" => "Day 40: Mini Project-5 (STM32 Based)", "topics" => [
            "STM32 embedded application",
            "Testing & validation"
        ]]
    ],

    "PHASE 8: MAJOR PROJECT & PROFESSIONAL SKILLS (Day 41–45)" => [
        ["id" => "module41", "title" => "Day 41: Major Project Planning", "topics" => [
            "Project selection",
            "Block diagram & flowchart"
        ]],
        ["id" => "module42", "title" => "Day 42: Major Project Implementation-I", "topics" => [
            "Hardware assembly",
            "Embedded coding"
        ]],
        ["id" => "module43", "title" => "Day 43: Major Project Implementation-II", "topics" => [
            "Debugging",
            "Optimization"
        ]],
        ["id" => "module44", "title" => "Day 44: Project Documentation & Presentation", "topics" => [
            "Report preparation",
            "Technical presentation"
        ]],
        ["id" => "module45", "title" => "Day 45: Final Demonstration & Evaluation", "topics" => [
            "Major project demo",
            "Assessment & certification"
        ]]
    ]
];
?>

<section class="breadcrumb-section">
    <h1>EMBEDDED SYSTEM & EMBEDDED PROGRAMMING</h1>
    <p class="breadcrumb">
        <a href="training.php">Training/Internship</a>
        <span>| Embedded System</span>
    </p>
</section>

<div class="container">
    <div class="row mt-5">
        <div class="col-sm-6">
            <img src="<?= $page['image']; ?>" class="img-fluid" alt="">
        </div>

        <div class="col-sm-6">
            <h5><?= $page['title']; ?></h5>
            <p style="text-align: justify;"><?= $page['description']; ?></p>

            <h6 class="text-success">New Batch Starting Soon – Enroll Today!</h6>

            <span class="btn btn-pink2">
                <i class="fa-solid fa-cart-shopping me-2"></i>Enroll Now
            </span>

            <span class="btn btn-outline-primary ms-5">
                <i class="fa-solid fa-share me-2"></i>Share
            </span>
        </div>
    </div>
</div>
<h3 class="my-4 fw-bold text-center">What Will You Learn?</h3>
<div class="container my-5">
    <div class="accordion" id="courseAccordion">

        <?php foreach ($course as $phase => $modules): ?>
            <h5><?= $phase ?></h5>

            <?php foreach ($modules as $m): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed"
                            data-bs-toggle="collapse"
                            data-bs-target="#<?= $m['id'] ?>">
                            <?= $m['title'] ?>
                        </button>
                    </h2>

                    <div id="<?= $m['id'] ?>" class="accordion-collapse collapse"
                        data-bs-parent="#courseAccordion">
                        <div class="accordion-body">
                            <ol>
                                <?php foreach ($m['topics'] as $t): ?>
                                    <li><?= $t ?></li>
                                <?php endforeach; ?>
                            </ol>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endforeach; ?>

    </div>
</div>

<?php include 'footer.php' ?>