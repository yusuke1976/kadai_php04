<?php
//0. SESSION開始！！
session_start();

include "funcs.php";

//１. DB接続します
$pdo = db_conn();

//LOGINチェック → funcs.phpへ関数化しましょう！
sschk();

// ユーザーのプロフィール画像を取得
$stmt = $pdo->prepare("SELECT profile_image FROM gs_user_table5 WHERE username = :username");
$stmt->bindValue(':username', $_SESSION['username'], PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$profile_image = $user['profile_image'] ? 'uploads/' . $user['profile_image'] : 'path/to/default/image.jpg';

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>悩み登録</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-image: url('./img/background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Noto Sans JP', sans-serif;
            font-size: 16px;
        }

        .profile-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;   /* 真円 */
            object-fit: cover;    /* 枠に合わせて切り取る */
        }

        .navbar {
            background-color: #ff9800;
            padding: 15px 15px;
        }
        
        .navbar-brand {
            color: #ffffff !important;
            font-weight: 350;
            font-size: 1.2rem;
            margin-left: 10px; /* この行を追加 */
        }

        .navbar-brand:hover {
            text-decoration: underline;
        }

        .welcome-message {
            padding-left: 15px; /* この行を追加 */
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .card-header {
            background-color: #4a5568;
            color: #ffffff;
            border-radius: 15px 15px 0 0 !important;
            padding: 15px;
        }

        .card-header h2 {
            font-size: 1.3rem;
            margin-bottom: 0;
        }

        .card-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px;
            font-size: 1rem;
        }

        textarea.form-control {
            min-height: 100px;
        }

        .btn-primary {
            background-color: #4a5568;
            border-color: #4a5568;
            border-radius: 10px;
            padding: 12px;
            font-size: 1.1rem;
        }

        .btn-primary:hover {
            background-color: #2c3340;
            border-color: #2c3340;
        }
        
        @media (max-width: 768px) {
            .container {
                padding-left: 20px;
                padding-right: 20px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a>
            <img src="<?= $profile_image ?>" alt="Profile Image" class="profile-img">
            &thinsp;
            <?=$_SESSION["username"]?>さん、一緒に悩みを解決しましょう！
            </a>
            <a class="navbar-brand" href="select.php"><i class="fa fa-table"></i>登録データ一覧</a>
            <a class="navbar-brand" href="logout.php"><i class="fas fa-sign-out-alt"></i>ログアウト</a>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="mb-0">悩みの登録</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="insert2.php">
                            <input type="hidden" name="username" value="<?=$_SESSION['username']?>">
                            <div class="form-group">
                                <label for="worry">あなたの悩み</label>
                                <textarea class="form-control" id="worry" name="worry" rows="4" placeholder="ここに悩みを入力してください"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">送信</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>