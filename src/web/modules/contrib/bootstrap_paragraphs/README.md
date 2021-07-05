# Bootstrap Paragraphs

CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Maintainers


INTRODUCTION
------------

A suite of Paragraph bundles made with the Bootstrap framework.

For content creators, attempts to use WYSIWYG editors to create structured
layouts typically lead to frustration and compromise. With this module, you can
easily position chunks of content (Paragraph bundles) within a structured
layout of your own design.

This suite of [Paragraphs](https://www.drupal.org/project/paragraphs) bundles
works within the [Bootstrap](http://getbootstrap.com) framework.

 * For a full description of the module, visit the project page:
   https://drupal.org/project/bootstrap_paragraphs
   or
   https://www.drupal.org/docs/8/modules/bootstrap-paragraphs

 * To submit bug reports and feature suggestions, or to track changes:
   https://drupal.org/project/issues/bootstrap_paragraphs

This module is built on the premise that all good things in Drupal 8 are
entities and we can use Paragraphs and Reference fields to allow our content
creators to harness the power of the Bootstrap framework for functionality
and layout.

This module is ready for [Drupal 9](https://www.drupal.org/project/bootstrap_paragraphs/issues/3042806).

**Bundle Types:**

 * Simple HTML
 * Image
 * Blank
 * Accordion
 * Carousel
 * Columns (Equal, up to 6)
 * Columns (Three Uneven)
 * Columns (Two Uneven)
 * Contact Form
 * Drupal Block
 * Modal
 * Tabs
 * View

**Backgrounds:**

Each Paragraph has width and background color options. Included are over 50
background colors and five empty background classes for you to customize in
your own theme.

**Widths:**

 * Tiny - col-4, offset-4
 * Narrow - col-6, offset-3
 * Medium - col-8, offset-2
 * Wide - col-10, offset-1
 * Full - col-12


REQUIREMENTS
------------

This module requires the following modules outside of Drupal core:

 * [Contact Formatter](https://www.drupal.org/project/contact_formatter)
 * [Entity Reference Revisions](https://www.drupal.org/project/entity_reference_revisions)
 * [Paragraphs](https://www.drupal.org/project/paragraphs)
 * [Views Reference Field](https://www.drupal.org/project/viewsreference)
 * Bootstrap framework's CSS and JS included in your theme


INSTALLATION
------------

 * Install as you would normally install a contributed Drupal module. Visit:
   https://www.drupal.org/node/1897420 for further information.
 * Verify installation by visiting /admin/structure/paragraphs_type and seeing
   your new Paragraph bundles.
 * On the Simple and Blank bundles, select Manage fields and choose which Text
   formats to use.
   We recommend a *Full HTML* for the Simple, and a *Full HTML - No Editor* for
    the Blank.


CONFIGURATION
-------------

 * Go to your content type and add a new field of type Entity revisions,
   Paragraphs.
 * Allow unlimited so creators can add more than one Paragraph to each node.
 * On the field edit screen, you can add instructions, and choose which
   bundles you want to allow for this field. Check all but Accordion Section and
   Tab Section. Those should only be used inside Accordions and Tabs.
 * Arrange them as you see fit. I prefer Simple, Image, and Blank at the top,
   then the rest in Alphabetical order. Select Save Settings.
 * Adjust your form display, placing the field where you want it.
 * Add the field into the Manage display tab.
 * Start creating content!


MAINTAINERS
-----------

Current maintainers:
 * [thejimbirch](https://www.drupal.org/u/thejimbirch)
 * [albertski](https://www.drupal.org/u/albertski)

This project has been sponsored by:
 * [Kanopi studios](https://www.drupal.org/kanopi-studios)
 * [Xeno Media, Inc.](http://www.xenomedia.com)
 * [Zoomdata, Inc.](http://www.zoomdata.com)
