<?php

namespace Drupal\twig_tweak;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\file\FileInterface;
use Drupal\link\LinkItemInterface;
use Drupal\media\MediaInterface;
use Drupal\media\Plugin\media\Source\OEmbedInterface;

/**
 * URL extractor service.
 */
class UrlExtractor {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a UrlExtractor object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Extracts file URL from a string or object.
   *
   * @param string|object $input
   *   Can be either file URI or an object that contains the URI.
   * @param bool $relative
   *   (optional) Whether the URL should be root-relative, defaults to true.
   *
   * @return string|null
   *   A URL that may be used to access the file.
   */
  public function extractUrl($input, bool $relative = TRUE): ?string {
    if (is_string($input)) {
      $url = file_create_url($input);
      return $relative ? file_url_transform_relative($url) : $url;
    }
    elseif ($input instanceof LinkItemInterface) {
      return $input->getUrl()->toString();
    }
    elseif ($input instanceof FieldItemList && $input->first() instanceof LinkItemInterface) {
      return $input->first()->getUrl()->toString();
    }

    $entity = $input;
    if ($input instanceof EntityReferenceFieldItemListInterface) {
      if ($item = $input->first()) {
        $entity = $item->entity;
      }
    }
    elseif ($input instanceof EntityReferenceItem) {
      $entity = $input->entity;
    }
    // Drupal does not clean up references to deleted entities. So that the
    // entity property might be empty while the field item might not.
    // @see https://www.drupal.org/project/drupal/issues/2723323
    return $entity instanceof ContentEntityInterface ?
      $this->getUrlFromEntity($entity, $relative) : NULL;
  }

  /**
   * Extracts file URL from content entity.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   Entity object that contains information about the file.
   * @param bool $relative
   *   (optional) Whether the URL should be root-relative, defaults to true.
   *
   * @return string|null
   *   A URL that may be used to access the file.
   */
  private function getUrlFromEntity(ContentEntityInterface $entity, bool $relative = TRUE): ?string {
    if ($entity instanceof MediaInterface) {
      $source = $entity->getSource();
      $value = $source->getSourceFieldValue($entity);
      if (!$value) {
        return NULL;
      }
      elseif ($source instanceof OEmbedInterface) {
        return $value;
      }
      else {
        /** @var \Drupal\file\FileInterface $file */
        $file = $this->entityTypeManager->getStorage('file')->load($value);
        if ($file) {
          return $file->createFileUrl($relative);
        }
      }
    }
    elseif ($entity instanceof FileInterface) {
      return $entity->createFileUrl($relative);
    }
    return NULL;
  }

}
