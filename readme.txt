=== WooCommerce e-conomic Integration ===
Contributors:      WooConomics
Plugin Name:       WooCommerce e-conomic Plugin
Plugin URI:        www.wooconomics.com
Tags:              WooCommerce, Order, E-Commerce, Accounting, Bookkeeping, invoice, invoicing, e-conomic, WooCommerce, order sync, customer sync, product sync, sync, Customers, Integration, woocommerce e-conomic integration, woocommerce integration, economic integration, woocommerceeconomic, wocommerce economic, woocomerce economic, wocomerce economic
Author URI:        www.wooconomics.com
Author:            wooconomics
Requires at least: 3.8
Tested up to:      4.7.2
Stable tag:        1.9.26
Version:           1.9.26

WooCommerce e-conomic integration synchronizes your WooCommerce Orders, Customers and Products to your e-conomic account.

== Description ==

WooCommerce e-conomic integration synchronizes your WooCommerce Orders, Customers and Products to your e-conomic accounting system. 
e-conomic invoices can be automatically created. This plugin requires the WooCommerce plugin. 
The INVOICE and PRODUCT sync features require a license purchase from http://wooconomics.com. 
WooCommerce e-conomic integration plugin connects to license server hosted at http://onlineforce.net to check the validity of the license key you type in the settings page.

