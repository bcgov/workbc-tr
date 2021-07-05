# Bootstrap Paragraphs Contact Form

CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Maintainers

INTRODUCTION
------------

Drupal 8 module that creates a Contact form Paragraphs bundle.

REQUIREMENTS
------------

  * [Paragraphs](https://www.drupal.org/project/paragraphs)
  * [Bootstrap Paragraphs](https://www.drupal.org/project/bootstrap_paragraphs)
  - Contact
  * [Contact Formatter](https://www.drupal.org/project/contact_formatter)

INSTALLATION
------------

  * Install the module as you normally would.
  * Verify installation by visiting /admin/structure/paragraphs_type and seeing
  your new Paragraph bundle.

CONFIGURATION
-------------

  * Go to your content type and add a new field to type Entity revisions,
  Paragraphs.
  * Allow unlimited so creators can add more that one Paragraph to the node.
  * On the field edit screen, you can add instructions, and choose which
  bundles you want to allow for this field. Check all but Accordion Section and
  Tab Section. Those should only be used inside Accordions and Tabs.
  * Arrange them as you see fit. I prefer Simple, Image, and Blank at the top,
  then the rest in Alphabetical order. Click Save Settings.
  * Adjust your form display, placing the field where you want it.
  * Add the field into the Manage display tab.
  * Start creating content!

MAINTAINERS
-----------

Current maintainers:
  * [thejimbirch](https://www.drupal.org/u/thejimbirch)
  * [albertski](https://www.drupal.org/u/albertski)

This project has been sponsored by:
  * [Xeno Media, Inc.](http://www.xenomedia.com)
  * [Zoomdata, Inc.](http://www.zoomdata.com)
