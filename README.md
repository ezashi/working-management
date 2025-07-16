# working-management


## 環境構築

**Dockerビルド**
1. `git clone https://github.com/ezashi/flea-market.git`
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`

**Laravel環境構築**
1. PHPコンテナに入る：`docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルから「.env」を作成し、環境環境変数を変更：`cp .env.example .env`
4. `.env`に以下の環境変数を追加
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5. アプリケーションキーの作成：`php artisan key:generate`
6. マイグレーションの実行：`php artisan migrate`
7. シーディングの実行：`php artisan db:seed`
8. シンボリックリンクの作成：`php artisan storage:link`

## 使用技術(実行環境)
- PHP 7.4.9
- Laravel 8.83.8
- MySQL 8.0.26
- nginx 1.21.1
- Docker
- Docker-compose

## ER図


## URL
**開発環境**: http://localhost/

| 機能 | URL | HTTPメソッド | ユーザー |
|------|-----|-------------|------|


## 機能一覧

### 認証機能
- **会員登録**: ユーザー名、メールアドレス、パスワードで新規登録
- **ログイン/ログアウト**: メールアドレスとパスワードによる認証

#### 管理者ユーザーログイン情報
- メールアドレス: admin@example.com
- パスワード: 12345678