[vimeo http://vimeo.com/131753163]

= Data export to e-conomic: =
	*	CUSTOMER:
		*	Billing Company / Last Name
		*	Billing Last Name
		*	Billing First Name
		*	Email
		*	Billing Address 1
		*	Billing Address 2
		*	Billing Country
		*	Billing City
		*	Billing Postcode
		*	Shipping Address 1
		*	Shipping Address 2
		*	Shipping Country
		*	Shipping City
		*	Shipping Postcode
		*   VAT Zone
	*	PRODUCT/ARTICLE:
		*	Product name
		*	ArticleNumber (SKU + product prefix)
		*	Regular Price / Sale Price
		*	Description
		*	Inventory stock quantity (updated from e-conomic to WooCommerce)
	*	INVOICE:
		*	Order ID (as reference)
		*	Customer number
		*	Delivery Address
		*	Delivery City
		*	Delivery Postcode
		*	Delivery Country
		*	Product Title
		*	Product quantity
		*	Product Price
		*	Shipping cost (as orderline - workaround) 
		*	Currency

= Features of WooCommerce e-conomic Integration: =

1.	Automatic sync of all Customers from WooCommerce to e-conomic invoicing service dashboard.
2.	Automatic sync of all Orders from WooCommerce to e-conomic invoicing service dashboard. Sync initiated when order status is changed to 'Completed'.
3.	Automatic sync of all products from WooCommerce to e-conomic invoicing service Items. This function also updates products data modified after initial sync. Supports variable products.
4.	Manual sync of all Shipping methods (excluding the additional cost for flat_shipping) from WooCommerce to e-conomic invoicing service dashboard.
5.	Sync Order, Products, Customers to e-conomic when Order status is changed to 'Completed' at WooCommerce->Orders Management section.
6.  Stock Sync from e-conomic to WooCommerce. Real Time Stock Sync and Scheduled Stock Sync.
7.  Sync orders created before wooconomics installation using "Activate old orders sync" option.
8.  "Activate product sync" option syncs product information from WooCommerce to e-conomic. (Stock information is updated regardless of this setting)
9. Using "Product group" option, new products from the selected group are added at e-conomic product group.       
10. Prefix added to the products stored from woocommerce to e-conomic using "Product prefix" option.
11. New customers are added at e-conomic customer group using "Customer group" option(domestic, european and overseas).
12. Multishop support. Use "Order reference prefix" to add a prefix to the order reference of an Order synced from woocommerce to e-conomic.
13. Manual sync of all Products and Customers data from WooCommerce send to e-conomic using "WooCommerce to e-conomic". Manual sync of all Products and Customers data from e-conomic saved at WooCommerce using "e-conomic to WooCommerce". Choose this option before using "Manual sync contacts" and "Manual sync products" option, default will be WooCommerce to e-conomic.
14. Multishop support. Support for multiple stores with different currency. Option to use base currency setting in e-conomic (default setting) or use currency setup in WooCommerce.

= Compatiblity: =

*	Compatible up to: WooCommerce 2.6.4

= Supported Plugins: =

1. Product Bundles WooCommerce Extension.
2. Weight Based Shipping for WooCommerce.
3. WooCommerce Sequential Order Numbers.
4. WooCommerce Product Price Based on Countries.



== Plugin Requirement ==

*	PHP version : 5.3 or higher, tested upto 5.5.X
*	WordPress   : Wordpress 3.8 or higher
*	SOAP client
*	CURL module
*	Woocommerce: 2.6.0 or higher, tested upto 2.7.0

== Installation ==

[vimeo http://vimeo.com/131753163]

1.	Install WooCommerce e-conomic Integration either via the WordPress.org plugin directory, or by uploading the files to your server
2.	Activate the plugin in your WordPress Admin and go to the admin panel Setting -> WooCommerce e-conomic Integration.
3.	Active the plugin with your License Key that you have received by mail and your e-conomic API-USER ID.
4.	Configure your plugin as needed.
5.	That's it. You're ready to focus on sales, marketing and other cool stuff :-)

== Screenshots ==

1.	*General settings*

2.	*Manual Sync function*

3.	*Support*

4.	*Welcome Screen*

Read the FAQ or business hours mail support except weekends and holidays.

== Frequently Asked Questions ==

http://wooconomics.com/category/faq/

== Changelog ==

= 1.9.26 =
* Support for full refund added.

= 1.9.25 =
* Improvements

= 1.9.24 =
* Improved product sync.

= 1.9.23 =
* Improved customer data sync for guest user
* Invalid UTF-8 content in Product data is handled.

= 1.9.22 =
* Order Notes are now added as Order/Current invoice "Text 2"
* Customer currency is updated if "WooCommerce Product Price Based on Countries" plugin is used.
* Bug fixes.

= 1.9.21 =
* Improvements and Bug fixes.

= 1.9.20 =
* Feature addition: Sync customer on user profile update.

= 1.9.19 =
* Bug fixes.

= 1.9.18 =
* Added support for "WooCommerce Product Price Based on Countries" plugin.
* Minor plugin settings UI changes.

= 1.9.17 =
* Added compatiblity for WooCommerce 2.6.2

= 1.9.16 =
* Bug fixes and Improvements.

= 1.9.15 =
* Added support for Customer Identification number for non SE customers.

= 1.9.14 =
* Added support for WordPress Multisite.
* Bug fixes.

= 1.9.13 =
* Added support for handling VAT number. Customers are updated with VAT number during checkout, if the order has 'billing_vat_number' meta key and value.

= 1.9.12 =
* Bug fixes.

= 1.9.11 =
* Improved performance.

= 1.9.10 =
* Improved performance.
* Added AppIdentifier header compliance with e-conomic standard.

= 1.9.9.18 =
* bug fixes for e-conomic old ledger layout.

= 1.9.9.17 =
* Compatibility for WooCommerce Subscription is added.

= 1.9.9.16 =
* Added option to specify when an order sync should happen from WooCommerce to e-conomic, Based on an Event or Based on Order status.

= 1.9.9.15 =
* Bug fix: Fix for Latin characters not displayed properly in product name and description.

= 1.9.9.14 =
* Improved Customer Sync in both direction between WooCommerce and e-Conomic

= 1.9.9.13 =
* Improved real time stock sync from e-conomic to WooCommerce.

= 1.9.9.12 =
* Added support for real time stock sync from e-conomic to WooCommerce.

= 1.9.9.11 =
* Support added for "WooCommerce Sequential Order Numbers" plugin.
* Bug fixes.

= 1.9.9.10 =
* Product sales price synced to e-conomic based on store currency.
* Bug fixes.

= 1.9.9.9 =
* Plugin settings updated. New options added to choose what should be created at e-conomic (order, draft invoice, invoice or do nothing) for e-conomic payment checkout (e-conomic checkout) and other payment checkout (Other checkout). Couple of old options were removed "Create" and "Activate all orders sync"

= 1.9.9.8 =
* Payment method and date is captured in order/current invoice primary line of text.
* For EAN payment method, customers are updated with EAN no. if the order has 'billing_ean_number' meta key and value.
* e-conomic customer number can be added or edited at user profile page.

= 1.9.9.7 =
* Bug fix.

= 1.9.9.6 =
* Products can be synced to different e-conomic product groups. There is site wide option to select single product group for all products and product group selection for each products in product edit page.

= 1.9.9.5 =
* Feature for WooCommerce Coupons.
* Improvements in Automatic syncs.
* Bug fixes.

= 1.9.9.4 =
* Bug fix.

= 1.9.9.3 =
* Fix for Shipping method ID more than 25 characters.

= 1.9.9.2 =
* Bug fixes.

= 1.9.9.1 =
* Bug fixes.
* Duplicate order creation after upgrading e-conomic orders to draft invoices/invoices is fixed.
* Now WooConomics supports WooThemes Product Bundles WooCommerce Extension.

= 1.9.9 =
* Bug fixes.

= 1.9.8 =
* Bug fixes.

= 1.9.7 =
* Multishop support: Support for multiple stores with different currency. Option to use base currency setting in e-conomic (default setting) or use currency setup in WooCommerce.
* Bug fixes.

= 1.9.6 =
* Multishop support: Prefix for order reference is added to identify from which store an Order is synced to e-conomic. This feature is usefull to sync orders from multiple online stores to a single e-conomic account with reference.
* Bug fixes.

= 1.9.5 =
* WooConomics now supports Variable products.
* Bug fixes.

= 1.9.4 =
* Vat Zone for Debtors/Customers created at e-conomic is updated based on the WooCommerce Tax settings.
* Order synced when Admin add orders manually at admin dashboard.

= 1.9.3 =
* Bug fixes.

= 1.9.2 =
* Sync Customers and Products in both direction. Added option to select sync direction.
* Bug fixes.

= 1.9.1 =
* Wordpress cron feature for product sync every hour, or twice a day, or every day added.
* Bug fixes.

= 1.9 =
* Bug fixes for fsockopen connection.

= 1.8 =
* Settings to sync orders created before wooconomic installation added.
* Bug fixes.

= 1.7 =
* Bug fix.

= 1.6 =
* Now the plugin can support guest customer checkouts and sync guest customer data to e-conomic.
* Few bug fixes done.

= 1.5 =
* Supports stock/inventory sync from e-conomic to WooCommerce.
* New option to select product sync is added in settings.
* Now supports WordPress 4.3

= 1.4 =
* Bug fixes and automatic e-conomic token access authentication added

= 1.3 =
* Sending PDF inovice for e-conomic payment checkout option added

= 1.2 =
* Option to select between order or invoice added.
* Plugin authentication method changed to Token access ID and Private App ID.
* Language support for Svenska, Dansk, Finnish, Norsk bokm�l, Deutsche, Fran�ais, Polski, English and Espa�ol

= 1.1 =
* Improvements & Issue fixes

= 1.0 =
* Initial Release