<?php
//0. SESSION開始！！
session_start();

require_once('funcs.php');

//１. DB接続します
$pdo = db_conn();

//LOGINチェック → funcs.phpへ関数化しましょう！
sschk();

// 検索キーワードを取得
$search_keyword = isset($_GET['search']) ? $_GET['search'] : '';

//２．データ取得SQL作成
if (!empty($search_keyword)) {
    $stmt = $pdo->prepare("SELECT * FROM gs_bm_table WHERE book LIKE :keyword OR worry LIKE :keyword OR coment LIKE :keyword");
    $stmt->bindValue(':keyword', '%'.$search_keyword.'%', PDO::PARAM_STR);
} else {
    $stmt = $pdo->prepare("SELECT * FROM gs_bm_table");
}
$status = $stmt->execute();

//３．データ表示
$view = "";
if ($status == false) {
    $error = $stmt->errorInfo();
    exit("ErrorQuery:".$error[2]);
} else {
    $view .= '<div class="row">';
    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $view .= '<div class="col-md-4 mb-4">';
        $view .= '<div class="card h-100">';
        $view .= '<div class="card-body">';
        $view .= '<h5 class="card-title">' . h($result['book']) . '</h5>';
        $view .= '<h6 class="card-subtitle mb-2 text-muted">' . h($result['date']) . '</h6>';
        $view .= '<p class="card-text"><strong>投稿者：</strong>' . h($result['username']) . '</p>';
        $view .= '<p class="card-text"><strong>悩み：</strong>' . h($result['worry']) . '</p>';
        $view .= '<p class="card-text"><strong>コメント：</strong>' . h($result['coment']) . '</p>';
        $view .= '<a href="' . h($result['url']) . '" class="btn btn-primary btn-block mb-2" target="_blank">詳細を見る</a>';
        if ($result['username'] === $_SESSION['username']) {
            $view .= '<div class="d-flex justify-content-between">';
            $view .= '<a href="detail.php?id=' . h($result['id']) . '" class="btn btn-success flex-grow-1 mr-2">更新</a>';
            $view .= '<a href="delete.php?id=' . h($result['id']) . '" class="btn btn-danger flex-grow-1" onclick="return confirm(\'本当に削除しますか？\');">削除</a>';
            $view .= '</div>';
        }
        $view .= '</div></div></div>';
    }
    $view .= '</div>';
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>登録データ表示</title>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        background-image: url('./img/background.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-color: #f8f9fa;
    }
    .navbar { background-color: #007bff; }
    .navbar-brand { color: white !important; }
    .navbar-brand:hover { text-decoration: underline; }
    .card { box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: 0.3s; }
    .card:hover { box-shadow: 0 8px 16px rgba(0,0,0,0.2); }
    .btn-primary { background-color: #007bff; border-color: #007bff; }
    .btn-primary:hover { background-color: #0056b3; border-color: #0056b3; }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark mb-4">
  <div class="container">
    <?=$_SESSION["username"]?>さん、一緒に悩みを解決しましょう！
    <a class="navbar-brand" href="index2.php">データ登録</a>
    <a class="navbar-brand" href="logout.php">ログアウト</a>
  </div>
</nav>

<div class="container">
  <h2 class="text-center mb-4 font-weight-bold">登録データ一覧</h2>
  
  <!-- 検索フォーム -->
  <form action="" method="GET" class="mb-4"  id="searchForm">
    <div class="input-group">
      <input type="text" class="form-control" placeholder="キーワードを入力" name="search" id="searchInput" value="<?= h($search_keyword) ?>">
      <div class="input-group-append">
        <button class="btn btn-primary" type="submit">検索</button>
        <button class="btn btn-secondary" type="button" id="resetSearch">リセット</button>
      </div>
    </div>
  </form>

  <?= $view ?>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<!-- 検索リセット用のJavaScript -->
<script>
document.getElementById('resetSearch').addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('searchForm').submit();
});
</script>

</body>
</html>