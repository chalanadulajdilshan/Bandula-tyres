<?php
include 'class/include.php';

// Get active company logo and theme
$company = new CompanyProfile();
$activeCompany = $company->getActiveCompany();
$logoPath = 'assets/images/logo.png'; // Default logo path
$themeColor = '#5b73e8'; // Default theme color
$companyName = '';

if (!empty($activeCompany[0])) {
    $companyData = $activeCompany[0];
    if (!empty($companyData['image_name'])) {
        $logoPath = 'uploads/company-logos/' . $companyData['image_name'];
    }
    if (!empty($companyData['theme'])) {
        $themeColor = $companyData['theme'];
    }
    if (!empty($companyData['name'])) {
        $companyName = $companyData['name'];
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Login | <?php echo $companyName; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="#" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Theme Color Variables -->
    <style>
        :root {
            --bs-primary: <?php echo $themeColor; ?>;
            --bs-primary-rgb: <?php echo implode(', ', sscanf(ltrim($themeColor, '#'), '%02x%02x%02x')); ?>;
        }

        .authentication-bg {
            background: url('assets/images/Bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2) !important;
            border-radius: 20px !important;
        }

        .auth-card .card-body {
            color: #ffffff !important;
        }

        .auth-card .text-muted {
            color: rgba(255, 255, 255, 0.75) !important;
        }

        .auth-card h5 {
            color: #ffffff !important;
        }

        .auth-card label {
            color: #ffffff !important;
        }

        .auth-card .form-control {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: #ffffff !important;
        }

        .auth-card .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        .auth-card .form-check-label {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        .btn-premium-primary {
            background: linear-gradient(135deg, <?php echo $themeColor; ?> 0%, <?php echo adjustBrightness($themeColor, -20); ?> 100%) !important;
            border: none !important;
            color: white !important;
            padding: 10px 30px !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
            letter-spacing: 0.5px !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
        }

        .btn-premium-primary:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3) !important;
            filter: brightness(1.1) !important;
        }

        .btn-premium-outline {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            color: white !important;
            padding: 8px 20px !important;
            border-radius: 10px !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
            backdrop-filter: blur(5px) !important;
        }

        .btn-premium-outline:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            border-color: rgba(255, 255, 255, 0.5) !important;
            transform: translateY(-1px) !important;
        }

        .btn-primary {
            background-color: <?php echo $themeColor; ?>;
            border-color: <?php echo $themeColor; ?>;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: <?php echo adjustBrightness($themeColor, -10); ?>;
            border-color: <?php echo adjustBrightness($themeColor, -10); ?>;
        }

        .form-check-input:checked {
            background-color: <?php echo $themeColor; ?>;
            border-color: <?php echo $themeColor; ?>;
        }
    </style>

    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <link href="assets/libs/sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />

    <?php
    // Helper function to adjust color brightness
    function adjustBrightness($hex, $steps)
    {
        // Remove # if present
        $hex = str_replace('#', '', $hex);

        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Adjust brightness
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));

        // Convert back to hex
        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
            . str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
            . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }
    ?>
</head>

<body class="authentication-bg">

    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card auth-card">

                        <div class="card-body p-4">
                            <div class="text-center mt-2">
                                <a href="index.php" class="d-block auth-logo mb-4">
                                    <img src="<?php echo $logoPath; ?>" alt="Company Logo" class="img-fluid" style="max-height: 80px; max-width: 280px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.15));">
                                </a>
                                <h5 class="text-white">Welcome Back !</h5>
                                <p class="text-white-50">Sign in to continue again.</p>
                                <a href="live-stock-public.php" class="btn btn-premium-outline btn-sm mt-3">View Live Stock (Public)</a>
                            </div>

                            <div class="p-2 mt-4">
                                <form action="#" method="post" id="login">

                                    <div class="mb-3">
                                        <label class="form-label" for="username">Username</label>
                                        <input type="text" class="form-control" name="username" id="username" placeholder="Username">
                                    </div>

                                    <div class="mb-3">
                                        <div class="float-end">
                                            <a href="forget-password.php" class="text-muted">Forgot password?</a>
                                        </div>
                                        <label class="form-label" for="userpassword">Password</label>
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="auth-remember-check">
                                        <label class="form-check-label" for="auth-remember-check">Remember me</label>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <button class="btn btn-premium-primary w-100 waves-effect waves-light" type="submit" id="login-button">Log In Now</button>
                                    </div>


                                </form>
                            </div>

                        </div>
                    </div>

                    <div class="mt-5 text-center" style="color:white;">
                        <p> &copy; <script>
                                document.write(new Date().getFullYear())
                            </script> AI ERP Development <i class="mdi mdi-heart text-danger"></i> by sourcecode.lk</p>
                    </div>

                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>

    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
    <script src="assets/libs/jquery.counterup/jquery.counterup.min.js"></script>
    <script src="assets/libs/sweetalert/sweetalert.min.js" type="text/javascript"></script>
    <!-- App js -->
    <script src="assets/js/app.js"></script>
    <script src="ajax/js/login.js" type="text/javascript"></script>

</body>