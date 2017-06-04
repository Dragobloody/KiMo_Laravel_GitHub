<?php

function calculateDistance($lat1, $lng1,$lat2,$lng2) {
    $R = 6371; // km
    $dLat = deg2rad($lat2-$lat1);
    $dLon = deg2rad($lng2-$lng1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $d = $R * $c;
    return $d;
}

function distanceKidObject($userID,$connect)
{

    $query1=" select k.lat AS lat,k.lng AS lng,k.id_kid as id_kid from kids k join user_kid uk on k.id_kid=uk.id_kid
              where k.followed=1 and uk.id_user=".$userID;
    $result1 = mysqli_query($connect, $query1);

    while($row1 = @mysqli_fetch_assoc($result1))
    {

        $query2=" select o.name as nume, o.lat AS lat,o.lng AS lng,o.id_object as id_object,ko.status as status from objects o 
                        join kid_object ko on ko.id_object=o.id_object where ko.id_kid=".$row1['id_kid'];
        $result2 = mysqli_query($connect, $query2);

        while($row2 = @mysqli_fetch_assoc($result2))
        {

            if(calculateDistance($row1['lat'],$row1['lng'],$row2['lat'],$row2['lng'])<6)
            {

                if($row2['status']==0)
                {
                    $query3=" update kid_object set status=1 where id_kid=".$row1['id_kid']." and id_object=".$row2['id_object'];
                    $result3 = mysqli_query($connect, $query3);

                    $query4="insert into notifications (message,status) values('Se afla in apropierea unui pericol:".$row2['nume']."',0) ";
                    $result4 = mysqli_query($connect, $query4);

                    $query5="select max(id_notification) as id_notification from notifications";
                    $result5 = mysqli_query($connect, $query5);
                    $row3 = @mysqli_fetch_assoc($result5);

                    $query6="insert into kid_notification (id_kid,id_notification) values(".$row1['id_kid'].",".$row3['id_notification'].") ";
                    $result6 = mysqli_query($connect, $query6);
                }
            }
            else{
                if($row2['status']==1)
                {
                    $query7=" update kid_object set status=0 where id_kid=".$row1['id_kid']." and id_object=".$row2['id_object'];
                    $result7 = mysqli_query($connect, $query5);

                }
            }
        }
    }
}
?>