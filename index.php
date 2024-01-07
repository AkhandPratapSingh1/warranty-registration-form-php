<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Warranty Registration Form</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            width: 80%;
            margin: auto;
            padding-top: 20px;
        }

        h2 {
            text-align: center;
            color: #007bff;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-section {
            display: flex;
            justify-content: space-between;
        }

        .form-section > div {
            width: 48%;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            margin-top: 5px;
        }
        #message {
            color: green;
            margin-top: 10px;
        }
    </style>

</head>
<body>
    <div class="container">
        <h2>Warranty Registration Form</h2>
        <div id="messageContainer"></div>
        <?php print_r($message); ?>
        <form id="warrantyForm" action="email-script.php" method="post" enctype="multipart/form-data">
        <div class="form-section">
                <div>
                    <label for="iso_number">1. Installation Service Order No <span style="color: #FF0000">*</span></label>
                    <input type="text" pattern="[A-Za-z]{3}[0-9]{10}" title="Invalid format. Example: BLR1234567890" name="iso_number" id="iso_number" placeholder="ex. BLR1234567890" required />
                </div>
                <div>
                    <label for="model_name">2. Model Name <span style="color: #FF0000">*</span></label>
                    <select name="model_name" id="model_name" required>
                        <option value="LTW">LTW</option>
                        <option value="Aero">Aero</option>
                    </select>
                </div>
            </div>

            <div class="form-section">
                <div id="customer-info">
                    <h3>3. Customer Information</h3>
                    <input type="text" name="customer_name" id="customer_name" placeholder="Name" required />

                    <input type="email" name="customer_email" id="customer_email" placeholder="Email" required />

                    <input type="tel" name="customer_mobile" id="customer_mobile" placeholder="Mobile No" required />
                </div>
                
                <div id="customer-address">
                    <label for="customer_address">Address <span style="color: #FF0000">*</span></label>
                    <input type="text" name="customer_address" id="customer_address" placeholder="locality House No./ Street" required />

                    <input type="text" name="customer_city" id="customer_city" placeholder="City" required />

                    <input type="text" name="customer_state" id="customer_state" placeholder="State" required />

                    <input type="text" name="customer_pincode" id="customer_pincode" placeholder="Pincode" required />
                </div>
            </div>

            <div class="form-section">
                <div id="product-info">
                    <h3>4. Product Information</h3>
                    <label for="product_serial">Serial Number</label>
                    <input type="text" name="product_serial" id="product_serial" />

                    <label for="purchase_date">Purchase Date</label>
                    <input type="date" name="purchase_date" id="purchase_date" />
                </div>
                
                <div id="invoice-warranty-scan">
                    <label for="invoice_scan">Scan of Invoice (PDF)</label>
                    <input type="file" name="invoice" id="invoice_scan" accept=".pdf" />

                    <label for="warranty_scan">Scan of Lifetime Warranty Registration Form (PDF)</label>
                    <input type="file" name="warranty" id="warranty_scan" accept=".pdf" />
                    <button type="submit" name="sendMailBtn">Submit</button>
                </div>
                
            </div>

            
        </form>
    </div>
</body>
</html>