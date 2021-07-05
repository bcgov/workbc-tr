
CONTENTS OF THIS FILE
---------------------
 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Maintainers


INTRODUCTION
------------

The Paragraph Blocks module allows you to place each value of a multi-value
paragraph field into a different block. And further it allows you to place
paragraph fields from related entities in a similar manner. It does so by
extending both paragraphs with an admin title that is only used in the UI for
layout and extending panels by providing the blocks for placement.

 * For a full description of the module, visit the project page:
   https://drupal.org/project/paragraph_blocks

 * To submit bug reports and feature suggestions, or to track changes:
   https://drupal.org/project/issues/paragraph_blocks


REQUIREMENTS
------------

This module requires the following modules outside of Drupal core:

 * Ctool - https://drupal.org/project/ctool
 * Paragraphs - https://drupal.org/project/paragraphs



INSTALLATION
------------

 * Install as you would normally install a contributed Drupal module. Visit:
   https://www.drupal.org/node/1897420 for further information.


CONFIGURATION
-------------

    1. Navigate to Administration > Extend and enable the project and its
       dependencies.
    2. Navigate to Administration > Configuration > Content authoring >
       Paragraph Blocks Settings and set the maximum number of paragraphs you
       wish to see. Save Configuration.

Using with Core Layout Builder per entity type
    1. Check "Use Layout Builder" on the entity referencing the paragraph.
    2. Select "Save".
    3. Select on "Manage Layout."
    4. Select on "Add Block" from somewhere in the layout builder.
    5. The blocks will be named "Paragraph Item N".

Using with Core Layout Builder per entity
    1. If you also select "Allow each content", you can place blocks based on
       the admin label you give for each paragraph item, rather than using the
       "Paragraph Item N" admin labels you see for the per entity type.
    2. Then edit the entity itself. Select on the "Layout" tab. Now when you
       select "Add Block" instead of seeing "Paragraph Item N" you will see
       paragraph items with the admin labels provided during editing.


MAINTAINERS
-----------

Current maintainers:
 * Doug Green (douggreen) - https://www.drupal.org/u/douggreen
