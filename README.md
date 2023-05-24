# 打刻keeper

## マイグレーション
```
実行
$ npm run up
( php public_html/public/index.php migrate )

ロールバック
$ npm run back
( php public_html/public/index.php Migrate rollback 0 )
```

## build
```
$ npm run build
```
# watch
```
$ npm run watch
```

## local MySQL
```
$ mysql.server start

$ mysql.server stop
```

## 新規追加手順
### XSERVERサーバパネル
[X-SERVERビジネス]: https://business.xserver.ne.jp/login/server.php
サーバーID　　　　　： eweb

サーバーパスワード　： ahtq5yh9

- dk-keeper.comのサブドメイン追加設定

### MySQL追加
- eweb_dk001から連番とする
- DB_lists.txtに記述

### Sequel Proにてデータベースアクセス
- dakoku_sql.sqlファイル インポート

- configテーブル修正
- company_name
- system_id

### eweb_dakoku DB global_dataテーブルに追記

### FTP
dk-keeper.com/public_html/作成したサブドメイン内に

public_html/public下にある

.htaccess

favicon.ico

fonts/

index.php

をアップロード

### filesフォルダを作成777にする

### imagesフォルダを作成
layout変更があればimages/内に画像をアップロード

### アップロードしたindex.phpを編集
```
<?php

// 初期設定

// 環境
// define('ENVIRONMENT', 'development'); // 開発環境
define('ENVIRONMENT', 'production'); // 本番環境

// base uri
// define('BASE_URI', 'http://localhost:8000/'); // 開発環境
define('BASE_URI', 'https://dk-keeper.com/'); // 本番環境

// データベース名
// define('DATABASE', 'dk_demo'); // 開発環境
define('DATABASE', 'eweb_dk003'); // 本番環境

// サブドメイン
// define('SUB_DOMAIN', ''); // 開発環境
define('SUB_DOMAIN', 'resq-demo'); // 本番環境

// full base uri
// define('FULL_BASE_URI', 'http://localhost:8000/'); // 開発環境
define('FULL_BASE_URI', 'https://'.SUB_DOMAIN.'.dk-keeper.com/'); // 本番環境
```
### アップロードした.htaccessの下記のコメントアウトを外す
```
 # RewriteCond %{HTTPS} off
 # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]
```

### 邪魔だからデフォルトであるdefault_page.pngとindex.htmlは消す

完了！