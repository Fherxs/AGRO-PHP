<?php
session_start();
require '../config/db.php'; // pastikan path ini benar

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        $error = "Email dan password wajib diisi.";
    } else {
        $query = $conn->prepare("SELECT * FROM karyawan WHERE email=? LIMIT 1");
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];

                // redirect sesuai role
                if ($user['role'] === 'admin') {
                    header("Location: ../admin/dashboard.php");
                    exit();
                } else {
                    header("Location: ../karyawan/dashboard.php");
                    exit();
                }
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Email tidak ditemukan!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login  AGRO GROUP</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* supaya ikon mata terlihat clickable */
    .input-group-text {
      cursor: pointer;
      user-select: none;
    }
    .card { max-width: 420px; width:100%; }
  </style>
</head>
<body class="bg-light">
  <div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
    <div class="card shadow-lg p-4">
      <h4 class="text-center mb-4 text-primary">Login  AGRO GROUP</h4>

      <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="POST" novalidate>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input id="email" type="email" name="email" class="form-control" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <div class="input-group">
            <input id="password" type="password" name="password" class="form-control" required aria-describedby="togglePassword">
            <span class="input-group-text" id="togglePassword" role="button" title="Tampilkan/Sembunyikan password" aria-label="Tampilkan atau sembunyikan password">
              <!-- default: mata tertutup (eye-slash). Kita akan ganti lewat JS -->
              <svg id="iconEye" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17.94 17.94A10.06 10.06 0 0 1 12 20c-5 0-9.27-3.11-11-7 1.17-2.55 3.08-4.68 5.34-6.05"></path>
                <path d="M1 1l22 22"></path>
                <path d="M12 8a4 4 0 0 0 4 4"></path>
              </svg>
            </span>
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Masuk</button>

        <p class="text-center mt-3 mb-0">
          Belum punya akun? <a href="register.php">Daftar disini</a>
        </p>
      </form>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Toggle visibility password + ubah ikon (eye / eye-slash)
  (function() {
    const toggle = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const icon = document.getElementById('iconEye');

    // two SVGs: eye (visible) and eye-slash (hidden)
    const svgEye = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
    const svgEyeSlash = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.06 10.06 0 0 1 12 20c-5 0-9.27-3.11-11-7 1.17-2.55 3.08-4.68 5.34-6.05"></path><path d="M1 1l22 22"></path><path d="M12 8a4 4 0 0 0 4 4"></path></svg>';

    // inisialisasi: set icon sesuai tipe input (password -> eye-slash)
    function setIcon() {
      if (passwordInput.type === 'password') {
        icon.parentNode.innerHTML = svgEyeSlash;
        // restore id and event
        icon.parentNode.id = 'togglePassword';
        // reassign icon element for later toggles
        // (we'll rely on parent innerHTML, keep listener on parent)
      } else {
        icon.parentNode.innerHTML = svgEye;
        icon.parentNode.id = 'togglePassword';
      }
    }

    // since we replaced innerHTML above sometimes, keep a safe handler via event delegation
    document.addEventListener('click', function(e) {
      const tgt = e.target;
      const parent = tgt.closest('#togglePassword');
      if (!parent) return;
      // toggle type
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
      } else {
        passwordInput.type = 'password';
      }
      // update icon
      const newIcon = passwordInput.type === 'password' ? svgEyeSlash : svgEye;
      parent.innerHTML = newIcon;
      parent.id = 'togglePassword';
    });

    // optional: toggle with keyboard (space/enter) when focused
    document.addEventListener('keydown', function(e) {
      const active = document.activeElement;
      if (active && active.id === 'togglePassword' && (e.key === ' ' || e.key === 'Enter')) {
        e.preventDefault();
        active.click();
      }
    });

    // initial set (in case)
    setIcon();
  })();
</script>
</body>
</html>
