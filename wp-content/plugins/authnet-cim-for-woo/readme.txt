=== Authorize.Net CIM for WooCommerce ===
Contributors: cardpaysolutions
Tags: authorize.net, woocommerce, woocommerce authorize.net, authorize.net cim, customer information manager, authorize.net payment gateway, payment gateway, woocommerce payment, woocommerce subscription payment, woocommerc pre order payment
Requires at least: 4.0
Tested up to: 4.9
Stable tag: 2.0.4
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Authorize.Net CIM for WooCommerce allows merchants to accept credit cards with support for stored cardholder profiles, subscriptions, and pre-orders.

== Description ==

The Authorize.Net CIM for WooCommerce plugin adds Authorize.Net as a payment method to your WooCommerce store. Authorize.Net makes accepting credit cards simple. 
Accept all major credit cards including Visa, MasterCard, American Express, Discover, JCB, and Diners Club. The Authorize.Net extension allows your logged in 
customers to securely store and re-use credit card profiles to speed up the checkout process. We also support all Subscription and Pre-Order features. 

= Features =

* Supports both "Authorize Only" and "Authorize & Capture" transaction types
* Optional automatic capture of "Authorize Only" transactions when order status is changed to "Completed"
* Supports WooCommerce 2.2+ automatic refunds
* Supports WooCommerce 2.6+ Tokenization features
* Customers can save credit card information to use for future orders
* Customers can Add, Edit, or Delete saved credit cards from the "My Account" menu
* Stored credit cards are securely tokenized using Authorize.Net Customer Information Manager (CIM)
* Supports all WooCommerce Subscriptions 2.x features
* Supports WooCommerce Pre-Orders
* Uses the WooCommerce built in checkout so the customer never leaves your website
* AVS and CVC responses are shown on Order Detail page to assist with fraud prevention

= Requirements =

An Authorize.Net Gateway Account and Merchant Account is required. Your Authorize.Net Gateway must have Customer Information Manager (CIM)
enabled for the stored credit card, subscription, and pre-orders features to work.

We are the largest Authorize.Net reseller in the United States and can set up your Authorize.Net gateway and merchant account for only $10 per month with
no set-up fees. This includes Customer Information Manager (CIM), Automated Recurring Billing (ARB), and Advanced Fraud Detection Suite. Our merchant
processing rates are the lowest in the industry.

[Click Here to Sign Up!](http://www.authnetsource.com/woocommerce?pid=da135059c7ef73c4)

== Installation ==

= Minimum Requirements =

* WordPress 3.8 or greater
* WooCommerce 2.2 or greater
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install,
follow these directions:

1. Log in to your WordPress dashboard
1. Navigate to the Plugins menu and click Add New
1. Search for "Authorize.Net CIM for WooCommerce" and click "Install Now"
1. Activate `uthorize.net CIM for WooCommerce` from the Plugins page
1. Complete the configuration by navigating to WooCommmerce -> Settings -> Checkout -> Authorize.Net CIM

= Manual installation =

1. Download and unzip the Authorize.Net CIM for WooCommerce plugin
1. Upload the plugin folder to the `/wp-content/plugins/` directory
1. Activate `Authorize.net CIM for WooCommerce` from the Plugins page
1. Complete the configuration by navigating to WooCommmerce -> Settings -> Checkout -> Authorize.Net CIM

== Frequently Asked Questions ==

= How do I obtain an Authorize.Net gateway and merchant account? =

[Click Here](http://www.authnetsource.com/woocommerce?pid=da135059c7ef73c4) to register for a low cost account.

= How do I get my API Login ID and Transaction Key? =

Call sales support at (866) 502-8910 and we can assist you.

= How do I test the plugin before going live? =

The plugin has a built-in test mode. Navigate to the configuration page at WooCommerce -> Settings -> Checkout -> Authorize.Net CIM
and check the "Use Sandbox" box and then click the "Save Changes" button. The API Login ID and Transaction Key fields are
not required in sandbox mode and can be left blank.

The following test cards can be used in Sandbox Mode with any future expiration date:

* Visa 4111111111111111
* MasterCard 5424000000000015

Zip Code 46214 will generate an AVS "match" response
Card Code 900 will generate a CVC "match" response

= Can I use the plugin without the stored credit card features? =

Yes. Navigate to the configuration page at WooCommerce -> Settings -> Checkout -> Authorize.Net CIM and uncheck the "Allow Stored Cards"
box and save your changes. Customers will then not see the option to save cards for future use in the checkout and will not see any stored credit
card information on the My Account page.

= What is the difference between the "Authorize Only" and "Authorize & Capture" transaction types? =

The Authorize Only transaction type reserves the amount of the transaction on the customer's credit card but does not start the process of
transferring the funds to your bank account until a separate "Capture" request is sent to the gateway. The capture request can be 
automatically sent when the order status is changed to "Completed" by enabling the "Auto Capture" feature in the configuration or by
logging into your Authorize.Net account and manually requesting the capture from there.

The Authorize & Capture transaction type authorizes the transaction and then automatically captures it at your designated batch cut-off
time each day. This starts the process of moving the funds to your bank account.

== Screenshots ==

1. Settings
2. Checkout
3. Customer credit card management
4. Admin order management

== Changelog ==

= 1.0.0 =

* Initial release

= 1.0.1 =

* Fixed error messages for declined transactions

= 1.0.2 =

* Fixed bill to address bug

= 1.0.4 =

* Fixed formatting of saved card dropdown on payment form
* Tested compatibility with Wordpress 4.7
* Tested compatibility with WooCommerce 3.0.7

= 2.0.0 =

* Added support for WC 2.6+ Tokenization
* Updated for WC 3.0+ CRUD
* Other minor bug fixes

= 2.0.1 =

* Updated card identification for new MasterCard BINs
* Other minor bug fixes

= 2.0.2 =

* Minor bug fixes

= 2.0.3 =

* Fixed pre-orders bug

= 2.0.4 =

* Fixed subscriptions bug

== Upgrade Notice ==

= 2.0.0 =
Increases compatibility with WooCommerce 3.0
