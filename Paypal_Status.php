<?php

    require_once 'Config.php'; 
    require_once 'DB_Conn.php'; 
   
    $payment_ref_id = $statusMsg = ''; 
    $status = 'error'; 
    
    if(!empty($_GET['checkout_ref_id']))
    { 
        $payment_txn_id  = base64_decode($_GET['checkout_ref_id']); 
        
        $sqlQ = "SELECT id,payer_id,payer_name,payer_email,payer_country,order_id,transaction_id,paid_amount,paid_amount_currency,payment_source,payment_status,created FROM transact WHERE transaction_id = ?"; 
        $stmt = $db->prepare($sqlQ);  
        $stmt->bind_param("s", $payment_txn_id); 
        $stmt->execute(); 
        $stmt->store_result(); 
    
        if($stmt->num_rows > 0)
        {
            $stmt->bind_result($payment_ref_id, $payer_id, $payer_name, $payer_email, $payer_country, $order_id, $transaction_id, $paid_amount, $paid_amount_currency, $payment_source, $payment_status, $created); 
            $stmt->fetch(); 
            
            $status = 'Success'; 
            $statusMsg = 'Your payment has been successful!'; 
        }
        
        else
        { 
            $statusMsg = "Transaction has been failed!"; 
        } 
    }
    
    else
    { 
        header("Location: Index.php"); 
        exit; 
    } 

?>

<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title> Vinzledo Shop </title>
        
        <link rel="stylesheet" href="CSS/Style_Status.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>

    </head>

    <body>

        <nav class="navbar navbar-expand-lg navbar-dark bg-primary bg-gradient">

            <div class="container">

                <a class="navbar-brand" href="./"><img src = "IMG/Logo.png" width = "50px"> &nbsp;&nbsp; Vinzledo Shop </a>
                
                <div>

                    <a href="./?logout" class="text-light fw-bolder h6 text-decoration-none"> Logout </a>

                </div>

            </div>

        </nav>

        <div class = "container2">

            <div class = "status">

                <?php

                    if(!empty($payment_ref_id))
                    {

                ?>

                <h1 class="<?php echo $status; ?>"><?php echo $statusMsg; ?></h1>
                
                <hr>

                <h4>Payment Information</h4>

                <p><b>Reference Number:</b> <?php echo $payment_ref_id; ?></p>
                <p><b>Order ID:</b> <?php echo $order_id; ?></p>
                <p><b>Transaction ID:</b> <?php echo $transaction_id; ?></p>
                <p><b>Paid Amount:</b> <?php echo $paid_amount.' '.$paid_amount_currency; ?></p>
                <p><b>Payment Status:</b> <?php echo $payment_status; ?></p>
                <p><b>Date:</b> <?php echo $created; ?></p>
                
                <hr>

                <h4>Payer Information</h4>

                <p><b>ID:</b> <?php echo $payer_id; ?></p>
                <p><b>Name:</b> <?php echo $payer_name; ?></p>
                <p><b>Email:</b> <?php echo $payer_email; ?></p>
                <p><b>Country:</b> <?php echo $payer_country; ?></p>
                
                <hr>

                <h4>Product Information</h4>

                <p><b>Name:</b> <?php echo $itemName; ?></p>
                <p><b>Price:</b> <?php echo $itemPrice.' '.$currency; ?></p>
                
                <?php
                    
                    }
                    
                    else
                    {?>
                        <h1 class="error">Your Payment been failed!</h1>
                        <p class="error"><?php echo $statusMsg; ?></p>

                <?php }?>

            </div>

            <div class = "bck-btn">

                <a href = "Index.php"> Back to Payment </a>

            </div>

        </div>

    </body>

</html>