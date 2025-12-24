<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unnamed Blog</title>
    <style>
        :root {
            --bg-color: #e0e0e0;
            --header-bg: #333333;
            --card-bg: #ffffff;
            --text-color: #333;
            --accent-color: #4a90e2;
            /* UI 中的藍色 */
            --input-bg: #f0f0f0;
            --nav-height: 60px;
        }

        body.dark-mode {
            --bg-color: #1a1a1a;
            --header-bg: #000000;
            --card-bg: #2c2c2c;
            --text-color: #f0f0f0;
            --input-bg: #333;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s, color 0.3s;
            padding-top: var(--nav-height);
            /* 留給 Header 的空間 */
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: var(--nav-height);
            background-color: var(--header-bg);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 1000;
        }

        .brand {
            font-size: 1.5rem;
            color: #6dd5fa;
            font-weight: 300;
        }

        .nav-controls {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-icon {
            font-size: 1.5rem;
            cursor: pointer;
        }

        .home-icon {
            background: #ddd;
            color: #000;
            border-radius: 50%;
            padding: 5px;
            width: 32px;
            height: 32px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .mode-toggle {
            background: #888;
            border: none;
            padding: 5px 15px;
            border-radius: 20px;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* --- 頁面切換 --- */
        .page {
            display: none;
            padding: 20px;
            animation: fadeIn 0.5s;
        }

        .page.active {
            display: block;
        }

        .d-none{
            display: none !important
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ---  首頁  --- */
        .grid-container {
            display: flex;
            gap: 40px;
        }

        .section-col {
            flex: 1;
        }

        .section-title {
            margin-bottom: 15px;
            font-weight: 300;
            letter-spacing: 1px;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
        }

        .card {
            background: var(--card-bg);
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-img {
            height: 150px;
            background-color: #7f8c8d;
            position: relative;
        }

        /* 模擬圖片內容 */
        .card-img::after {
            content: "IMG";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: rgba(255, 255, 255, 0.5);
        }

        .card-info {
            padding: 10px;
        }

        .card-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .card-date {
            font-size: 0.7rem;
            color: #888;
        }

        /* POST 按鈕 */
        .fab-post {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 80px;
            height: 80px;
            background: #ddd;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            cursor: pointer;
            color: #000;
            font-weight: bold;
            border: 2px solid #333;
        }

        .fab-post span {
            font-size: 2rem;
            line-height: 1rem;
            margin-bottom: 5px;
        }

        .detail-layout {
            display: flex;
            background: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            min-height: 80vh;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* 媒體左側圖 */
        .detail-image {
            flex: 2;
            background-color: #7f8c8d;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image-placeholder {
            width: 80%;
            height: 80%;
            background: #bdc3c7;
            border: 10px solid #555;
            border-radius: 10px;
            position: relative;
        }

        /* 中間資訊 */
        .detail-info {
            flex: 1.5;
            padding: 40px 20px;
            border-right: 1px solid #eee;
        }

        .user-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .avatar-small {
            width: 40px;
            height: 40px;
            background: #8c7b75;
            border-radius: 50%;
        }

        .content-text {
            line-height: 1.8;
            font-size: 0.95rem;
            color: var(--text-color);
        }

        .date-stamp {
            margin-top: 20px;
            text-align: right;
            font-size: 0.8rem;
            color: #888;
        }

        /* 右側留言區 */
        .detail-comments {
            flex: 1;
            padding: 20px;
            background: #f9f9f9;
            display: flex;
            flex-direction: column;
        }

        .body.dark-mode .detail-comments {
            background: #222;
        }

        .comment-list {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .comment-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .comment-button {
            border-style: none;
            cursor: pointer;

        }

        .comment-avatar {
            width: 30px;
            height: 30px;
            background: #8c7b75;
            border-radius: 50%;
        }

        .comment-content div:first-child {
            font-weight: bold;
            font-size: 0.85rem;
        }

        .comment-content div:last-child {
            font-size: 0.7rem;
            color: #888;
        }

        .comment-input-area {
            display: flex;
            gap: 10px;
        }

        .comment-input {
            flex: 1;
            padding: 8px;
            border-radius: 20px;
            border: 1px solid #ccc;
            background: var(--input-bg);
            color: var(--text-color);
        }

        /* --- 建立頁 --- */
        .create-container {
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            gap: 30px;
        }

        .upload-zone {
            flex: 1;
            border: 2px dashed #555;
            border-radius: 10px;
            height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }

        .form-zone {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background: var(--input-bg);
            color: var(--text-color);
        }

        textarea.form-control {
            height: 150px;
            resize: none;
        }

        .btn-primary {
            background-color: #0056b3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 1rem;
            align-self: flex-end;
        }

        /* --- Profile --- */
        .profile-header {
            display: flex;
            align-items: center;
            gap: 40px;
            padding: 40px;
            background: var(--card-bg);
            /* 簡化背景 */
            margin-bottom: 30px;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            background: #795548;
            border-radius: 50%;
        }

        .profile-stats {
            display: flex;
            gap: 20px;
            margin: 10px 0;
            font-family: serif;
        }

        .profile-intro {
            background: white;
            padding: 20px;
            border-radius: 15px;
            width: 300px;
            float: left;
            margin-right: 20px;
            color: black;
        }

        .profile-about-us {
            background: white;
            padding: 20px;
            border-radius: 15px;
            width: 1000px;
            float: left;
            color: black;
        }

        /* RWD */
        @media (max-width: 768px) {
            .grid-container {
                flex-direction: column;
            }

            .detail-layout {
                flex-direction: column;
            }

            .create-container {
                flex-direction: column;
            }

            .detail-image,
            .detail-info,
            .detail-comments {
                flex: none;
                width: 100%;
            }

            .detail-info {
                border-right: none;
                border-bottom: 1px solid #eee;
            }
        }
    </style>
</head>

<body>

    <header>
        <div class="nav-icon" onclick="navigateTo('home')">🏠</div>
        <div class="brand">Unnamed Blog</div>
        <div class="nav-controls">
            <a href="#" onclick="navigateTo('about-us')" style="color:inherit; text-decoration:none;">about us</a>
            <button class="mode-toggle" onclick="toggleTheme()">
                ☀️ 淺色模式
            </button>
            <div class="nav-icon" onclick="navigateTo('profile')">👤</div>
        </div>
    </header>

    <main id="app">

        <section id="home" class="page active">
            <div class="grid-container">
                <div class="section-col">
                    <h2 class="section-title">ALBUMS</h2>
                    <div class="card-grid">
                        <div class="card" id="albums1" onclick="navigateTo('detail')">
                            <div class="card-img"></div>
                            <div class="card-info">
                                <div class="card-title">ALBUM1</div>
                                <div class="card-date">2024/11/21</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-col">
                    <h2 class="section-title">MEDIA</h2>
                    <div class="card-grid">
                        <div class="card" id="media1" onclick="navigateTo('detail')">
                            <div class="card-img"></div>
                            <div class="card-info">
                                <div class="card-title">MEDIA1</div>
                                <div class="card-date">2024/11/21</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="fab-post" onclick="navigateTo('create')">
                <span>+</span>
                <div style="font-size: 0.8rem;">POST</div>
            </div>
        </section>

        <section id="detail" class="page">
            <div class="detail-layout">
                <div class="detail-image">
                    <div class="image-placeholder"></div>
                </div>

                <div class="detail-info">
                    <div class="user-header">
                        <div class="avatar-small"></div>
                        <h3>MY NAME</h3>
                    </div>
                    <h4>media name</h4>
                    <br>
                    <p class="content-text">
                        description
                    </p>
                    <div class="date-stamp">2000/01/01</div>

                    <div
                        style="margin-top: 30px; display: flex; flex-direction: column; gap: 15px; align-items: flex-end;">
                        <form action="" method="get">
                            <input type="hidden" name="like">
                            <button type="submit" class="comment-button" style="font-size: 2rem; color: #ff5252;">❤</button>
                        </form>
                    </div>
                </div>

                <div class="detail-comments">
                    <h3 style="margin-bottom: 15px; text-align: center;">comments</h3>
                    <div class="comment-list">

                        <div class="comment-input-area">
                            <form action="" method="get">
                                <input type="text" class="comment-input" name="comment-contents" placeholder="留下你的足跡吧!">
                                <button type="submit" style="background:none; border:none; cursor:pointer;">➤</button>
                            </form>
                        </div>
                    </div>
                </div>
        </section>

        <section id="create" class="page">
            <h2 style="margin-bottom: 20px;">Create New Post</h2>
            <div class="create-container">
                <div class="upload-zone">
                    <div style="font-size: 3rem;">📁</div>
                    <p>+新增媒體</p>
                    <input type="file" id="fileInput" name="upload_file" accept="image/*">
                </div>
                <div class="form-zone">
                    <div class="form-group">
                        <label>名稱</label>
                        <input type="text" name="album-title" class="form-control" placeholder="輸入標題...">
                    </div>

                    <div class="form-group">
                        <label>媒體類型</label>
                        <select name="media-type" class="form-control">
                            <option>請選擇</option>
                            <option value="type1">類型1 (Image)</option>
                            <option value="type2">類型2 (Video)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>圖片敘述</label>
                        <textarea name="description" class="form-control" placeholder="輸入內容..."></textarea>
                    </div>

                    <button type="submit" class="btn-primary"
                        onclick="alert('上傳成功！並返回首頁'); navigateTo('home');">上傳</button>
                </div>
            </div>
        </section>

        <section id="about-us" class="page">
            <div style="background: #d3d3d3; padding: 50px;">
                <div style="display: flex; align-items: flex-start; gap: 50px;">
                    <div class="profile-about-us">
                        <h2>關於我們</h2>
                        <br>
                        <h3>歡迎來到我們的部落格！我們是高雄大學資訊工程系大二學生</h3>
                    </div>
                    <div style="margin-left: auto;">
                        加入日期 2025/01/01
                    </div>
                </div>


            </div>
        </section>

        <section id="profile" class="page">
            <div style="background: #d3d3d3; padding: 50px;">
                <div style="display: flex; align-items: flex-start; gap: 50px;">
                    <div class="profile-intro">
                        <h2>自我介紹</h2>
                        <br>
                        <p>歡迎來到我的部落格！這裡是分享學習、生活、與一些靈光乍現想法的小基地。如果你對我的文章有任何想法，歡迎留言跟我交流！</p>
                    </div>

                    <div>
                        <div style="display: flex; align-items: center; gap: 20px;">
                            <div class="profile-avatar"></div>
                            <div>
                                <h1 style="font-size: 3rem; font-weight: bold;">MY NAME</h1>
                                <div class="profile-stats">
                                    <span id="fans-number">粉絲:</span>
                                    <span id="like-number">Like:</span>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top: 20px; font-size: 1.1rem;">
                            <div>✉ xxxxxxxx@gmail.com</div>
                            <div style="margin: 5px 0;">📷 instagram_account</div>
                            <div>▶ this_is_my_youtube</div>
                        </div>
                        <button id="add-btn" onclick="addInformation(true)" style="border-radius: 5px; margin-top: 15px; padding: 5px; cursor: pointer;">新增資訊</button>
                        <form id="addContactForm" class="d-none"
                            style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #ccc; display: flex; gap: 5px;">
                            <select class="addItem" name="add-type" style="padding: 5px;">
                                <option value="phone-number">📞 電話</option>
                                <option value="link">🔗 連結</option>
                                <option value="email">📧 E-mail</option>
                                <option value="💬">💬 IG</option>
                                <option value="📘">📘 FB</option>
                            </select>

                            <input type="text" name="add-value" placeholder="輸入資訊..." required style="padding: 5px; flex: 1;">
                            <button type="submit" style="cursor: pointer;">+</button>
                            <button type="button" onclick="addInformation(false)" style="cursor: pointer;">X</button>
                        </form>
                    </div>
                    <div style="margin-left: auto;">
                        加入日期 2025/09/01
                    </div>
                </div>
            </div>
        </section>

    </main>

    <script>
        function navigateTo(pageId) {
            // 隱藏所有頁面
            const pages = document.querySelectorAll('.page');
            pages.forEach(page => page.classList.remove('active'));

            // 顯示頁面
            const targetPage = document.getElementById(pageId);
            if (targetPage) {
                targetPage.classList.add('active');
            }

            // 滾動到頂部
            window.scrollTo(0, 0);
        }

        // 深色模式
        function toggleTheme() {
            const body = document.body;
            const btn = document.querySelector('.mode-toggle');

            body.classList.toggle('dark-mode');

            if (body.classList.contains('dark-mode')) {
                btn.innerHTML = '🌙 深色模式';
            } else {
                btn.innerHTML = '☀️ 淺色模式';
            }
        }

        function addInformation(show){
            const btn=document.getElementById('add-btn');
            const form=document.getElementById('addContactForm');

            if (show) {
                btn.classList.add('d-none');
                form.classList.remove('d-none');
            }
            else{
                btn.classList.remove('d-none');
                form.classList.add('d-none');
            }
        }
    </script>
</body>

</html>