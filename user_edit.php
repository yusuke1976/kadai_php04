<?php

session_start();
include "funcs.php";
sschk();

// DB接続
$pdo = db_conn();

// 現在のユーザー情報を取得
$stmt = $pdo->prepare("SELECT * FROM gs_user_table5 WHERE username = :username");
$stmt->bindValue(':username', $_SESSION['username'], PDO::PARAM_STR);
$status = $stmt->execute();

if ($status == false) {
    sql_error($stmt);
} else {
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// POSTデータ取得
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_input(INPUT_POST, "username");
    $email = filter_input(INPUT_POST, "email");
    $new_password = filter_input(INPUT_POST, "new_password");
    $concern = filter_input(INPUT_POST, "concern");
    $genre = filter_input(INPUT_POST, "genre");

    // パスワード更新（新しいパスワードが入力された場合のみ）
    $password_sql = "";
    $password_param = [];
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $password_sql = ", password = :password";
        $password_param = [':password' => $hashed_password];
    }

    // プロフィール画像のアップロード処理
    $profile_image = $user['profile_image']; // デフォルトで現在の画像を保持
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $upload_dir = 'uploads/';
        $file_name = uniqid() . '_' . $_FILES['profile_image']['name'];
        $upload_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_file)) {
            // 古い画像を削除
            if (!empty($user['profile_image']) && file_exists($upload_dir . $user['profile_image'])) {
                unlink($upload_dir . $user['profile_image']);
            }
            $profile_image = $file_name;
        }
    }

    // SQLを作成
    $sql = "UPDATE gs_user_table5 SET username = :username, email = :email, 
            concern = :concern, genre = :genre";
    
    if (!empty($profile_image)) {
        $sql .= ", profile_image = :profile_image";
    }
    
    $sql .= " $password_sql WHERE username = :old_username";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':concern', $concern, PDO::PARAM_STR);
    $stmt->bindValue(':genre', $genre, PDO::PARAM_STR);
    $stmt->bindValue(':old_username', $_SESSION['username'], PDO::PARAM_STR);
    
    if (!empty($profile_image)) {
        $stmt->bindValue(':profile_image', $profile_image, PDO::PARAM_STR);
    }
    
    if (!empty($password_param)) {
        $stmt->bindValue(':password', $password_param[':password'], PDO::PARAM_STR);
    }

    $status = $stmt->execute();

    if ($status == false) {
        sql_error($stmt);
    } else {
        $_SESSION['username'] = $username; // セッション情報を更新
        redirect("select2.php");
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー情報編集</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-image: url('./img/background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Noto Sans JP', sans-serif;
            font-size: 16px;
        }
        .navbar {
            background-color: #ff9800;
            padding: 15px 0;
            display: flex;
            justify-content: center;
        }

        .navbar-content {
            width: 65%;
            padding: 0 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .profile-image-small {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .profile-image-nav {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .profile-image-placeholder {
            width: 100%;
            height: 100%;
            background-color: #ccc;
            border-radius: 50%;
        }

        .navbar-brand {
            color: #ffffff !important;
            font-weight: 350;
            font-size: 1.2rem;
            margin-left: 10px;
        }
        .navbar-brand:hover {
            text-decoration: underline;
        }
                
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 20px;
            margin: 0 auto;
            max-width: 50%;
        }
        .profile-image-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            position: relative;
            width: 200px;
            height: 200px;
            margin: 0 auto 20px;
        }
        .profile-image, #image-preview {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        #image-preview {
            display: none;
        }
        @media (max-width: 768px) {
            .container {
                max-width: 90%;
            }
        }
        @media (min-width: 768px) {
            .navbar-content {
                flex-direction: row;
                justify-content: space-between;
            }

            .user-info {
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-dark mb-4">
    <div class="navbar-content">
        <div class="user-info">
            <div class="profile-image-small">
                <?php if (!empty($user['profile_image'])): ?>
                    <img src="uploads/<?= $user['profile_image'] ?>" alt="Profile Image" id="current-image" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                <?php else: ?>
                    <div class="profile-image-placeholder"></div>
                <?php endif; ?>
            </div>
            <a>&ensp;</a>
            <span class="welcome-message"><?=$_SESSION["username"]?>さん、一緒に悩みを解決しましょう！</span>
        </div>
        <div class="nav-links">
            <a class="navbar-brand" href="select.php">登録データ一覧</a>
            <a class="navbar-brand" href="logout.php">ログアウト</a>
        </div>
    </div>
</nav>

    <div class="container">
        <h2 class="mb-4 font-weight-bold">ユーザー情報編集</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="profile-image-container">
                <?php if (!empty($user['profile_image'])): ?>
                    <img src="uploads/<?= h($user['profile_image']) ?>" alt="プロフィール画像" class="profile-image" id="current-image">
                <?php endif; ?>
                <img id="image-preview" src="#" alt="画像プレビュー">
            </div>
            <div class="form-group">
                <label for="profile_image">プロフィール画像</label>
                <input type="file" class="form-control-file" id="profile_image" name="profile_image" onchange="previewImage(this);">
            </div>
            <!-- 他のフォーム要素は変更なし -->
            <div class="form-group">
                <label for="username">ユーザー名</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= h($user['username']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= h($user['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="new_password">新しいパスワード（変更する場合のみ）</label>
                <input type="password" class="form-control" id="new_password" name="new_password">
            </div>
            <div class="form-group">
                <label for="concern">現在の悩み</label>
                <select class="form-control" id="concern" name="concern">
                    <option value="work" <?= $user['concern'] == 'work' ? 'selected' : '' ?>>仕事</option>
                    <option value="relationship" <?= $user['concern'] == 'relationship' ? 'selected' : '' ?>>人間関係</option>
                    <option value="health" <?= $user['concern'] == 'health' ? 'selected' : '' ?>>健康</option>
                    <option value="future" <?= $user['concern'] == 'future' ? 'selected' : '' ?>>将来</option>
                    <option value="other" <?= $user['concern'] == 'other' ? 'selected' : '' ?>>その他</option>
                </select>
            </div>
            <div class="form-group">
                <label for="genre">好きな本のジャンル</label>
                <select class="form-control" id="genre" name="genre">
                    <option value="selfhelp" <?= $user['genre'] == 'selfhelp' ? 'selected' : '' ?>>自己啓発</option>
                    <option value="psychology" <?= $user['genre'] == 'psychology' ? 'selected' : '' ?>>心理学</option>
                    <option value="philosophy" <?= $user['genre'] == 'philosophy' ? 'selected' : '' ?>>哲学</option>
                    <option value="fiction" <?= $user['genre'] == 'fiction' ? 'selected' : '' ?>>小説</option>
                    <option value="biography" <?= $user['genre'] == 'biography' ? 'selected' : '' ?>>伝記</option>
                    <option value="another" <?= $user['genre'] == 'another' ? 'selected' : '' ?>>その他</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">更新</button>
        </form>
    </div>

    <script>
    function previewImage(input) {
        var preview = document.getElementById('image-preview');
        var currentImage = document.getElementById('current-image');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (currentImage) {
                    currentImage.style.display = 'none';
                }
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
            if (currentImage) {
                currentImage.style.display = 'block';
            }
        }
    }
    </script>
</body>
</html>