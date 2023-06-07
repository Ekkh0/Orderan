<?php 
    require_once 'connect.php';
    session_start();

    //check role -> if incorrect redirect
    if(!(isset($_SESSION["userid"]) OR $_SESSION["userid"]==true OR $_SESSION["RoleID"]==3)){
        header("Location: Login.php");
        exit;
    }

    if(isset($_POST['logout'])){
        session_destroy();
        session_start();
        $_SESSION['formstep']='2';
        header("Location: Login.php");
        exit;
    }

    if(isset($_GET['checkbtn'])){
        $_SESSION['vieworderid']=$_GET['checkbtn'];
        header("Location: Vieworderdetail.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orderan</title>
</head>
<body>
    <header>
        <div id="logo">Orderan</div>

        <form id="searchbar" method="post">
            <input type="text" id="searchbar" name='searchbar'>
            <input type="submit" name="search" value="search"></input>
        </form>

        <div id="profile">
            <?php
            echo $_SESSION['user']['UserUsername'];
            ?>
            <div id="dropdownmenu">
                <form action="" method="post">
                    <a href="Pickupper.php">Home</a>
                    <a href="Completedorderpickupper.php">Completed Orders</a>
                    <input type="submit" value="Logout" name="logout">
                </form>
            </div>
        </div>
    </header>
    <div id="content">

        <a href="Pickupper.php" id="backbtn">Back</a>
        <!-- Halamannya kalau di back manual di browser akan berkali kali dan nge send ulang data jadi saya tambahkan back button -->

        <?php
        $query="SELECT * FROM user u JOIN orderheader oh ON u.UserID=oh.CustomerID WHERE oh.PickupperID LIKE '".$_SESSION['userid']."' AND oh.OrderStatus=3 ";

        if(isset($_POST['search']) AND $_POST['searchbar']!=''){
            $query=$query."AND LOWER(u.UserUsername) LIKE '".strtolower($_POST['searchbar'])."%'";
        }

        $query=mysqli_query($conn, $query);

        echo '<div id="orderlist">';
        ?>
        <h2>Completed Orders</h2>
        <?php
        if(mysqli_num_rows($query)!=0){
            while ($d=$query->fetch_assoc()){
                $ordernum=mysqli_query($conn, "SELECT SUM(Quantity) as qty FROM orderdetail od JOIN orderheader oh ON od.OrderID=oh.OrderID WHERE od.OrderID LIKE '".$d['OrderID']."'")->fetch_assoc()['qty'];

                $total=mysqli_query($conn, "SELECT SUM(Quantity*ItemPrice) as total FROM orderdetail od JOIN item i ON od.ItemID=i.ItemID WHERE od.OrderID LIKE '".$d['OrderID']."'")->fetch_assoc()['total'];

                $datetime=mysqli_query($conn, "SELECT DATE_FORMAT(OrderDate, '%d %M %Y %H:%i') AS Dates FROM orderheader WHERE OrderID LIKE '".$d['OrderID']."'")->fetch_assoc()['Dates'];

                echo '<div id="orderlistcontainer">';
                echo '<h1>'.$d['UserUsername'].'</h1>';
                echo '<p>'.$ordernum.' Item(s)</p>';
                echo '<p>Place: '.$d['Alamat'].'</p>';
                echo '<div id="totalndate">';
                echo '<p>Total: '.$total.'</p>';
                echo '<p>'.$datetime.'</p>';
                echo '</div>';
                echo '</div>';

                unset($ordernum);
                unset($total);
                unset($datetime);
            }
        }else{
            if(isset($_POST['search'])){
                echo '<h2>There is no person with that name that has ordered</h2>';
            }else{
                echo '<h2>There is no completed orders</h2>';
            }
        }
        
        echo '</div>';

        ?>

    </div>
</body>
<link rel="stylesheet" href="style.css">
</html>