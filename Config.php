<?php 
 
    define('DB_HOST', 'localhost');  
    define('DB_USERNAME', 'root');  
    define('DB_PASSWORD', ''); 
    define('DB_NAME', 'sia_db');
 
    $itemNumber = "AXSOP1"; 
    $itemName = "Araxys Operator"; 
    $itemPrice = 75;  
    $currency = "USD"; 
    
    define('PAYPAL_SANDBOX', TRUE);
    define('PAYPAL_SANDBOX_CLIENT_ID', 'AdoNVnUiEMP7gsAvzHQrBN72uXtgiLrP4YOD9KdivjUbaxX-1kijpFsStowKgJicbHQ2erXoGuFWdfoS'); 
    define('PAYPAL_SANDBOX_CLIENT_SECRET', 'EN7poiVI39WhqAd3X3avfUx3OJsbDqbG6-jNMjWFXf7JuAEImEY41HWzBIOUNrzA4EAIDp9B8kg01zIO'); 
    define('PAYPAL_PROD_CLIENT_ID', 'Insert_Live_PayPal_Client_ID_Here'); 
    define('PAYPAL_PROD_CLIENT_SECRET', 'Insert_Live_PayPal_Secret_Key_Here'); 

    $productID = "AXSOP1"; 
    $productName = "Araxys Operator"; 
    $productPrice = 75;
    $currency = "USD"; 

    define('STRIPE_API_KEY', 'sk_test_51OK0xwLLOQJmSpnWwviqGV8Yx80KMiqOvYSfqvUVxT7IXTDSTHRrg06bAiLR9j2yzxF3IBBprBkrzpf2g54E17jr00veT2BkDI'); 
    define('STRIPE_PUBLISHABLE_KEY', 'pk_test_51OK0xwLLOQJmSpnWekf616fVcX2fONJ25h2kkJHWfnvuY6vfg73uLTBt8QT79phExggR3eERQvMdGjEWUCOn1QAG00XYUS9etY'); 
    define('STRIPE_SUCCESS_URL', 'https://localhost/SIA/Stripe_Status.php');
    define('STRIPE_CANCEL_URL', 'https://localhost/SIA/Stripe_Cancel.php'); 
 
?>