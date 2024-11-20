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

  <h1>ALL VIDEOS</h1>
  </section>


<?php 
// Database configuration
$host = 'localhost';
$db = 'video_streaming';
$user = 'root';
$pass = '1234';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch all videos from the database
$stmt = $pdo->query("SELECT * FROM videos ORDER BY upload_date DESC");
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View All Uploaded Videos</title>
    <link href="https://vjs.zencdn.net/7.15.4/video-js.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script> <!-- Load HLS.js once here -->
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
</head>
<body>
    <h1>Uploaded Videos</h1>
    <?php if (empty($videos)): ?>
        <p>No videos found.</p>
    <?php else: ?>
        <?php foreach ($videos as $video): ?>
            <div>
                <h3><?php echo htmlspecialchars($video['name']); ?></h3>
                <video id="video-<?php echo $video['id']; ?>" class="video-js vjs-default-skin" controls width="640" height="360"></video>
            </div>
            <script>
                if (!window.videoData) window.videoData = [];
                window.videoData.push({
                    id: "video-<?php echo $video['id']; ?>",
                    path: "<?php echo $video['master_playlist_path']; ?>"
                });
            </script>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        function initializeVideos() {
            window.videoData.forEach(function(video) {
                const videoElement = document.getElementById(video.id);
                const videoPath = video.path;

                if (Hls.isSupported()) {
                    const hls = new Hls({
                        maxBufferLength: 10,        // Buffer 10 seconds of video
                        maxMaxBufferLength: 10,     // Max buffer cap at 20 seconds
                        maxBufferSize: 50 * 1000 * 1000,  // Limit buffer size to 50MB
                        maxBufferHole: 0.5,         // Allow a 0.5s gap to prevent over-fetching
                    });

                    hls.loadSource(videoPath);
                    hls.attachMedia(videoElement);

                    hls.on(Hls.Events.ERROR, function(event, data) {
                        console.error("HLS error:", data);
                        if (data.fatal) {
                            switch(data.type) {
                                case Hls.ErrorTypes.NETWORK_ERROR:
                                    console.error("Network error:", data);
                                    break;
                                case Hls.ErrorTypes.MEDIA_ERROR:
                                    console.error("Media error:", data);
                                    hls.recoverMediaError();
                                    break;
                                default:
                                    console.error("Other error:", data);
                                    hls.destroy();
                                    break;
                            }
                        }
                    });
                } else if (videoElement.canPlayType('application/vnd.apple.mpegurl')) {
                    videoElement.src = videoPath;
                }
            });
        }

        document.addEventListener("DOMContentLoaded", initializeVideos);
    </script>  
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