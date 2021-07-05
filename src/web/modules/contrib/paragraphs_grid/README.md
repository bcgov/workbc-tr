INTRODUCTION
------------

## Paragraphs Grid
> With Paragraphs Grid, you can display multiple paragraph entities in a grid.
Supports Out-Of-The-Box Bootstrap 3.0 and 4.0 and CSS grid from MDC (Material
Design).

For a full description of the module, visit the [project page][paragraphs_grid].

To submit bug reports and feature suggestions, or track changes,
[use Issue page on Drupal.org][paragraphs_grid_issues]

## Benefits
1. Avoid nested paragraphs for general design problems.
2. Reduce the number of different paragraph types.

## Additional features:
* Includes a view mode selector that makes it easy, e.g. Display media entities
in the appropriate responsive design. This can reduce enormously the number of 
required paragraph types.
* Includes break-out classes that allow you to break the boundaries of the
container and display individual paragraphs across the width of the entire
display port.
* The configuration of the grid (breakpoints, number of columns, additional CSS
classes, ...) can be adjusted via YAML files.

INSTALLATION
------------
* Just download and install Paragraphs Grid as [described here](
https://www.drupal.org/docs/8/extending-drupal-8/installing-drupal-8-modules).

CONFIGURATION
-------------
2. In the menu you go to „Configuration > Content authoring > Paragraphs grid“
   (/admin/config/content/paragraphs_grid) and choose your grid system.
3. Add a "Paragraphs Grid" field to all your paragraph types (Optional).
4. Go to the Administer page of your node type 
   (e.g. /admin/structure/types/manage/article/display) and select the 
   „Paragraphs Grid formatter“ for the Paragraphs field (Type: Entity Reference
    Revisions).
  
## Getting started with this YouTube video:

> [Drupal 8: Einführung in das Modul "Paragraphs Grid"][youtube]

REQUIREMENTS
------------
* [Paragraphs][paragraphs]


RECOMMENDED MODULES
-------------------
* [Media (in Core)][media]

MAINTAINERS
-----------

Current maintainer:
 * [Joachim Feltkamp (JFeltkamp)][jfeltkamp]

## Drupal 8: 
Paragraphs grid is ready for use with Bootstrap 3 and 4. Other grid systems 
(like CSS grid) are still under development. For me it works fine even in 
production environments. 

[jfeltkamp]: https://www.drupal.org/u/jfeltkamp
[media]: https://www.drupal.org/docs/8/core/modules/media
[paragraphs]: https://www.drupal.org/project/paragraphs
[paragraphs_grid]: https://www.drupal.org/project/paragraphs
[paragraphs_grid_issues]: https://www.drupal.org/project/issues/paragraphs_grid
[youtube]: https://youtu.be/onU1E2Z6Jvc
