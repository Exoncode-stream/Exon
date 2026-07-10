<?php

try {
    $db = new PDO('sqlite:database.sqlite');
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

    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        token TEXT
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

    $countUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($countUsers == 0) {
        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $hash = password_hash("admin", PASSWORD_DEFAULT);
        $stmt->execute(['username' => 'admin', 'password' => $hash]);
        echo "Initial admin user inserted successfully!\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
