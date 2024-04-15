=== Paid Memberships Pro - Pods Add On ===
Contributors: strangerstudios, sc0ttkclark, paidmembershipspro
Tags: paid memberships pro, pods
Requires at least: 5.5
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integrates Paid Memberships Pro with the [Pods Framework](https://pods.io/) to add groups of fields to many areas of your membership site including Member Profiles, the Membership Checkout page, and admin screens including Membership Levels and Orders.

== Description ==

= Integrate Paid Memberships Pro and Pods to Extend User, Checkout and Admin Fields =

This integration allows you to create and manage groups of fields for many areas in Paid Membership Pro, including:

* User and Member profiles on the frontend and within the WordPress admin’s Edit User screen.
* Add fields to the Membership Checkout page as an alternative to our Register Helper Add On. Fields added to the Membership Checkout page can be added in 10 unique locations.
* Add fields to the Add/Edit Membership Level page in the WordPress admin.
* Add fields to the Add/Edit Orders page in the WordPress admin.

Each group and field can have custom membership level requirements. This means that only users of a specific membership level or a user completing checkout for that membership level can view and complete protected fields.

*Requires [Pods Framework 2.8.17+](https://wordpress.org/plugins/pods/) and [Paid Memberships Pro 2.6+](https://wordpress.org/plugins/paid-memberships-pro/)*

https://youtu.be/oNUtPO2K0L4

= Add Groups and Fields with the Pods Framework =

This plugin adds three new options to the Pods Framework to the Add New Pod screen which allows you to choose from PMPro Member, PMPro Order, and PMPro Membership Level.

= Member Profile and Checkout Fields =

You can extend the Member object and choose to show groups of fields on the Member Profile page, the WP Dashboard User Profile form, and/or on the Checkout form itself.

= Membership Level Fields =

Have you ever wanted to store extra information about Membership Levels themselves, or add fields that can be referenced in your theme when displaying them? For the first time ever in a PMPro integration — you can now extend Membership Levels with their own custom groups and fields.

= Membership Order Fields =

Now you can track completely separate information per order like Shipping Tracking numbers for swag or anything you can dream of. For the first time ever in a PMPro integration — you can now extend Orders with their own custom groups and fields.

= Pods Block Integration =

Use any of the PMPro objects in your Pods Blocks to unlock the advanced templating you’ve been dreaming of for PMPro.

= Advanced: Pods Blocks Query Customization =

You can query your PMPro objects with Pods Blocks (used in "Order By" and the "Where" block settings) by [following the short guide in our documentation page](https://www.paidmembershipspro.com/add-ons/pods-integration/#query).

== Installation ==

1. Make sure you have the Paid Memberships Pro and Pods Framework plugins installed and activated.
1. Upload the `pmpro-pods` directory to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= I found a bug in the plugin. =

Please post it in the GitHub issue tracker here: [https://github.com/strangerstudios/pmpro-pods/issues](https://github.com/strangerstudios/pmpro-pods/issues)

For immediate help, also post to our premium support site at [https://www.paidmembershipspro.com](https://www.paidmembershipspro.com) for more documentation and our support forums.

= I need help installing, configuring, or customizing the plugin. =

Please visit our premium support site at [https://www.paidmembershipspro.com](https://www.paidmembershipspro.com) for more documentation and our support forums.

== Screenshots ==

1. Add New Pod: PMPro Member, PMPro Order, and PMPro Membership Level
2. Edit PMPro Member Pod
3. Edit PMPro Member Group Options
4. Edit PMPro Member Field Options
5. Custom field on Edit User screen in the WordPress admin
6. Custom field on the Member Profile Edit page (frontend)
7. Custom field on the Membership Checkout form
8. Edit PMPro Membership Level Pod
9. Edit PMPro Membership Level Group Options
10. Custom field on the Edit Membership Level page in the WordPress admin
11. Edit PMPro Order Pod
12. Custom field on the Edit Order form
13. Use Pods Blocks to display PMPro information in new ways

== Changelog ==
= 1.0.3 - 2023-10-06 =
* ENHANCEMENT: Added support for the "Add Member From Admin" Add On. Show fields that are shown at checkout on the Add Member page.
* ENHANCEMENT: Improved accessibility for screen readers.
* BUG FIX/ENHANCEMENT: Improved compatibility with the latest version of Pods.

= 1.0.2 - January 4th, 2022 =
* ENHANCEMENT: Added support for two new locations on the PMPro checkout page: After User Fields and Checkout Boxes. (@sc0ttkclark)
* BUG FIX: Improved appearance of Pods fields on the PMPro checkout page so they more closely match styles used by PMPro. (@sc0ttkclark)
* BUG FIX: Prevent PHP notices from usage of certain PMPro hooks when `$user` object is not passed in. (@sc0ttkclark)
* BUG FIX: Prevent PHP notices on the PMPro checkout page when the PMPro Member pod does not exist and has not yet been extended. (@sc0ttkclark)

= 1.0.1 - November 12th, 2021 =
* ENHANCEMENT: Added support for showing PMPro Member fields on the Membership Account page under the Account Details area. #5 (@sc0ttkclark)
* BUG FIX: Prevent PHP notices on checkout page when logged out. (@sc0ttkclark)

= 1.0 - October 26th, 2021 =
* Initial release
