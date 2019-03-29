<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require '../lib/paysonapi.php';
// Your agent ID and md5 key
$agentID = "4";
$md5Key = "2acab30d-fe50-426f-90d7-8c60a7eb31d4";
// Fetch the token to the purchase.
$token = "YOUR TOKEN";

$credentials = new PaysonCredentials($agentID, $md5Key);
$api = new PaysonApi($credentials, TRUE);
// Get the details about this purchase
$detailsResponse = $api->paymentDetails(new PaymentDetailsData($token));

// First we verify that the call to payson succeeded.
if ($detailsResponse->getResponseEnvelope()->wasSuccessful()) {

    // Get the payment details from the response
    $details = $detailsResponse->getPaymentDetails();

    /*API Action PaymentUpdate
    Update an existing payment, for instance mark an order as shipped or canceled. The following updating actions are available:

    * CANCELORDER – Cancel a payment before it is shipped.
      Note: Possible if type is INVOICE and invoiceStatus is ORDERCREATED.
    * SHIPORDER – Mark an invoice payment as shipped (capture payment). The sender will be notified by Payson that an invoice has been created.
      Note: Possible if type is INVOICE and invoiceStatus is ORDERCREATED.
    * CREDITORDER – Credit an invoice payment.
      Note: If the receiver account has insufficient funds it is not possible to credit the order. Possible if type is INVOICE and invoiceStatus is SHIPPED or DONE.
    * REFUND – Refunds a bank or card payment to the sender. If the sender deposited the amount it will be refunded to the origin if the origin supports it. If the sender payment was made in full or partially with funds from the sender’s Payson account the credited amount will be refunded to the sender’s Payson account.
      Note: Possible if type is TRANSFER.
    * CANCELPAYMENT – Cancels a pending bank or card payment.
      Note: Possible if type is TRANSFER.*/
    
    $paymentUpdateData = new PaymentUpdateData(
    $details->getToken(),
    PaymentUpdateMethod::ShipOrder
    //PaymentUpdateMethod::CancelOrder
    //PaymentUpdateMethod::CreditOrder
    //PaymentUpdateMethod::Refund
    );

    $paymentUpdateResponse = $api->paymentUpdate($paymentUpdateData);



} else {
    $detailsErrors = $detailsResponse->getResponseEnvelope()->getErrors();
    ?>
    <h3>Error</h3>
    <table>
        <tr>
            <th>
                Error id
            </th>

            <th>
                Message
            </th>

            <th>
                Parameter
            </th>
        </tr>
        <?php
        foreach ($detailsErrors as $error) {
            echo "<tr>";
            echo "<td>" . $error->getErrorId() . '</td>';
            echo "<td>" . $error->getMessage() . '</td>';
            echo "<td>" . $error->getParameter() . '</td>';
            echo "</tr>";
        }
        ?>
    </table>
    <?php
}
exit;
?>
