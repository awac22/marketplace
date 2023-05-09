<?php
define('BASEPATH', "/");
define('ENVIRONMENT', 'production');
require_once "application/config/database.php";

//set database credentials
$database = $db['default'];
$db_host = $database['hostname'];
$db_name = $database['database'];
$db_user = $database['username'];
$db_password = $database['password'];

/* Connect */
$connection = mysqli_connect($db_host, $db_user, $db_password, $db_name);
$connection->query("SET CHARACTER SET utf8");
$connection->query("SET NAMES utf8");
if (!$connection) {
    $error = "Connect failed! Please check your database credentials.";
}
if (isset($_POST["btn_submit"])) {
    update($connection);
    $success = 'The update has been successfully completed!';
}

function update($connection)
{
    update_17_to_18($connection);
    sleep(1);
    update_18_to_19($connection);
    sleep(1);
    update_19_to_20($connection);
    sleep(1);
}

function update_17_to_18($connection)
{
    $table_abuse_reports = "CREATE TABLE `abuse_reports` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `item_type` varchar(30) DEFAULT 'product',
      `item_id` int(11) DEFAULT NULL,
      `report_user_id` int(11) DEFAULT NULL,
      `description` varchar(10000) DEFAULT NULL,
      `created_at` timestamp NULL DEFAULT current_timestamp()
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $table_payment_gateways = "CREATE TABLE `payment_gateways` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `name` varchar(255) DEFAULT NULL,
      `name_key` varchar(255) DEFAULT NULL,
      `public_key` varchar(500) DEFAULT NULL,
      `secret_key` varchar(500) DEFAULT NULL,
      `environment` varchar(30) DEFAULT 'production',
      `locale` varchar(30) DEFAULT NULL,
      `base_currency` varchar(30) DEFAULT 'all',
      `status` tinyint(1) DEFAULT 0,
      `logos` varchar(500) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $table_product_settings = "CREATE TABLE `product_settings` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `marketplace_sku` tinyint(1) DEFAULT 1,
      `marketplace_variations` tinyint(1) DEFAULT 1,
      `marketplace_shipping` tinyint(1) DEFAULT 1,
      `marketplace_product_location` tinyint(1) DEFAULT 1,
      `classified_price` tinyint(1) DEFAULT 1,
      `classified_price_required` tinyint(1) DEFAULT 1,
      `classified_product_location` tinyint(1) DEFAULT 1,
      `classified_external_link` tinyint(1) DEFAULT 1,
      `physical_demo_url` tinyint(1) DEFAULT 0,
      `physical_video_preview` tinyint(1) DEFAULT 1,
      `physical_audio_preview` tinyint(1) DEFAULT 1,
      `digital_demo_url` tinyint(1) DEFAULT 1,
      `digital_video_preview` tinyint(1) DEFAULT 1,
      `digital_audio_preview` tinyint(1) DEFAULT 1,
      `digital_allowed_file_extensions` varchar(500) DEFAULT 'zip',
      `sitemap_frequency` varchar(30) DEFAULT 'monthly',
      `sitemap_last_modification` varchar(30) DEFAULT 'server_response',
      `sitemap_priority` varchar(30) DEFAULT 'automatically'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $table_shipping_addresses = "CREATE TABLE `shipping_addresses` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `user_id` int(11) DEFAULT NULL,
      `title` varchar(255) DEFAULT NULL,
      `first_name` varchar(255) DEFAULT NULL,
      `last_name` varchar(255) DEFAULT NULL,
      `email` varchar(100) DEFAULT NULL,
      `phone_number` varchar(100) DEFAULT NULL,
      `address` varchar(500) DEFAULT NULL,
      `country_id` varchar(20) DEFAULT NULL,
      `state_id` int(11) DEFAULT NULL,
      `city` varchar(255) DEFAULT NULL,
      `zip_code` varchar(50) DEFAULT NULL,
      `created_at` timestamp NULL DEFAULT current_timestamp()
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $table_shipping_classes = "CREATE TABLE `shipping_classes` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `name_array` text DEFAULT NULL,
      `user_id` int(11) DEFAULT NULL,
      `status` tinyint(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $table_shipping_delivery_times = "CREATE TABLE `shipping_delivery_times` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `user_id` int(11) DEFAULT NULL,
      `option_array` text DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $table_shipping_zones = "CREATE TABLE `shipping_zones` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `name_array` text DEFAULT NULL,
      `user_id` int(11) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $table_shipping_zone_locations = "CREATE TABLE `shipping_zone_locations` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `zone_id` int(11) NOT NULL,
      `user_id` int(11) DEFAULT NULL,
      `continent_code` varchar(10) DEFAULT NULL,
      `country_id` int(11) DEFAULT 0,
      `state_id` int(11) DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $table_shipping_zone_methods = "CREATE TABLE `shipping_zone_methods` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `name_array` text DEFAULT NULL,
      `zone_id` int(11) DEFAULT NULL,
      `user_id` int(11) DEFAULT NULL,
      `method_type` varchar(100) DEFAULT NULL,
      `flat_rate_cost_calculation_type` varchar(100) DEFAULT NULL,
      `flat_rate_cost` bigint(20) DEFAULT NULL,
      `flat_rate_class_costs_array` text DEFAULT NULL,
      `local_pickup_cost` bigint(20) DEFAULT NULL,
      `free_shipping_min_amount` bigint(20) DEFAULT NULL,
      `status` tinyint(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


    mysqli_query($connection, $table_abuse_reports);
    mysqli_query($connection, $table_payment_gateways);
    mysqli_query($connection, $table_product_settings);
    mysqli_query($connection, $table_shipping_addresses);
    mysqli_query($connection, $table_shipping_classes);
    mysqli_query($connection, $table_shipping_delivery_times);
    mysqli_query($connection, $table_shipping_zones);
    mysqli_query($connection, $table_shipping_zone_locations);
    mysqli_query($connection, $table_shipping_zone_methods);
    mysqli_query($connection, "DROP TABLE `product_options`;");
    mysqli_query($connection, "DROP TABLE `form_settings`;");
    sleep(1);
    mysqli_query($connection, "ALTER TABLE categories ADD COLUMN `parent_tree` varchar(255);");
    mysqli_query($connection, "ALTER TABLE categories ADD COLUMN `show_subcategory_products` TINYINT(1) DEFAULT 0;");
    mysqli_query($connection, "ALTER TABLE currencies ADD COLUMN `currency_format` VARCHAR(30) DEFAULT 'us';");
    mysqli_query($connection, "ALTER TABLE currencies ADD COLUMN `symbol_direction` VARCHAR(30) DEFAULT 'left';");
    mysqli_query($connection, "ALTER TABLE currencies ADD COLUMN `space_money_symbol` TINYINT(1) DEFAULT 0;");
    mysqli_query($connection, "ALTER TABLE currencies ADD COLUMN `exchange_rate` DOUBLE DEFAULT 1;");
    mysqli_query($connection, "ALTER TABLE currencies ADD COLUMN `status` TINYINT(1) DEFAULT 0;");
    mysqli_query($connection, "ALTER TABLE currencies DROP COLUMN `hex`;");
    mysqli_query($connection, "ALTER TABLE earnings ADD COLUMN `exchange_rate` DOUBLE DEFAULT 1;");
    //update first currency
    mysqli_query($connection, "UPDATE currencies SET status = 1 ORDER BY id LIMIT 2");

    mysqli_query($connection, "ALTER TABLE general_settings ADD COLUMN `selling_license_keys_system` TINYINT(1) DEFAULT 1;");
    mysqli_query($connection, "ALTER TABLE general_settings CHANGE `cache_system` `product_cache_system` TINYINT(1) DEFAULT 0;");
    mysqli_query($connection, "ALTER TABLE general_settings ADD COLUMN `static_content_cache_system` TINYINT(1) DEFAULT 0;");

    mysqli_query($connection, "ALTER TABLE general_settings ADD COLUMN `location_search_header` TINYINT(1) DEFAULT 1;");
    mysqli_query($connection, "ALTER TABLE general_settings ADD COLUMN `vendor_bulk_product_upload` TINYINT(1) DEFAULT 1;");
    mysqli_query($connection, "ALTER TABLE general_settings ADD COLUMN `show_sold_products` TINYINT(1) DEFAULT 1;");
    mysqli_query($connection, "ALTER TABLE general_settings ADD COLUMN `last_cron_update_long` timestamp NULL DEFAULT NULL");

    mysqli_query($connection, "ALTER TABLE location_countries ADD COLUMN `continent_code` varchar(10);");
    mysqli_query($connection, "ALTER TABLE order_products ADD COLUMN `listing_type` varchar(20);");
    mysqli_query($connection, "ALTER TABLE order_products ADD COLUMN `shipping_method` varchar(255);");
    mysqli_query($connection, "ALTER TABLE order_products ADD COLUMN `seller_shipping_cost` bigint(20) DEFAULT NULL;");
    mysqli_query($connection, "ALTER TABLE order_products DROP COLUMN `product_shipping_cost`;");
    mysqli_query($connection, "ALTER TABLE order_products CHANGE `product_vat_rate` `product_vat_rate` DOUBLE DEFAULT 0;");

    mysqli_query($connection, "ALTER TABLE order_shipping CHANGE `shipping_address_1` `shipping_address` varchar(255);");
    mysqli_query($connection, "ALTER TABLE order_shipping CHANGE `billing_address_1` `billing_address` varchar(255);");
    mysqli_query($connection, "ALTER TABLE order_shipping DROP COLUMN `shipping_address_2`;");
    mysqli_query($connection, "ALTER TABLE order_shipping DROP COLUMN `billing_address_2`;");

    //add product settings
    $sql_product_settings="INSERT INTO `product_settings` (`id`, `marketplace_sku`, `marketplace_variations`, `marketplace_shipping`, `marketplace_product_location`, `classified_price`, `classified_price_required`, `classified_product_location`, `classified_external_link`, `physical_demo_url`, `physical_video_preview`, `physical_audio_preview`, `digital_demo_url`, `digital_video_preview`, `digital_audio_preview`, `digital_allowed_file_extensions`, `sitemap_frequency`, `sitemap_last_modification`, `sitemap_priority`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '\"zip\"', 'daily', 'server_response', 'automatically');";
    mysqli_query($connection, $sql_product_settings);
    mysqli_query($connection, "UPDATE general_settings SET site_color='#222222' WHERE id='1'");
    //add payment gateways
    $sql_gateways = "INSERT INTO `payment_gateways` (`id`, `name`, `name_key`, `public_key`, `secret_key`, `environment`, `locale`, `base_currency`, `status`, `logos`) VALUES
        (1, 'PayPal', 'paypal', '', '', 'production', '', 'all', 0, 'visa,mastercard,amex,discover,paypal'),
        (2, 'Stripe', 'stripe', '', '', 'production', 'auto', 'all', 0, 'visa,mastercard,amex,stripe'),
        (3, 'Paystack', 'paystack', '', '', 'production', '', 'all', 0, 'visa,mastercard,verve,paystack'),
        (4, 'Razorpay', 'razorpay', '', '', 'production', '', 'INR', 0, 'visa,mastercard,amex,maestro,diners,rupay,razorpay'),
        (5, 'Flutterwave', 'flutterwave', '', '', 'production', '', 'all', 0, 'visa,mastercard,amex,maestro,flutterwave'),
        (6, 'Iyzico', 'iyzico', '', '', 'production', '', 'TRY', 0, 'visa,mastercard,amex,troy,iyzico'),
        (7, 'Midtrans', 'midtrans', '', '', 'production', '', 'IDR', 0, 'visa,mastercard,amex,jcb,midtrans');";
    mysqli_query($connection, $sql_gateways);
    $payment_settings = mysqli_query($connection, "SELECT * FROM payment_settings WHERE id = 1");
    $row = mysqli_fetch_assoc($payment_settings);
    if (!empty($row)) {
        if (!empty($row['paypal_client_id']) && !empty($row['paypal_secret_key'])) {
            mysqli_query($connection, "UPDATE `payment_gateways` SET `public_key` = '" . $row['paypal_client_id'] . "', `secret_key` = '" . $row['paypal_secret_key'] . "', `status` = '1' WHERE name_key= 'paypal';");
        }
        if (!empty($row['stripe_publishable_key']) && !empty($row['stripe_secret_key'])) {
            mysqli_query($connection, "UPDATE `payment_gateways` SET `public_key` = '" . $row['stripe_publishable_key'] . "', `secret_key` = '" . $row['stripe_secret_key'] . "', `status` = '1' WHERE name_key= 'stripe';");
        }
        if (!empty($row['paystack_public_key']) && !empty($row['paystack_secret_key'])) {
            mysqli_query($connection, "UPDATE `payment_gateways` SET `public_key` = '" . $row['paystack_public_key'] . "', `secret_key` = '" . $row['paystack_secret_key'] . "', `status` = '1' WHERE name_key= 'paystack';");
        }
        if (!empty($row['razorpay_key_id']) && !empty($row['razorpay_key_secret'])) {
            mysqli_query($connection, "UPDATE `payment_gateways` SET `public_key` = '" . $row['razorpay_key_id'] . "', `secret_key` = '" . $row['razorpay_key_secret'] . "', `status` = '1' WHERE name_key= 'razorpay';");
        }
        if (!empty($row['iyzico_api_key']) && !empty($row['iyzico_secret_key'])) {
            mysqli_query($connection, "UPDATE `payment_gateways` SET `public_key` = '" . $row['iyzico_api_key'] . "', `secret_key` = '" . $row['iyzico_secret_key'] . "', `status` = '1' WHERE name_key= 'iyzico';");
        }
    }

    mysqli_query($connection, "ALTER TABLE payment_settings ADD COLUMN `currency_converter` TINYINT(1) DEFAULT 0;");
    mysqli_query($connection, "ALTER TABLE payment_settings ADD COLUMN `auto_update_exchange_rates` TINYINT(1) DEFAULT 1;");
    mysqli_query($connection, "ALTER TABLE payment_settings ADD COLUMN `currency_converter_api` varchar(100);");
    mysqli_query($connection, "ALTER TABLE payment_settings ADD COLUMN `currency_converter_api_key` varchar(255);");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `currency_format`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `currency_symbol_format`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `space_between_money_currency`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `paypal_enabled`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `paypal_mode`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `paypal_client_id`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `paypal_secret_key`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `stripe_enabled`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `stripe_publishable_key`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `stripe_secret_key`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `stripe_locale`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `paystack_enabled`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `paystack_secret_key`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `paystack_public_key`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `razorpay_enabled`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `razorpay_key_id`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `razorpay_key_secret`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `pagseguro_enabled`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `pagseguro_mode`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `pagseguro_email`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `pagseguro_token`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `iyzico_enabled`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `iyzico_mode`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `iyzico_api_key`;");
    mysqli_query($connection, "ALTER TABLE payment_settings DROP COLUMN `iyzico_secret_key`;");

    mysqli_query($connection, "ALTER TABLE products ADD COLUMN `shipping_class_id` int(11);");
    mysqli_query($connection, "ALTER TABLE products ADD COLUMN `shipping_delivery_time_id` int(11);");
    mysqli_query($connection, "ALTER TABLE products ADD COLUMN `multiple_sale` TINYINT(1) DEFAULT 1;");
    mysqli_query($connection, "ALTER TABLE products ADD COLUMN `is_sold` TINYINT(1) DEFAULT 0;");
    mysqli_query($connection, "ALTER TABLE products CHANGE `vat_rate` `vat_rate` DOUBLE DEFAULT 0;");

    mysqli_query($connection, "ALTER TABLE products DROP COLUMN `country_id`;");
    mysqli_query($connection, "ALTER TABLE products DROP COLUMN `state_id`;");
    mysqli_query($connection, "ALTER TABLE products DROP COLUMN `city_id`;");
    mysqli_query($connection, "ALTER TABLE products DROP COLUMN `address`;");
    mysqli_query($connection, "ALTER TABLE products DROP COLUMN `zip_code`;");
    mysqli_query($connection, "ALTER TABLE products DROP COLUMN `shipping_time`;");
    mysqli_query($connection, "ALTER TABLE products DROP COLUMN `shipping_cost_type`;");
    mysqli_query($connection, "ALTER TABLE products DROP COLUMN `shipping_cost`;");
    mysqli_query($connection, "ALTER TABLE products DROP COLUMN `shipping_cost_additional`;");

    //add new routes
    $sql_routes = "INSERT INTO `routes` (`route_key`, `route`) VALUES
    ('cover_image', 'cover-image'),
    ('sold_products', 'sold-products'),
    ('shipping_settings', 'shipping-settings'),
    ('add_shipping_zone', 'add-shipping-zone'),
    ('edit_shipping_zone', 'edit-shipping-zone');";
    mysqli_query($connection, $sql_routes);
    mysqli_query($connection, "DELETE FROM routes WHERE route_key = 'personal_information'");
    mysqli_query($connection, "ALTER TABLE settings ADD COLUMN `dashboard_font` smallint(6) DEFAULT 22");
    mysqli_query($connection, "ALTER TABLE settings ADD COLUMN `whatsapp_url` varchar(500);");
    mysqli_query($connection, "ALTER TABLE settings ADD COLUMN `telegram_url` varchar(500);");
    mysqli_query($connection, "ALTER TABLE users DROP COLUMN `shipping_first_name`;");
    mysqli_query($connection, "ALTER TABLE users DROP COLUMN `shipping_last_name`;");
    mysqli_query($connection, "ALTER TABLE users DROP COLUMN `shipping_email`;");
    mysqli_query($connection, "ALTER TABLE users DROP COLUMN `shipping_phone_number`;");
    mysqli_query($connection, "ALTER TABLE users DROP COLUMN `shipping_address_1`;");
    mysqli_query($connection, "ALTER TABLE users DROP COLUMN `shipping_address_2`;");
    mysqli_query($connection, "ALTER TABLE users DROP COLUMN `shipping_country_id`;");
    mysqli_query($connection, "ALTER TABLE users DROP COLUMN `shipping_state`;");
    mysqli_query($connection, "ALTER TABLE users DROP COLUMN `shipping_city`;");
    mysqli_query($connection, "ALTER TABLE users DROP COLUMN `shipping_zip_code`;");
    mysqli_query($connection, "ALTER TABLE users DROP COLUMN `send_email_when_item_sold`;");
    mysqli_query($connection, "ALTER TABLE users ADD COLUMN `cover_image` varchar(255);");
    mysqli_query($connection, "ALTER TABLE users ADD COLUMN `cover_image_type` varchar(30) DEFAULT 'full_width';");
    mysqli_query($connection, "ALTER TABLE users ADD COLUMN `personal_website_url` varchar(500);");
    mysqli_query($connection, "ALTER TABLE users ADD COLUMN `whatsapp_url` varchar(500);");
    mysqli_query($connection, "ALTER TABLE users ADD COLUMN `telegram_url` varchar(500);");
    mysqli_query($connection, "ALTER TABLE users ADD COLUMN `is_used_free_plan` TINYINT(1) DEFAULT 0;");
    mysqli_query($connection, "ALTER TABLE users_membership_plans ADD COLUMN `plan_id` int(11);");

    //delete translations
    mysqli_query($connection, "DELETE FROM language_translations WHERE label = 'add_shipping_tracking_number'");
    mysqli_query($connection, "DELETE FROM language_translations WHERE label = 'add_tracking_number'");
    mysqli_query($connection, "DELETE FROM language_translations WHERE label = 'license_keys_exp'");
    mysqli_query($connection, "DELETE FROM language_translations WHERE label = 'address_2'");
    mysqli_query($connection, "DELETE FROM language_translations WHERE label = 'currency_hexcode'");
    mysqli_query($connection, "DELETE FROM language_translations WHERE label = 'edit_shipping_option'");
    mysqli_query($connection, "DELETE FROM language_translations WHERE label = 'form_settings'");
    mysqli_query($connection, "DELETE FROM language_translations WHERE label = 'no_shipping'");
    mysqli_query($connection, "DELETE FROM language_translations WHERE label = 'only_for_ordinary_listing'");
    mysqli_query($connection, "DELETE FROM language_translations WHERE label = 'option_label'");
    mysqli_query($connection, "DELETE FROM language_translations WHERE label = 'paypal'");
    mysqli_query($connection, "DELETE FROM language_translations WHERE label = 'personal_information'");
    mysqli_query($connection, "DELETE FROM language_translations WHERE label = 'send_email_item_sold'");
    mysqli_query($connection, "DELETE FROM language_translations WHERE label = 'shipping_cost_per_additional_product'");
    mysqli_query($connection, "DELETE FROM language_translations WHERE label = 'shipping_options'");
    mysqli_query($connection, "DELETE FROM language_translations WHERE label = 'shipping_cost_per_additional_product_exp'");
    mysqli_query($connection, "DELETE FROM language_translations WHERE label = 'tracking_number'");

    //add translations
    $translations = array("exchange_rate" => "Exchange Rate", "currency_converter" => "Currency Converter", "automatically_update_exchange_rates" => "Automatically Update Exchange Rates", "currency_converter_api" => "Currency Converter API", "access_key" => "Access Key", "base_currency" => "Base Currency", "all_active_currencies" => "All active currencies", "server_key" => "Server Key", "abuse_report_exp" => "Briefly describe the issue you are facing", "abuse_report_msg" => "Your report has reached us. Thank you!", "content_type" => "Content Type", "sent_by" => "Sent By", "view_content" => "View Content", "cover_image" => "Cover Image", "deleted" => "Deleted", "product_or_seller_profile_url" => "Product or seller profile URL", "search_by_location" => "Search by Location", "most_viewed_products" => "Most Viewed Products", "vendor_bulk_product_upload" => "Vendor Bulk Product Upload", "product_cache_system" => "Product Cache System", "static_content_cache_system" => "Static Content Cache System", "warning_static_content_cache_system" => "Static content cache system is for records (categories, custom fields, language translations etc.) that do not change much on the site. The cache files will be refreshed automatically when there is a change in these records. It is recommended to activate this system always for a faster site.", "warning_plan_used" => "This plan has been used before", "warning_cannot_choose_plan" => "You cannot choose this plan due to the number of products you have added", "warning_membership_admin_role" => "Admin role does not require a membership plan.", "assign_membership_plan" => "Assign Membership Plan", "personal_website_url" => "Personal Website URL", "contact_seller" => "Contact Seller", "already_have_active_request" => "You already have an active request for this product!", "order_not_yet_shipped" => "The order has not yet been shipped.", "warning_add_order_tracking_code" => "You can add the order tracking code and link while changing the order status.", "tracking_code" => "Tracking Code", "tracking_url" => "Tracking URL", "order_has_been_shipped" => "The order has been shipped!", "multiple_sale" => "Multiple Sale", "multiple_sale_option_1" => "Yes, I want to sell this product to more than one customer", "multiple_sale_option_2" => "No, I want to sell this product to a single customer", "sold_products" => "Sold Products", "show_sold_products_on_site" => "Show Sold Products on the Site", "report_comment" => "Report Comment", "report_review" => "Report Review", "report" => "Report", "abuse_reports" => "Abuse Reports", "reported_content" => "Reported Content", "report_this_product" => "Report this product", "report_this_seller" => "Report this seller", "filter_key" => "Filter Key", "filter_key_exp" => "Don't add special characters", "product_settings" => "Product Settings", "whatsapp_url" => "WhatsApp URL", "telegram_url" => "Telegram URL", "dashboard_font" => "Dashboard Font", "classified_ads" => "Classified Ads", "show_subcategory_products" => "Show subcategory products", "msg_error_sku" => "This SKU is used for your another product!", "msg_dont_have_downloadable_files" => "You don't have any downloadable files.", "paid" => "Paid", "shipping_settings" => "Shipping Settings", "shipping_zones" => "Shipping Zones ", "zone_name" => "Zone Name", "regions" => "Regions", "shipping_methods" => "Shipping Methods", "add_shipping_zone" => "Add Shipping Zone", "select_region" => "Select Region", "continent" => "Continent", "no_results_found" => "No Results Found", "all_countries" => "All Countries", "all_states" => "All States", "flat_rate" => "Flat Rate", "local_pickup" => "Local Pickup", "free_shipping" => "Free Shipping", "add_shipping_method" => "Add Shipping Method", "method_name" => "Method Name", "cost" => "Cost", "minimum_order_amount" => "Minimum order amount", "charge_shipping_for_each_product" => "Charge shipping for each product in the cart", "charge_shipping_for_each_different_product" => "Charge shipping for each different product in the cart", "cost_calculation_type" => "Cost Calculation Type", "edit_shipping_zone" => "Edit Shipping Zone", "add_new_address" => "Add New Address", "address_title" => "Address Title", "shipping_delivery_times" => "Shipping Delivery Times", "shipping_delivery_times_exp" => "You can add shipping delivery times from here (E.g: Ready to ship in 1 Business Day)", "add_delivery_time" => "Add Delivery Time", "edit_delivery_time" => "Edit Delivery Time", "delivery_time" => "Delivery Time", "edit_address" => "Edit Address", "shipping_method" => "Shipping Method", "not_added_shipping_address" => "You have not added a shipping address yet.", "no_delivery_is_made_to_address" => "No delivery is made to the address you have chosen.", "products_sent_different_stores" => "Your products will be sent by different stores.", "fixed_shipping_cost_for_cart_total" => "Fixed Shipping Cost for Cart Total", "seller_does_not_ship_to_address" => "This seller does not ship to the address you have chosen. You can continue by removing the products of this seller from your cart.", "add_product_sell_license_keys" => "Add a Product to Sell License Keys", "add_product_sell_license_keys_exp" => "Add a product to sell only license keys", "selling_license_keys" => "Selling License Keys", "license_keys_system_exp" => "Add all your license keys from here. The system will automatically give a license key to each buyer.", "download_license_key" => "Download License Key", "shop_location" => "Shop Location", "shipping_classes" => "Shipping Classes", "add_shipping_class" => "Add Shipping Class", "edit_shipping_class" => "Edit Shipping Class", "shipping_classes_exp" => "Shipping classes allow you to define different shipping costs for your products. If you have products with high shipping costs (large in weight and size), you can add a class from here and set a different price for each shipping method for this class. You can choose shipping classes during adding your products.", "shipping_class_costs" => "Shipping Class Costs", "shipping_class" => "Shipping Class", "select_your_location" => "Select Your Location", "product_does_not_ship_location" => "This product does not ship to this location. ", "bank_accounts_exp" => "You can make your payment to one of these bank accounts.", "update_exchange_rates" => "Update Exchange Rates");
    add_lang_translations($connection, $translations);

    //update countries
    $array_countries = array("Afghanistan" => "AS", "Albania" => "EU", "Algeria" => "AF", "American Samoa" => "OC", "Andorra" => "EU", "Angola" => "AF", "Anguilla" => "NA", "Antarctica" => "AN", "Antigua and Barbuda" => "NA", "Argentina" => "SA", "Armenia" => "AS", "Aruba" => "NA", "Australia" => "OC", "Austria" => "EU", "Azerbaijan" => "AS", "Bahamas" => "NA", "Bahrain" => "AS", "Bangladesh" => "AS", "Barbados" => "NA", "Belarus" => "EU", "Belgium" => "EU", "Belize" => "NA", "Benin" => "AF", "Bermuda" => "NA", "Bhutan" => "AS", "Bolivia" => "SA", "Bosnia and Herzegovina" => "EU", "Botswana" => "AF", "Bouvet Island" => "AN", "Brazil" => "SA", "British Indian Ocean Territory" => "AS", "Brunei Darussalam" => "AS", "Bulgaria" => "EU", "Burkina Faso" => "AF", "Burundi" => "AF", "Cambodia" => "AS", "Cameroon" => "AF", "Canada" => "NA", "Cape Verde" => "AF", "Cayman Islands" => "NA", "Central African Republic" => "AF", "Chad" => "AF", "Chile" => "SA", "China" => "AS", "Christmas Island" => "AS", "Cocos (Keeling) Islands" => "AS", "Colombia" => "SA", "Comoros" => "AF", "Congo" => "AF", "Cook Islands" => "OC", "Costa Rica" => "NA", "Croatia (Hrvatska)" => "EU", "Cuba" => "NA", "Cyprus" => "AS", "Czech Republic" => "EU", "Denmark" => "EU", "Djibouti" => "AF", "Dominica" => "NA", "Dominican Republic" => "NA", "East Timor" => "AS", "Ecuador" => "SA", "Egypt" => "AF", "El Salvador" => "NA", "Equatorial Guinea" => "AF", "Eritrea" => "AF", "Estonia" => "EU", "Ethiopia" => "AF", "Falkland Islands (Malvinas)" => "SA", "Faroe Islands" => "EU", "Fiji" => "OC", "Finland" => "EU", "France" => "EU", "France, Metropolitan" => "EU", "French Guiana" => "SA", "French Polynesia" => "OC", "French Southern Territories" => "AN", "Gabon" => "AF", "Gambia" => "AF", "Georgia" => "AS", "Germany" => "EU", "Ghana" => "AF", "Gibraltar" => "EU", "Greece" => "EU", "Greenland" => "NA", "Grenada" => "NA", "Guadeloupe" => "NA", "Guam" => "OC", "Guatemala" => "NA", "Guernsey" => "EU", "Guinea" => "AF", "Guinea-Bissau" => "AF", "Guyana" => "SA", "Haiti" => "NA", "Heard and McDonald Islands" => "AN", "Honduras" => "NA", "Hong Kong" => "AS", "Hungary" => "EU", "Iceland" => "EU", "India" => "AS", "Indonesia" => "AS", "Iran" => "AS", "Iraq" => "AS", "Ireland" => "EU", "Isle of Man" => "EU", "Israel" => "AS", "Italy" => "EU", "Ivory Coast" => "AF", "Jamaica" => "NA", "Japan" => "AS", "Jersey" => "EU", "Jordan" => "AS", "Kazakhstan" => "AS", "Kenya" => "AF", "Kiribati" => "OC", "Kosovo" => "EU", "Kuwait" => "AS", "Kyrgyzstan" => "AS", "Lao" => "AS", "Latvia" => "EU", "Lebanon" => "AS", "Lesotho" => "AF", "Liberia" => "AF", "Libyan Arab Jamahiriya" => "AF", "Liechtenstein" => "EU", "Lithuania" => "EU", "Luxembourg" => "EU", "Macau" => "AS", "Macedonia" => "EU", "Madagascar" => "AF", "Malawi" => "AF", "Malaysia" => "AS", "Maldives" => "AS", "Mali" => "AF", "Malta" => "EU", "Marshall Islands" => "OC", "Martinique" => "NA", "Mauritania" => "AF", "Mauritius" => "AF", "Mayotte" => "AF", "Mexico" => "NA", "Micronesia, Federated States of" => "OC", "Moldova, Republic of" => "EU", "Monaco" => "EU", "Mongolia" => "AS", "Montenegro" => "EU", "Montserrat" => "NA", "Morocco" => "AF", "Mozambique" => "AF", "Myanmar" => "AS", "Namibia" => "AF", "Nauru" => "OC", "Nepal" => "AS", "Netherlands" => "EU", "Netherlands Antilles" => "NA", "New Caledonia" => "OC", "New Zealand" => "OC", "Nicaragua" => "NA", "Niger" => "AF", "Nigeria" => "AF", "Niue" => "OC", "Norfolk Island" => "OC", "North Korea" => "AS", "Northern Mariana Islands" => "OC", "Norway" => "EU", "Oman" => "AS", "Pakistan" => "AS", "Palau" => "OC", "Palestine" => "AS", "Panama" => "NA", "Papua New Guinea" => "OC", "Paraguay" => "SA", "Peru" => "SA", "Philippines" => "AS", "Pitcairn" => "OC", "Poland" => "EU", "Portugal" => "EU", "Puerto Rico" => "NA", "Qatar" => "AS", "Reunion" => "AF", "Romania" => "EU", "Russian Federation" => "EU", "Rwanda" => "AF", "Saint Kitts and Nevis" => "NA", "Saint Lucia" => "NA", "Saint Vincent and the Grenadines" => "NA", "Samoa" => "OC", "San Marino" => "EU", "Sao Tome and Principe" => "AF", "Saudi Arabia" => "AS", "Senegal" => "AF", "Serbia" => "EU", "Seychelles" => "AF", "Sierra Leone" => "AF", "Singapore" => "AS", "Slovakia" => "EU", "Slovenia" => "EU", "Solomon Islands" => "OC", "Somalia" => "AF", "South Africa" => "AF", "South Georgia South Sandwich Islands" => "AN", "South Korea" => "AS", "Spain" => "EU", "Sri Lanka" => "AS", "St. Helena" => "AF", "St. Pierre and Miquelon" => "NA", "Sudan" => "AF", "Suriname" => "SA", "Svalbard and Jan Mayen Islands" => "EU", "Swaziland" => "AF", "Sweden" => "EU", "Switzerland" => "EU", "Syrian Arab Republic" => "AS", "Taiwan" => "AS", "Tajikistan" => "AS", "Tanzania" => "AF", "Thailand" => "AS", "Togo" => "AF", "Tokelau" => "OC", "Tonga" => "OC", "Trinidad and Tobago" => "NA", "Tunisia" => "AF", "Turkey" => "AS", "Turkmenistan" => "AS", "Turks and Caicos Islands" => "NA", "Tuvalu" => "OC", "Uganda" => "AF", "Ukraine" => "EU", "United Arab Emirates" => "AS", "United Kingdom" => "EU", "United States" => "NA", "United States minor outlying islands" => "OC", "Uruguay" => "SA", "Uzbekistan" => "AS", "Vanuatu" => "OC", "Vatican City State" => "EU", "Venezuela" => "SA", "Vietnam" => "AS", "Virgin Islands (British)" => "NA", "Virgin Islands (U.S.)" => "NA", "Wallis and Futuna Islands" => "OC", "Western Sahara" => "AF", "Yemen" => "AS", "Zaire" => "AF", "Zambia" => "AF", "Zimbabwe" => "AF");
    $countries = mysqli_query($connection, "SELECT * FROM location_countries");
    if (!empty($countries->num_rows)) {
        while ($country = mysqli_fetch_array($countries)) {
            if (!empty($country['name']) && !empty($country['id'])) {
                $continent_code = "";
                if (!empty($array_countries[$country['name']])) {
                    $continent_code = $array_countries[$country['name']];
                }
                mysqli_query($connection, "UPDATE `location_countries` SET `continent_code` = '" . $continent_code . "' WHERE id= '" . $country['id'] . "';");
                //add states
                $states = mysqli_query($connection, "SELECT id FROM location_states WHERE country_id = " . $country['id']);
                if (empty($states->num_rows)) {
                    mysqli_query($connection, "INSERT INTO `location_states` (`name`, `country_id`) VALUES ('" . $country['name'] . "', '" . $country['id'] . "');");
                }
            }
        }
    }

    //update categories parent tree
    $categories = mysqli_query($connection, "SELECT * FROM categories WHERE parent_id = 0");
    if (!empty($categories->num_rows)) {
        while ($category = mysqli_fetch_array($categories)) {
            mysqli_query($connection, "UPDATE `categories` SET `parent_tree` = '' WHERE id= '" . $category['id'] . "';");
            update_subcategories_parent_tree($connection, $category);
        }
    }

    //add indexes
    mysqli_query($connection, "ALTER TABLE categories ADD INDEX idx_category_order (category_order);");
    mysqli_query($connection, "ALTER TABLE categories ADD INDEX idx_featured_order (featured_order);");
    mysqli_query($connection, "ALTER TABLE products ADD INDEX idx_price (price);");
    mysqli_query($connection, "ALTER TABLE products ADD INDEX idx_discount_rate (discount_rate);");
    mysqli_query($connection, "ALTER TABLE products ADD INDEX idx_is_special_offer (is_special_offer);");
    mysqli_query($connection, "ALTER TABLE products ADD INDEX idx_is_sold (is_sold);");
    mysqli_query($connection, "ALTER TABLE product_license_keys ADD INDEX idx_is_used (is_used);");
    mysqli_query($connection, "ALTER TABLE quote_requests DROP COLUMN `shipping_cost`;");
    mysqli_query($connection, "ALTER TABLE shipping_addresses ADD INDEX idx_user_id (user_id);");
    mysqli_query($connection, "ALTER TABLE shipping_classes ADD INDEX idx_user_id (user_id);");
    mysqli_query($connection, "ALTER TABLE shipping_classes ADD INDEX idx_status (status);");
    mysqli_query($connection, "ALTER TABLE shipping_delivery_times ADD INDEX idx_user_id (user_id);");
    mysqli_query($connection, "ALTER TABLE shipping_zones ADD INDEX idx_user_id (user_id);");
    mysqli_query($connection, "ALTER TABLE shipping_zone_locations ADD INDEX idx_zone_id (zone_id);");
    mysqli_query($connection, "ALTER TABLE shipping_zone_locations ADD INDEX idx_user_id (user_id);");
    mysqli_query($connection, "ALTER TABLE shipping_zone_methods ADD INDEX idx_zone_id (zone_id);");
    mysqli_query($connection, "ALTER TABLE shipping_zone_methods ADD INDEX idx_user_id (user_id);");
    mysqli_query($connection, "ALTER TABLE users ADD INDEX idx_banned (banned);");
    mysqli_query($connection, "UPDATE general_settings SET version='1.8' WHERE id='1'");
}

function update_18_to_19($connection)
{

    $table_knowledge_base = "CREATE TABLE `knowledge_base` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `lang_id` tinyint(4) DEFAULT NULL,
        `title` varchar(500) DEFAULT NULL,
        `slug` varchar(500) DEFAULT NULL,
        `content` longtext DEFAULT NULL,
        `category_id` varchar(50) DEFAULT NULL,
        `content_order` smallint(6) DEFAULT 1,
        `created_at` timestamp NULL DEFAULT current_timestamp()
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $table_knowledge_base_categories = "CREATE TABLE `knowledge_base_categories` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `lang_id` int(11) DEFAULT NULL,
      `name` varchar(255) DEFAULT NULL,
      `slug` varchar(255) DEFAULT NULL,
      `category_order` smallint(6) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $table_refund_requests = "CREATE TABLE `refund_requests` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `buyer_id` int(11) DEFAULT NULL,
      `seller_id` int(11) DEFAULT NULL,
      `order_id` int(11) DEFAULT NULL,
      `order_number` bigint(20) DEFAULT NULL,
      `order_product_id` int(11) DEFAULT NULL,
      `status` tinyint(1) DEFAULT 0,
      `is_completed` tinyint(1) DEFAULT 0,
      `updated_at` timestamp NULL DEFAULT NULL,
      `created_at` timestamp NULL DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $table_refund_requests_messages = "CREATE TABLE `refund_requests_messages` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `request_id` int(11) DEFAULT NULL,
      `user_id` int(11) DEFAULT NULL,
      `is_buyer` tinyint(1) NOT NULL DEFAULT 1,
      `message` text DEFAULT NULL,
      `created_at` timestamp NULL DEFAULT current_timestamp()
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $table_support_tickets = "CREATE TABLE `support_tickets` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `user_id` int(11) DEFAULT NULL,
      `name` varchar(255) DEFAULT NULL,
      `email` varchar(255) DEFAULT NULL,
      `subject` varchar(500) DEFAULT NULL,
      `is_guest` tinyint(1) DEFAULT 0,
      `status` smallint(6) DEFAULT 1,
      `updated_at` timestamp NULL DEFAULT NULL,
      `created_at` timestamp NULL DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $table_support_subtickets = "CREATE TABLE `support_subtickets` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `ticket_id` int(11) DEFAULT NULL,
      `user_id` int(11) DEFAULT NULL,
      `message` text DEFAULT NULL,
      `attachments` text DEFAULT NULL,
      `is_support_reply` tinyint(1) DEFAULT 0,
      `storage` varchar(20) DEFAULT 'local',
      `created_at` timestamp NULL DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


    mysqli_query($connection, $table_knowledge_base);
    mysqli_query($connection, $table_knowledge_base_categories);
    mysqli_query($connection, $table_refund_requests);
    mysqli_query($connection, $table_refund_requests_messages);
    mysqli_query($connection, $table_support_tickets);
    mysqli_query($connection, $table_support_subtickets);

    mysqli_query($connection, "ALTER TABLE categories ADD COLUMN `show_on_main_menu` TINYINT(1) DEFAULT 1;");
    mysqli_query($connection, "ALTER TABLE categories CHANGE `show_image_on_navigation` `show_image_on_main_menu` TINYINT(1) DEFAULT 0;");
    mysqli_query($connection, "ALTER TABLE categories ADD INDEX idx_show_on_main_menu (show_on_main_menu);");
    mysqli_query($connection, "ALTER TABLE earnings ADD COLUMN `is_refunded` TINYINT(1) DEFAULT 0;");

    mysqli_query($connection, "ALTER TABLE general_settings ADD COLUMN `mail_encryption` VARCHAR(100) DEFAULT 'tls';");
    mysqli_query($connection, "ALTER TABLE general_settings ADD COLUMN `mail_reply_to` VARCHAR(255) DEFAULT 'noreply@domain.com';");
    mysqli_query($connection, "ALTER TABLE general_settings ADD COLUMN `newsletter_status` TINYINT(1) DEFAULT 1;");
    mysqli_query($connection, "ALTER TABLE general_settings ADD COLUMN `newsletter_popup` TINYINT(1) DEFAULT 1;");
    mysqli_query($connection, "ALTER TABLE general_settings ADD COLUMN `request_documents_vendors` TINYINT(1) DEFAULT 0;");
    mysqli_query($connection, "ALTER TABLE general_settings ADD COLUMN `explanation_documents_vendors` VARCHAR(500);");
    mysqli_query($connection, "ALTER TABLE general_settings CHANGE `version` `version` VARCHAR(30) DEFAULT '1.9';");
    mysqli_query($connection, "UPDATE general_settings SET version='1.9' WHERE id='1';");

    mysqli_query($connection, "ALTER TABLE payment_settings ADD COLUMN `payout_bitcoin_enabled` TINYINT(1) DEFAULT 0;");
    mysqli_query($connection, "ALTER TABLE payment_settings ADD COLUMN `min_payout_bitcoin` bigint(20) DEFAULT 5000;");

    mysqli_query($connection, "ALTER TABLE products ADD COLUMN `is_rejected` TINYINT(1) DEFAULT 0;");
    mysqli_query($connection, "ALTER TABLE products ADD COLUMN `reject_reason` VARCHAR(1000);");

    mysqli_query($connection, "ALTER TABLE users ADD COLUMN `vendor_documents` VARCHAR(1000);");
    mysqli_query($connection, "ALTER TABLE users_payout_accounts ADD COLUMN `payout_bitcoin_address` VARCHAR(500);");

    //add new routes
    $sql_routes = "INSERT INTO `routes` (`route_key`, `route`) VALUES
    ('help_center', 'help-center'),
    ('tickets', 'tickets'),
    ('submit_request', 'submit-request'),
    ('ticket', 'ticket'),
    ('shops', 'shops'),
    ('cancelled_sales', 'cancelled-sales'),
    ('refund_requests', 'refund-requests')";
    mysqli_query($connection, $sql_routes);

    //add pages
    $languages = mysqli_query($connection, "SELECT * FROM languages ORDER BY language_order;");
    if (!empty($languages->num_rows)) {
        while ($language = mysqli_fetch_array($languages)) {
            $sql = "INSERT INTO `pages` (`lang_id`, `title`, `slug`, `description`, `keywords`, `page_content`, `page_order`, `visibility`, `title_active`, `location`, `is_custom`, `page_default_name`, `created_at`) VALUES
            (" . $language['id'] . ", 'Shops', 'shops', 'Shops Page', 'Shops, Page', NULL, 1, 1, 1, 'quick_links', 0, 'shops', '2020-11-21 10:40:30');";
            mysqli_query($connection, $sql);
        }
    }

    $p["form_validation_required"] = "The {field} field is required.";
    $p["form_validation_min_length"] = "The {field} field must be at least {param} characters in length.";
    $p["form_validation_max_length"] = "The {field} field cannot exceed {param} characters in length.";
    $p["form_validation_matches"] = "The {field} field does not match the {param} field.";
    $p["form_validation_is_unique"] = "The {field} field must contain a unique value.";
    $p["show_on_main_menu"] = "Show on Main Menu";
    $p["show_image_on_main_menu"] = "Show Image on Main Menu";
    $p["main_menu"] = "Main Menu";
    $p["all_categories"] = "All Categories";
    $p["added_to_cart"] = "Added to Cart";
    $p["bitcoin"] = "Bitcoin (BTC)";
    $p["btc_address"] = "BTC Address";
    $p["msg_payout_bitcoin_address_error"] = "You must enter your BTC address to make this payment request";
    $p["help_center"] = "Help Center";
    $p["knowledge_base"] = "Knowledge Base";
    $p["add_content"] = "Add Content";
    $p["edit_content"] = "Edit Content";
    $p["contents"] = "Contents";
    $p["num_articles"] = "{field} Articles";
    $p["location_explanation"] = "{field} allows you to shop from anywhere in the world.";
    $p["all_help_topics"] = "All Help Topics";
    $p["contact_support"] = "Contact Support";
    $p["contact_support_exp"] = "If you didn't find what you were looking for, you can submit a support request here.";
    $p["need_more_help"] = "Need more help?";
    $p["open"] = "Open";
    $p["responded"] = "Responded";
    $p["submit_a_request"] = "Submit a Request";
    $p["last_update"] = "Last Update";
    $p["ticket"] = "Ticket";
    $p["close_ticket"] = "Close Ticket";
    $p["still_have_questions"] = "Still have questions?";
    $p["still_have_questions_exp"] = "If you still have a question, you can submit a support request here.";
    $p["support_tickets"] = "Support Tickets";
    $p["attachments"] = "Attachments";
    $p["related_help_topics"] = "Related Help Topics";
    $p["confirm_action"] = "Are you sure you want to perform this action?";
    $p["how_can_we_help"] = "How can we help?";
    $p["search_results"] = "Search Results";
    $p["number_of_results"] = "Number of Results";
    $p["reason"] = "Reason";
    $p["reject"] = "Reject";
    $p["rejected"] = "Rejected";
    $p["show_reason"] = "Show Reason";
    $p["refund"] = "Refund";
    $p["refund_requests"] = "Refund Requests";
    $p["cancel_order"] = "Cancel Order";
    $p["cancelled_sales"] = "Cancelled Sales";
    $p["cod_cancel_exp"] = "You can cancel your order within 24 hours after the order date.";
    $p["submit_refund_request"] = "Submit a Refund Request";
    $p["refund_reason_explain"] = "Why do you want a refund? Explain in detail.";
    $p["approve_refund"] = "Approve Refund";
    $p["refund_declined_exp"] = "Your refund request has been declined by the seller. If you want to raise a dispute, you can contact the site management.";
    $p["refund_approved_exp"] = "Your refund request has been approved by the seller. The total amount for this product will be refunded to you.";
    $p["edit_order"] = "Edit Order";
    $p["encryption"] = "Encryption";
    $p["reply_to"] = "Reply to";
    $p["newsletter_popup"] = "Newsletter Popup";
    $p["newsletter_send_many_exp"] = "Some servers do not allow mass mailing. Therefore, instead of sending your mails to all subscribers at once, you can send them part by part (Example: 50 subscribers at once). If your mail server stops sending mail, the sending process will also stop.";
    $p["join_newsletter"] = "Join Our Newsletter";
    $p["newsletter_desc"] = "Join our subscribers list to get the latest news, updates and special offers directly in your inbox";
    $p["no_thanks"] = "No, thanks";
    $p["msg_invalid_email"] = "Invalid email address!";
    $p["paypal"] = "PayPal";
    $p["shop"] = "Shop";
    $p["request_documents_vendors"] = "Request Documents from Vendors to Open a Store";
    $p["input_explanation"] = "Input Explanation";
    $p["required_files"] = "Required Files";
    $p["refund_admin_complete_exp"] = "To complete a refund request, you must return the buyer money. If you click the Approve Refund button, the system will change the order status to Refund Approved and deduct the order amount from the seller balance.";
    $p["refund_approved"] = "Refund Approved";
    $p["not_added_vendor_balance"] = "Not Added to Vendor Balance";
    $p["vendor_no_shipping_option_warning"] = "If you want to sell a physical product, you must add your shipping options before adding the product. Please go to this section and add your shipping options:";
    $p["refund_request"] = "Refund Request";
    $p["msg_refund_request_email"] = "You have received a refund request. Please click the button below to see the details.";
    $p["msg_refund_request_update_email"] = "There is an update for your refund request. Please click the button below to see the details.";
    add_lang_translations($connection, $p);
}

function update_19_to_20($connection)
{

    $table_coupons = "CREATE TABLE `coupons` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `seller_id` int(11) DEFAULT NULL,
        `coupon_code` varchar(50) DEFAULT NULL,
        `discount_rate` smallint(6) DEFAULT NULL,
        `coupon_count` int(11) DEFAULT NULL,
        `minimum_order_amount` bigint(20) DEFAULT NULL,
        `currency` varchar(20) DEFAULT NULL,
        `usage_type` varchar(20) DEFAULT 'single',
        `category_ids` text DEFAULT NULL,
        `expiry_date` timestamp NULL DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $table_coupons_used = "CREATE TABLE `coupons_used` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `order_id` int(11) DEFAULT NULL,
        `user_id` int(11) DEFAULT NULL,
        `coupon_code` varchar(255) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp()
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    $table_coupon_products = "CREATE TABLE `coupon_products` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `coupon_id` int(11) NOT NULL,
        `product_id` int(11) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


    $table_roles_permissions = "CREATE TABLE `roles_permissions` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `role_name` text DEFAULT NULL,
        `permissions` text DEFAULT NULL,
        `is_super_admin` tinyint(1) DEFAULT 0,
        `is_default` tinyint(1) DEFAULT 0,
        `is_admin` tinyint(1) DEFAULT 0,
        `is_vendor` tinyint(1) DEFAULT 0,
        `is_member` tinyint(1) DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    mysqli_query($connection, $table_coupons);
    mysqli_query($connection, $table_coupons_used);
    mysqli_query($connection, $table_coupon_products);
    mysqli_query($connection, $table_roles_permissions);
    sleep(1);

    mysqli_query($connection, "ALTER TABLE orders ADD COLUMN `coupon_code` VARCHAR(255);");
    mysqli_query($connection, "ALTER TABLE orders ADD COLUMN `coupon_discount` bigint(20);");
    mysqli_query($connection, "ALTER TABLE orders ADD COLUMN `coupon_seller_id` INT;");
    mysqli_query($connection, "ALTER TABLE users ADD COLUMN `role_id` smallint(6) DEFAULT 3;");
    mysqli_query($connection, "ALTER TABLE users ADD COLUMN `has_active_shop` TINYINT(1) DEFAULT 0;");

    $languages = mysqli_query($connection, "SELECT * FROM languages;");
    $name_array_admin = array();
    while ($language = mysqli_fetch_array($languages)) {
        $item = array(
            'lang_id' => $language['id'],
            'name' => "Super Admin"
        );
        array_push($name_array_admin, $item);
    }
    $name_array_admin = serialize($name_array_admin);

    $languages = mysqli_query($connection, "SELECT * FROM languages;");
    $name_array_vendor = array();
    while ($language = mysqli_fetch_array($languages)) {
        $item = array(
            'lang_id' => $language['id'],
            'name' => "Vendor"
        );
        array_push($name_array_vendor, $item);
    }
    $name_array_vendor = serialize($name_array_vendor);

    $languages = mysqli_query($connection, "SELECT * FROM languages;");
    $name_array_member = array();
    while ($language = mysqli_fetch_array($languages)) {
        $item = array(
            'lang_id' => $language['id'],
            'name' => "Member"
        );
        array_push($name_array_member, $item);
    }
    $name_array_member = serialize($name_array_member);

    $sql_role = "INSERT INTO `roles_permissions` (`id`, `role_name`, `permissions`, `is_super_admin`, `is_default`, `is_admin`, `is_vendor`, `is_member`) VALUES
                (1, '" . $name_array_admin . "', 'all', 1, 1, 1, 0, 0),
                (2, '" . $name_array_vendor . "', '2', 0, 1, 0, 1, 0),
                (3, '" . $name_array_member . "', '', 0, 1, 0, 0, 1);";
    mysqli_query($connection, $sql_role);

    $users = mysqli_query($connection, "SELECT * FROM users;");
    if (!empty($users->num_rows)) {
        while ($user = mysqli_fetch_array($users)) {
            $has_active_shop = 0;
            $role_id = 3;
            if ($user['role'] == "admin") {
                $role_id = 1;
                $has_active_shop = 1;
            } elseif ($user['role'] == "vendor") {
                $role_id = 2;
                $has_active_shop = 1;
            }
            $sql = "UPDATE `users` SET `role_id` = " . $role_id . ",`has_active_shop`=  " . $has_active_shop . "  WHERE id = " . $user['id'];
            mysqli_query($connection, $sql);
        }
    }
    mysqli_query($connection, "ALTER TABLE users DROP COLUMN `role`;");

    mysqli_query($connection, "INSERT INTO `routes` (`route_key`, `route`) VALUES ('coupons', 'coupons');");
    mysqli_query($connection, "INSERT INTO `routes` (`route_key`, `route`) VALUES ('add_coupon', 'add-coupon');");
    mysqli_query($connection, "INSERT INTO `routes` (`route_key`, `route`) VALUES ('edit_coupon', 'edit-coupon');");

    $p["coupons"] = "Coupons";
    $p["add_coupon"] = "Add Coupon";
    $p["edit_coupon"] = "Edit Coupon";
    $p["coupon_code"] = "Coupon Code";
    $p["number_of_coupons"] = "Number of Coupons";
    $p["number_of_coupons_exp"] = "How many times a coupon can be used by all customers before being invalid";
    $p["coupon_minimum_cart_total_exp"] = "Minimum cart total needed to use the coupon";
    $p["coupon_usage_type"] = "Coupon Usage Type";
    $p["coupon_usage_type_1"] = "Each user can use it for only one order";
    $p["coupon_usage_type_2"] = "Each user can use it for multiple orders (Guests can use)";
    $p["expiry_date"] = "Expiry Date";
    $p["msg_coupon_code_added_before"] = "This coupon code has already been added before. Please add another coupon code.";
    $p["exp_special_characters"] = "Do not use special characters";
    $p["expired"] = "Expired";
    $p["roles_permissions"] = "Roles & Permissions";
    $p["add_role"] = "Add Role";
    $p["roles"] = "Roles";
    $p["role_name"] = "Role Name";
    $p["permissions"] = "Permissions";
    $p["edit_role"] = "Edit Role";
    $p["all_permissions"] = "All Permissions";
    $p["change_user_role"] = "Change User Role";
    $p["role"] = "Role";
    $p["add_user"] = "Add User";
    $p["close_seller_shop"] = "Close Seller's Shop";
    $p["apply"] = "Apply";
    $p["msg_invalid_coupon"] = "This coupon code is invalid or has expired!";
    $p["discount_coupon"] = "Discount Coupon";
    $p["coupon"] = "Coupon";
    $p["discount"] = "Discount";
    $p["msg_coupon_limit"] = "Coupon usage limit has been reached!";
    $p["msg_coupon_cart_total"] = "Your cart total is not enough to use this coupon. Minimum cart total:";
    $p["msg_coupon_auth"] = "This coupon is for registered members only!";
    $p["msg_coupon_used"] = "This coupon code has been used before!";

    add_lang_translations($connection, $p);
}

//add language translations
function add_lang_translations($connection, $translations)
{
    $languages = mysqli_query($connection, "SELECT * FROM languages ORDER BY language_order;");
    if (!empty($languages->num_rows) && !empty($translations)) {
        while ($language = mysqli_fetch_array($languages)) {
            foreach ($translations as $key => $value) {
                $trans = mysqli_query($connection, "SELECT * FROM language_translations WHERE label ='" . $key . "' AND lang_id = " . $language['id']);
                if (empty($trans->num_rows)) {
                    mysqli_query($connection, "INSERT INTO `language_translations` (`lang_id`, `label`, `translation`) VALUES (" . $language['id'] . ", '" . $key . "', '" . $value . "');");
                }
            }
        }
    }
} ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modesy - Update Wizard</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700" rel="stylesheet">
    <!-- Font-awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #444 !important;
            font-size: 14px;

            background: #007991; /* fallback for old browsers */
            background: -webkit-linear-gradient(to left, #007991, #6fe7c2); /* Chrome 10-25, Safari 5.1-6 */
            background: linear-gradient(to left, #007991, #6fe7c2); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

        }

        .logo-cnt {
            text-align: center;
            color: #fff;
            padding: 60px 0 60px 0;
        }

        .logo-cnt .logo {
            font-size: 42px;
            line-height: 42px;
        }

        .logo-cnt p {
            font-size: 22px;
        }

        .install-box {
            width: 100%;
            padding: 30px;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            margin: auto;
            background-color: #fff;
            border-radius: 4px;
            display: block;
            float: left;
            margin-bottom: 100px;
        }

        .form-input {
            box-shadow: none !important;
            border: 1px solid #ddd;
            height: 44px;
            line-height: 44px;
            padding: 0 20px;
        }

        .form-input:focus {
            border-color: #239CA1 !important;
        }

        .btn-custom {
            background-color: #239CA1 !important;
            border-color: #239CA1 !important;
            border: 0 none;
            border-radius: 4px;
            box-shadow: none;
            color: #fff !important;
            font-size: 16px;
            font-weight: 300;
            height: 40px;
            line-height: 40px;
            margin: 0;
            min-width: 105px;
            padding: 0 20px;
            text-shadow: none;
            vertical-align: middle;
        }

        .btn-custom:hover, .btn-custom:active, .btn-custom:focus {
            background-color: #239CA1;
            border-color: #239CA1;
            opacity: .8;
        }

        .tab-content {
            width: 100%;
            float: left;
            display: block;
        }

        .tab-footer {
            width: 100%;
            float: left;
            display: block;
        }

        .buttons {
            display: block;
            float: left;
            width: 100%;
            margin-top: 30px;
        }

        .title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
            margin-top: 0;
            text-align: center;
        }

        .sub-title {
            font-size: 14px;
            font-weight: 400;
            margin-bottom: 30px;
            margin-top: 0;
            text-align: center;
        }

        .alert {
            text-align: center;
        }

        .alert strong {
            font-weight: 500 !important;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-sm-12 col-md-offset-2">
            <div class="row">
                <div class="col-sm-12 logo-cnt">
                    <h1>Modesy</h1>
                    <p>Welcome to the Update Wizard</p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="install-box">
                        <h2 class="title">Update from v1.8.x to v2.0.1</h2>
                        <br><br>
                        <?php if (!empty($success)): ?>
                            <div class="messages">
                                <div class="alert alert-success">
                                    <strong><?php echo $success; ?></strong>
                                </div>
                                <?php @unlink(__FILE__); ?>
                            </div>
                        <?php else: ?>
                            <div class="step-contents">
                                <div class="tab-1">
                                    <?php if (empty($success)): ?>
                                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                            <div class="tab-footer text-center">
                                                <button type="submit" name="btn_submit" class="btn-custom">Update My Database</button>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>