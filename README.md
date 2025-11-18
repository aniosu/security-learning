# 構築と攻撃から学べるCTF問題集 (Security Learning)

これは、Webセキュリティの脆弱性を「攻撃側」と「構築側」の両面から実践的に学ぶための、DockerベースのCTF問題集です。

## 🎯 このプロジェクトの目的

多くのCTF問題集は、クラウド上の「リモート環境」を攻撃するため、「構築側」のコード（Dockerfileやサーバー設定）を見ることができません。

この問題集は、あえて「ローカル実行型」を採用しています。
すべての問題はDocker-Composeで定義されており、学習者は「脆弱な環境の設計図」をすべて手元で確認・改変できます。

* **攻撃側 (Attacker):** 脆弱性を見つけ、エクスプロイト（攻撃）する技術を学びます。
* **構築側 (Builder):** なぜその脆弱性が生まれたのかを、コード、設定、インフラ（Docker）のレベルで学びます。

## 💻 必要な環境

* Docker Desktop
* WSL 2 (Windowsの場合)
* (VS Code + Remote - WSL 拡張機能を推奨)

---

## 🚀 問題一覧

### 01: SQLインジェクション (社内管理ポータル)

* **フォルダ:** `sample01/`
* **学べること:**
    * [攻撃側] 認証回避、`UNION`攻撃、`information_schema`を使った偵察
    * [構築側] Nginx + PHP-FPM + MySQL の3層構造、Docker-Composeでの連携、`Dockerfile`のカスタマイズ、脆弱なPHPコードの仕組み

#### 実行方法

1.  まず、このプロジェクト全体をあなたのPCにダウンロード（または`git clone`）します。
2.  VS Codeのターミナルなどで、`sample01` フォルダに移動します。

    ```bash
    cd sample01
    ```

3.  以下のコマンドを実行し、Nginx, PHP, MySQLのコンテナを起動します。

    ```bash
    docker compose up -d --build
    ```
    * `up`: 起動
    * `-d`: バックグラウンドで実行
    * `--build`: `php/Dockerfile` の内容を反映してイメージを構築

4.  ブラウザで `http://localhost:8080` にアクセスすると、問題のログインページが表示されます。

#### 解説 (Write-up)

問題が解けたら、または答えが知りたい場合は、環境内の以下のページにアクセスしてください。

* **攻撃側の解説:** `http://localhost:8080/writeupSample01.html`
* **構築側の解説:** `http://localhost:8080/build.html`

---

*(ここに、2問目、3問目ができたら追記していく...)*
