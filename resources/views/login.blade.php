<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Employee Management</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="#"><b>Employee</b> Management</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>

                <form id="loginForm">
                    <div id="loginError" class="alert alert-danger" style="display: none;"></div>
                    <div class="input-group mb-3">
                        <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block" id="loginBtn">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                Sign In
                            </button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#loginForm').submit(function(e) {
                e.preventDefault();

                let email = $('#email').val().trim();
                let password = $('#password').val();
                let remember = $('#remember').is(':checked');

                if (!email || !password) {
                    $('#loginError').text('Please enter both email and password').show();
                    return;
                }

                $('#loginError').hide();
                $('#loginBtn').prop('disabled', true);
                $('#loginBtn .spinner-border').show();

                $.ajax({
                    url: '/api/login',
                    type: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({
                        email: email,
                        password: password
                    }),
                    success: function(response) {
                        if (response.status && response.data && response.data.token) {
                            // Store token
                            localStorage.setItem('auth_token', response.data.token);

                            // Store user info
                            if (response.data.user) {
                                localStorage.setItem('user_name', response.data.user.name);
                                localStorage.setItem('user_email', response.data.user.email);
                                localStorage.setItem('user_role', response.data.user.role || 'user');
                            }

                            // Redirect to departments page
                            window.location.href = '/departments';
                        } else {
                            $('#loginError').text('Invalid response from server').show();
                            $('#loginBtn').prop('disabled', false);
                            $('#loginBtn .spinner-border').hide();
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Login failed. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            errorMsg = '';
                            for (let field in errors) {
                                errorMsg += errors[field][0] + '\n';
                            }
                        }
                        $('#loginError').text(errorMsg).show();
                        $('#loginBtn').prop('disabled', false);
                        $('#loginBtn .spinner-border').hide();
                    }
                });
            });
        });
    </script>
</body>
</html>

