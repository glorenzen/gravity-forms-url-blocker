# Gravity Forms URL Blocker

Gravity Forms URL Blocker is a (simple) WordPress plugin I made to block Gravity Form submissions when a URL is present in a textarea field. This plugin is useful for avoiding spam and ensuring that users do not input unwanted or malicious links in form submissions.

## Features

- **URL Detection**: Automatically detects URLs in textarea fields and prevents form submission if any are found.
- **Customizable**: Allows administrators to specify which forms and textarea fields should be checked for URLs.
- **User-friendly Messages**: Provides customizable error messages to inform users about the restriction on URL submissions.

## Installation

1. Download the plugin files and upload them to your WordPress plugin directory (`wp-content/plugins/`).
2. Navigate to the WordPress admin panel, go to Plugins, and activate the Gravity Forms URL Blocker plugin.
3. Once activated, go to the plugin's settings page to configure which forms and fields should be checked for URLs.

## Configuration

After activation, a new menu item "GF URL Blocker" will appear in the WordPress admin menu. Click on it to access the plugin's settings page. Here, you can enter the IDs of the forms and the corresponding textarea fields you wish to monitor for URLs.

## Usage

Once configured, the plugin will automatically validate the specified textarea fields in the selected forms. If a user attempts to submit a form with a URL in a monitored field, the submission will be blocked, and a message will be displayed to the user.

## License

This plugin is licensed under the GPL v2 or later.

## Author

Greg Lorenzen