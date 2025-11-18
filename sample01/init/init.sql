-- データベースがなければ作成
CREATE DATABASE IF NOT EXISTS ctf_db;
-- ctf_db を使用
USE ctf_db;

-- users テーブルを作成
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL
);

-- データを挿入
INSERT INTO users (username, password) VALUES
('admin', 'password123'),
('user', 'pass'),
('guest', 'guest');

-- ctf_db の ctf_user に権限を付与
GRANT ALL PRIVILEGES ON ctf_db.* TO 'ctf_user'@'%';
FLUSH PRIVILEGES;
-- --- ここから追記 ---

-- 価値向上のため、フラグ専用の秘密のテーブルを作成
CREATE TABLE IF NOT EXISTS secret_flag (
    id INT PRIMARY KEY,
    flag_value VARCHAR(100),
    description VARCHAR(255)
);

-- 秘密のフラグを挿入（これこそがUNION攻撃のターゲット）
INSERT INTO secret_flag (id, flag_value, description) VALUES
(1, 'flag{Kaisetsu_Page_URL_ha_/writeupSample01.html_desu}', 'おめでとう！君はUNION攻撃をマスターした！');