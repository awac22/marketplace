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
    update_18_to_19($connection);
    sleep(1);
    update_19_to_20($connection);
    sleep(1);
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