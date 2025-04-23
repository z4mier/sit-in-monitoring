<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab Resources</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
            body { font-family: 'Inter', sans-serif; background-color: #0d121e; margin: 0; padding: 0; color: white; }
            .content { margin-left: 270px; padding: 20px; box-sizing: border-box; }
            .resources-wrapper { display: flex; flex-wrap: wrap; gap: 20px; }
            .resources, .materials { flex: 1 1 45%; display: flex; flex-direction: column; gap: 20px; padding: 20px; border-radius: 15px; border: 2px solid white; background-color: #0d121e; box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); }
            .resources h3, .materials h3 { font-weight: bold; font-size: 20px; text-align: center; }
            .card { background-color: #212b40; padding: 18px; border-radius: 10px; box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2); color: white; border: 1px solid rgba(0, 0, 0, 0.1); }
            .card h4 { margin: 0; font-size: 18px; color: #fff; }
            .card p { font-size: 14px; color: #ccc; margin-top: 6px; margin-bottom: 12px; }
            .card a { color: #61dafb; text-decoration: none; }
            .card a:hover { text-decoration: underline; }
            @media screen and (max-width: 1024px) { .resources-wrapper { flex-direction: column; } .resources, .materials { width: 100%; } }
    </style>

</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="content">
    <div class="resources-wrapper">

        <div class="resources">
            <h3><i class="fas fa-book"></i> Lab Resources</h3>

            <div class="card">
                <h4>Learn PHP</h4>
                <p>Comprehensive guide to learning PHP for backend development.</p>
                <a href="https://www.w3schools.com/php/" target="_blank">Visit W3Schools PHP Tutorial</a>
            </div>

            <div class="card">
                <h4>JavaScript for Beginners</h4>
                <p>Interactive JavaScript tutorials with hands-on exercises.</p>
                <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide" target="_blank">View MDN JS Guide</a>
            </div>

            <div class="card">
                <h4>GitHub Repositories</h4>
                <p>Explore open-source projects and contribute to real-world code.</p>
                <a href="https://github.com/explore" target="_blank">Browse GitHub Projects</a>
            </div>

            <div class="card">
                <h4>Python Programming</h4>
                <p>Start learning Python from basics to advanced topics.</p>
                <a href="https://realpython.com/" target="_blank">Go to Real Python</a>
            </div>

            <div class="card">
                <h4>FreeCodeCamp Courses</h4>
                <p>Interactive coding lessons on web development, data structures, and more.</p>
                <a href="https://www.freecodecamp.org/" target="_blank">Start Learning on freeCodeCamp</a>
            </div>
        </div>

        <div class="materials">
            <h3><i class="fas fa-laptop-code"></i> Tutorial Materials</h3>

            <div class="card">
                <h4>Responsive Web Design</h4>
                <p>Build responsive websites using HTML & CSS.</p>
                <a href="https://www.freecodecamp.org/learn/responsive-web-design/" target="_blank">Explore Tutorial</a>
            </div>

            <div class="card">
                <h4>Git & GitHub</h4>
                <p>Learn how to version control your code and collaborate.</p>
                <a href="https://www.atlassian.com/git/tutorials" target="_blank">Git Tutorials by Atlassian</a>
            </div>

            <div class="card">
                <h4>SQL & MySQL</h4>
                <p>Master relational database queries with SQL tutorials.</p>
                <a href="https://www.w3schools.com/sql/" target="_blank">Start SQL Tutorial</a>
            </div>

            <div class="card">
                <h4>Data Structures & Algorithms</h4>
                <p>Improve coding skills for technical interviews.</p>
                <a href="https://www.geeksforgeeks.org/data-structures/" target="_blank">Visit GeeksforGeeks</a>
            </div>

            <div class="card">
                <h4>C# Fundamentals</h4>
                <p>Understand the basics of C# programming and syntax.</p>
                <a href="https://learn.microsoft.com/en-us/dotnet/csharp/tour-of-csharp/" target="_blank">C# Tour by Microsoft</a>
            </div>

        </div>
    </div>
</div>

</body>
</html>
