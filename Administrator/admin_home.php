<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>管理者ホーム画面</title>
    <link rel="stylesheet" href="css/style.css" />
    <style>
        /* PC横幅では縦横センター、スマホでは縦スクロールで中央付近に */
        .home-wrap {
            display: grid;
            place-items: center;
            padding: 24px 16px 40px;
        }

        .home-stack {
            width: min(560px, 100%);
            display: grid;
            gap: clamp(56px, 8vh, 120px);
            margin-top: clamp(24px, 12vh, 160px);
        }

        /* PC(>= 960px)では横2列のカードにもできる */
        @media (min-width: 960px) {
            .home-stack {
                gap: 48px;
                margin-top: 8vh;
                grid-template-columns: 1fr;
            }

            .big-button {
                height: 104px;
                font-size: 24px;
            }

            .admin-app {
                width: min(980px, 100vw);
                height: min(720px, 100vh);
            }

            .admin-banner {
                height: 100px;
            }
        }

        /* 超ワイド画面ではさらに余白調整 */
        @media (min-width: 1400px) {
            .admin-app {
                width: 1100px;
                height: 720px;
            }
        }
    </style>
</head>

<body>
    <main class="admin-app" data-title="管理者ホーム画面">
        <div class="admin-banner"></div>

        <section class="page home-wrap">
            <div class="home-stack" role="navigation" aria-label="ホームメニュー">
                <a class="big-button" href="/admin/members.html" aria-label="会員一覧へ">会員一覧</a>
                <a class="big-button" href="/admin/shops.html" aria-label="店舗一覧へ">店舗一覧</a>
            </div>
        </section>
    </main>

    <script>
        // キーボード操作(Enter/Spaceでフォーカス中の大ボタンを押下)
        document.querySelectorAll('.big-button').forEach(btn => {
            btn.setAttribute('tabindex', '0');
            btn.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    btn.click();
                }
            });
        });
    </script>
</body>

</html>