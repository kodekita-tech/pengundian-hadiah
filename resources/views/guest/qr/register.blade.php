@extends('guest.layouts.app')

@section('title', 'Pendaftaran - ' . $event->nm_event)

@section('content')
<div class="register-page-wrapper">
    <!-- Background Animation -->
    <div class="register-background">
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <!-- Main Registration Card -->
                <div class="register-card">
                    <!-- Header Section -->
                    <div class="register-header">
                        <div class="header-icon-wrapper">
                            <div class="header-icon-circle">
                                <i class="fi fi-rr-calendar"></i>
                            </div>
                        </div>
                        <h1 class="register-title">{{ $event->nm_event }}</h1>
                        <div class="event-meta">
                            <div class="meta-item">
                                <i class="fi fi-rr-calendar"></i>
                                <span>{{ $event->tgl_mulai->format('d M Y') }} - {{ $event->tgl_selesai->format('d M Y')
                                    }}</span>
                            </div>
                            @if($event->opd)
                            <div class="meta-item">
                                <i class="fi fi-rr-building"></i>
                                <span>{{ $event->opd->nama_penyelenggara }}</span>
                            </div>
                            @endif
                        </div>
                        <div class="status-badge">
                            <i class="fi fi-rr-check-circle"></i>
                            <span>Pendaftaran Dibuka</span>
                        </div>
                    </div>

                    <!-- Form Section -->
                    <div class="form-section">
                        @if($errors->has('event') || $errors->has('error'))
                        <div class="error-alert">
                            <i class="fi fi-rr-exclamation-triangle"></i>
                            <span>{{ $errors->first('event') ?: $errors->first('error') }}</span>
                        </div>
                        @endif

                        <div class="form-header">
                            <i class="fi fi-rr-user-add"></i>
                            <h3>Form Pendaftaran</h3>
                        </div>

                        <form action="{{ route('qr.register', $token) }}" method="POST" id="registrationForm"
                            class="registration-form">
                            @csrf

                            <!-- Name Field -->
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    <i class="fi fi-rr-user"></i>
                                    <span>Nama Lengkap <span class="required">*</span></span>
                                </label>
                                <div class="input-wrapper">
                                    <input type="text" class="form-input @error('name') is-invalid @enderror" id="name"
                                        name="name" placeholder="Masukkan nama lengkap Anda" value="{{ old('name') }}"
                                        required autofocus>
                                    <div class="input-icon">
                                        <i class="fi fi-rr-user"></i>
                                    </div>
                                </div>
                                @error('name')
                                <div class="error-message">
                                    <i class="fi fi-rr-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <!-- Phone Field -->
                            <div class="form-group">
                                <label for="phone" class="form-label">
                                    <i class="fi fi-rr-phone-call"></i>
                                    <span>Nomor HP <span class="required">*</span></span>
                                </label>
                                <div class="input-wrapper">
                                    <input type="tel" class="form-input @error('phone') is-invalid @enderror" id="phone"
                                        name="phone" placeholder="08xxxxxxxxxx" value="{{ old('phone') }}" required>
                                    <div class="input-icon">
                                        <i class="fi fi-rr-phone-call"></i>
                                    </div>
                                </div>
                                @error('phone')
                                <div class="error-message">
                                    <i class="fi fi-rr-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                                @enderror
                                <div class="input-hint">
                                    <i class="fi fi-rr-info"></i>
                                    <span>Nomor HP akan digunakan untuk verifikasi dan notifikasi</span>
                                </div>
                            </div>

                            <!-- Asal Field -->
                            <div class="form-group">
                                <label for="asal" class="form-label">
                                    <i class="fi fi-rr-marker"></i>
                                    <span>Asal <span class="required">*</span></span>
                                </label>
                                <div class="input-wrapper">
                                    <input type="text" class="form-input @error('asal') is-invalid @enderror" id="asal"
                                        name="asal" placeholder="Masukkan asal Anda" value="{{ old('asal') }}" required>
                                    <div class="input-icon">
                                        <i class="fi fi-rr-marker"></i>
                                    </div>
                                </div>
                                @error('asal')
                                <div class="error-message">
                                    <i class="fi fi-rr-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <!-- Captcha Field -->
                            @if(isset($captcha))
                            <div class="form-group">
                                <label for="captcha" class="form-label">
                                    <i class="fi fi-rr-shield-check"></i>
                                    <span>Verifikasi <span class="required">*</span></span>
                                </label>
                                <div class="captcha-wrapper">
                                    <div class="captcha-question">
                                        <span class="captcha-text">{{ $captcha['question'] }}</span>
                                        <button type="button" class="captcha-refresh" onclick="refreshCaptcha()" title="Refresh Captcha">
                                            <i class="fi fi-rr-refresh"></i>
                                        </button>
                                    </div>
                                    <div class="input-wrapper">
                                        <input type="number" class="form-input @error('captcha') is-invalid @enderror" 
                                            id="captcha" name="captcha" placeholder="Masukkan jawaban" 
                                            value="{{ old('captcha') }}" required>
                                        <div class="input-icon">
                                            <i class="fi fi-rr-shield-check"></i>
                                        </div>
                                    </div>
                                </div>
                                @error('captcha')
                                <div class="error-message">
                                    <i class="fi fi-rr-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            @endif

                            <!-- Info Box -->
                            <div class="info-box">
                                <div class="info-icon">
                                    <i class="fi fi-rr-ticket"></i>
                                </div>
                                <div class="info-content">
                                    <strong>Nomor Kupon Otomatis</strong>
                                    <p>Nomor kupon akan otomatis di-generate setelah pendaftaran berhasil</p>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="submit-button" id="submitBtn">
                                <span class="button-content">
                                    <i class="fi fi-rr-check"></i>
                                    <span>Daftar Sekarang</span>
                                </span>
                                <span class="button-loader" style="display: none;">
                                    <i class="fi fi-rr-spinner fi-spin"></i>
                                    <span>Memproses...</span>
                                </span>
                            </button>

                            <!-- Privacy Notice -->
                            <div class="privacy-notice">
                                <i class="fi fi-rr-shield-check"></i>
                                <span>Data yang Anda berikan akan digunakan untuk keperluan event ini</span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .register-page-wrapper {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        position: relative;
        overflow: hidden;
        padding: 2rem 0;
    }

    .register-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        overflow: hidden;
    }

    .floating-shapes {
        position: absolute;
        width: 100%;
        height: 100%;
    }

    .shape {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        animation: float 20s infinite ease-in-out;
    }

    .shape-1 {
        width: 300px;
        height: 300px;
        top: -100px;
        left: -100px;
        animation-delay: 0s;
    }

    .shape-2 {
        width: 200px;
        height: 200px;
        bottom: -50px;
        right: -50px;
        animation-delay: 5s;
    }

    .shape-3 {
        width: 150px;
        height: 150px;
        top: 50%;
        right: 10%;
        animation-delay: 10s;
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

    .register-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        position: relative;
        z-index: 1;
        overflow: hidden;
        margin-top: 1rem;
    }

    .register-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
    }

    /* Header Section */
    .register-header {
        text-align: center;
        padding: 3rem 2rem 2rem;
        background: linear-gradient(180deg, rgba(102, 126, 234, 0.05) 0%, transparent 100%);
    }

    .header-icon-wrapper {
        margin-bottom: 1.5rem;
    }

    .header-icon-circle {
        width: 90px;
        height: 90px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        50% {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.6);
        }
    }

    .register-title {
        font-size: 1.75rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1rem;
    }

    .event-meta {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6c757d;
        font-size: 0.95rem;
    }

    .meta-item i {
        color: #667eea;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.15) 100%);
        color: #28a745;
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        border: 2px solid rgba(40, 167, 69, 0.2);
    }

    .status-badge i {
        font-size: 1rem;
    }

    /* Form Section */
    .form-section {
        padding: 2rem;
    }

    .error-alert {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: #fee;
        color: #c33;
        padding: 1rem 1.25rem;
        border-radius: 12px;
        border-left: 4px solid #c33;
        margin-bottom: 1.5rem;
        animation: shake 0.5s;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-10px);
        }

        75% {
            transform: translateX(10px);
        }
    }

    .form-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
    }

    .form-header i {
        font-size: 1.5rem;
        color: #667eea;
    }

    .form-header h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #212529;
        margin: 0;
    }

    /* Form Groups */
    .form-group {
        margin-bottom: 1.75rem;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }

    .form-label i {
        color: #667eea;
        font-size: 1rem;
    }

    .required {
        color: #dc3545;
    }

    .input-wrapper {
        position: relative;
    }

    .form-input {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s;
        background: #fff;
    }

    .form-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        transform: translateY(-2px);
    }

    .form-input.is-invalid {
        border-color: #dc3545;
    }

    .form-input.is-invalid:focus {
        box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.1);
    }

    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #667eea;
        font-size: 1.1rem;
        pointer-events: none;
    }

    .input-hint {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
        color: #6c757d;
        font-size: 0.85rem;
    }

    .input-hint i {
        color: #667eea;
        font-size: 0.9rem;
    }

    .error-message {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #dc3545;
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }

    .error-message i {
        font-size: 0.9rem;
    }

    /* Info Box */
    .info-box {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
        padding: 1.25rem;
        border-radius: 12px;
        border-left: 4px solid #667eea;
        margin-bottom: 2rem;
    }

    .info-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .info-content {
        flex: 1;
    }

    .info-content strong {
        display: block;
        color: #212529;
        margin-bottom: 0.25rem;
        font-size: 0.95rem;
    }

    .info-content p {
        margin: 0;
        color: #6c757d;
        font-size: 0.85rem;
    }

    /* Submit Button */
    .submit-button {
        width: 100%;
        padding: 1.25rem 2rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .submit-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .submit-button:hover::before {
        left: 100%;
    }

    .submit-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
    }

    .submit-button:active {
        transform: translateY(0);
    }

    .submit-button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .button-content,
    .button-loader {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    /* Privacy Notice */
    .privacy-notice {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        color: #6c757d;
        font-size: 0.85rem;
        text-align: center;
    }

    .privacy-notice i {
        color: #667eea;
    }

    /* Captcha */
    .captcha-wrapper {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .captcha-question {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.875rem 1rem;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border: 2px solid rgba(102, 126, 234, 0.2);
        border-radius: 12px;
        gap: 1rem;
    }

    .captcha-text {
        font-size: 1.25rem;
        font-weight: 700;
        color: #667eea;
        font-family: 'Courier New', monospace;
        letter-spacing: 1px;
        flex: 1;
        text-align: center;
    }

    .captcha-refresh {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        cursor: pointer;
        transition: all 0.3s;
        flex-shrink: 0;
    }

    .captcha-refresh:hover {
        transform: rotate(180deg);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .captcha-refresh:active {
        transform: rotate(180deg) scale(0.95);
    }

    .captcha-refresh i {
        font-size: 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .register-header {
            padding: 2rem 1.5rem 1.5rem;
        }

        .form-section {
            padding: 1.5rem;
        }

        .register-title {
            font-size: 1.5rem;
        }

        .event-meta {
            flex-direction: column;
            gap: 0.75rem;
        }

        .form-input {
            padding: 0.875rem 0.875rem 0.875rem 2.75rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function refreshCaptcha() {
        $.ajax({
            url: "{{ route('qr.refresh-captcha', $token) }}",
            type: 'GET',
            success: function(response) {
                $('.captcha-text').text(response.question);
                $('#captcha').val('').focus();
            },
            error: function() {
                alert('Gagal refresh captcha. Silakan refresh halaman.');
            }
        });
    }

    $(document).ready(function() {
    $('#registrationForm').on('submit', function(e) {
        const $submitBtn = $('#submitBtn');
        const $buttonContent = $submitBtn.find('.button-content');
        const $buttonLoader = $submitBtn.find('.button-loader');
        
        $submitBtn.prop('disabled', true);
        $buttonContent.hide();
        $buttonLoader.show();
    });

    // Format phone number
    $('#phone').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        $(this).val(value);
    });

    // Add focus animation
    $('.form-input').on('focus', function() {
        $(this).closest('.form-group').addClass('focused');
    }).on('blur', function() {
        $(this).closest('.form-group').removeClass('focused');
    });
});
</script>
@endpush