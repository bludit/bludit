<html>
  <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Checkout with PayPal Demo</title>
      <!--Including Bootstrap style files-->
      <link href="css/bootstrap.min.css" rel="stylesheet">
      <link href="css/bootstrap-responsive.min.css" rel="stylesheet">

      <style>
          /* http://angrytools.com/gradient/ */
          .bg-color {
              color: white;
              background: -moz-linear-gradient(0deg, #004094 0%, #0096D9 50%, #004094 100%); /* ff3.6+ */
              background: -webkit-gradient(linear, left top, right top, color-stop(0%, #004094), color-stop(50%, #0096D9), color-stop(100%, #004094)); /* safari4+,chrome */
              background: -webkit-linear-gradient(0deg, #004094 0%, #0096D9 50%, #004094 100%); /* safari5.1+,chrome10+ */
              background: -o-linear-gradient(0deg, #004094 0%, #0096D9 50%, #004094 100%); /* opera 11.10+ */
              background: -ms-linear-gradient(0deg, #004094 0%, #0096D9 50%, #004094 100%); /* ie10+ */
              background: linear-gradient(90deg, #004094 0%, #0096D9 50%, #004094 100%); /* w3c */
              filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#004094', endColorstr='#004094',GradientType=1 ); /* ie6-9 */
          }
          .mark{
              max-width: 45%;
          }
          .mark-input-field{
              height:30px !important;
          }
          .hero-unit {
            background-color: #e0e2e5;
            border-radius: 10px;
            font-weight: bold;
            padding: 13px;
          }
      </style>
  </head>
  <body>
      <div class="container-fluid">
      <div class="well bg-color">
          <h2 class="text-center">Checkout with PayPal Demo</h2>
          <h4 class="text-center">REST API with Checkout.js v4</h4>
      </div>
      <div class="row-fluid">
      