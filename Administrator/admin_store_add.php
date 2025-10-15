<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>店舗追加画面</title>
    <link rel="stylesheet" href="css/store_add.css">
</head>
<body>
    <div class="header-bar">
        <a href="admin_home.php" class="logo-link">
            <img src="../images/Akanpo.png" alt="サイトロゴ">
        </a>
        <div class="page-title">店舗追加</div>
    </div>

    <form class="store-add-form">
        <div class="form-group">
            <label>店名：</label>
            <input type="text" name="store_name">
        </div>

        <div class="form-group">
            <label>ジャンル：</label>
            <input type="text" name="genre">
        </div>

        <div class="form-group">
            <label>お問い合わせ：</label>
            <input type="text" name="contact">
        </div>

        <div class="form-group">
            <label>予約可否：</label>
            <div class="radio-group">
                <label><input type="radio" name="reservation" value="1">可</label>
                <label><input type="radio" name="reservation" value="0">不可</label>
            </div>
        </div>

        <div class="form-group">
            <label>住所：</label>
            <input type="text" name="address">
        </div>

        <div class="form-group">
            <label>交通手段：</label>
            <input type="text" name="access">
        </div>

        <div class="form-group">
            <label>営業時間：</label>
            <input type="text" name="business_hours">
        </div>

        <div class="form-group">
            <label>予算口コミ：</label>
            <input type="text" name="budget_review">
        </div>

        <div class="form-group">
            <label>支払い方法：</label>
            <input type="text" name="payment_method">
        </div>

        <div class="form-group">
            <label>貸切：</label>
            <div class="radio-group">
                <label><input type="radio" name="chartering" value="1">可</label>
                <label><input type="radio" name="chartering" value="0">不可</label>
            </div>
        </div>

        <div class="form-group">
            <label>ホームページ：</label>
            <input type="text" name="homepage">
        </div>

        <div class="form-group">
            <label>オープン日：</label>
            <input type="date" name="open_date">
        </div>

        <div class="form-group">
            <label>店舗画像追加：</label>
            <input type="file" name="store_image">
        </div>

        <div class="btn-center">
            <button class="add-btn" onclick="location.href='admin_stores.php'">登録</button>
        </div>
    </form>
</body>
</html>
