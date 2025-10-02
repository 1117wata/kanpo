<!DOCTYPE html> 
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録画面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            flex-direction: column;
        }

        /* ヘッダーバー */
        .header-bar {
            width: 100%;
            background-color: #FFEC6E;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            box-shadow: 0px 2px 6px rgba(0,0,0,0.2);
            border-bottom: 3px solid black; /* 黒線を追加 */
        }

        .header-bar img {
            height: 40px;
            width: auto;
            margin-left: 20px;
            cursor: pointer;
        }

        .logo-link {
            display: inline-block;
        }

        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            margin-top: 40px;
        }

        h1 {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            background-color: #ccc;
            padding: 8px;
            border-radius: 5px 5px 0 0;
        }

        .input-wrapper {
            background-color: #eee;
            padding: 10px;
            border-radius: 0 0 5px 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            background-color: white;
        }

        .note {
            font-size: 0.9em;
            color: #555;
            margin-top: 5px;
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        button {
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            width: 100%;
            border: 2px solid black;
        }

        /* 戻るボタン専用 */
        .back-btn {
            background-color: #FFEC6E;
            color: black;
        }
        .back-btn:hover {
            background-color: #ddc50bff;
        }

        /* 登録ボタン専用 */
        .submit-btn {
            background-color: #454646ff;
            color: white;
        }
        .submit-btn:hover {
            background-color: #212120ff;
        }
    </style>
</head>
<body>
    <!-- ヘッダーバー -->
    <div class="header-bar">
        <a href="index.html" class="logo-link">
            <img src="kanpo.png" alt="サイトロゴ">
        </a>
    </div>

    <div class="form-container">
        <h2>新規会員登録</h2>
        
        <div class="form-group">
            <label class="form-label">お名前</label>
            <div class="input-wrapper">
                <input type="text" name="name">
                <div class="note">※苗字と名前の間にスペースは不要です</div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">ニックネーム</label>
            <div class="input-wrapper">
                <input type="text" name="nickname">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">メールアドレス</label>
            <div class="input-wrapper">
                <input type="email" name="email">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">パスワード</label>
            <div class="input-wrapper">
                <input type="password" name="password">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">住所</label>
            <div class="input-wrapper">
                <input type="text" name="address">
            </div>
        </div>

        <div class="button-group">
            <button type="button" class="back-btn">戻る</button>
            <button type="submit" class="submit-btn">この内容で会員登録する</button>
        </div>
    </div>
</body>
</html>
