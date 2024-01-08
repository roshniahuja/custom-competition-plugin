# custom-competition-plugin
> * This WordPress plugin adds a custom functionality for managing competitions and user entries. The plugin creates two custom post types: "Competitions" and "Entries." Below are the key features of the plugin:

> * Competitions Post Type
> * The plugin creates a post type named "Competitions" with the following fields:

Title
Description
Featured Image

> * Entries Post Type
> * The "Entries" post type has the following custom fields:

First Name
Last Name
Email
Phone
Description
Competition ID

## Shortcode
> * A [competition_list] shortcode is available to display a list of competitions. The list includes the title, description, and image.

> * Competition Detail Page
> * Each competition has a detail page with a "Submit Entry" button. Clicking on the "Submit Entry" button will navigate to the submission page with the URL structure: {siteURL}/competition-slug/submit-entry. To facilitate this, the plugin creates a parent page with the competition-slug and a child page named "submit-entry."

> * Submit Entry Page
> * The "Submit Entry" page contains a form with the following fields:

First Name
Last Name
Email
Phone
Description

> * The form submission utilizes Ajax. Submitted entries are inserted into the "Entries" post type, including corresponding meta fields and the associated competition ID.

## Installation
1. Download the plugin files and upload them to the /wp-content/plugins/custom-competition-plugin/ directory.
2. Activate the plugin through the WordPress plugins page.

## Composer
Install composer packages to autoload class file.
`composer install`

## Usage
Use the [competition_list] shortcode to display a list of competitions.
Click on the "Submit Entry" button on a competition's detail page to access the entry submission form.
Complete the form with the required information and submit. Ajax is used for a seamless submission process.

# [Plugin Demo](https://app.screencast.com/dlMY5M2FtlaJc)

## Author

* **Roshni Ahuja**