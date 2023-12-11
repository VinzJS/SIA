<?php 

    require_once 'Config.php'; 
    include_once 'DB_Conn.php'; 
    
    $payment_id = $statusMsg = ''; 
    $status = 'error'; 
    
    if(!empty($_GET['session_id']))
    { 
        $session_id = $_GET['session_id']; 
        
        $sqlQ = "SELECT * FROM transact2 WHERE stripe_checkout_session_id = ?"; 
        $stmt = $db->prepare($sqlQ);  
        $stmt->bind_param("s", $db_session_id); 
        $db_session_id = $session_id; 
        $stmt->execute(); 
        $result = $stmt->get_result(); 
    
        if($result->num_rows > 0)
        { 
            $transData = $result->fetch_assoc(); 
            $payment_id = $transData['id']; 
            $transactionID = $transData['txn_id']; 
            $paidAmount = $transData['paid_amount']; 
            $paidCurrency = $transData['paid_amount_currency']; 
            $payment_status = $transData['payment_status']; 
            
            $customer_name = $transData['customer_name']; 
            $customer_email = $transData['customer_email']; 
            
            $status = 'success'; 
            $statusMsg = 'Your Payment has been successful!'; 
        }
        
        else
        { 
            require_once 'StripeAPI/init.php';

            $stripe = new \Stripe\StripeClient(STRIPE_API_KEY); 
            
            try { 
                $checkout_session = $stripe->checkout->sessions->retrieve($session_id); 
            } catch(Exception $e) {  
                $api_error = $e->getMessage();  
            } 
            
            if(empty($api_error) && $checkout_session)
            { 
                $customer_details = $checkout_session->customer_details; 
    
                try { 
                    $paymentIntent = $stripe->paymentIntents->retrieve($checkout_session->payment_intent); 
                } catch (\Stripe\Exception\ApiErrorException $e) { 
                    $api_error = $e->getMessage(); 
                } 
                
                if(empty($api_error) && $paymentIntent)
                {
                    if(!empty($paymentIntent) && $paymentIntent->status == 'succeeded')
                    { 
                        $transactionID = $paymentIntent->id; 
                        $paidAmount = $paymentIntent->amount; 
                        $paidAmount = ($paidAmount/100); 
                        $paidCurrency = $paymentIntent->currency; 
                        $payment_status = $paymentIntent->status; 
                        
                        $customer_name = $customer_email = ''; 
                        if(!empty($customer_details))
                        { 
                            $customer_name = !empty($customer_details->name)?$customer_details->name:''; 
                            $customer_email = !empty($customer_details->email)?$customer_details->email:''; 
                        } 
                        
                        $sqlQ = "SELECT id FROM transact2 WHERE txn_id = ?"; 
                        $stmt = $db->prepare($sqlQ);  
                        $stmt->bind_param("s", $transactionID); 
                        $stmt->execute(); 
                        $result = $stmt->get_result(); 
                        $prevRow = $result->fetch_assoc(); 
                        
                        if(!empty($prevRow))
                        { 
                            $payment_id = $prevRow['id']; 
                        }
                        
                        else
                        { 
                            $sqlQ = "INSERT INTO transact2 (customer_name,customer_email,item_name,item_number,item_price,item_price_currency,paid_amount,paid_amount_currency,txn_id,payment_status,stripe_checkout_session_id,created,modified) VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())"; 
                            $stmt = $db->prepare($sqlQ); 
                            $stmt->bind_param("ssssdsdssss", $customer_name, $customer_email, $productName, $productID, $productPrice, $currency, $paidAmount, $paidCurrency, $transactionID, $payment_status, $session_id); 
                            $insert = $stmt->execute(); 
                            
                            if($insert){ 
                                $payment_id = $stmt->insert_id; 
                            } 
                        } 
                        
                        $status = 'success'; 
                        $statusMsg = 'Your payment has been successful!'; 
                    }
                    
                    else
                    { 
                        $statusMsg = "Transaction has been failed!"; 
                    } 
                }
                
                else
                { 
                    $statusMsg = "Unable to fetch the transaction details! $api_error";  
                } 
            }
            
            else
            { 
                $statusMsg = "Invalid Transaction! $api_error";  
            } 
        } 
    }
    
    else
    { 
        $statusMsg = "Invalid Request!"; 
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

                <?php if(!empty($payment_id)){ ?>

                    <h1 class="<?php echo $status; ?>"><?php echo $statusMsg; ?></h1>

                    <hr>

                    <h4>Payment Information</h4>

                    <p><b>Reference Number:</b> <?php echo $payment_id; ?></p>
                    <p><b>Transaction ID:</b> <?php echo $transactionID; ?></p>
                    <p><b>Paid Amount:</b> <?php echo $paidAmount.' '.$paidCurrency; ?></p>
                    <p><b>Payment Status:</b> <?php echo $payment_status; ?></p>

                    <hr>
                    
                    <h4>Customer Information</h4>

                    <p><b>Name:</b> <?php echo $customer_name; ?></p>
                    <p><b>Email:</b> <?php echo $customer_email; ?></p>

                    <hr>
                    
                    <h4>Product Information</h4>

                    <p><b>Name:</b> <?php echo $productName; ?></p>
                    <p><b>Price:</b> <?php echo $productPrice.' '.$currency; ?></p>
                    
                <?php }else{ ?>
                    
                        <h1 class="error">Your Payment has been failed!</h1>
                        <p class="error"><?php echo $statusMsg; ?></p>
                        
                <?php } ?>

            </div>

            <div class = "bck-btn">

                <a href = "Index.php"> Back to Payment </a>

            </div>

        </div>

    </body>

</html>