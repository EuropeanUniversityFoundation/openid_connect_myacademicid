# OpenID Connect MyAcademicID client

MyAcademicID client compatible with the **8.x-1.x version** of the [OpenID Connect](https://www.drupal.org/project/openid_connect/) module.

This is a pure clone of the Generic client provided by the contributed module.

## Installation

Include the repository in your project's `composer.json` file:

    "repositories": [
        ...
        {
            "type": "vcs",
            "url": "https://github.com/EuropeanUniversityFoundation/openid_connect_myacademicid"
        }
    ],

Then you can require the package as usual:

    composer require euf/openid_connect_myacademicid

Finally, install the module:

    drush en openid_connect_myacademicid

## Usage

A new OpenID Connect client will be available at `/admin/config/services/openid-connect`. The configuration options available are the same as the Generic client and the module ships with some defaults.

### Overriding defaults

Out of the box, the module points to the acceptance environment of MyAcademicID. Once all relevant testing has been carried out, it is safe to edit the configuration to point to the production environment.

**However** overriding the placeholders for Client ID and Client Secret should be done via `settings.php` or `settings.local.php` so that these credentials are not committed to the `git` repository. This approach is also useful to enable and disable certain clients per environment. See configuration override syntax below:

    /* MyAcademicID settings */
    #$config['openid_connect.settings.myacademicid']['enabled'] = FALSE;
    $config['openid_connect.settings.myacademicid']['settings']['client_id'] = 'real_client_id';
    $config['openid_connect.settings.myacademicid']['settings']['client_secret'] = 'real_client_secret';
