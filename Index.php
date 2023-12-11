<?php require_once 'Auth.php' ?>
<?php require_once 'Config.php' ?>

<?php 

    if(isset($_GET['logout']))
    {
        session_destroy();
        header('location: Login.php');
    }

?>

<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title> Vinzledo Shop </title>

        <link rel="stylesheet" href="CSS/Style_Index.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        
        <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
        

        <script src="https://js.stripe.com/v3/"></script>
        <script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_SANDBOX?PAYPAL_SANDBOX_CLIENT_ID:PAYPAL_PROD_CLIENT_ID; ?>&currency=<?php echo $currency; ?>"></script>

    </head>

    <body>

        <nav class="navbar navbar-expand-lg navbar-dark bg-primary bg-gradient">

            <div class="container">

                <a class="navbar-brand" href="./"> <img src="<?= $_SESSION['login_picture'] ?>" alt="" class="img-thumb-nail rounded-circle" width = "50px"> &nbsp; <?= ucwords($_SESSION['login_givenName'] . " " .$_SESSION['login_familyName']) ?> </a> 
                
                <div>

                    <a href="./?logout" class="text-light fw-bolder h6 text-decoration-none"> Logout </a>

                </div>

            </div>

        </nav>

        <div class = "con">

            <div class = "item-imgs">

                <div class = "item-display">

                    <div class = "item-showcase">

                        <img src = "IMG/OP.jpg" alt = "item-img">
                        <img src = "IMG/OP2.jpg" alt = "item-img">
                        <img src = "IMG/OP3.jpg" alt = "item-img">
                        <img src = "IMG/OP4.jpg" alt = "item-img">

                    </div>

                </div>

                <div class = "item-select">

                    <div class = "item-imgs2">

                        <a href = "#" data-id = "1">

                            <img src = "IMG/OP.jpg" alt = "item-img">

                        </a>

                    </div>

                    <div class = "item-imgs2">

                        <a href = "#" data-id = "2">

                            <img src = "IMG/OP2.jpg" alt = "item-img">

                        </a>

                    </div>

                    <div class = "item-imgs2">

                        <a href = "#" data-id = "3">

                            <img src = "IMG/OP3.jpg" alt = "item-img">

                        </a>

                    </div>

                    <div class = "item-imgs2">

                        <a href = "#" data-id = "4">

                            <img src = "IMG/OP4.jpg" alt = "item-img">

                        </a>

                    </div>

                </div>

            </div>

            <script src = "JS/Script.js"></script>

            <div class = "panel">

                <div class = "panel-heading">

                    <h3 class="panel-title"><?php echo $itemName; ?></h3>

                    <p> The best Operator skin in VALORANT. It comes with 4 variants; Brown, Purple, Black and White </p>
                    
                    <p><b>Price:</b> <?php echo '$'.$itemPrice.' '.$currency; ?></p>

                </div>

                <div class = "panel-body">

                    <div id="paymentResponse" class="hidden"></div>

                    <div id="paypal-button-container"></div>

                </div>

                <!-- PAYPAL API -->

                <script>

                    paypal.Buttons({
                        createOrder: (data, actions) => {
                            return actions.order.create({
                                "purchase_units": [{
                                    "custom_id": "<?php echo $itemNumber; ?>",
                                    "description": "<?php echo $itemName; ?>",
                                    "amount": {
                                        "currency_code": "<?php echo $currency; ?>",
                                        "value": <?php echo $itemPrice; ?>,
                                        "breakdown": {
                                            "item_total": {
                                                "currency_code": "<?php echo $currency; ?>",
                                                "value": <?php echo $itemPrice; ?>
                                            }
                                        }
                                    },
                                    "items": [
                                        {
                                            "name": "<?php echo $itemName; ?>",
                                            "description": "<?php echo $itemName; ?>",
                                            "unit_amount": {
                                                "currency_code": "<?php echo $currency; ?>",
                                                "value": <?php echo $itemPrice; ?>
                                            },
                                            "quantity": "1",
                                            "category": "DIGITAL_GOODS"
                                        },
                                    ]
                                }]
                            });
                        },

                        onApprove: (data, actions) => {
                            return actions.order.capture().then(function(orderData) {
                                setProcessing(true);

                                var postData = {paypal_order_check: 1, order_id: orderData.id};
                                fetch('Paypal_checkout_validate.php', {
                                    method: 'POST',
                                    headers: {'Accept': 'application/json'},
                                    body: encodeFormData(postData)
                                })
                                .then((response) => response.json())
                                .then((result) => {
                                    if(result.status == 1)
                                    {
                                        window.location.href = 'Paypal_Status.php?checkout_ref_id='+result.ref_id;
                                    }
                                    
                                    else
                                    {
                                        const messageContainer = document.querySelector("#paymentResponse");
                                        messageContainer.classList.remove("hidden");
                                        messageContainer.textContent = result.msg;
                                        
                                        setTimeout(function () {
                                            messageContainer.classList.add("hidden");
                                            messageText.textContent = "";
                                        }, 5000);
                                    }
                                    setProcessing(false);
                                })
                                .catch(error => console.log(error));
                            });
                        }
                    }).render('#paypal-button-container');

                    const encodeFormData = (data) => {
                    var form_data = new FormData();

                    for ( var key in data )
                    {
                        form_data.append(key, data[key]);
                    }
                    return form_data;   
                    }

                    const setProcessing = (isProcessing) => {
                        if (isProcessing)
                        {
    
                        }
                    }

                </script>

                <!-- STRIPE API -->

                <button class = "stripe-button" id = "payButton">

                    <div class = "spinner hidden" id = "spinner"></div>

                    <img src = "IMG/Stripe.png" width = 25px> <span id = "buttonText"> Pay with Stripe </span>

                </button>

                <script>

                    const stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');

                    const payBtn = document.querySelector("#payButton");

                    payBtn.addEventListener("click", function (evt) {
                        setLoading(true);

                        createCheckoutSession().then(function (data) {
                            if(data.sessionId)
                            {
                                stripe.redirectToCheckout({
                                    sessionId: data.sessionId,
                                }).then(handleResult);
                            }
                            
                            else
                            {
                                handleResult(data);
                            }
                        });
                    });
                        
                    const createCheckoutSession = function (stripe)
                    {
                        return fetch("Stripe_Init.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                            },
                            body: JSON.stringify({
                                createCheckoutSession: 1,
                            }),
                        }).then(function (result) {
                            return result.json();
                        });
                    };

                    const handleResult = function (result)
                    {
                        if (result.error)
                        {
                            showMessage(result.error.message);
                        }
                        
                        setLoading(false);
                    };

                    function setLoading(isLoading)
                    {
                        if (isLoading)
                        {
                            payBtn.disabled = true;
                            document.querySelector("#spinner").classList.remove("hidden");
                            document.querySelector("#buttonText").classList.add("hidden");
                        }
                        
                        else
                        {
                            payBtn.disabled = false;
                            document.querySelector("#spinner").classList.add("hidden");
                            document.querySelector("#buttonText").classList.remove("hidden");
                        }
                    }

                    function showMessage(messageText)
                    {
                        const messageContainer = document.querySelector("#paymentResponse");
                        
                        messageContainer.classList.remove("hidden");
                        messageContainer.textContent = messageText;
                        
                        setTimeout(function () {
                            messageContainer.classList.add("hidden");
                            messageText.textContent = "";
                        }, 5000);
                    }

                </script>

            </div>

        </div>

    </body>

</html>