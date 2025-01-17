<?php

    // Create store place for each user (we place this in /var/www/html/upload for easily handle)
    session_start();
    if (!isset($_SESSION['dir'])) {

        $_SESSION['dir'] = '/var/www/html/upload/' . bin2hex(random_bytes(16)); // tạo ra folder dựa trên session 
    }
    $dir = $_SESSION['dir'];

    if ( !file_exists($dir) )
        mkdir($dir);
    
    echo '$_FILES["file"]["name"]' . "\n";
    var_dump($_FILES["file"]["name"]);
        
    if(isset($_FILES["file"]) ) {
        try {
          
          $file_name = $_FILES["file"]["name"]; // lấy tên file 
          if(substr($file_name,-4,4) == ".zip") // kiểm tra xem file có phải là file zip hay không 
          {
            $result = _unzip_file_ziparchive($_FILES["file"]["tmp_name"],$dir); // nếu phải thì unzip file đó dựa trên hàm _unzip_file_ziparchive 
          }
          else
          {
            $newFile = $dir . "/" . $file_name; // nếu file đó không phải là file zip thì lưu vào folder đã tạo ở trên có session

            echo "\$newFile here\n";
            var_dump($newFile);

            move_uploaded_file($_FILES["file"]["tmp_name"], $newFile); // di chuyển file đó từ /tmp vào folder đã tạo ở trên
          }

       } catch(Exception $e) {
            $error = $e->getMessage(); // nếu có lỗi thì in ra lỗi đó
         }
    }

    function _unzip_file_ziparchive($file, $to) // hàm giải nén file zip
    {
        $z = new ZipArchive(); // tạo ra một đối tượng zip
        $zopen = $z->open( $file, ZipArchive::CHECKCONS); // mở file zip 
        if ( true !== $zopen ) // nếu không mở được thì return false
          return false;
        for ( $i = 0; $i < $z->numFiles; $i++ ) { // duyệt qua từng file trong file zip

            if ( ! $info = $z->statIndex($i) ) // lấy thông tin file trong file zip
              return false; //Could not retrieve file from archive.
            
            echo "\$info['name'] here\n";
            var_dump($info['name']);

            // ở đây còn 1 biến untrusted data -> $info['name'] => có thể làm cho hàm này trở thành lỗ hổng
            if ( '/' == substr($info['name'], -1) ) // directory // nếu là directory thì bỏ qua
              continue;

            $contents = $z->getFromIndex($i); // lấy nội dung file trong file zip
            if ( false === $contents ) // nếu không lấy được thì return false
              return false; //Could not extract file from archive.
            
            if(file_exists(dirname($to . "/" . $info['name']))){ // directory exists // kiểm tra xem folder đã tồn tại chưa
                file_put_contents($to . "/" . $info['name'], $contents); // nếu tồn tại rồi thì ghi nội dung file vào folder đó
            }
          }

        $z->close();
        return true;
    }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Free Icon</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link rel="stylesheet" href="css/bootstrap4-neon-glow.min.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel='stylesheet' href='//cdn.jsdelivr.net/font-hack/2.020/css/hack.min.css'>
  </head>
  <body>
<div class="jumbotron bg-transparent mb-0 radius-0">
  <div class="container">
      <div class="row">
        <div class="col-xl-6">
          <h1 class="display-2">Free Ico<span class="vim-caret">n</span></h1>
          <div class="lead mb-3 text-mono text-success">FREE icons and stickers for your projects.</div>
        </div> 
      </div>
      <hr class="mb-4">
      <div class="row">
      <div class="col-xl-4">
      <h3 class="ht-tm-cat-title">Upload Your Image or ZIP</h3>
      <br>
          <div class="ht-tm-codeblock ht-tm-btn-replaceable ht-tm-element ht-tm-element-inner">
            <form method="post" enctype="multipart/form-data">
                    <div class="ht-tm-element custom-file">
                        <input type="file" class="custom-file-input" name="file">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>

              <hr>
              <button type="submit" class="btn btn-primary" >Submit</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="container py-5 mb5">
  <h4 class="mb-3">FREE</h4>
  <div class="row py-4">
  <?php
  $images  = glob("./images/*");
  foreach($images as $image){
      $name = str_replace("/usr","",$image);
  ?>
    <div class="col-lg-2">
      <div class="ht-tm-codeblock">
        <div class="ht-tm-element card bg-primary text-white mb-3 text-center">
          <img class="card-img-top" src="<?php echo $name; ?>" alt="Card image cap" style= 'background-color:white'>
          <div class="card-body">
            <a href="<?php echo $name; ?>" class="btn btn-light btn-shadow px-3 my-2 ml-0 ml-sm-1 text-left">Download</a>
          </div>
        </div>
      </div>
    </div>
<?php 
}
?>
<?php
$images  = glob($dir."/*");
foreach($images as $image){
    $name = str_replace("/var/www/html","",$image);
?>
    <div class="col-lg-2">
      <div class="ht-tm-codeblock">
        <div class="ht-tm-element card bg-primary text-white mb-3 text-center">
          <img class="card-img-top" src="<?php echo $name; ?>" alt="Card image cap" style= 'background-color:white'>
          <div class="card-body">
            <a href="<?php echo $name; ?>" class="btn btn-light btn-shadow px-3 my-2 ml-0 ml-sm-1 text-left">Download</a>
          </div>
        </div>
      </div>
    </div>
<?php 
}
?>
<?php
    if(isset($result))
    {
    ?>
    <div class="row">
        <div  class="col-md-12"  >
                <div id="myModal" class="modal" style="display: block;" >
                    <div class="modal-dialog" >
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">Thông báo!</h3>
                            </div>
                            <div class="modal-body">
                                <?php
                                if($result)
                                  echo "<p>Đã giải nén thành công!</p>";
                                else
                                  echo "<p>Đã giải nén thất bại!</p>";
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
    <?php }; ?>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script>
      // Get the modal
      var modal = document.getElementById("myModal");
      //Sau 2s thì none
      setTimeout(function(){
          modal.style.display = "none";
      }, 3000);
  </script>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>
