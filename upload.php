<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jai shree ram</title>
  <link rel="stylesheet" type="text/css" href="web1.css">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;600&display=swap">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/fontawesome.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <script src="https://kit.fontawesome.com/8967c8ebdc.js" crossorigin="anonymous"></script>
  <meta charset="UTF-8">
</head>

<body>
  <section class="sub-header">
    <nav>
      <a href="index.html"> <img src="img/result.png "></a>
      <div class="nav-links" id="navlinks">
        <i class="fa fa-times" onclick="hideMenue()"></i>

        <ul>
          <li><a href="index.html">HOME</a></li>
          <li><a href="resources.html">RESOURCES</a></li>
          <li><a href="upload.php">UPLOAD</a></li>
          <li><a href="view.php">VIEW</a></li>
          <li><a href="contact.html">CONTACT</a></li>
        </ul>
      </div>
      <i class="fa fa-bars" onclick="showMenue()"></i>
    </nav>

  <h1>UPLOAD VIDEO</h1>
  </section>

<?php
// Database configuration
$host = 'localhost';       
$db = 'video_streaming';    
$user = 'root';             
$pass = '1234';             

// Establish a MySQL connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Define the upload and output directories
$uploadDir = 'uploads/';
$outputDir = 'output/';

// Ensure the directories exist
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
if (!file_exists($outputDir)) {
    mkdir($outputDir, 0777, true);
}

// Check if a file has been uploaded
if (isset($_FILES['videoFile']) && $_FILES['videoFile']['error'] === UPLOAD_ERR_OK) {
    // Get the file details
    $fileTmpPath = $_FILES['videoFile']['tmp_name'];
    $fileName = $_FILES['videoFile']['name'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

    // Generate a unique name for the uploaded file
    $uniqueName = uniqid('video_', true) . '.' . $fileExtension;
    $destinationPath = $uploadDir . $uniqueName;

    // Move the uploaded file to the uploads directory
    if (move_uploaded_file($fileTmpPath, $destinationPath)) {
        // Define the output HLS files and directories
        $outputFileName = pathinfo($uniqueName, PATHINFO_FILENAME);
        $outputPath = $outputDir . $outputFileName;
        mkdir($outputPath, 0777, true); // Create a directory for each video

        // Define each rendition (resolution) and FFmpeg command separately
        $renditions = [
            ['name' => '360p', 'scale' => '640x360', 'bitrate' => '800k', 'bufsize' => '1200k'],
            ['name' => '480p', 'scale' => '854x480', 'bitrate' => '1000k', 'bufsize' => '2000k'],
            ['name' => '720p', 'scale' => '1280x720', 'bitrate' => '2800k', 'bufsize' => '4200k'],
           // ['name' => '1080p', 'scale' => '1920x1080', 'bitrate' => '5000k', 'bufsize' => '7500k'],
        ];

        foreach ($renditions as $rendition) {
            $outputFile = $outputPath . "/{$rendition['name']}.m3u8";

            $ffmpegCmd = "ffmpeg -i $destinationPath "
                . "-vf scale={$rendition['scale']} -c:v libx264 -b:v {$rendition['bitrate']} -bufsize {$rendition['bufsize']} "
                . "-c:a aac -b:a 128k "
                . "-f hls -hls_time 4 -hls_playlist_type vod "
                . "-hls_segment_filename \"$outputPath/{$rendition['name']}_%03d.ts\" "
                . "$outputFile";

            // Execute each FFmpeg command for each rendition
            exec($ffmpegCmd, $output, $returnVar);

            // Check if FFmpeg executed successfully for this rendition
            if ($returnVar !== 0) {
                echo "Error processing {$rendition['name']} rendition.";
                return;
            }
        }

        // Create the master playlist
        $masterPlaylist = "$outputPath/master.m3u8";
        $masterContent = "#EXTM3U\n";
        foreach ($renditions as $rendition) {
            $masterContent .= "#EXT-X-STREAM-INF:BANDWIDTH={$rendition['bitrate']},RESOLUTION={$rendition['scale']}\n";
            $masterContent .= "{$rendition['name']}.m3u8\n";
        }
        file_put_contents($masterPlaylist, $masterContent);

        // Insert video metadata into the database
        $stmt = $pdo->prepare("INSERT INTO videos (name, file_path, master_playlist_path) VALUES (?, ?, ?)");
        $stmt->execute([$fileName, $destinationPath, $masterPlaylist]);

        echo "Video uploaded and processed successfully!";
    } else {
        echo "There was an error moving the uploaded file.";
    }
} else {
    echo "No file uploaded or an error occurred.";
}
?>

<!-- HTML form to upload the video -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Video for Adaptive Streaming</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to the external CSS file -->
</head>
<body>
    <section class="upload">
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="videoFile">Upload Video:</label>
            <input type="file" name="videoFile" id="videoFile" required>
            <button type="submit">Upload and Process</button>
        </form>
    </section>
</body>
</html>


  <!-------------java script for toggle menu-------------------->

  <script>
    var navlinks = document.getElementById("navlinks")

    function showMenue() {
      navlinks.style.right = "0";
    }
    function hideMenue() {
      navlinks.style.right = "-200px";
    }
  </script>
  
                      <!--------------- FOOTER ------------------->
  <section class="footer">
    <h4>ABOUT US</h4>
    <p>
      Lorem ipsum dolor, sit amet consectetur adipisicing elit. Inventore atque, unde recusandae eaque facere, sunt hic voluptatem dicta similique natus exercitationem illum sit? Illo, <br> veniam adipisci? Quia, saepe delectus. Delectus.
    </p>  
    <div class="icon">
      <i class="fa fa-facebook"></i>
      <i class="fa fa-instagram"></i>
      <i class="fa fa-twitter"></i>
      <i class="fa fa-linkedin"></i>
      <i class="fa fa-youtube"></i>
    </div>
    <p class="name">Made With <i class="fa fa-heart-o"></i> By Raj... </p>
    
  </sesction>



</body>

</html>