<?php
session_start();
$login_error = '';

// Check if the user is already logged in
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if ($username == "usm" && $password == "123") { // Credentials yang Anda berikan
        // Set session variable
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        $login_error = "Username atau password salah!"; // Pesan error jika login gagal
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebDasar</title> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary-gradient-start: #8e2de2; /* Deep Purple */
            --primary-gradient-end: #4a00e0;   /* Darker Purple */
            --secondary-gradient-start: #fc00ff; /* Pink-Purple */
            --secondary-gradient-end: #00dbde;   /* Cyan */
            --text-color-light: #ffffff;
            --text-color-dark: #cccccc;
            --input-bg: rgba(255, 255, 255, 0.1);
            --input-border: rgba(255, 255, 255, 0.2);
            --button-bg-start: #6a11cb;
            --button-bg-end: #2575fc;
            --error-color: #ff4d4d;
            --error-bg: rgba(255, 77, 77, 0.1);

            /* 3D specific colors/values */
            --container-shadow-1: rgba(0, 0, 0, 0.4);
            --container-shadow-2: rgba(255, 255, 255, 0.05) inset;
            --input-shadow-inner: inset 0 1px 3px rgba(0, 0, 0, 0.2);
            --input-shadow-focus: 0 0 0 3px rgba(0, 219, 222, 0.3);
            --button-shadow-offset: 4px;
            --button-shadow-color: rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            /* Menggunakan background image yang sudah kita sepakati sebelumnya */
            background-image: url('https://static.vecteezy.com/system/resources/previews/001/436/361/large_2x/abstract-blue-gradient-geometric-style-design-free-vector.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: var(--text-color-light);
            overflow: hidden;
            perspective: 1000px;
            position: relative;
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .login-wrapper {
            width: 90%;
            max-width: 500px;
            background: rgba(0, 0, 0, 0.5); /* Transparansi disesuaikan */
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow:
                0 15px 30px var(--container-shadow-1),
                0 -3px 10px var(--container-shadow-2) inset;
            border: 1px solid rgba(255, 255, 255, 0.15);
            overflow: hidden;
            position: relative;
            z-index: 1;
            transform-style: preserve-3d;
            transition: transform 0.3s ease-out, box-shadow 0.3s ease-out;
            padding: 50px;

            opacity: 0;
            transform: translateY(50px) translateZ(5px);
            animation: fadeInSlideUp 0.8s ease-out forwards;
            animation-delay: 0.3s;
        }

        @keyframes fadeInSlideUp {
            0% {
                opacity: 0;
                transform: translateY(50px) translateZ(5px);
            }
            100% {
                opacity: 1;
                transform: translateY(0) translateZ(5px);
            }
        }

        .login-wrapper:hover {
            transform: translateY(-3px) translateZ(5px);
            box-shadow:
                0 20px 40px var(--container-shadow-1),
                0 -5px 12px var(--container-shadow-2) inset;
        }

        .login-form-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 2;
        }

        .logo-and-title-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 15px;
        }

        h1 {
            font-size: 2.8em;
            margin-bottom: 0;
            font-weight: 700;
            color: var(--text-color-light);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .subtitle {
            font-size: 1.2em;
            color: var(--text-color-dark);
            margin-bottom: 30px;
            font-weight: 300;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .input-group {
            position: relative;
            margin-bottom: 22px;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-color-dark);
            font-size: 1em;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
        }

        .input-group input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 1px solid var(--input-border);
            border-radius: 8px;
            background: var(--input-bg);
            color: var(--text-color-light);
            font-size: 0.95em;
            outline: none;
            transition: all 0.3s ease; /* Transisi untuk efek hover/focus */
            box-shadow: var(--input-shadow-inner);
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        /* Efek 3D saat hover pada input */
        .input-group input:hover {
            transform: translateY(-2px) scale(1.01) translateZ(5px); /* Sedikit terangkat dan membesar */
            box-shadow:
                var(--input-shadow-inner),
                0 4px 8px rgba(0, 0, 0, 0.3), /* Bayangan lebih dalam */
                0 0 8px rgba(0, 219, 222, 0.5); /* Efek glow dari warna sekunder */
            border-color: var(--secondary-gradient-end);
        }

        .input-group input:focus {
            border-color: var(--secondary-gradient-end);
            box-shadow: var(--input-shadow-inner), var(--input-shadow-focus), 0 0 10px rgba(0, 219, 222, 0.7); /* Glow lebih kuat saat focus */
            transform: translateY(-1px) translateZ(7px); /* Lebih menonjol saat focus */
        }

        .options-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .checkbox-container {
            display: block;
            position: relative;
            padding-left: 30px;
            cursor: pointer;
            font-size: 0.9em;
            color: var(--text-color-dark);
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease; /* Transisi untuk efek hover */
        }

        .checkbox-container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkbox-container:hover {
            transform: translateY(-2px) translateZ(2px); /* Efek timbul pada hover */
            text-shadow: 0 0 5px rgba(255,255,255,0.2); /* Glow halus */
        }

        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 18px;
            width: 18px;
            background-color: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 4px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.2);
            transition: all 0.2s ease;
        }

        .checkbox-container:hover input ~ .checkmark {
            background-color: rgba(255, 255, 255, 0.15);
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.3), 0 0 3px rgba(0, 219, 222, 0.15);
        }

        .checkbox-container input:checked ~ .checkmark {
            background: linear-gradient(45deg, var(--secondary-gradient-start), var(--secondary-gradient-end));
            border-color: var(--secondary-gradient-end);
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        .checkbox-container input:checked ~ .checkmark:after {
            display: block;
        }

        .checkbox-container .checkmark:after {
            left: 6px;
            top: 3px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
        }

        .forgot-password {
            color: var(--text-color-dark);
            text-decoration: none;
            font-size: 0.9em;
            transition: all 0.3s ease; /* Transisi untuk efek hover */
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
        }

        .forgot-password:hover {
            color: var(--secondary-gradient-end);
            text-shadow: 0 0 8px rgba(0, 219, 222, 0.7); /* Glow lebih kuat */
            transform: scale(1.02) translateZ(2px); /* Efek zoom/timbul halus */
        }

        .login-button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(45deg, var(--button-bg-start), var(--button-bg-end));
            color: var(--text-color-light);
            font-size: 1.1em;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow:
                0 var(--button-shadow-offset) 10px var(--button-shadow-color),
                inset 0 1px 4px rgba(255, 255, 255, 0.1);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            position: relative;
            transform: translateZ(5px);
        }

        .login-button:hover {
            transform: translateY(-2px) translateZ(8px);
            box-shadow:
                0 calc(var(--button-shadow-offset) + 2px) 12px var(--button-shadow-color),
                inset 0 2px 6px rgba(255, 255, 255, 0.2);
            background: linear-gradient(45deg, var(--button-bg-end), var(--button-bg-start));
        }

        .login-button:active {
            transform: translateY(0) translateZ(2px);
            box-shadow:
                0 1px 5px var(--button-shadow-color),
                inset 0 0.5px 1px rgba(255, 255, 255, 0.05);
        }

        .signup-text {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9em;
            color: var(--text-color-dark);
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
        }

        .signup-text a {
            color: var(--secondary-gradient-end);
            text-decoration: none;
            font-weight: 400;
            transition: all 0.3s ease; /* Transisi untuk efek hover */
        }

        .signup-text a:hover {
            text-decoration: underline;
            color: var(--secondary-gradient-start);
            text-shadow: 0 0 8px rgba(0, 219, 222, 0.7); /* Glow lebih kuat */
            transform: scale(1.02) translateZ(2px); /* Efek zoom/timbul halus */
        }

        .error-message {
            color: var(--error-color);
            background-color: var(--error-bg);
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid var(--error-color);
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.9em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            animation: shake 0.5s ease-in-out;
            box-shadow: 0 1px 4px rgba(255, 77, 77, 0.2);
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-8px); }
            40%, 80% { transform: translateX(8px); }
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .login-wrapper {
                width: 90%;
                padding: 40px;
            }
        }

        @media (max-width: 768px) {
            .login-wrapper {
                width: 95%;
                margin: 15px;
                padding: 30px;
            }
            h1 {
                font-size: 2.2em;
            }
            .subtitle {
                font-size: 1em;
            }
        }

        @media (max-width: 480px) {
            .login-wrapper {
                padding: 20px;
            }
            .input-group input {
                padding: 12px 12px 12px 40px;
                font-size: 0.85em;
            }
            .input-group i {
                font-size: 0.9em;
                left: 12px;
            }
            .login-button {
                font-size: 1em;
                padding: 12px;
            }
            .options-group {
                margin-bottom: 20px;
            }
            .error-message {
                padding: 8px 12px;
                font-size: 0.8em;
            }
        }
    </style>
