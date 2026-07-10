<?php

// 1. Cors Authorization
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 2. Content Type
header("Content-Type: application/json; charset=UTF-8");

// 3. Data
$data = [
    "pseudo" => "Exon",
    "description" =>
        "Full-Stack student developer, learning code and sharing thsee on my socials",
    "links" => [
        [
            "name" => "YouTube",
            "url" => "https://www.youtube.com/@exon9858",
        ],
        [
            "name" => "GitHub",
            "url" => "https://github.com/Exoncode-stream/",
        ],
        [
            "name" => "Discord",
            "url" => "guiireg",
        ],
    ],
    "videos" => [
        [
            "title" => "Creating Learn Code website with Next.js",
            "youtube_id" => "https://www.youtube.com/watch?v=ILW91gXl30Y",
            "category" => "Web development",
        ],
    ],
];

// 4. The answer
echo json_encode($data);

?>