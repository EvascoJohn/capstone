<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice #6</title>

    <style>
      html,
      body {
        margin: auto 40px;
        padding: 10px;
        font-family: sans-serif;
        width: 8.5in;
        height: 5.5in;
        align-items: center;
      }
      h1,
      h2,
      h3,
      h4,
      h5,
      h6,
      p,
      span,
      label {
        font-family: sans-serif;
      }
      table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0px !important;
      }
      table thead th {
        height: 28px;
        text-align: left;
        font-size: 16px;
        font-family: sans-serif;
      }
      table,
      th,
      td {
        border: 1px solid #ddd;
        padding: 8px;
        font-size: 14px;
      }

      .heading {
        font-size: 24px;
        margin-top: 12px;
        margin-bottom: 12px;
        font-family: sans-serif;
      }
      .small-heading {
        font-size: 18px;
        font-family: sans-serif;
      }
      .total-heading {
        font-size: 18px;
        font-weight: 700;
        font-family: sans-serif;
      }
      .order-details tbody tr td:nth-child(1) {
        width: 20%;
      }
      .order-details tbody tr td:nth-child(3) {
        width: 20%;
      }

      .text-start {
        text-align: left;
      }
      .text-end {
        text-align: right;
      }
      .text-center {
        text-align: center;
      }
      .company-data span {
        margin-bottom: 4px;
        display: inline-block;
        font-family: sans-serif;
        font-size: 14px;
        font-weight: 400;
      }
      .no-border {
        border: 1px solid #fff !important;
      }
      .bg-blue {
        background-color: #414ab1;
        color: #fff;
      }
    </style>
  </head>
  <body>
    <table class="order-details">
      <thead>
        <tr>
          <th width="50%" colspan="2">
            <h2 class="text-start">Probikes Motorcycle Center</h2>
            <span>Nasugbu Branch</span> <br />
          </th>
          <th width="50%" colspan="2" class="text-end company-data">
            <span>Invoice Id: #6</span> <br />
            <span>Date: 24 / 09 / 2022</span> <br />
            <span>Zip code : 4231</span> <br />
            <span
              >Address: #164 J.P Laurel St. Brgy. 2 (Pob). 4231, Nasugbu,
              Batangas</span
            >
            <br />
          </th>
        </tr>
        <tr class="bg-blue">
          <th width="50%" colspan="2">Order Details</th>
          <th width="50%" colspan="2">Customer Details</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Application Id:</td>
          <td>6</td>

          <td>Full Name:</td>
          <td>Jess Gabriel Miranda</td>
        </tr>
        <tr>
          <td>Reference No.:</td>
          <td>02342343278932</td>

          <td>Email Id:</td>
          <td>jessmiranda0429@gmail.com</td>
        </tr>
        <tr>
          <td>Payment Date:</td>
          <td>22-09-2022 10:54 AM</td>

          <td>Phone:</td>
          <td>09476256034</td>
        </tr>
        <tr>
          <td>Payment Mode:</td>
          <td>Cash</td>

          <td>Address:</td>
        <td>{{$record->$customerApplication->applicant_present_address}}</td>
        </tr>
        <tr>
          <td>Payment Status:</td>
          <td>completed</td>

          <td>Engine No.:</td>
          <td>56699926274</td>
        </tr>
      </tbody>
    </table>

    <table>
      <thead>
        <tr>
          <th class="no-border text-start heading" colspan="5">Order</th>
        </tr>
        <tr class="bg-blue">
          <th>Frame No.</th>
          <th>Unit</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td width="10%">16235235260</td>
          <td>StarX 150</td>
          <td width="10%">$14000</td>
          <td width="10%">1</td>
          <td width="15%" class="fw-bold">$14000</td>
        </tr>
        <tr>
          <td colspan="4" class="total-heading">
            Total Amount - <small>Inc. all vat/tax</small> :
          </td>
          <td colspan="1" class="total-heading">$14699</td>
        </tr>
      </tbody>
    </table>

    <br />
    <p class="text-center">Thank your for Purchase! Ride Safe!</p>
  </body>
</html>
