KEYCLOAK 8.x-1.x <a name="top"></a>
================

CONTENTS OF THIS DOCUMENT
-------------------------
1. [Introduction](#introduction)
2. [Features](#features)
3. [Requirements](#requirements)
4. [Dependencies](#dependencies)
5. [Installation](#installation)
6. [Configuration](#configuration)
7. [Similar Projects](#similar)
8. [Maintainers](#maintainers)
9. [Supporting Organizations](#supporting)


INTRODUCTION <a name="introduction"></a>[top](#top)
-----------
The Keycloak module provides a Keycloak login provider client for the
[OpenID Connect](https://www.drupal.org/project/openid_connect) module.
It allows you to authenticate your users against a
[Keycloak](http://www.keycloak.org) authentication server.  
  
Keycloak is an Open Source Identity and Access Management system that supports
OpenID Connect, OAuth 2.0 and SAML 2.0 login, LDAP and Active Directory user
federation, OpenID Connect or SAML 2.0 identity brokering and various Social
Logins out of the box.  


FEATURES <a name="features"></a>[top](#top)
--------
  * Login to Drupal using Keycloak OpenID Connect.  
  * Synchronize user fields with OpenID attributes provided by Keycloak using
    the OpenID Connect module's claim mapping.  
  * Additionally synchronize email address changes from within Keycloak with
    the connected Drupal user's email address.  
  * Multi-language support:  
      * Forward language parameters to Keycloak, so the login/user registration
        of Keycloak opens up in the same language as your multi-language Drupal
        site.  
      * Map Keycloak's user locale settings to Drupal languages.  


REQUIREMENTS <a name="requirements"></a>[top](#top)
------------
A working Keycloak authentication server with a realm and an OpenID client for
your Drupal website.  

Please note: How to setup Keycloak is out of the scope of this document. The
[Keycloak documentation](http://www.keycloak.org/documentation.html) may get
you started.  


DEPENDENCIES <a name="dependencies"></a>[top](#top)
------------
  * [OpenID Connect](https://www.drupal.org/project/openid_connect)  


INSTALLATION <a name="installation"></a>[top](#top)
------------
  * Install the module and all its dependencies as you would do with any other
    Drupal module.  
    If you install using composer, the openid_connect will be installed
    automatically:  
    `composer require "drupal/keycloak:^1.0"`  
  * Enable the module.  
  * Go to the openid_connect settings and enable the Keycloak client at  
    Administration / Configuration / Web services / OpenID Connect  


CONFIGURATION <a name="configuration"></a>[top](#top)
-------------
The module configuration is available within the OpenID Connect client
settings. After enabling the client, you may provide the following Keycloak
specific settings:  

  * Client ID  
    The ID of your Keycloak client.  

  * Client secret  
    The client secret of your Keycloak client.  

  * Keycloak base URL  
    The base URL of your Keycloak authentication server. This is the URL
    that shows the Keycloak welcome page and typically looks like  
    `https://example.com{:PORT}/auth`  
    where example.com is the domain of your Keycloak server and the
    optional {:PORT} the port, if the server does not use standard ports.  

  * Keycloak realm  
    The name of the realm your users belong to.

  * Update email address in user profile  
    The OpenID Connect module has no means to synchronize changed email
    addresses from the OpenID Connect provider. If you wish to update the email
    addresses with changes from Keycloak, then enable this option.  
    WARNING: This is safe only, if changing email addresses from within Drupal
    is disabled. (E.g. by hiding the email address field in the user edit form.)
    If changed email addresses from Keycloak are used for other users within
    your Drupal already, the module will show an error message and not change
    the existing email address in Drupal. This may lead to inconsistencies with
    your Keycloak user database.  

  * Enable multi-language support  
    This option is available only, if you work with a multi-language Drupal
    site. It enables language parameter forwarding to Keycloak and translates
    Keycloak locales to Drupal language codes (refer to the following Language
    mappings setting).  

      * Language mappings  
        Drupal uses IETF script language codes for its language interface,
        while Keycloak may use IETF region language codes.
        (Read more about IETF languages code syntax
        [here](https://tools.ietf.org/html/bcp47#section-2.1).) If you are
        using languages as Chinese Simplified (zh-hans in Drupal and zh-CN in
        Keycloak), you may edit the locale codes accordingly in this section.  

  * Enable Keycloak single sign-on (SSO)  
    This option allows you to use Drupal with Keycloak as sole authentication
    provider. The default authentication mechanisms of Drupal will be replaced
    by the Keycloak login. E.g. opening the `/user/login` page of your Drupal
    will automatically redirect to Keycloak for authentication.  
    Please note: Existing users with a password set (e.g. the administrator
    account) are still able to login using the fallback login page at
    `/keycloak/login`, which will show the regular Drupal login form.  

  * Enable Drupal-initiated single sign-out  
    If a user logged in to Drupal using Keycloak, this option allows to end
    the Keycloak session of this user, if he logs out of Drupal.  

  * Enable Keycloak-initiated single sign-out
    If a user logged in to Drupal using Keycloak, this option allows Drupal
    to regularily check the validity of the Keycloak session using the
    Keycloak check session Iframe. If the user ended its Keycloak session,
    he will be logged out of Drupal as well.  

      * Check session interval  
        If Keycloak-initiated single sign-out is enabled, this value determines
        the interval in seconds, in which Drupal will check whether the
        Keycloak session has ended.

  
For all other configuration options, please refer to the OpenID Connect module
documentation.  


SIMILAR PROJECTS <a name="similar"></a>[top](#top)
----------------
Keycloak supports OpenID Connect, OAuth2 and SAML standards for authentication
clients. You might wish to also have a look to the following contributed
modules to authenticate your Drupal users with Keycloak:  

  * [SAML Authentication](https://www.drupal.org/project/samlauth)  
    This module features SAML-based user authentication. User attributes
    mapping is in development.  

  * [simpleSAMLphp Authentication]
    (https://www.drupal.org/project/simplesamlphp_auth)  
    This module requires a working setup of
    [SimpleSAMLphp](https://simplesamlphp.org) as service provider on your
    webserver to connect to the Keycloak Identity Provider. It features
    SAML-based authentication and user role provisioning.  

  * [OAuth2 Client](https://www.drupal.org/project/oauth2_client)  
    A basic OAuth2.0 client for Drupal that can be extended programmatically.  


MAINTAINERS <a name="maintainers"></a>[top](#top)
-----------
  * [Mario Steinitz](https://www.drupal.org/u/mario-steinitz)


SUPPORTING ORGANIZATIONS <a name="supporting"></a>[top](#top)
------------------------
[SHORELESS Limited](https://www.drupal.org/shoreless-limited)
SHORELESS Limited is an IT consulting and software solutions provider. The
development of the initial version of this module was funded by SHORELESS to
integrate Drupal 8 based websites with Keycloak SSO servers.  
  
It also grants me paid working hours to further enhance and improve the module
according to the needs of the Drupal community.  