</head>
<body>
    <div id="particles-js"></div> <div class="login-wrapper">
        <div class="login-form-container">
            <div class="logo-and-title-wrapper">
                <h1>WebDasar</h1>
            </div>
            <p class="subtitle">Inget Engga..?</p> <?php if (!empty($login_error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $login_error; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="options-group">
                    <label class="checkbox-container">Ingatkan saya
                        <input type="checkbox" name="remember_me">
                        <span class="checkmark"></span>
                    </label>
                    <a href="#" class="forgot-password">Lupa password?</a>
                </div>
                <button type="submit" class="login-button">SUBMIT</button> </form>
            <div class="signup-text">
                Belum punya akun? <a href="#">Daftar di sini.</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script>
        /* Konfigurasi Particles.js */
        particlesJS('particles-js', {
            "particles": {
                "number": {
                    "value": 180, /* Jumlah partikel lebih banyak */
                    "density": {
                        "enable": true,
                        "value_area": 800
                    }
                },
                "color": {
                    "value": "#ffffff"
                },
                "shape": {
                    "type": "circle",
                    "stroke": {
                        "width": 0,
                        "color": "#000000"
                    },
                    "polygon": {
                        "nb_sides": 5
                    }
                },
                "opacity": {
                    "value": 0.8, /* Opacity partikel lebih tinggi */
                    "random": false,
                    "anim": {
                        "enable": false,
                        "speed": 1,
                        "opacity_min": 0.1,
                        "sync": false
                    }
                },
                "size": {
                    "value": 5, /* Ukuran partikel lebih besar */
                    "random": true,
                    "anim": {
                        "enable": false,
                        "speed": 40,
                        "size_min": 0.1,
                        "sync": false
                    }
                },
                "line_linked": {
                    "enable": true,
                    "distance": 150,
                    "color": "#ffffff",
                    "opacity": 0.7, /* Opacity garis penghubung lebih tinggi */
                    "width": 1
                },
                "move": {
                    "enable": true,
                    "speed": 10, /* Kecepatan gerakan lebih cepat */
                    "direction": "none",
                    "random": false,
                    "straight": false,
                    "out_mode": "out",
                    "bounce": false,
                    "attract": {
                        "enable": false,
                        "rotateX": 600,
                        "rotateY": 1200
                    }
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": {
                    "onhover": {
                        "enable": true,
                        "mode": "grab"
                    },
                    "onclick": {
                        "enable": true,
                        "mode": "push"
                    },
                    "resize": true
                },
                "modes": {
                    "grab": {
                        "distance": 140,
                        "line_linked": {
                            "opacity": 1
                        }
                    },
                    "bubble": {
                        "distance": 400,
                        "size": 40,
                        "duration": 2,
                        "opacity": 8,
                        "speed": 3
                    },
                    "repulse": {
                        "distance": 200,
                        "duration": 0.4
                    },
                    "push": {
                        "particles_nb": 4
                    },
                    "remove": {
                        "particles_nb": 2
                    }
                }
            },
            "retina_detect": true
        });
    </script>
</body>
</html>