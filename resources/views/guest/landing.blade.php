@extends('guest.layouts.app')

@section('title', 'Sistem Pengundian Hadiah')

@section('content')
<div class="landing-page-wrapper">
    <!-- Wave Background -->
    <div class="wave-background">
        <svg class="wave wave-top" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none">
            <path fill="rgba(152, 251, 152, 0.1)" fill-opacity="1"
                d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z">
            </path>
        </svg>
        <svg class="wave wave-middle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"
            preserveAspectRatio="none">
            <path fill="rgba(0, 191, 255, 0.08)" fill-opacity="1"
                d="M0,192L48,208C96,224,192,256,288,256C384,256,480,224,576,213.3C672,203,768,213,864,208C960,203,1056,181,1152,181.3C1248,181,1344,203,1392,213.3L1440,224L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z">
            </path>
        </svg>
        <svg class="wave wave-bottom" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"
            preserveAspectRatio="none">
            <path fill="rgba(152, 251, 152, 0.1)" fill-opacity="1"
                d="M0,288L48,272C96,256,192,224,288,224C384,224,480,256,576,261.3C672,267,768,245,864,229.3C960,213,1056,203,1152,197.3C1248,192,1344,192,1392,192L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
            </path>
        </svg>
    </div>

    <!-- Animated Background Elements -->
    <div class="background-elements">
        <div class="floating-circle circle-1"></div>
        <div class="floating-circle circle-2"></div>
        <div class="floating-circle circle-3"></div>
        <div class="geometric-shape shape-1"></div>
        <div class="geometric-shape shape-2"></div>
        <div class="geometric-shape shape-3"></div>
    </div>

    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <div class="hero-content">
                <div class="hero-icon-wrapper">
                    <div class="hero-icon-circle">
                        <i class="fi fi-rr-trophy"></i>
                        <div class="icon-glow"></div>
                    </div>
                </div>
                <h1 class="hero-title">
                    <span class="title-line">Sistem</span>
                    <span class="title-line gradient-text">Pengundian Hadiah</span>
                </h1>
                <p class="hero-subtitle">Platform digital untuk pengundian hadiah yang transparan dan terpercaya</p>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="features-section">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fi fi-rr-shield-check"></i>
                        </div>
                        <h3 class="feature-title">Transparan & Adil</h3>
                        <p class="feature-text">Sistem pengundian yang menggunakan teknologi modern untuk memastikan
                            proses yang transparan dan adil bagi semua peserta.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fi fi-rr-users"></i>
                        </div>
                        <h3 class="feature-title">Mudah Digunakan</h3>
                        <p class="feature-text">Platform yang user-friendly dengan proses pendaftaran yang sederhana
                            melalui QR Code atau link pendaftaran.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fi fi-rr-calendar-check"></i>
                        </div>
                        <h3 class="feature-title">Terintegrasi</h3>
                        <p class="feature-text">Sistem terintegrasi untuk manajemen event, pendaftaran peserta, dan
                            pengundian hadiah dalam satu platform.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guide Section -->
        <div class="guide-section">
            <div class="section-header">
                <h2 class="section-title">Petunjuk Penggunaan</h2>
                <p class="section-subtitle">Ikuti langkah-langkah berikut untuk mendaftar dan mendapatkan nomor kupon
                </p>
            </div>

            <div class="guide-timeline">
                <!-- Step 1 -->
                <div class="timeline-step">
                    <div class="step-connector"></div>
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <div class="step-icon">
                            <i class="fi fi-rr-qrcode"></i>
                        </div>
                        <h4 class="step-title">Akses Link atau Scan QR Code</h4>
                        <p class="step-description">Buka link pendaftaran yang diberikan atau scan QR Code yang tersedia
                            untuk mengakses halaman pendaftaran.</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="timeline-step">
                    <div class="step-connector"></div>
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <div class="step-icon">
                            <i class="fi fi-rr-user"></i>
                        </div>
                        <h4 class="step-title">Isi Nama Lengkap</h4>
                        <p class="step-description">Masukkan nama lengkap Anda pada form pendaftaran. Pastikan nama yang
                            diisi sesuai dengan identitas Anda.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="timeline-step">
                    <div class="step-connector"></div>
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <div class="step-icon">
                            <i class="fi fi-rr-phone-call"></i>
                        </div>
                        <h4 class="step-title">Isi Nomor HP</h4>
                        <p class="step-description">Masukkan nomor handphone yang aktif dan dapat dihubungi. Nomor HP
                            ini akan digunakan untuk keperluan verifikasi.</p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="timeline-step">
                    <div class="step-connector"></div>
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <div class="step-icon">
                            <i class="fi fi-rr-marker"></i>
                        </div>
                        <h4 class="step-title">Isi Asal</h4>
                        <p class="step-description">Masukkan asal daerah atau instansi Anda. Informasi ini diperlukan
                            untuk keperluan data pendaftaran.</p>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="timeline-step">
                    <div class="step-connector"></div>
                    <div class="step-card">
                        <div class="step-number">5</div>
                        <div class="step-icon">
                            <i class="fi fi-rr-shield-check"></i>
                        </div>
                        <h4 class="step-title">Isi Captcha</h4>
                        <p class="step-description">Masukkan kode captcha yang ditampilkan untuk memverifikasi bahwa
                            Anda bukan robot. Jika kode tidak jelas, klik tombol refresh untuk mendapatkan kode baru.
                        </p>
                    </div>
                </div>

                <!-- Step 6 -->
                <div class="timeline-step">
                    <div class="step-connector"></div>
                    <div class="step-card">
                        <div class="step-number">6</div>
                        <div class="step-icon">
                            <i class="fi fi-rr-check"></i>
                        </div>
                        <h4 class="step-title">Submit Pendaftaran</h4>
                        <p class="step-description">Setelah semua data terisi dengan benar, klik tombol "Daftar" atau
                            "Submit" untuk mengirimkan formulir pendaftaran.</p>
                    </div>
                </div>

                <!-- Step 7 -->
                <div class="timeline-step">
                    <div class="step-connector"></div>
                    <div class="step-card step-final">
                        <div class="step-number">7</div>
                        <div class="step-icon">
                            <i class="fi fi-rr-ticket"></i>
                        </div>
                        <h4 class="step-title">Dapatkan Nomor Kupon</h4>
                        <p class="step-description">Setelah pendaftaran berhasil, Anda akan mendapatkan nomor kupon
                            unik. Simpan nomor kupon ini dengan baik karena akan digunakan saat pengundian hadiah.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .landing-page-wrapper {
        min-height: 100vh;
        /* Final Recommendation: Background menggunakan #00BFFF yang digelapkan 95% */
        background: #f0f9ff;
        /* Subtle pattern overlay untuk depth */
        background-image:
            radial-gradient(circle at 20% 50%, rgba(0, 191, 255, 0.03) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(152, 251, 152, 0.02) 0%, transparent 50%);
        padding: 4rem 0;
        position: relative;
        overflow: hidden;
    }

    /* Subtle gradient overlay untuk depth tambahan */
    .landing-page-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg,
                rgba(152, 251, 152, 0.02) 0%,
                transparent 50%,
                rgba(0, 191, 255, 0.02) 100%);
        z-index: 0;
        pointer-events: none;
    }

    /* Wave Background */
    .wave-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
        overflow: hidden;
    }

    .wave {
        position: absolute;
        width: 100%;
        height: auto;
        opacity: 0.4;
    }

    .wave-top {
        top: 0;
        animation: waveMove 20s ease-in-out infinite;
    }

    .wave-middle {
        top: 20%;
        animation: waveMove 25s ease-in-out infinite reverse;
        opacity: 0.25;
    }

    .wave-bottom {
        bottom: 0;
        animation: waveMove 30s ease-in-out infinite;
    }

    @keyframes waveMove {

        0%,
        100% {
            transform: translateX(0) translateY(0);
        }

        50% {
            transform: translateX(-50px) translateY(20px);
        }
    }

    /* Animated Background Elements */
    .background-elements {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
    }

    .floating-circle {
        position: absolute;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(152, 251, 152, 0.12) 0%, rgba(0, 191, 255, 0.12) 100%);
        animation: float 30s infinite ease-in-out;
        filter: blur(50px);
    }

    .circle-1 {
        width: 400px;
        height: 400px;
        top: -150px;
        left: -150px;
        animation-delay: 0s;
    }

    .circle-2 {
        width: 300px;
        height: 300px;
        bottom: -100px;
        right: -100px;
        animation-delay: 5s;
    }

    .circle-3 {
        width: 250px;
        height: 250px;
        top: 60%;
        right: 15%;
        animation-delay: 10s;
    }

    /* Geometric Shapes */
    .geometric-shape {
        position: absolute;
        opacity: 0.06;
        animation: float 35s infinite ease-in-out;
    }

    .shape-1 {
        width: 200px;
        height: 200px;
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
        clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
        top: 10%;
        left: 5%;
        animation-delay: 0s;
    }

    .shape-2 {
        width: 150px;
        height: 150px;
        background: linear-gradient(135deg, #00BFFF 0%, #98FB98 100%);
        clip-path: polygon(25% 0%, 100% 0%, 75% 100%, 0% 100%);
        bottom: 20%;
        left: 10%;
        animation-delay: 7s;
    }

    .shape-3 {
        width: 180px;
        height: 180px;
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
        border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
        top: 30%;
        right: 8%;
        animation-delay: 14s;
    }

    @keyframes float {

        0%,
        100% {
            transform: translate(0, 0) rotate(0deg);
        }

        33% {
            transform: translate(30px, -30px) rotate(120deg);
        }

        66% {
            transform: translate(-20px, 20px) rotate(240deg);
        }
    }

    .container {
        position: relative;
        z-index: 2;
    }

    /* Hero Section */
    .hero-section {
        text-align: center;
        padding: 3rem 0 4rem;
        animation: fadeInDown 0.8s ease-out;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .hero-icon-wrapper {
        margin-bottom: 2rem;
        position: relative;
        display: inline-block;
    }

    .hero-icon-circle {
        width: 140px;
        height: 140px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        position: relative;
        animation: scaleIn 1s ease-out;
    }

    .hero-icon-circle i {
        font-size: 4.5rem;
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        position: relative;
        z-index: 2;
    }

    .icon-glow {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(152, 251, 152, 0.3) 0%, rgba(0, 191, 255, 0.3) 100%);
        border-radius: 50%;
        filter: blur(20px);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 0.5;
        }

        50% {
            transform: translate(-50%, -50%) scale(1.2);
            opacity: 0.8;
        }
    }

    @keyframes scaleIn {
        0% {
            transform: scale(0);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        line-height: 1.2;
    }

    .title-line {
        display: block;
    }

    .gradient-text {
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .hero-subtitle {
        font-size: 1.35rem;
        color: #6c757d;
        font-weight: 400;
        max-width: 700px;
        margin: 0 auto;
    }

    /* Features Section */
    .features-section {
        margin: 4rem 0;
    }

    .feature-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        padding: 2.5rem 2rem;
        height: 100%;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0, 191, 255, 0.15);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 191, 255, 0.05);
        position: relative;
        overflow: hidden;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #98FB98, #00BFFF);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .feature-card:hover::before {
        transform: scaleX(1);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 30px rgba(0, 191, 255, 0.3);
        transition: transform 0.4s ease;
    }

    .feature-card:hover .feature-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .feature-icon i {
        font-size: 2.5rem;
        color: white;
    }

    .feature-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 1rem;
    }

    .feature-text {
        color: #6c757d;
        line-height: 1.7;
        margin-bottom: 0;
    }

    /* Guide Section */
    .guide-section {
        margin-top: 5rem;
        padding: 3rem 0;
    }

    .section-header {
        text-align: center;
        margin-bottom: 4rem;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #212529;
        margin-bottom: 1rem;
    }

    .section-subtitle {
        font-size: 1.2rem;
        color: #6c757d;
    }

    /* Timeline Guide */
    .guide-timeline {
        position: relative;
        max-width: 900px;
        margin: 0 auto;
    }

    .timeline-step {
        position: relative;
        margin-bottom: 3rem;
        animation: fadeInUp 0.6s ease-out both;
    }

    .timeline-step:nth-child(1) {
        animation-delay: 0.1s;
    }

    .timeline-step:nth-child(2) {
        animation-delay: 0.2s;
    }

    .timeline-step:nth-child(3) {
        animation-delay: 0.3s;
    }

    .timeline-step:nth-child(4) {
        animation-delay: 0.4s;
    }

    .timeline-step:nth-child(5) {
        animation-delay: 0.5s;
    }

    .timeline-step:nth-child(6) {
        animation-delay: 0.6s;
    }

    .timeline-step:nth-child(7) {
        animation-delay: 0.7s;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .step-connector {
        position: absolute;
        left: 40px;
        top: 100px;
        width: 3px;
        height: calc(100% + 3rem);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.5), transparent);
        z-index: 0;
    }

    .timeline-step:last-child .step-connector {
        display: none;
    }

    .step-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 2rem;
        margin-left: 5rem;
        position: relative;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 191, 255, 0.05);
        border: 1px solid rgba(0, 191, 255, 0.15);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .step-card:hover {
        transform: translateX(10px);
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
    }

    .step-card.step-final {
        background: linear-gradient(135deg, rgba(152, 251, 152, 0.1) 0%, rgba(0, 191, 255, 0.1) 100%);
        border: 2px solid rgba(0, 191, 255, 0.3);
    }

    .step-number {
        position: absolute;
        left: -5rem;
        top: 2rem;
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        font-weight: 800;
        box-shadow: 0 10px 30px rgba(0, 191, 255, 0.4);
        border: 5px solid rgba(255, 255, 255, 0.95);
        z-index: 2;
    }

    .step-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, rgba(152, 251, 152, 0.2) 0%, rgba(0, 191, 255, 0.2) 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .step-icon i {
        font-size: 2rem;
        background: linear-gradient(135deg, #98FB98 0%, #00BFFF 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .step-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 0.75rem;
    }

    .step-description {
        color: #6c757d;
        line-height: 1.7;
        margin-bottom: 0;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .hero-title {
            font-size: 2.5rem;
        }

        .hero-subtitle {
            font-size: 1.15rem;
        }

        .section-title {
            font-size: 2rem;
        }
    }

    @media (max-width: 768px) {
        .landing-page-wrapper {
            padding: 2rem 0;
        }

        .hero-section {
            padding: 2rem 0 3rem;
        }

        .hero-icon-circle {
            width: 120px;
            height: 120px;
        }

        .hero-icon-circle i {
            font-size: 3.5rem;
        }

        .hero-title {
            font-size: 2rem;
        }

        .hero-subtitle {
            font-size: 1rem;
        }

        .features-section {
            margin: 3rem 0;
        }

        .feature-card {
            padding: 2rem 1.5rem;
        }

        .guide-section {
            margin-top: 3rem;
            padding: 2rem 0;
        }

        .section-header {
            margin-bottom: 3rem;
        }

        .section-title {
            font-size: 1.75rem;
        }

        .section-subtitle {
            font-size: 1rem;
        }

        .step-card {
            margin-left: 0;
            padding: 1.5rem;
        }

        .step-number {
            position: static;
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            margin-right: 1rem;
            display: inline-flex;
        }

        .step-connector {
            display: none;
        }

        .timeline-step {
            margin-bottom: 2rem;
        }

        .step-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .step-icon {
            margin: 0 auto 1rem;
        }
    }

    @media (max-width: 576px) {
        .hero-title {
            font-size: 1.75rem;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
        }

        .feature-icon i {
            font-size: 2rem;
        }

        .feature-title {
            font-size: 1.25rem;
        }

        .step-title {
            font-size: 1.2rem;
        }
    }
</style>
@endpush