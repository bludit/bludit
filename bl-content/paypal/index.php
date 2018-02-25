<?php
    /*
        * Home Page - has Sample Buyer credentials, Camera (Product) Details and Instructiosn for using the code sample
    */
    // include('apiCallsData.php');
    include('header.php');
    include_once('paypalConfig.php');

?>
    <style>
        tr{
            line-height:30px;
        }
        td{
            padding:5px;
        }
    </style>
    <div class="row">
        <div class="col-md-3">
            <div>
                <img src="img/camera.jpg" width="200" height="150"/>
            </div>
            <h3>Digital SLR Camera </h3>
            <br/><br/>
            <table class="table table-striped">
                <tr><td colspan="2"><h4> Sample Sandbox Buyer Credentials:</h4></td></tr>
                <tr><th>Buyer Email</th><th>Password</th></tr>
                <tr><td>emily_doe@buyer.com</td><td>qwer1234</td></tr>
                <tr><td>bill_bong@buyer.com</td><td>qwer1234</td></tr>
                <tr><td>jack_potter@buyer.com</td><td>123456789</td></tr>
                <tr><td>harry_doe@buyer.com</td><td>123456789</td></tr>
                <tr><td>ron_brown@buyer.com</td><td>qwer1234</td></tr>
                <tr><td>bella_brown@buyer.com</td><td>qwer1234</td></tr>
            </table>
        </div>

        <div class="col-md-4">
            <h3> Pricing Details </h3>
            <form action="startPayment.php" method="POST">
                 <input type="text" name="csrf" value="<?php echo($_SESSION['csrf']);?>" hidden readonly/>
                 <table>
                    <!-- Item Details - Actual values set in apiCallsData.php -->
                     <tr><td>Camera </td><td><input class="form-control" type="text" name="camera_amount"  value="300" readonly></input></td></tr>
                     <tr><td>Tax </td><td><input class="form-control" type="text" id="tax_amt" name="tax" value="5" readonly></input> </td></tr>
                     <tr><td>Insurance </td><td><input class="form-control" type="text" name="insurance" id="insurance_fee"value="10" readonly></input> </td></tr>
                     <tr><td>Handling Fee </td><td><input class="form-control" type="text" name="handling_fee" id="handling_fee" value="5" readonly></input> </td></tr>
                     <tr><td>Estimated Shipping </td><td><input class="form-control" type="text" name="estimated_shipping" value="2" id="shipping_amt" readonly></input> </td></tr>
                     <tr><td>Shipping Discount </td><td><input class="form-control" type="text" id="shipping_discount" name="shipping_discount" value="-2" readonly></input> </td></tr>
                     <tr><td>Total Amount </td><td><input class="form-control" type="text" name="total_amount" id="total_amt" value="320" readonly></input> </td></tr>
                     <tr><td>Currency</td><td>
                        <select class="form-control" name="currencyCodeType">
                        						<option value="AUD">AUD</option>
                        						<option value="BRL">BRL</option>
                        						<option value="CAD">CAD</option>
                        						<option value="CZK">CZK</option>
                        						<option value="DKK">DKK</option>
                        						<option value="EUR">EUR</option>
                        						<option value="HKD">HKD</option>
                        						<option value="MYR">MYR</option>
                        						<option value="MXN">MXN</option>
                        						<option value="NOK">NOK</option>
                        						<option value="NZD">NZD</option>
                        						<option value="PHP">PHP</option>
                        						<option value="PLN">PLN</option>
                        						<option value="GBP">GBP</option>
                        						<option value="RUB">RUB</option>
                        						<option value="SGD">SGD</option>
                        						<option value="SEK">SEK</option>
                        						<option value="CHF">CHF</option>
                        						<option value="THB">THB</option>
                        						<option value="USD" selected>USD</option>
                     </td></tr>

                 </table>

                <br/>
                <!--Container for Checkout with PayPal button-->
                <div id="myContainer"></div>
                <div id="paypal-end" style="display:none">
                <h2>Your payment is complete</h2>
                <!-- <pre id="paypal-execute-details"></pre> -->
                <div class="hero-unit">
                    <!-- Display the Transaction Details-->
                    <h4> <span id="paypal-execute-details-first-name""></span>
                        <span id="paypal-execute-details-last-name"></span>, Thank you for your Order </h4>
                    
                    <h4> Shipping Details: </h4>
                    <span id="paypal-execute-details-recipient-name"></span><br>
                    <span id="paypal-execute-details-addressLine1"></span>
                    <span id="paypal-execute-details-addressLine2"></span><br>
                    <span id="paypal-execute-details-city"></span><br>
                    <span id="paypal-execute-details-state"></span> -
                    <span id="paypal-execute-details-postal-code"></span></p>
                    <p>Transaction ID: <span id="paypal-execute-details-transaction-ID"></span></p>
                    <p>Payment Total Amount: <span id="paypal-execute-details-final-amount"></span> </p>
                    <p>Currency Code: <span id="paypal-execute-details-currency"></span></p>
                    <p>Payment Status: <span id="paypal-execute-details-payment-state"></span></p>
                    <p>Payment Type: <span id="paypal-execute-details-transaction-type"></span> </p>
                    <h3> Click <a href='index.php'>here </a> to return to Home Page</h3>
                    </div>
                </div>

                <div id="confirm" style="display: none;">
                    <div>Ship to:</div>
                    <div><span id="recipient"></span>, <span id="line1"></span>, <span id="city"></span></div>
                    <div><span id="state"></span>, <span id="zip"></span>, <span id="country"></span> <span id="shipping_amt_updated" hidden></span></div>

                    <button id="confirmButton">Complete Payment</button>
                </div>

                <br/>
                <span style="margin-left:60px">OR</span>
                <br/><br/>
                <div>
                    <button class="btn btn-primary" formaction="shipping.php" role="button">Proceed to Checkout</button>
                </div>
            </form>
        </div>
        <div class="col-md-5" id="instruction">
            <h4> README: </h4>
            <p>
              1) Click on 'PayPal Checkout’ button and see the experience.
              <br>
              2) If you get any Firewall warning, add rule to the Firewall to allow incoming connections for your application.
              <br>
              3) Checkout with PayPal using a buyer sandbox account provided on this page. And you're done!
              <br>
              4) The sample code uses default sandbox REST App credentials which are set in index.php PayPal button section. You can get your own REST app credentials by creating a REST app with the steps outlined  <i><a href="https://developer.paypal.com/docs/integration/direct/make-your-first-call/#create-a-paypal-app" target="_blank">here</a></i>.
              <br>
              5) Make following changes in paypal_config.php:<br>
              - If using your own Sandbox seller account, update 'client'->'sandbox' parameter in Checkout.js v4 script (in index.php and shipping.php) with your Sandbox REST app Client ID.<br>
              - env parameter oin Checkout.js v4 script: Kept 'sandbox' for working with Sandbox, it will be 'production' for LIVE.<br>
              </p>
               <h4 id="incontext"> In-Context Checkout integration steps with Checkout.js v4: </h4>
                             1) Copy the files and folders under 'Checkout' package to the same location where you have your shopping cart page.
                             <br>
                             2) Include the following Checkout.js v4 script on your shopping cart page:
                             <br><br>
                 <pre><code>&lt;script type="text/javascript"&gt;
    paypal.Button.render({

            env: 'sandbox', // sandbox | production

            // PayPal Client IDs - replace with your own
            // Create a PayPal app: https://developer.paypal.com/developer/applications/create
            client: {
                sandbox:    '<Sandbox client id>',
                production: '<insert production client id>'
            },

            // Show the buyer a 'Pay Now' button in the checkout flow
            commit: true,

            // payment() is called when the button is clicked
            payment: function(data, actions) {

                // Make a call to the REST api to create the payment
                return actions.payment.create({
                     meta: {
                         partner_attribution_id: '<?php echo(SBN_CODE)?>'
                     },
                    payment: {
                        transactions: [
                            {
                                amount: { total: '0.01', currency: 'USD' }
                            }
                        ]
                    }
                });
            },

            // onAuthorize() is called when the buyer approves the payment
            onAuthorize: function(data, actions) {

                // Make a call to the REST api to execute the payment
                return actions.payment.execute().then(function() {
                    window.alert('Payment Complete!');
                });
            }

        }, '#paypal-button-container');
