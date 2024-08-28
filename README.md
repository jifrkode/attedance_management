# 勤怠管理システム

## 作成目的

## アプリケーションのURL

## 機能一覧
会員登録
ログイン/ログアウト
勤務開始/勤務終了
休憩開始/休憩終了
日付別勤怠情報取得して表示

## 環境構築
Dockerビルド
1. git clone リンク
2. docker-compose up-d --build
＊MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせてdocker-compose.ymlファイルを編集してください。
Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. .env.exampleファイルから.envを作成し、環境変数を変更、
   ＊Gメールでの送信設定は以下参照し、アプリパスワードを発行
   https://support.google.com/accounts/answer/185833?hl=ja
4. php artisan key:generate
5. php artisan migrate --seed

# 使用技術
<!-- ((例) Laravel 8.x(言語やフレームワーク、バージョンなどが記載されていると良い)) -->
* PHP 7.4.9
* Laravel 8.6.12
* MySQL 8.0.26
# URL
* 開発環境：http://localhost/
* phpMyadmin：http://localhost:8080/
## ER図
![erd](https://github.com/user-attachments/assets/bd941f94-0b65-44d5-941e-36f196b99572)

## テーブル
![image](https://github.com/user-attachments/assets/99097325-c73f-451d-be77-3c2bb7878626)

