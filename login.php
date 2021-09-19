<?php
require('dbconnect.php');
session_start();

if ($_COOKIE['email'] != '') {
    $_POST['email'] = $_COOKIE['email'];
    $_POST['password'] = $_COOKIE['password'];
    $_POST['save'] = 'on';
}

if (!empty($_POST)) {
    // ログインの処理
    if ($_POST['email'] != '' && $_POST['password'] != '') {
        $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
        $login->execute(array($_POST['email'], sha1($_POST['password'])));
        $member = $login->fetch();

        if ($member) {
            // ログイン成功
            $_SESSION['id'] = $member['id'];
            $_SESSION['time'] = time();

            // ログイン情報を記録する
            if ($_POST['save'] == 'on') {
                setcookie('email', $_POST['email'], time() + 60 * 60 * 24 * 14);
                setcookie('password', $_POST['password'], time() + 60 * 60 * 24 * 14);
            }

            header('Location: index.php');
            exit();
        } else {
            $error['login'] = 'failed';
        }
    } else {
        $error['login'] = 'blank';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>bbs</title>
</head>

<body>
    <div id="lead">
        <p>メールアドレスとパスワードを記入してログインしてください。</p>
        <p>入会手続きがまだの方はこちらからどうぞ。</p>
        <p>&raquo;<a href="join/index.php">入会手続きをする</a></p>
    </div>
    <form action="" method="POST">
        <dl>
            <dt>メールアドレス</dt>
            <dd>
                <input type="text" name="email" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['email'], ENT_QUOTES); ?>" />
                <?php if ($error['login'] == 'blank') : ?>
                    <p style="color: red;">*メールアドレスとパスワードをご記入ください</p>
                <?php elseif ($error['login'] == 'failed') : ?>
                    <p style="color: red;">*ログインに失敗しました。正しくご記入ください。</p>
                <?php endif; ?>
            </dd>
            <dt>パスワード</dt>
            <dd>
                <input type="password" name="password" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['password'], ENT_QUOTES); ?>" />
            </dd>
            <dt>ログイン情報の記録</dt>
            <dd>
                <input id="save" type="checkbox" name="save" value="on">
                <label for="save">次回からは自動的にログインする</label>
            </dd>
        </dl>
        <div><input type="submit" value="ログインする"></div>
    </form>
</body>

</html>