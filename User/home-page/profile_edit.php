<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>プロフィール変更画面</title>
  <style>
    body {
      font-family: sans-serif;
      margin: 0;
      background: #fff;
    }
    header {
      background: #f7d76b;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px;
    }
    header h1 {
      margin: 0;
      font-size: 20px;
      font-weight: bold;
    }
    form {
      padding: 16px;
    }
    label {
      display: block;
      font-size: 14px;
      font-weight: bold;
      margin: 10px 0 4px;
    }
    input, select {
      width: 100%;
      padding: 8px;
      border: 1px solid #aaa;
      border-radius: 4px;
    }
    .icon-upload {
      display: flex;
      justify-content: center;
      margin: 20px 0;
    }
    .icon-upload button {
      width: 56px;
      height: 56px;
      border: 1px solid #666;
      border-radius: 50%;
      font-size: 28px;
      background: none;
      cursor: pointer;
    }
    .btn {
      display: block;
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      margin-top: 10px;
      cursor: pointer;
    }
    .btn-update {
      background: #2dbe60;
      color: #fff;
    }
    .btn-logout {
      background: none;
      color: red;
      text-decoration: underline;
    }
  </style>
</head>
<body>

<header>
  <button onclick="history.back()">←</button>
  <h1>マイページ</h1>
  <div>👤</div>
</header>

<form action="profile_update.php" method="post" enctype="multipart/form-data">
  <label>ニックネーム <span style="color:orange;">必須</span></label>
  <input type="text" name="nickname" required>

  <label>メールアドレス</label>
  <input type="email" name="email">

  <label>お名前</label>
  <input type="text" name="name">

  <label>現住所</label>
  <input type="text" name="address">

  <label>性別</label>
  <select name="gender">
    <option value="">選択してください</option>
    <option value="男性">男性</option>
    <option value="女性">女性</option>
    <option value="その他">その他</option>
    <option value="無回答">無回答</option>
  </select>

  <label>アイコン追加</label>
  <div class="icon-upload">
    <button type="button">＋</button>
    <!-- 実際は<input type="file">を隠してJSで連動させると良い -->
  </div>

  <button type="submit" class="btn btn-update">更新</button>
  <button type="button" class="btn btn-logout" onclick="location.href='logout.php'">ログアウト</button>
</form>

</body>
</html>
