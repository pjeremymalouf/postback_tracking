This plugin is very simply to add postback tracking to your Wordpress site through either:
 - Specific pages that you define based on their permalink
 - PayPal transactions using the WooCommerce plugin

All you have to do is go into Settings>Postback URL Manager and enter the details for as many or as few pages as you like.

Postback URL: This is the tracking link from your adserver. In order to include sub id's use any of these codes ({sid1}, {sid2} or {sid3}) depending on which Sub ID's you populate. Example: http://yoursite.com?tracking_id={sid1}&customer_id={sid2}

Page: This is what the plugin uses to detect when to fire the Postback. This is derived from the permalink of the page or post that you want to track. Example: if the permalink is http://yoursite.com/test-page/ then you should put "test-page" into this field.

Sub ID fields: Enter here the Sub ID's that the plugin should store to later apply to the Postback URL when firing. Example: the link to your site is http://yoursite.com?referrer=google&user=user1 and you want to store the data provided by the URL, then put "referrer" into Sub ID and "user" into Sub ID 2. You can then put {sid1} and {sid2} into the Postback URL where you want these values to appear.

WooCommerce PayPal:
 - Only one Sub ID can be passed for this feature.
 - No page needs to be added as the WooCommerce PayPal tracking will be fired when a response is received from PayPal.

*Note: There are two hidden boxes in the admin settings for Postback URL Manager which lists the PayPal transactions that were begun by a user and all of the responses from the PayPal IPN service.

Support email: products@pjeremymalouf.com
