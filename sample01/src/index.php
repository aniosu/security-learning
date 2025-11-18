<?php
// DB接続情報
$db_host = 'mysql';
$db_name = 'ctf_db';
$db_user = 'ctf_user';
$db_pass = 'ctf_password';

// PDOでDBに接続
try {
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
    // エラーモードを「例外」に設定
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB接続エラー: " - $e->getMessage());
}

// メッセージと結果を初期化
$message = '';
$query_results = []; // UNION攻撃の結果を格納する配列

// ... (上部のDB接続コードはそのまま) ...

// フォームが送信された（POSTリクエスト）場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // SQLクエリを構築（脆弱な方法で）
    $sql = "SELECT * FROM users WHERE username = '{$username}' AND password = '{$password}'";

    try {
        // SQLを実行
        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 1件でも結果が返ってきたら（＝認証回避またはUNION攻撃）
        if (count($rows) > 0) {
            
            $first_row = $rows[0];
            // 取得した行に 'username' カラムが存在するかチェック
            $first_row_username = $first_row['username'] ?? null;

            if ($first_row_username === 'admin') {
                // 【目的B】'admin'-- や ' OR 1=1-- でログインした場合
                $message = "ログイン成功！...しかし、あなたは「管理者」としてログインしてしまいました。";
                $message .= "<br>これは意図せず管理者になれてしまう恐ろしい現象ですが、<strong>CTFのフラグはここにはありません。</strong>";
            
            } else if ($first_row_username === 'user' || $first_row_username === 'guest') {
                // 【目的Aのヒント】'user'-- や 'guest'-- でログインした場合
                $message = "ログイン成功！" . htmlspecialchars($first_row_username, ENT_QUOTES, 'UTF-8') . "としてログインできました。";
                $message .= "<br>しかし、<strong>フラグはここにもないようです。</strong>";
                $message .= "<br>データベースの*他の場所*（別のテーブル）に隠されているかもしれません...。";
            
            } else {
                // 【UNION攻撃】'admin', 'user', 'guest' 以外の何かで認証が通った場合
                // （例：username が '2' や 'flag{...}' になった場合）
                $message = "ログイン成功！...しかし、何か様子がおかしいようです。";
                $message .= "<br>あなたの攻撃は認証を突破しましたが、これはAdminやUserではありません。";
                $message .= "<br>下の「クエリ実行結果」に、あなたの攻撃の<strong>本当の成果（フラグ）</strong>が隠されているはずです！";
            }

            // クエリ実行結果を表示するロジック（これは変更不要）
            foreach ($rows as $row) {
                $query_results[] = print_r($row, true);
            }

        } else {
            // ログイン失敗
            $message = "ログイン失敗。ユーザー名またはパスワードが違います。";
        }

    } catch (PDOException $e) {
        $message = "SQLエラー: " . $e->getMessage();
    }
}
// ... (以下のHTML部分はそのまま) ...
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>社内管理ポータル</title> <style>
        body { font-family: sans-serif; max-width: 500px; margin: 40px auto; }
        form { border: 1px solid #ccc; padding: 20px; border-radius: 8px; }
        div { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        .message { margin-top: 20px; padding: 10px; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        .results { margin-top: 20px; }
        .results pre { background-color: #eee; padding: 10px; border-radius: 5px; white-space: pre-wrap; word-wrap: break-word; }
    </style>
</head>
<body>
    <h2>社内管理ポータル - ログイン</h2> <p>関係者以外のアカウント特定および不正アクセスを固く禁じます。</p>
    
    <form method="POST">
        <div>
            <label for="username">ユーザーID:</label>
            <input type="text" id="username" name="username">
        </div>
        <div>
            <label for="password">パスワード:</label>
            <input type="password" id="password" name="password">
        </div>
        <button type="submit">ログイン</button>
    </form>

    <?php if ($message): ?>
        <div class="message <?php echo (strpos($message, '成功') !== false || strpos($message, 'flag') !== false) ? 'success' : 'error'; ?>">
            <?php echo $message; // FlagはHTMLなのでhtmlspecialcharsしない ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($query_results)): ?>
        <div class="results">
            <h3>クエリ実行結果 (全<?php echo count($query_results); ?>件)</h3>
            <?php foreach ($query_results as $result_text): ?>
                <pre><?php echo htmlspecialchars($result_text, ENT_QUOTES, 'UTF-8'); ?></pre>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    </body>
</html>