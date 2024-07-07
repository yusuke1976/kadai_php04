<?php
//$_SESSION使うよ！
session_start();

include "funcs.php";
// sschk();

//1. POSTデータ取得
$username = filter_input( INPUT_POST, "username" );
$email    = filter_input( INPUT_POST, "email" );
$password = filter_input( INPUT_POST, "password" );
$concern  = filter_input( INPUT_POST, "concern" );
$genre    = filter_input( INPUT_POST, "genre" );
$password = password_hash($password, PASSWORD_DEFAULT);   //パスワードハッシュ化

//2. DB接続します
$pdo = db_conn();

//３．データ登録SQL作成
$sql = "INSERT INTO gs_user_table5(username,email,password,concern,genre,life_flg)VALUES(:username,:email,:password,:concern,:genre,0)";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':username', $username, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':email', $email, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':password', $password, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':concern', $concern, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':genre', $genre, PDO::PARAM_STR); //Integer（数値の場合 PDO::PARAM_INT)
$status = $stmt->execute();

//４．データ登録処理後
if ($status == false) {
    sql_error($stmt);
} else {
    redirect("login.php");
}
