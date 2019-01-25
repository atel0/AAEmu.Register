<?php
require_once('./inc/config.php');
require_once('./inc/utils.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AAEmu Sign Up</title>
    <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <?php
    $success = false;
    $errors = [];
    $username = '';
    $email = '';
    $password = '';

    if (isset($_POST['submit'])) {
        $username = StrToLower(trim($_POST['username']));
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if (empty($username))
            $errors['username'] = 'Username is required';
        if (mb_ereg('[^0-9a-zA-Z_-]', $username))
            $errors['username'] = 'Username contains invalid format';
        if (empty($email))
            $errors['email'] = 'Email is required';
        if (mb_ereg('[^0-9a-zA-Z_-]', $username))
            $errors['email'] = 'Email contains invalid format';
        if (empty($password))
            $errors['password'] = 'Password is required';
        if (mb_ereg('[^0-9a-zA-Z_-]', $username))
            $errors['password'] = 'Password contains invalid format';

        if (count($errors) === 0) {
            $db = mysqli_connect($_CONFIG['host'], $_CONFIG['username'], $_CONFIG['password'], $_CONFIG['database']);
            $username = mysqli_real_escape_string($db, $username);
            $email = mysqli_real_escape_string($db, $email);
            $password = mysqli_real_escape_string($db, $password);

            $user_check_query = "SELECT username FROM users WHERE username='$username' LIMIT 1";
            $result = mysqli_query($db, $user_check_query);
            $user = mysqli_fetch_assoc($result);
            if ($user) {
                $errors['username'] = 'Username already exists';
            } else {
                if ((StrLen($username) < 4) or (StrLen($username) > 16))
                    $errors['username'] = 'Username must be longer than 4, and no more than 16 characters';
                if ((StrLen($password) < 4) or (StrLen($password) > 16))
                    $errors['password'] = 'Password must be longer than 4, and no more than 16 characters';

                if (count($errors) === 0) {
                    $salt = base64_encode(hash('sha256', $password, true));
                    $success = mysqli_query($db, "INSERT INTO users (username, password, email, last_ip) VALUES ('$username', '$salt', '$email', '" . GetIP() . "')") === true;
                }
            }
        }
    }
    ?>
    <div class="signup-content">
        <form method="POST" id="signup-form" class="signup-form <?= $success ? 'hide' : 'show' ?>">
            <div class="form-group">
                <input type="text" class="form-input" name="username" placeholder="Username" required
                       value="<?= $username ?>" onchange="toLowerCase(this);"/>
                <div class="form-input__error <?= isset($errors['username']) ? 'show' : 'hide' ?>">
                    <?= $errors['username'] ?>
                </div>
            </div>
            <div class="form-group">
                <input type="email" class="form-input" name="email" placeholder="E-Mail" required value="<?= $email ?>"/>
                <div class="form-input__error <?= isset($errors['email']) ? 'show' : 'hide' ?>">
                    <?= $errors['email'] ?>
                </div>
            </div>
            <div class="form-group">
                <input type="password" class="form-input" name="password" id="password" placeholder="Password"
                       value="<?= $password ?>" required minlength="4"/>
                <span toggle="#password" class="zmdi zmdi-eye-off field-icon toggle-password"></span>
                <div class="form-input__error <?= isset($errors['password']) ? 'show' : 'hide' ?>">
                    <?= $errors['password'] ?>
                </div>
            </div>
            <div class="form-group flex-center">
                <input type="submit" name="submit" class="form-submit submit" value="Sign up"/>
            </div>
            <div class="form-group">
                <label class="label-agree-term">
                    By clicking “Sign up”, you agree to our <a href="#" class="term-service">terms of service</a> and <a
                            href="#" class="term-service">privacy statement</a>.
                </label>
            </div>
        </form>
        <div class="signup-form <?= $success ? 'show' : 'hide' ?>">
            Account <?= $username ?> successfully registered!
        </div>
    </div>
</div>

<script>
    let elements = document.getElementsByClassName('toggle-password');
    for (let i = 0; i < elements.length; i++) {
        elements[i].onclick = function () {
            this.classList.toggle('zmdi-eye');
            this.classList.toggle('zmdi-eye-off');

            let toggle = this.getAttribute('toggle');
            let input = document.querySelector(toggle);
            if (input) {
                if (input.getAttribute('type') === 'password') {
                    input.setAttribute('type', 'text');
                } else {
                    input.setAttribute('type', 'password');
                }
            } else {
                console.error(`Input with toggle "${toggle}" undefined!`);
            }
        };
    }

    function toLowerCase(input) {
        input.value = input.value.toLowerCase();
    }
</script>
</body>
</html>