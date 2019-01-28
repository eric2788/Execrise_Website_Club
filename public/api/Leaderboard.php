<?php
    include('Header.inc.php');
    include('MySQL.inc.php');
    
    $arr = array();
    $result = mysqli_query($conn,"SELECT * FROM CLUB");
    $num = mysqli_num_rows($result);
    if ($num > 0) {
        while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
            $givenname = str_replace("'","",$row['givenname']);
            $arr2 = array('familyname'=>$row['familyname'],'givenname'=>$givenname
            ,'sex'=>$row['sex'],'phone'=>$row['phone'],'sport'=>$row['sport'],'level'=>$row['level']);
            array_push($arr,$arr2);
        }
    }
    echo json_encode($arr);
    
?>