<!-- LOGIN PAGE -->
<section class="login-section d-flex align-items-center justify-content-center vh-100">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-5">
        <div class="card login-card shadow-lg border-0 rounded-4 text-center p-4">
          
          <!-- Header -->
          <div class="mb-4">
            <img 
              src="./assets/images/nwssu-logo.png" 
              alt="NwSSU Logo" 
              class="img-fluid mb-3" 
              style="width: clamp(3rem, 10vw, 4rem);"
            >
            <img 
              src="./assets/images/ccis-logo.png" 
              alt="CCIS Logo" 
              class="img-fluid mb-3" 
              style="width: clamp(3rem, 10vw, 4rem);"
            >
            <img 
              src="./assets/images/subLogoLight.png" 
              alt="WDC Logo" 
              class="img-fluid mb-3" 
              style="width: clamp(4rem, 10vw, 6rem);"
            >
            <h4 class="fw-bold mb-1 text-dark">Admin Login</h4>
            <p class="text-muted small">Please sign in to continue</p>
          </div>

          <!-- Body -->
          <form action="login_backend.php" method="POST">
            <div class="mb-3 text-start">
              <label for="username" class="form-label fw-semibold">Username</label>
              <input 
                type="text" 
                class="form-control form-control-lg" 
                id="username" 
                name="username" 
                placeholder="Enter your username" 
                required
              >
            </div>

            <div class="mb-4 text-start">
              <label for="password" class="form-label fw-semibold">Password</label>
              <input 
                type="password" 
                class="form-control form-control-lg" 
                id="password" 
                name="password" 
                placeholder="Enter your password" 
                required
              >
            </div>

            <!-- Submit -->
            <div class="d-grid mb-3">
              <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-semibold">
                Login
              </button>
            </div>
          </form>

          <!-- Footer -->
          <div class="text-center mt-3">
            <p class="small text-muted mb-0">© <?= date("Y") ?> Intramurals System</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
