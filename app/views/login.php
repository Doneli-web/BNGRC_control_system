<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription</title>
  <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header text-center"><h4>Login</h4></div>
        <div class="card-body">

          <?php if (!empty($success)): ?>
            <div class="alert alert-success">Inscription réussie ✅</div>
          <?php endif; ?>

          <form id="registerForm" method="post" action="/register" novalidate>
            <div id="formStatus" class="alert d-none"></div>

            <div class="mb-3">
              <label for="nom" class="form-label">Username</label>
              <input id="nom" name="username" class="form-control">
              <div class="invalid-feedback" id="nomError"></div>
            </div>

            <button class="btn btn-primary w-100" type="submit">Log</button>
          </form>

          <script src="/assets/js/validation-login.js" defer></script>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>