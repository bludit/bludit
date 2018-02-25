<?php
	/*
		* Shipping Details page : part of the Proceed to Checkout/Mark flow. Buyer can enter shipping address information and choose shipping option on this page.
	*/
    if (session_id() == "")
        session_start();

    include('header.php');
	include_once('paypalConfig.php');

	
?>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <h4>
                Please provide your shipping information:

                <form action="startPayment.php" method="POST">
                	<input type="text" name="csrf" value="<?php echo($_SESSION['csrf']);?>" hidden readonly/>
                    <input type="text" name="markFlow" value="true" hidden></input>
                    <br/>
                        <table>
                            <tr><td width="30%">Full Name</td><td><input class="form-control"  type="text" id="recipient_name" name="recipient_name" value="Jane Doe"></input></td></tr>
                            <tr><td><br/></td></tr>
                            <tr><td>Address</td><td><input class="form-control" type="text" name="line1" id="line1" value="55 East 52nd Street"></input></td></tr>
                            <tr><td><br/></td></tr>
                            <tr><td>Address 1</td><td><input class="form-control" type="text" name="line2" id="line2" value="21st Floor"></input></td></tr>
                            <tr><td><br/></td></tr>
                            <tr><td>City</td><td><input class="form-control" type="text" name="city" id="city" value="New York" ></input></td></tr>
                            <tr><td><br/></td></tr>
                            <tr><td>State</td><td><input class="form-control" type="text" name="state" id="state" value="NY" ></input></td></tr>
                            <tr><td><br/></td></tr>
                            <tr><td>Postal Code</td><td><input class="form-control" type="text" name="postal_code" id="postal_code" value="10022" ></input></td></tr>
                            <tr><td><br/></td></tr>
                            <tr><td>Country</td><td><select class="form-control" name="country_code" id="country_code">
                                                    							<option value="AF">Afghanistan</option>
                                                    							<option value="AX">Aland Islands</option>
                                                    							<option value="AL">Albania</option>
                                                    							<option value="DZ">Algeria</option>
                                                    							<option value="AS">American Samoa</option>
                                                    							<option value="AD">Andorra</option>
                                                    							<option value="AO">Angola</option>
                                                    							<option value="AI">Anguilla</option>
                                                    							<option value="AQ">Antarctica</option>
                                                    							<option value="AG">Antigua and Barbuda</option>
                                                    							<option value="AR">Argentina</option>
                                                    							<option value="AM">Armenia</option>
                                                    							<option value="AW">Aruba</option>
                                                    							<option value="AU">Australia</option>
                                                    							<option value="AT">Austria</option>
                                                    							<option value="AZ">Azerbaijan</option>
                                                    							<option value="BS">Bahamas</option>
                                                    							<option value="BH">Bahrain</option>
                                                    							<option value="BD">Bangladesh</option>
                                                    							<option value="BB">Barbados</option>
                                                    							<option value="BY">Belarus</option>
                                                    							<option value="BE">Belgium</option>
                                                    							<option value="BZ">Belize</option>
                                                    							<option value="BJ">Benin</option>
                                                    							<option value="BM">Bermuda</option>
                                                    							<option value="BT">Bhutan</option>
                                                    							<option value="BO">Bolivia</option>
                                                    							<option value="BA">Bosnia and Herzegovina</option>
                                                    							<option value="BW">Botswana</option>
                                                    							<option value="BV">Bouvet Island</option>
                                                    							<option value="BR">Brazil</option>
                                                    							<option value="IO">British Indian Ocean Territory</option>
                                                    							<option value="BN">Brunei Darussalam</option>
                                                    							<option value="BG">Bulgaria</option>
                                                    							<option value="BF">Burkina Faso</option>
                                                    							<option value="BI">Burundi</option>
                                                    							<option value="KH">Cambodia</option>
                                                    							<option value="CM">Cameroon</option>
                                                    							<option value="CA">Canada</option>
                                                    							<option value="CV">Cape Verde</option>
                                                    							<option value="KY">Cayman Islands</option>
                                                    							<option value="CF">Central African Republic</option>
                                                    							<option value="TD">Chad</option>
                                                    							<option value="CL">Chile</option>
                                                    							<option value="CN">China</option>
                                                    							<option value="CX">Christmas Island</option>
                                                    							<option value="CC">Cocos (Keeling) Islands</option>
                                                    							<option value="CO">Colombia</option>
                                                    							<option value="KM">Comoros</option>
                                                    							<option value="CG">Congo</option>
                                                    							<option value="CD">Congo, The Democratic Republic of The</option>
                                                    							<option value="CK">Cook Islands</option>
                                                    							<option value="CR">Costa Rica</option>
                                                    							<option value="CI">Cote D'ivoire</option>
                                                    							<option value="HR">Croatia</option>
                                                    							<option value="CU">Cuba</option>
                                                    							<option value="CY">Cyprus</option>
                                                    							<option value="CZ">Czech Republic</option>
                                                    							<option value="DK">Denmark</option>
                                                    							<option value="DJ">Djibouti</option>
                                                    							<option value="DM">Dominica</option>
                                                    							<option value="DO">Dominican Republic</option>
                                                    							<option value="EC">Ecuador</option>
                                                    							<option value="EG">Egypt</option>
                                                    							<option value="SV">El Salvador</option>
                                                    							<option value="GQ">Equatorial Guinea</option>
                                                    							<option value="ER">Eritrea</option>
                                                    							<option value="EE">Estonia</option>
                                                    							<option value="ET">Ethiopia</option>
                                                    							<option value="FK">Falkland Islands (Malvinas)</option>
                                                    							<option value="FO">Faroe Islands</option>
                                                    							<option value="FJ">Fiji</option>
                                                    							<option value="FI">Finland</option>
                                                    							<option value="FR">France</option>
                                                    							<option value="GF">French Guiana</option>
                                                    							<option value="PF">French Polynesia</option>
                                                    							<option value="TF">French Southern Territories</option>
                                                    							<option value="GA">Gabon</option>
                                                    							<option value="GM">Gambia</option>
                                                    							<option value="GE">Georgia</option>
                                                    							<option value="DE">Germany</option>
                                                    							<option value="GH">Ghana</option>
                                                    							<option value="GI">Gibraltar</option>
                                                    							<option value="GR">Greece</option>
                                                    							<option value="GL">Greenland</option>
                                                    							<option value="GD">Grenada</option>
                                                    							<option value="GP">Guadeloupe</option>
                                                    							<option value="GU">Guam</option>
                                                    							<option value="GT">Guatemala</option>
                                                    							<option value="GG">Guernsey</option>
                                                    							<option value="GN">Guinea</option>
                                                    							<option value="GW">Guinea-bissau</option>
                                                    							<option value="GY">Guyana</option>
                                                    							<option value="HT">Haiti</option>
                                                    							<option value="HM">Heard Island and Mcdonald Islands</option>
                                                    							<option value="VA">Holy See (Vatican City State)</option>
                                                    							<option value="HN">Honduras</option>
                                                    							<option value="HK">Hong Kong</option>
                                                    							<option value="HU">Hungary</option>
                                                    							<option value="IS">Iceland</option>
                                                    							<option value="IN">India</option>
                                                    							<option value="ID">Indonesia</option>
                                                    							<option value="IR">Iran, Islamic Republic of</option>
                                                    							<option value="IQ">Iraq</option>
                                                    							<option value="IE">Ireland</option>
                                                    							<option value="IM">Isle of Man</option>
                                                    							<option value="IL">Israel</option>
                                                    							<option value="IT">Italy</option>
                                                    							<option value="JM">Jamaica</option>
                                                    							<option value="JP">Japan</option>
                                                    							<option value="JE">Jersey</option>
                                                    							<option value="JO">Jordan</option>
                                                    							<option value="KZ">Kazakhstan</option>
                                                    							<option value="KE">Kenya</option>
                                                    							<option value="KI">Kiribati</option>
                                                    							<option value="KP">Korea, Democratic People's Republic of</option>
                                                    							<option value="KR">Korea, Republic of</option>
                                                    							<option value="KW">Kuwait</option>
                                                    							<option value="KG">Kyrgyzstan</option>
                                                    							<option value="LA">Lao People's Democratic Republic</option>
                                                    							<option value="LV">Latvia</option>
                                                    							<option value="LB">Lebanon</option>
                                                    							<option value="LS">Lesotho</option>
                                                    							<option value="LR">Liberia</option>
                                                    							<option value="LY">Libyan Arab Jamahiriya</option>
                                                    							<option value="LI">Liechtenstein</option>
                                                    							<option value="LT">Lithuania</option>
                                                    							<option value="LU">Luxembourg</option>
                                                    							<option value="MO">Macao</option>
                                                    							<option value="MK">Macedonia, The Former Yugoslav Republic of</option>
                                                    							<option value="MG">Madagascar</option>
                                                    							<option value="MW">Malawi</option>
                                                    							<option value="MY">Malaysia</option>
                                                    							<option value="MV">Maldives</option>
                                                    							<option value="ML">Mali</option>
                                                    							<option value="MT">Malta</option>
                                                    							<option value="MH">Marshall Islands</option>
                                                    							<option value="MQ">Martinique</option>
                                                    							<option value="MR">Mauritania</option>
                                                    							<option value="MU">Mauritius</option>
                                                    							<option value="YT">Mayotte</option>
                                                    							<option value="MX">Mexico</option>
                                                    							<option value="FM">Micronesia, Federated States of</option>
                                                    							<option value="MD">Moldova, Republic of</option>
                                                    							<option value="MC">Monaco</option>
                                                    							<option value="MN">Mongolia</option>
                                                    							<option value="ME">Montenegro</option>
                                                    							<option value="MS">Montserrat</option>
                                                    							<option value="MA">Morocco</option>
                                                    							<option value="MZ">Mozambique</option>
                                                    							<option value="MM">Myanmar</option>
                                                    							<option value="NA">Namibia</option>
                                                    							<option value="NR">Nauru</option>
                                                    							<option value="NP">Nepal</option>
                                                    							<option value="NL">Netherlands</option>
                                                    							<option value="AN">Netherlands Antilles</option>
                                                    							<option value="NC">New Caledonia</option>
                                                    							<option value="NZ">New Zealand</option>
                                                    							<option value="NI">Nicaragua</option>
                                                    							<option value="NE">Niger</option>
                                                    							<option value="NG">Nigeria</option>
                                                    							<option value="NU">Niue</option>
                                                    							<option value="NF">Norfolk Island</option>
                                                    							<option value="MP">Northern Mariana Islands</option>
                                                    							<option value="NO">Norway</option>
                                                    							<option value="OM">Oman</option>
                                                    							<option value="PK">Pakistan</option>
                                                    							<option value="PW">Palau</option>
                                                    							<option value="PS">Palestinian Territory, Occupied</option>
                                                    							<option value="PA">Panama</option>
                                                    							<option value="PG">Papua New Guinea</option>
                                                    							<option value="PY">Paraguay</option>
                                                    							<option value="PE">Peru</option>
                                                    							<option value="PH">Philippines</option>
                                                    							<option value="PN">Pitcairn</option>
                                                    							<option value="PL">Poland</option>
                                                    							<option value="PT">Portugal</option>
                                                    							<option value="PR">Puerto Rico</option>
                                                    							<option value="QA">Qatar</option>
                                                    							<option value="RE">Reunion</option>
                                                    							<option value="RO">Romania</option>
                                                    							<option value="RU">Russian Federation</option>
                                                    							<option value="RW">Rwanda</option>
                                                    							<option value="SH">Saint Helena</option>
                                                    							<option value="KN">Saint Kitts and Nevis</option>
                                                    							<option value="LC">Saint Lucia</option>
                                                    							<option value="PM">Saint Pierre and Miquelon</option>
                                                    							<option value="VC">Saint Vincent and The Grenadines</option>
                                                    							<option value="WS">Samoa</option>
                                                    							<option value="SM">San Marino</option>
                                                    							<option value="ST">Sao Tome and Principe</option>
                                                    							<option value="SA">Saudi Arabia</option>
                                                    							<option value="SN">Senegal</option>
                                                    							<option value="RS">Serbia</option>
                                                    							<option value="SC">Seychelles</option>
                                                    							<option value="SL">Sierra Leone</option>
                                                    							<option value="SG">Singapore</option>
                                                    							<option value="SK">Slovakia</option>
                                                    							<option value="SI">Slovenia</option>
                                                    							<option value="SB">Solomon Islands</option>
                                                    							<option value="SO">Somalia</option>
                                                    							<option value="ZA">South Africa</option>
                                                    							<option value="GS">South Georgia and The South Sandwich Islands</option>
                                                    							<option value="ES">Spain</option>
                                                    							<option value="LK">Sri Lanka</option>
                                                    							<option value="SD">Sudan</option>
                                                    							<option value="SR">Suriname</option>
                                                    							<option value="SJ">Svalbard and Jan Mayen</option>
                                                    							<option value="SZ">Swaziland</option>
                                                    							<option value="SE">Sweden</option>
                                                    							<option value="CH">Switzerland</option>
                                                    							<option value="SY">Syrian Arab Republic</option>
                                                    							<option value="TW">Taiwan, Province of China</option>
                                                    							<option value="TJ">Tajikistan</option>
                                                    							<option value="TZ">Tanzania, United Republic of</option>
                                                    							<option value="TH">Thailand</option>
                                                    							<option value="TL">Timor-leste</option>
                                                    							<option value="TG">Togo</option>
                                                    							<option value="TK">Tokelau</option>
                                                    							<option value="TO">Tonga</option>
                                                    							<option value="TT">Trinidad and Tobago</option>
                                                    							<option value="TN">Tunisia</option>
                                                    							<option value="TR">Turkey</option>
                                                    							<option value="TM">Turkmenistan</option>
                                                    							<option value="TC">Turks and Caicos Islands</option>
                                                    							<option value="TV">Tuvalu</option>
                                                    							<option value="UG">Uganda</option>
                                                    							<option value="UA">Ukraine</option>
                                                    							<option value="AE">United Arab Emirates</option>
                                                    							<option value="GB">United Kingdom</option>
                                                    							<option value="US" selected>United States</option>
                                                    							<option value="UM">United States Minor Outlying Islands</option>
                                                    							<option value="UY">Uruguay</option>
                                                    							<option value="UZ">Uzbekistan</option>
                                                    							<option value="VU">Vanuatu</option>
                                                    							<option value="VE">Venezuela</option>
                                                    							<option value="VN">Viet Nam</option>
                                                    							<option value="VG">Virgin Islands, British</option>
                                                    							<option value="VI">Virgin Islands, U.S.</option>
                                                    							<option value="WF">Wallis and Futuna</option>
                                                    							<option value="EH">Western Sahara</option>
                                                    							<option value="YE">Yemen</option>
                                                    							<option value="ZM">Zambia</option>
                                                    							<option value="ZW">Zimbabwe</option>
                                                    							</select>
                                                                            </td></tr>
                            <tr><td><br/></td></tr>
                            <tr><td>Shipping Type </td><td><select class="form-control" name="shipping_method" id="shipping_method" style="width: 250px;" class="required-entry">
                                <optgroup label="United Parcel Service" style="font-style:normal;">
                                <option value="8.00">
                                Worldwide Expedited - $8.00</option>
                                <option value="4.00">
                                Worldwide Express Saver - $4.00</option>
                                </optgroup>
                                <optgroup label="Flat Rate" style="font-style:normal;">
                                <option value="2.00" selected>
                                Fixed - $2.00</option>
                                </optgroup>
                                </select><br>
                            </td></tr>
                            <tr><td colspan="2" id="payment-selection-title">Payment Methods:</td></tr>
                            <tr><td colspan="2" id="payment-selection">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="paymentMethod" id="paypal_payment_option" value="paypal_express" checked>
                                            <img src="https://fpdbs.paypal.com/dynamicimageweb?cmd=_dynamic-image&amp;buttontype=ecmark&amp;locale=en_US" alt="Acceptance Mark" class="v-middle"></input>&nbsp;
                                            <a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=xpt/Marketing/popup/OLCWhatIsPayPal-outside" onclick="javascript:window.open('https://www.paypal.com/us/cgi-bin/webscr?cmd=xpt/Marketing/popup/OLCWhatIsPayPal-outside','olcwhatispaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, ,left=0, top=0, width=400, height=350'); return false;">What is PayPal?</a>
                                          </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="paymentMethod" id="credit_card_option" value="credit_card" disabled>
                                                Credit Card
                                        </label>
                                    </div>
                                </td>
                            </tr>
                           <tr><td><br/></td></tr>
                        </table>
                    <div id="myContainer"></div>
                    <div id="paypal-end" style="display:none">
                    <h2>Your payment is complete</h2>
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
                </form>
            </h4>
            <br/>
        </div>
        <div class="col-md-4"></div>
    </div>

    <!-- PayPal In-Context Checkout script -->
   <script src="//www.paypalobjects.com/api/checkout.js"></script>
    <!-- PayPal In-Context Checkout script -->
    <script type="text/javascript">
 
        var client = {
            sandbox:    '<?php echo(CLIENT_ID)?>'
        };

        var environment = 'sandbox';
        var transaction = '';

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
       function handleResponse(result) {
            // document.getElementById('paypal-execute-details').textContent = JSON.stringify(result, null, 2);
            // showDom('paypal-end');
            var resultDOM = JSON.stringify(result, null, 2);
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


            var paymentOptionTitle = document.getElementById('payment-selection-title');
            var paymentOption = document.getElementById('payment-selection');
            paymentOptionTitle.parentNode.style.display= 'none';
            paymentOption.parentNode.style.dispaly='none';

            showDom('paypal-end');

        }


 paypal.Button.render({

        // Set your environment

        env: 'sandbox', // sandbox | production

        client: {
            sandbox: '<?php echo(CLIENT_ID)?>'
        },

        // Set to 'Pay Now'

        commit: true,

        // Wait for the PayPal button to be clicked

        payment: function(actions) {

            var fullname = document.getElementById('recipient_name').value;
            var line1 = document.getElementById('line1').value;
            var line2 = document.getElementById('line2').value;
            var shipToCity = document.getElementById('city').value;
            var shipToState = document.getElementById('state').value;
            var shipToZip=document.getElementById('postal_code').value;
            var shipToCountry=document.getElementById('country_code').value;
            var shippingSel = document.getElementById('shipping_method');
            var shipping_amt = shippingSel.options[shippingSel.selectedIndex].value;
            var total_amt = parseFloat(320) - parseFloat(2) + parseFloat(shipping_amt);

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
							},
							item_list:
							{
								shipping_address:
								{
									recipient_name: fullname,
									line1: line1,
									line2: line2,
									city: shipToCity,
									country_code: shipToCountry,
									postal_code: shipToZip,
									phone: "011862212345678",
									state: shipToState,
								}
							}
						}
					]
				}
			});

        },

        // Wait for the payment to be authorized by the customer

        onAuthorize: function(data, actions) {

            return actions.payment.execute().then(handleResponse);
        }

    }, '#myContainer');
     </script>
<?php
    include('footer.php');
?>