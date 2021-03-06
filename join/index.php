<?php
session_start();
require('../dbconnect.php');

if (!empty($_POST)) {
    // エラー項目の確認
    if ($_POST['name'] == '') {
        $error['name'] = 'blank';
    }
    if ($_POST['email'] == '') {
        $error['email'] = 'blank';
    }
    if (strlen($_POST['password']) < 4) {
        $error['password'] = 'length';
    }
    if ($_POST['password'] == '') {
        $error['password'] = 'blank';
    }
    $fileName = $_FILES['image']['name'];
    if(!empty($fileName)) {
        $ext = substr($fileName, -3);
        if ($ext != 'jpg' && $ext != 'gif') {
            $error['image'] = 'type';
        }
    }

    // 重複アカウントのチェック
    if (empty($error)) {
        $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
        $member->execute(array($_POST['email']));
        $record = $member->fetch();
        if ($record['cnt'] > 0) {
            $error['email'] = 'duplicate';
        }
    }

    if (empty($error)) {
        // 画像をアップロードする
        $image = date('TmdHis') . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' . $image);

        $_SESSION['join'] = $_POST;
        $_SESSION['join']['image'] = $image;
        header('Location: check.php');
        exit();
    }
}

// 書き直し
if ($_REQUEST['action'] == 'rewrite') {
    $_POST = $_SESSION['join'];
    $error['rewrite'] = true;
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
    <p>次のフォームに必要事項を記入してください</p>
    <form action="" method="POST" enctype="multipart/form-data">
        <dl>
            <dt>ニックネーム<span>※必須</span></dt>
            <dd>
                <input type="text" name="name" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['name'], ENT_QUOTES); ?>" />
                <?php if ($error['name'] == 'blank'): ?>
                    <p style="color: red;">*ニックネームを入力してください</p>
                <?php endif; ?>
            </dd>
            <dt>メールアドレス<span>※必須</span></dt>
            <dd>
                <input type="text" name="email" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['email'], ENT_QUOTES); ?>" />
                <?php if ($error['email'] == 'blank'): ?>
                    <p style="color: red;">*メールアドレスを入力してください</p>
                <?php elseif ($error['email'] == 'duplicate'): ?>
                    <p style="color: red;">*指定されたメールアドレスは既に登録されています</p>
                <?php endif; ?>
            </dd>
            <dt>パスワード<span>※必須</span></dt>
            <dd>
                <input type="password" name="password" size="10" maxlength="20" value="<?php echo htmlspecialchars($_POST['password'], ENT_QUOTES); ?>">
                <?php if ($error['password'] == 'blank'): ?>
                    <p style="color: red;">*パスワードを入力してください</p>
                <?php elseif ($error['password'] == 'length'): ?>
                    <p style="color: red;">*パスワードは4文字以上で入力してください</p>
                <?php endif; ?>
            </dd>
            <dt>写真など</dt>
            <dd>
                <input type="file" name="image" size="35" />
                <?php if ($error['image'] == 'type'): ?>
                    <p style="color: red;">*写真などは「.gif」または「.jpg」の画像を指定してください</p>
                <?php elseif (!empty($error)): ?>
                    <p style="color: red;">*恐れ入りますが、画像を改めて指定してください</p>
                <?php endif; ?>
            </dd>
        </dl>
        <div><input type="submit" value="入力内容を確認する"></div>
    </form>
</body>
</html>