<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | TACTIC</title>
  <link href="<?= base_url('assets/img/favicon.png') ?>" rel="icon">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link href="<?= base_url('assets') ?>/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= base_url('assets') ?>/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= base_url('assets') ?>/css/toastr.min.css" rel="stylesheet">
  <link href="<?= base_url('assets') ?>/css/style.css" rel="stylesheet">

  <style>
    * {
      font-family: 'Inter', sans-serif;
    }

    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #c2c5ca 0%, #4dabf7 100%);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-wrapper {
      width: 100%;
      max-width: 460px;
    }

    /* Logo area */
    .logo-wrap {
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .logo-icon {
      width: 64px;
      height: 64px;
      background: linear-gradient(135deg, #1a5276, #0d6efd);
      border-radius: 18px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      color: #fff;
      box-shadow: 0 8px 20px rgba(13, 110, 253, .35);
      margin-bottom: .6rem;
    }

    .logo-title {
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      font-size: 22px;
      color: #fff;
      letter-spacing: .5px;
    }

    .logo-sub {
      color: rgba(255, 255, 255, .8);
      font-size: 13px;
    }

    /* Card */
    .login-card {
      background: rgba(255, 255, 255, .97);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      border: none;
      box-shadow: 0 16px 48px rgba(0, 0, 0, .18);
      padding: 2rem 2rem 1.5rem;
    }

    .login-card h5 {
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      font-size: 1.2rem;
      color: #1e293b;
    }

    /* Form */
    .form-label {
      font-weight: 600;
      font-size: 13px;
      color: #374151;
      margin-bottom: 5px;
    }

    .form-control {
      border-radius: 9px;
      height: 44px;
      border: 1.5px solid #e2e8f0;
      font-size: 14px;
      transition: all .2s;
    }

    .form-control:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 3px rgba(13, 110, 253, .12);
    }

    /* Password toggle */
    .input-eye {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #9ca3af;
      font-size: 1rem;
      transition: color .2s;
      z-index: 5;
    }

    .input-eye:hover {
      color: #0d6efd;
    }

    /* Button */
    .btn-login {
      height: 46px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 15px;
      letter-spacing: .3px;
      background: linear-gradient(135deg, #0d6efd, #0dcaf0);
      border: none;
      transition: all .3s;
    }

    .btn-login:hover:not(:disabled) {
      transform: translateY(-1px);
      box-shadow: 0 8px 24px rgba(13, 110, 253, .4);
    }

    .btn-login:disabled {
      opacity: .75;
    }

    /* Alert flash */
    .alert-flash {
      border-radius: 10px;
      font-size: 13.5px;
    }

    /* Attempt warning */
    #attempt-warn {
      font-size: 12px;
    }

    /* Divider */
    .login-footer {
      text-align: center;
      margin-top: 1.2rem;
      font-size: 12px;
      color: #9ca3af;
    }

    .logo-icon img {
      max-width: 50px;
      max-height: 50px;
      width: 100%;
      height: auto;
      display: block;
    }
  </style>
  <!-- CSRF Token Meta Tags -->
  <meta name="csrf-token-name" content="<?= $this->security->get_csrf_token_name(); ?>">
  <meta name="csrf-token-hash" content="<?= $this->security->get_csrf_hash(); ?>">
</head>

<body>

  <div class="container d-flex justify-content-center align-items-center min-vh-100 py-4">
    <div class="login-wrapper">

      <!-- Logo -->
      <div class="logo-wrap">
        <!-- <div class="logo-icon"><i class="bi bi-shield-check"></i></div> -->
        <div class="logo-icon"><img src="<?= base_url('assets/img/favicon.png') ?>" alt=""></div>
        <div class="logo-title">TACTIC</div>
        <div class="logo-sub">TOKA APPROVAL & COMMISSIONING TECHNICAL INSPECTION CENTER</div>
      </div>

      <!-- Card -->
      <div class="login-card">

        <h5 class="text-center mb-1">Masuk ke Akun Anda</h5>
        <p class="text-center text-muted mb-4" style="font-size:13px;">
          Masukkan username / email &amp; password
        </p>

        <?php if ($this->session->flashdata('success')): ?>
          <div class="alert alert-success alert-flash d-flex align-items-center gap-2 py-2">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            <span><?= $this->session->flashdata('success') ?></span>
          </div>
        <?php endif; ?>

        <form id="form-login" autocomplete="off">

          <!-- Identity -->
          <div class="mb-3">
            <label class="form-label">Username</label>
            <div class="input-group">
              <span class="input-group-text rounded-start" style="border:1.5px solid #e2e8f0;border-right:0;background:#f8fafc;">
                <i class="bi bi-person text-primary"></i>
              </span>
              <input type="text" name="identity" class="form-control rounded-end"
                style="border-left:0;"
                placeholder="username atau email@domain.com" required autofocus>
            </div>
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="position-relative">
              <div class="input-group">
                <span class="input-group-text rounded-start" style="border:1.5px solid #e2e8f0;border-right:0;background:#f8fafc;">
                  <i class="bi bi-lock text-primary"></i>
                </span>
                <input type="password" name="password" id="inputPassword"
                  class="form-control rounded-end" style="border-left:0;padding-right:42px;"
                  placeholder="Password" required>
              </div>
              <i class="bi bi-eye input-eye" id="togglePassword"></i>
            </div>
          </div>


          <!-- Submit -->
          <button type="submit" class="btn btn-login text-white w-100 mb-1" id="btnLogin">
            <span id="btnText"><i class="bi bi-box-arrow-in-right me-2"></i>Masuk</span>
            <span id="btnLoading" class="d-none">
              <span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...
            </span>
          </button>

        </form>

        <div class="login-footer">
          <i class="bi bi-shield-lock me-1"></i>
          Sistem terbatas untuk pengguna terotorisasi
        </div>
      </div>

    </div>
  </div>

  <script src="<?= base_url('assets') ?>/js/jquery.min.js"></script>
  <script src="<?= base_url('assets') ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?= base_url('assets') ?>/js/toastr.min.js"></script>

  <script>
    $(function() {

      // Setup global AJAX and Form CSRF protection for login page
      $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
        var csrfName = $('meta[name="csrf-token-name"]').attr('content');
        var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
        
        if (options.type.toUpperCase() === 'POST' && csrfName && csrfHash) {
          if (options.data instanceof FormData) {
            options.data.set(csrfName, csrfHash);
          } else if (typeof options.data === 'string') {
            var regex = new RegExp('(^|&)' + csrfName + '=[^&]*');
            if (regex.test(options.data)) {
              options.data = options.data.replace(regex, '$1' + csrfName + '=' + encodeURIComponent(csrfHash));
            } else {
              options.data += (options.data ? '&' : '') + csrfName + '=' + encodeURIComponent(csrfHash);
            }
          } else if (typeof options.data === 'object' && options.data !== null) {
            options.data[csrfName] = csrfHash;
          } else if (!options.data) {
            options.data = {};
            options.data[csrfName] = csrfHash;
          }
        }
      });

      // Update CSRF token on AJAX complete (for failure attempts)
      $(document).ajaxComplete(function(event, xhr, settings) {
        var csrfCookieName = 'csrf_cookie';
        
        function getCookie(name) {
          var value = "; " + document.cookie;
          var parts = value.split("; " + name + "=");
          if (parts.length === 2) return parts.pop().split(";").shift();
          return null;
        }

        var headerHash = xhr.getResponseHeader('X-CSRF-TOKEN');
        if (headerHash) {
          $('meta[name="csrf-token-hash"]').attr('content', headerHash);
          return;
        }

        try {
          var json = JSON.parse(xhr.responseText);
          var hash = json.csrf_hash || json.csrfHash || json.csrf_token;
          if (hash) {
            $('meta[name="csrf-token-hash"]').attr('content', hash);
            return;
          }
        } catch (e) {}

        setTimeout(function() {
          var cookieHash = getCookie(csrfCookieName);
          if (cookieHash) {
            $('meta[name="csrf-token-hash"]').attr('content', cookieHash);
          }
        }, 50);
      });

      // Toastr config
      toastr.options = {
        positionClass: 'toast-top-center',
        timeOut: 3000,
        progressBar: true,
        closeButton: true,
      };

      

      // ── Toggle password ─────────────────────────────────────
      $('#togglePassword').on('click', function() {
        var inp = $('#inputPassword');
        var isText = inp.attr('type') === 'text';
        inp.attr('type', isText ? 'password' : 'text');
        $(this).toggleClass('bi-eye bi-eye-slash');
      });

      // ── Submit login ────────────────────────────────────────
      var maxAttempt = 5;

      $('#form-login').on('submit', function(e) {
        e.preventDefault();

        // Loading state
        $('#btnText').addClass('d-none');
        $('#btnLoading').removeClass('d-none');
        $('#btnLogin').prop('disabled', true);

        $.ajax({
          url: '<?= base_url('auth/login') ?>',
          type: 'POST',
          data: $(this).serialize(),
          dataType: 'json',

          success: function(res) {
            if (res.status === 'success') {
              toastr.success(res.message);
              // Animasi sebelum redirect
              setTimeout(function() {
                window.location.href = res.redirect;
              }, 900);

            } else {
              toastr.error(res.message);

              // Tampilkan warning percobaan
              if (res.attempts !== undefined) {
                var sisa = maxAttempt - res.attempts;
                if (sisa > 0 && sisa <= 3) {
                  $('#attemptText').text('Sisa percobaan: ' + sisa + ' kali');
                  $('#attemptWarn').removeClass('d-none');
                }
              }
            }
          },

          error: function() {
            toastr.error('Terjadi kesalahan server. Coba lagi.');
          },

          complete: function() {
            $('#btnText').removeClass('d-none');
            $('#btnLoading').addClass('d-none');
            $('#btnLogin').prop('disabled', false);
          }
        });
      });

    });
  </script>

</body>

</html>