<?php
session_start();
include 'koneksi.php';

$error = "";

if (isset($_SESSION['id_user'])) {
    header("Location: admin/");
    exit;
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string(
        $koneksi,
        $_POST['username']
    );

    $password = $_POST['password'];

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM users WHERE username = '$username' LIMIT 1"
    );

    if ($query && mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);

        // Sesuai database saat ini, password masih berupa teks biasa.
        if ($password === $user['password']) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama_user'] = $user['nama_user'];
            $_SESSION['username'] = $user['username'];

            // Catat aktivitas login admin.
            $stmtLog = mysqli_prepare(
                $koneksi,
                "INSERT INTO log_aktivitas (id_user, aktivitas)
                 VALUES (?, ?)"
            );

            if ($stmtLog) {
                $id_user = (int) $user['id_user'];
                $aktivitas = "Login ke sistem";

                mysqli_stmt_bind_param(
                    $stmtLog,
                    "is",
                    $id_user,
                    $aktivitas
                );

                mysqli_stmt_execute($stmtLog);
                mysqli_stmt_close($stmtLog);
            }

            header("Location: admin/");
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | SembakoKu</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            background: #fff;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        .logo {
            text-align: center;
            font-size: 48px;
            margin-bottom: 10px;
        }

        h2 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            color: #777;
            font-size: 13px;
            margin-bottom: 28px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 14px;
            outline: none;
        }

        input:focus {
            border-color: #2e7d32;
        }

        button {
            width: 100%;
            padding: 13px;
            background: #2e7d32;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #1b5e20;
        }

        .error {
            background: #ffebee;
            color: #c62828;
            padding: 11px;
            border-radius: 7px;
            font-size: 13px;
            margin-bottom: 18px;
            text-align: center;
        }

        .footer {
            text-align: center;
            color: #888;
            font-size: 12px;
            margin-top: 22px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo">🛒</div>

        <h2>SembakoKu</h2>
        <p class="subtitle">Belanja Hemat, Kebutuhan Lengkap.</p>

        <?php if ($error != ""): ?>
            <div class="error">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                placeholder="Masukkan username"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Masukkan password"
                required
            >

            <button type="submit" name="login">Login</button>
        </form>

        <div class="footer">
            &copy; <?= date('Y'); ?> SembakoKu
        </div>
    </div>
</body>
</html>