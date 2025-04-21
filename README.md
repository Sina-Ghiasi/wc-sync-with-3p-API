# WC Sync with Third Party API

## Overview
WC Sync with Third Party API is a WordPress plugin designed to synchronize WooCommerce product data with a third-party API. It allows administrators to manually update product information via an intuitive admin interface and automatically updates products daily. The plugin stores update history in a custom database table and provides a secure, user-friendly way to manage API keys and monitor synchronization status.

## Features
- **Manual Product Updates**: Trigger product updates with a single click from the WordPress admin panel.
- **Automatic Daily Updates**: Products are automatically synchronized with the third-party API daily.
- **API Key Management**: Securely store and manage API keys through the WordPress admin settings.
- **Update History Tracking**: Logs the date, time, and number of products updated in a custom database table.
- **Secure Ajax Requests**: Uses nonces to secure AJAX requests for updating products.
- **User Timezone Support**: Captures and stores the user's timezone for accurate update timestamps.
- **Feedback Notifications**: Displays success or error messages after update attempts.

## Requirements
- WordPress 5.0 or higher
- WooCommerce 3.0 or higher
- PHP 7.0 or higher
- A valid API key from the third-party service

## Installation
1. **Download the Plugin**:
   - Clone or download the plugin from the [GitHub repository](https://github.com/your-repo-link).
2. **Install the Plugin**:
   - Upload the plugin folder to the `/wp-content/plugins/` directory, or install it via the WordPress admin panel by uploading the ZIP file.
3. **Activate the Plugin**:
   - Navigate to the WordPress admin panel, go to **Plugins**, and activate "WC Sync with Third Party API".
4. **Configure the Plugin**:
   - After activation, a new menu item, **Update Products**, will appear in the WordPress admin menu.
   - Go to **Update Products** to set your API key and initiate product updates.

## Usage
1. **Set the API Key**:
   - Navigate to **Update Products** in the WordPress admin menu.
   - Enter your third-party API key in the provided field and click **Save Key**.
2. **Manual Product Update**:
   - Click the **Update Products** button to manually synchronize product data with the third-party API.
   - The plugin will display the date of the last update and the number of products updated.
3. **Monitor Updates**:
   - Success or error messages will appear after each update attempt.
   - The plugin logs update details (last update time, timezone, and product counts) in the database.
4. **Automatic Updates**:
   - The plugin automatically updates products daily, ensuring your WooCommerce store stays in sync with the third-party API.

## Database
The plugin creates a custom database table (`wp_wcswa_db`) upon activation to store update history. The table includes the following fields:
- `id`: Unique identifier for the record.
- `lastUpdate`: Timestamp of the last update.
- `userTimezone`: The timezone of the user who initiated the update.
- `updatedProducts`: Number of products updated in the last sync.
- `allProducts`: Total number of products processed.

## Security
- **Nonce Verification**: All AJAX requests are secured with nonces to prevent unauthorized access.
- **Sanitized Inputs**: API keys are sanitized before being saved to the database.
- **Access Control**: Only users with `manage_options` capability can access the plugin's settings and initiate updates.

## Development
The plugin is built with the following components:
- **PHP**: Handles the core functionality, including admin menu creation, settings management, database operations, and AJAX handling.
- **JavaScript/jQuery**: Manages the front-end AJAX requests for updating products and displaying results.
- **WordPress Hooks**:
  - `admin_menu`: Adds the plugin's settings page to the admin menu.
  - `admin_init`: Registers settings for API key storage.
  - `wp_ajax`: Handles AJAX requests for logged-in users.
  - `wp_ajax_nopriv`: Restricts AJAX access for logged-out users.
  - `init`: Enqueues the JavaScript file for AJAX functionality.

## Contributing
Contributions are welcome! To contribute:
1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Submit a pull request with a clear description of your changes.

Please ensure your code follows WordPress coding standards and includes appropriate documentation.
