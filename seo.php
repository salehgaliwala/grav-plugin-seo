<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class SEOPlugin
 * @package Grav\Plugin
 */
class SEOPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     * that the plugin wants to listen to. The key of each
     * array section is the event that the plugin listens to
     * and the value (in the form of an array) contains the
     * callable (or function) as well as the priority. The
     * higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            $this->enable([
                'onBlueprintCreated' => ['onBlueprintCreated', 0]
            ]);
            return;
        }

        // Enable the main event we are interested in
        $this->enable([
            'onPageInitialized' => ['onPageInitialized', 0]
        ]);
    }

    /**
     * Inject SEO fields into page blueprints.
     *
     * @param Event $event
     */
    public function onBlueprintCreated(Event $event)
    {
        $blueprint = $event['blueprint'];

        // Ensure we only process page blueprints and it has the tabs structure
        if ($blueprint && $blueprint->get('form/fields/tabs', null, '/')) {
            $blueprints = new \Grav\Common\Config\Blueprints(__DIR__ . '/blueprints/');
            $extends = $blueprints->get('seo');
            if ($extends) {
                $blueprint->extend($extends, true);
            }
        }
    }


    /**
     * On Page Initialized
     * 
     * This is where we inject the SEO tags into the page metadata
     */
    public function onPageInitialized()
    {
        $page = $this->grav['page'];
        $config = $this->config->get('plugins.seo');

        if (!$config['enabled']) {
            return;
        }

        // Get page-level SEO overrides
        $header = $page->header();
        $seo = isset($header->seo) ? (array) $header->seo : [];

        // 1. Basic Meta Tags
        $title = $seo['title'] ?? ($page->title() . ' | ' . ($config['site_name'] ?? ''));
        $description = $seo['description'] ?? ($config['description'] ?? '');
        $robots = $seo['robots'] ?? ($config['robots'] ?? 'index, follow');

        // Set the page title directly (overrides default)
        if (isset($seo['title'])) {
            $page->title($seo['title']);
        }

        // Add metadata for description and robots
        $metadata = $page->metadata();
        $metadata['description'] = ['name' => 'description', 'content' => $description];
        $metadata['robots'] = ['name' => 'robots', 'content' => $robots];

        // 1.1 Canonical URL
        $canonical = $seo['canonical'] ?? $this->grav['uri']->url(true, true);
        $metadata['canonical'] = ['name' => 'link', 'rel' => 'canonical', 'href' => $canonical];

        // 2. Open Graph Tags
        if ($config['og']['enabled']) {
            $og_title = $seo['og_title'] ?? $title;
            $og_description = $seo['og_description'] ?? $description;
            $og_type = $config['og']['type'] ?? 'website';
            $og_image_raw = $seo['og_image'] ?? $config['og']['image'] ?? '';
            $og_image = '';

            if ($og_image_raw) {
                // Check if it's a file in page media
                $media = $page->media();
                if (isset($media[$og_image_raw])) {
                    $og_image = $media[$og_image_raw]->url(true);
                } else {
                    // Assume it's a relative path from root
                    $og_image = $this->grav['uri']->rootUrl(true) . '/' . $og_image_raw;
                }
            }

            $metadata['og:title'] = ['property' => 'og:title', 'content' => $og_title];
            $metadata['og:description'] = ['property' => 'og:description', 'content' => $og_description];
            $metadata['og:type'] = ['property' => 'og:type', 'content' => $og_type];
            $metadata['og:url'] = ['property' => 'og:url', 'content' => $canonical];

            if ($og_image) {
                $metadata['og:image'] = ['property' => 'og:image', 'content' => $og_image];
            }
        }

        // 3. Twitter Tags
        if ($config['twitter']['enabled']) {
            $twitter_title = $seo['twitter_title'] ?? $title;
            $twitter_description = $seo['twitter_description'] ?? $description;
            $twitter_card = $config['twitter']['card'] ?? 'summary_large_image';
            $twitter_site = $config['twitter']['site'] ?? '';
            $twitter_image_raw = $seo['twitter_image'] ?? $seo['og_image'] ?? $config['og']['image'] ?? '';
            $twitter_image = '';

            if ($twitter_image_raw) {
                $media = $page->media();
                if (isset($media[$twitter_image_raw])) {
                    $twitter_image = $media[$twitter_image_raw]->url(true);
                } else {
                    $twitter_image = $this->grav['uri']->rootUrl(true) . '/' . $twitter_image_raw;
                }
            }

            $metadata['twitter:card'] = ['name' => 'twitter:card', 'content' => $twitter_card];
            $metadata['twitter:title'] = ['name' => 'twitter:title', 'content' => $twitter_title];
            $metadata['twitter:description'] = ['name' => 'twitter:description', 'content' => $twitter_description];

            if ($twitter_site) {
                $metadata['twitter:site'] = ['name' => 'twitter:site', 'content' => $twitter_site];
            }

            if ($twitter_image) {
                $metadata['twitter:image'] = ['name' => 'twitter:image', 'content' => $twitter_image];
            }
        }

        // Save metadata back to the page
        $page->metadata($metadata);
    }
}
