<?PHP header("Content-Type: text/html; charset=utf-8");?>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />

<form action="index.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="1048576">
    <input type="file" name="uploadfile">
    <input type="submit" value="Send">
</form>

<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $filename = $_FILES['uploadfile']['tmp_name'];
//function for checking type
    function check_file_type($file)
    {
        $file_types = array('png', 'jpg', 'jpeg');
        //$current_file_type - file type not case-sensitive
        $current_file_type = mb_strtolower(substr(strrchr($file['name'], '.'), 1));

        if(!in_array($current_file_type, $file_types)){
            return false;
        }
        if(@getimagesize($file['tmp_name'])==null){
            return false;
        }
        $file_mime_types = array('image/png', 'image/jpeg');
        $mime = getimagesize($file['tmp_name'])['mime'];
        if(!in_array($mime, $file_mime_types)){
            return false;
        }
        return true;
    }
//function for checking size
    function check_file_size($file)
    {
        $error = $file['error']; //UPLOAD_ERR_INI_SIZE = 1, UPLOAD_ERR_FORM_SIZE = 2
        $max_image_size = 1024*1024;
        if($error == 1 || $error == 2){
            return false;
        }
        if(filesize($file['tmp_name']) > $max_image_size){
            return false;
        }
        return true;
    }

    if($_FILES['uploadfile']['error'] == 4){  //UPLOAD_ERR_NO_FILE = 4
        echo "You didn't choose any picture";
    } elseif (!(check_file_size($_FILES['uploadfile']))) {
        echo "Please, upload picture less than 1MB";
    } elseif(!(check_file_type($_FILES['uploadfile']))){
        echo "Please, upload only jpeg/jpg/png pictures";
    } else {//uploading files
        if (isset($_FILES['uploadfile'])) {
            //image format
            $ext = substr(getimagesize($_FILES['uploadfile']['tmp_name'])['mime'], 6);
            $new_name = uniqid() . "." . $ext;
            copy($filename, "uploads/" . basename($new_name));
        }
    }
}
//making table
    $dir = 'uploads/';
    $cols = 4; //amount of pics in row
    $files = scandir($dir);
    echo "<table>";
    $k = 0;
    for ($i = 0; $i < count($files); $i++) {
        if (($files[$i] != ".") && ($files[$i] != "..")) {
            if ($k % $cols == 0) echo "<tr>";
            echo "<td>";
            $path = $dir . $files[$i];
            echo "<a href='$path'>";
            echo "<img src='$path' alt='' width='200' height='200' />";
            echo "</a>";
            echo "</td>";
            if ((($k + 1) % $cols == 0) || (($i + 1) == count($files))) echo "</tr>";
            $k++;
        }
    }
    echo "</table>";
