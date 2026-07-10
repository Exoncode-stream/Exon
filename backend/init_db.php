<?php

try {
    $db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec("CREATE TABLE IF NOT EXISTS links (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        url TEXT NOT NULL
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS videos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        youtube_id TEXT NOT NULL,
        category TEXT NOT NULL
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS articles (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        content TEXT NOT NULL
    )");

    echo "Database and tables created successfully!\n";

    $countLinks = $db->query("SELECT COUNT(*) FROM links")->fetchColumn();
    if ($countLinks == 0) {
        $stmt = $db->prepare("INSERT INTO links (name, url) VALUES (:name, :url)");
        
        $links = [
            ["name" => "YouTube", "url" => "https://www.youtube.com/@exon9858"],
            ["name" => "GitHub", "url" => "https://github.com/Exoncode-stream/"],
            ["name" => "Discord", "url" => "guiireg"]
        ];

        foreach ($links as $link) {
            $stmt->execute($link);
        }
        echo "Initial links inserted successfully!\n";
    }

    $countVideos = $db->query("SELECT COUNT(*) FROM videos")->fetchColumn();
    if ($countVideos == 0) {
        $stmt = $db->prepare("INSERT INTO videos (title, youtube_id, category) VALUES (:title, :youtube_id, :category)");

        $videos = [
            [
                "title" => "Creating Learn Code website with Next.js",
                "youtube_id" => "https://www.youtube.com/watch?v=ILW91gXl30Y",
                "category" => "Web development"
            ]
        ];

        foreach ($videos as $video) {
            $stmt->execute($video);
        }
        echo "Initial videos inserted successfully!\n";
    }

    $countArticles = $db->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    if ($countArticles == 0) {
        $stmt = $db->prepare("INSERT INTO articles (title, content) VALUES (:title, :content)");

        $articles = [
            [
                "title" => "Introduction to Next.js",
                "content" => "Next.js is a React framework that gives you building blocks to create web applications..."
            ],
            [
                "title" => "Understanding Docker",
                "content" => "Docker is a set of platform as a service products that use OS-level virtualization to deliver software in packages called containers..."
            ]
        ];

        foreach ($articles as $article) {
            $stmt->execute($article);
        }
        echo "Initial articles inserted successfully!\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