&lt;/script&gt;
&lt;script src="//www.paypalobjects.com/api/checkout.js" async&gt;&lt;/script&gt;</code></pre><br>
                             3) Open your browser and navigate to your Shopping cart page. Click on 'Checkout with PayPal' button and complete the flow.<br>
                             4) You can use the sample Buyer Sandbox credentials provided on index.php/home page. <br/>
                             5) Read overview of REST API <a href="https://developer.paypal.com/docs/integration/direct/rest-payments-overview/" target=_blank>here</a> and find the API reference <a href="https://developer.paypal.com/docs/api/" target="_blank">here</a>. You can also try the interactive demo <a href="https://developer.paypal.com/demo/checkout/#/pattern/client" target="_blank">here</a>.<br/><br/>
                           <br><br>
                         
        </div>
    </div>
<!--     <div id="shipping_amt_updated"> add $2 shipping</div>
 -->
    <script src="//www.paypalobjects.com/api/checkout.js"></script>
    <!-- PayPal In-Context Checkout script -->
    <script type="text/javascript">
 
        var client = {
            sandbox:    '<?php echo(CLIENT_ID)?>'
        };
        var environment = 'sandbox';
        var transaction = {
            transactions: [
                {
                    amount: {
                        total:    '15.00',
                        currency: 'USD'
                    }
                }
            ]
        };

        function showDom(id) {
            var arr;
            if (!Array.isArray(id)) {
                arr = [id];
            } else {
                arr = id;
            }
            arr.forEach(function (domid) {
                document.getElementById(domid).style.display = 'initial';
            });
        }

       function hideDom(id) {
            var arr;
            if (!Array.isArray(id)) {
                arr = [id];
            } else {
                arr = id;
            }
            arr.forEach(function (domid) {
                document.getElementById(domid).style.display = 'none';
            });
        }

       function handleResponse(result) {
            document.getElementById('confirm').style.display ='none';
            // var resultDOM = document.getElementById('paypal-execute-details').textContent;
            // document.getElementById('paypal-execute-details').textContent = JSON.stringify(result, null, 2);

            var resultDOM = JSON.stringify(result, null, 2);
            console.log(resultDOM);

            $json_response = result;
            // console.log($json_response['id']);
            var payID = $json_response['id'];

            var paymentState = $json_response['state'];
            var finalAmount = $json_response['transactions'][0]['amount']['total'];
            var currency = $json_response['transactions'][0]['amount']['currency'];
            var transactionID= $json_response['transactions'][0]['related_resources'][0]['sale']['id'];
            var payerFirstName = $json_response['payer']['payer_info']['first_name'];
            var payerLastName = $json_response['payer']['payer_info']['last_name'];
            var recipientName= $json_response['payer']['payer_info']['shipping_address']['recipient_name'],FILTER_SANITIZE_SPECIAL_CHARS;
            var addressLine1= $json_response['payer']['payer_info']['shipping_address']['line1'];
            var addressLine2 = $json_response['payer']['payer_info']['shipping_address']['line2'];
            var city= $json_response['payer']['payer_info']['shipping_address']['city'];
            var state= $json_response['payer']['payer_info']['shipping_address']['state'];
            var postalCode =$json_response['payer']['payer_info']['shipping_address']['postal_code'];
            var transactionType = $json_response['intent'];
            // var countryCode= filter_var($json_response['payer']['payer_info']['shipping_address']['country_code'],FILTER_SANITIZE_SPECIAL_CHARS);

            document.getElementById('paypal-execute-details-postal-code').textContent = postalCode; 
            document.getElementById('paypal-execute-details-state').textContent = state; 
            document.getElementById('paypal-execute-details-recipient-name').textContent = recipientName; 
            document.getElementById('paypal-execute-details-transaction-type').textContent = transactionType; 
            document.getElementById('paypal-execute-details-transaction-ID').textContent = transactionID; 
            document.getElementById('paypal-execute-details-first-name').textContent = payerFirstName; 
            // document.getElementById('paypal-execute-details-last-name').textContent = payerLastName; 
            document.getElementById('paypal-execute-details-payment-state').textContent = paymentState;
            document.getElementById('paypal-execute-details-final-amount').textContent = finalAmount; 
            document.getElementById('paypal-execute-details-currency').textContent = currency; 
            document.getElementById('paypal-execute-details-addressLine1').textContent = addressLine1;
            document.getElementById('paypal-execute-details-addressLine2').textContent = addressLine2;
            document.getElementById('paypal-execute-details-city').textContent = city;



            showDom('paypal-end');
            var button = document.getElementById('myContainer');
           // button.link.style.display = 'none';
            var instructionNode = document.getElementById('instruction');
            instructionNode.style.display= 'none';
        }


 paypal.Button.render({

        // Set your environment

        env: 'sandbox', // sandbox | production

        client: {
            sandbox: '<?php echo(CLIENT_ID)?>'
        },


        // Wait for the PayPal button to be clicked

        payment: function(actions) {

            var tax_amt = document.getElementById('tax_amt').value;
            var shipping_discount = document.getElementById('shipping_discount').value;
            var handling_fee = document.getElementById('handling_fee').value;
            var insurance_fee = document.getElementById('insurance_fee').value;
            // var shippingSel = document.getElementById('shipping_method');
            var shipping_amt = document.getElementById('shipping_amt').value;

            var total_amt = document.getElementById('total_amt').value;

            return actions.payment.create({
             meta: {
                 partner_attribution_id: '<?php echo(SBN_CODE)?>'
             },
             payment: {
                 transactions: [
                     {
                         amount: {
                             total: total_amt ,
                             currency: 'USD',
                             details:
                             {
                                 subtotal: total_amt - shipping_amt,
                                 shipping: shipping_amt,
                             }
                         }
                     }
                 ]
             }
            });


        },

        // Wait for the payment to be authorized by the customer

        onAuthorize: function(data, actions) {

     return actions.payment.get().then(function(data) {       

      var currentShippingVal = data.transactions[0].amount.details.shipping;
      var shipping = data.payer.payer_info.shipping_address;

      var currentTotal = data.transactions[0].amount.total;

                document.querySelector('#recipient').innerText = shipping.recipient_name;
                document.querySelector('#line1').innerText     = shipping.line1;
                document.querySelector('#city').innerText      = shipping.city;
                document.querySelector('#state').innerText     = shipping.state;
                document.querySelector('#zip').innerText       = shipping.postal_code;
                document.querySelector('#country').innerText   = shipping.country_code;

                var updatedShipping = parseInt(currentShippingVal) + parseInt(2);
                 document.querySelector('#shipping_amt_updated').innerText = updatedShipping;

                 console.log('Updated Shipping : '+ updatedShipping);

                //total_amt =+ total_amt + shipping_amt_updated;

                document.querySelector('#myContainer').style.display = 'none';
                document.querySelector('#confirm').style.display = 'block';

                // Listen for click on confirm button

                document.querySelector('#confirmButton').addEventListener('click', function() {

                    // Disable the button and show a loading message

                    document.querySelector('#confirm').innerText = 'Loading...';
                    document.querySelector('#confirm').disabled = true;

                    // Execute the payment
                  
                  var totalAmount = currentTotal - parseInt(updatedShipping);
                  var subtotal = currentTotal - parseInt(updatedShipping);

                    return actions.payment.execute(
                    {
                     transactions: [
                        {
                            amount: {
                                total: totalAmount,
                                currency: 'USD',
                                details: 
                                {
                                  subtotal: subtotal,
                                  shipping: updatedShipping,
                                }
                            }
                        }
                    ]    
                }).then(handleResponse);

              })      

            // return actions.payment.execute().then(handleResponse);
         })   
       } 

    }, '#myContainer');
     </script>
<?php
     include('footer.php');
?>