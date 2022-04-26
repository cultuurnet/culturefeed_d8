## Important note
This module is provided by  publiq vzw (formerly known as CultuurNet) as it is.
We do not provide support, nor do we actively continue to develop this module. Pull Requests will be reviewed when time is available with our developers. However we do appreciate the sharing of bugfixes with us.
If you need active support, we provide it for our API & Widgets, see https://projectaanvraag.uitdatabank.be/ .
As an exception, critical security updates will still be provided if needed.


## Install

Prerequisites:

- php > v7.3 and the php intl extension
- composer installed (http://getcomposer.org/doc/00-intro.md#downloading-the-composer-executable)
- Drupal installed via composer

```bash
composer require cultuurnet/culturefeed-d8
```

## Modules

Please enable only the modules you need.

### Culturefeed api

Provides the API client to connect with Culturefeed. No caching / logging is foreseen in this module.

### Culturefeed user

Currently only provides an option to login and request info of the current __UiTID__ user.

### Culturefeed search api

Provides the api client to search in the events database. Caching / logging is foreseen in this module. (See Activating Debugging)

### Culturefeed search

Basic elements to build up an event search (such as provided by Culturefeed Agenda).

- Abstract search page service to extend on (like Culturefeed Agenda)
- Sort block
- Active filters
- Facets

### Culturefeed agenda
Provides a Culturefeed search page available on 'agenda/search' and detail pages. The blocks provided by this module can be used to extend the detail pages of events, actors and productions. Includes also a simple search form.
This module provides a controller, but all the agenda components are split up into blocks. You can use the default controller or take over the controller via page manager.

### Culturefeed_content
Adds a CultureFeed content field to add a search query to any of your entity types.


## Activating debugging
The debugging system works via monolog. To activate this you will need to install and configure the drupal monolog module (http://drupal.org/project/monolog).

Once monolog is installed, update the 'monolog.channel_handlers' section in the parameters of your services.yml:

```bash
  monolog.channel_handlers:
    culturefeed_search_api: ['untranslated_drupal_log']
```

## License

[Apache-2.0](http://www.apache.org/licenses/LICENSE-2.0.html)
