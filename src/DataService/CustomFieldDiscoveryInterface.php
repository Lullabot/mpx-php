<?php

namespace Lullabot\Mpx\DataService;

/**
 * Interface for classes implementing discovery of Custom Field plugins.
 *
 * Different frameworks and content management systems have various methods of
 * handling discovery of annotated classes. For example, a CMS like Drupal
 * doesn't strictly follow PSR-4, and only should expose classes for modules
 * that are actually enabled.
 *
 * This interface allows those frameworks to discover custom field classes in
 * whatever way they see fit.
 *
 * @see CustomFieldDiscovery
 */
interface CustomFieldDiscoveryInterface
{
    /**
     * Returns all Custom Fields.
     *
     * @return array An array of all discovered data services, indexed by service name, object type, and namespace.
     */
    public function getCustomFields(): array;
}
