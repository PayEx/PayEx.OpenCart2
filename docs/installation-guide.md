Prerequisites
-------------

Php extensions:

-   Openssl

-   Curl

-   Soap

Installation
------------

We can only guarantee the modules work on opencart version 1.5.6.1-1.5.6.4 aswell as the standard opencart checkout.

*Transferring the modules over to your FTP/SFTP location:*

First you need to unzip the modules from the original zip file to your computer, sometimes there are two layers of archived files, unzip them so you have the files looking like this on your computer:

![](https://payex.github.io/PayEx.OpenCart2/media/image1.png)

Now transfer them over to the root of your opencart site using FTP/SFTP, the root is usually the public\_html folder. The files will then migrate automatically to the appropriate location in your opencart application. If the ftp-client complains of over written xml-files, it is nothing to worry about.

Sign in to opencart as Administrator, <http://yoursite.com/admin>

![](https://payex.github.io/PayEx.OpenCart2/media/image2.jpeg)

Click the menu item called “Extensions” and in the drop menu click “Payments”

![](https://payex.github.io/PayEx.OpenCart2/media/image3.png)

In the list bellow, find the module you want to install and click “install” in the column to the right

![](https://payex.github.io/PayEx.OpenCart2/media/image4.jpeg)

To activate factoring fee you have to click extensions and then order totals.

Find Factoring Fee and click install

![](https://payex.github.io/PayEx.OpenCart2/media/image5.png)

Done!

Updating modules
----------------

Transfer the modules to the root of your site using ftp/sftp.

When you have updated a module it is important that you go in to that module and click the “save” button. Otherwise the new settings will not take place.

Configuration
-------------

To start configuring any module just find the module you want to configure In the same list as before and click the “edit” button in the right most column.

PayEx Bank debit
----------------

![](https://payex.github.io/PayEx.OpenCart2/media/image6.png)

Account number: You can collect the account number in Payex Merchant Admin; for production mode: <https://secure.payex.com/Admin/Logon.aspx> and for test mode: <http://test-secure.payex.com/Admin/Logon.aspx> Remember there are different account numbers for test and production mode. For more info contact PayEx support.solutions@payex.com

Encryption Key: The encryption key you get in Payex Merchant Admin (Choose md5). Remember there are different encryption keys for test and production mode For more information contact PayEx support <support.solutions@payex.com>.

To generate an encryption key control click [here](#section)

banks: Which banks are allowed to be used

mode: chooce between test and live mode

Total: The checkout total the order must reach before this payment method becomes active

Complete status: which status the order should show when completed

Pending status: which status the order should show when its pending

Canceled status: which status the order should show when its canceled

Failed status: which status the order should show when it failed

Refunded status: which status the order should show when it is refunded

Geo zone: chooce geological zone, GEO scandinavien, Uk shipping or UK VAT zone

Status: the status for the modules, enabled or disabled

Sort order: which order the module will show for the client

PayEx factoring and part payment
--------------------------------

![](https://payex.github.io/PayEx.OpenCart2/media/image7.png)

Account number: You can collect the account number in Payex Merchant Admin; for production mode: <https://secure.payex.com/Admin/Logon.aspx> and for test mode: <http://test-secure.payex.com/Admin/Logon.aspx> Remember there are different account numbers for test and production mode. For more info contact PayEx support.solutions@payex.com

Encryption Key: The encryption key you get in Payex Merchant Admin (Choose md5). Remember there are different encryption keys for test and production mode For more information contact PayEx support <support.solutions@payex.com>.

To learn how to generate an encryption key control click [here](#section)

Mode: chooce between test mode and live shop

Payment type: Payment type: You can choose between factoring, part payment or user select. On user select the customer will choose either factoring or part payment in the checkout.

Total: The checkout total the order must reach before this payment method becomes active

Complete status: which status the order should show when completed

Pending status: which status the order should show when its pending

Canceled status: which status the order should show when its canceled

Failed status: which status the order should show when it failed

Refunded status: which status the order should show when it is refunded

Geo zone: chooce geological zone, GEO scandinavien, Uk shipping or UK VAT zone

Status: the status for the modules, enabled or disabled

Sort order: which order the module will show for the client

Factoring Fee
-------------

To add a factoring fee you have to first install the factoring fee extension(see end of installation). After you have installed it you can click edit.

![](https://payex.github.io/PayEx.OpenCart2/media/image8.png)

Order total: This feature is not yet supported and should be set to 0 at all times.

Invoice fee: the cost of the invoice

Tax class: add if tax should be added to the invoice fee

Status: enable/disable

Get address
-----------

Installation: See installation for payment module(it is done the same way)

*Activating the module*

Login as admin, click extensions-&gt;modules

Click the install button next to “Social Security Number”

![](https://payex.github.io/PayEx.OpenCart2/media/image9.png)

Then click edit

![](https://payex.github.io/PayEx.OpenCart2/media/image10.png)

Status: enable or disable

Account number: You can collect the account number in Payex Merchant Admin; for production mode: <https://secure.payex.com/Admin/Logon.aspx> and for test mode: <http://test-secure.payex.com/Admin/Logon.aspx> Remember there are different account numbers for test and production mode. For more info contact PayEx support.solutions@payex.com

Encryption Key: The encryption key you get in Payex Merchant Admin (Choose md5). Remember there are different encryption keys for test and production mode For more information contact PayEx support <support.solutions@payex.com>.

Mode: chooce between test mode and live shop

PayEx Payments
--------------

![](https://payex.github.io/PayEx.OpenCart2/media/image11.png)

Account number: You can collect the account number in Payex Merchant Admin; for production mode: <https://secure.payex.com/Admin/Logon.aspx> and for test mode: <http://test-secure.payex.com/Admin/Logon.aspx> Remember there are different account numbers for test and production mode. For more info contact PayEx support.solutions@payex.com

Encryption Key: The encryption key you get in Payex Merchant Admin (Choose md5). Remember there are different encryption keys for test and production mode For more information contact PayEx support <support.solutions@payex.com>.

To learn how to generate an encryption key control click [here](#section)

Payment view: Which type of payment model you would like to use

Mode: chooce between test mode and live shop

Transaction Type: Authorize is the standard transaction type, it requires a capture of the order. With Sale the amount ordered is processed immediately and withdrawn from the customers card. For more info contact PayEx support support.solutions@payex.com

Total: The checkout total the order must reach before this payment method becomes active

Complete status: which status the order should show when completed

Pending status: which status the order should show when its pending

Canceled status: which status the order should show when its canceled

Failed status: which status the order should show when it failed

Refunded status: which status the order should show when it is refunded

Geo zone: chooce geological zone, GEO scandinavien, Uk shipping or UK VAT zone

Status: the status for the modules, enabled or disabled

Sort order: which order the module will show for the client

Generating an encryption key
----------------------------

Step 1:

you must go to <http://www.payexpim.com/> and choose admin for either test or production environment. ![](https://payex.github.io/PayEx.OpenCart2/media/image12.png)

Step 2:

Sign in with the information you have been given by payex

![](https://payex.github.io/PayEx.OpenCart2/media/image13.png)

Step 3: In the margin on the left, find “Merchant” and click on “Merchant profile”

![](https://payex.github.io/PayEx.OpenCart2/media/image14.png)

Step 4:

Click on “new encryption key”

![](https://payex.github.io/PayEx.OpenCart2/media/image15.png)

And save it.

Complete

How to translate the modules
----------------------------

Login to your site using ftp/sftp and from the root go to /catalog/language/English/payment/ and copy bankdebit.php, factoring.php, payex.php and payex\_error.php to your computer.

Translate the files to your language and upload them to the correct folder on your ftp/sftp. IE if you translated them to Swedish you would upload them to catalog/language/swedish/payment/

You might have to create the folders yourself.

<span id="_Toc381349250" class="anchor"><span id="_Toc393281939" class="anchor"></span></span>How to activate Transaction Callback
----------------------------------------------------------------------------------------------------------------------------------

Transaction callback is an extra process used by PayEx to verify that the webshop is informed of the result of the payment processing. It is useful if your server goes down during payment or if customer close the webbrowser or lose connection just after payment. Callback is a required functionality.

![](https://payex.github.io/PayEx.OpenCart2/media/image16.jpg)

Use the following URL

http://www.shopsite.com/index.php?route=payment/payex/transaction

Change www.shopsite.com for your shop's url

### 

FAQ
---

Q: When I capture a payment the capture button doesn’t change from “please wait” until I refresh the page, can I change that?

A: This is an error related to opencart core. To prevent this set the error\_log = 0 in php configuration.
