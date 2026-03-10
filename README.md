# Grav SEO Plugin

The **SEO** plugin for [Grav](http://github.com/getgrav/grav) provides a comprehensive set of tools to manage meta tags, Open Graph (OG) tags, and Twitter Cards, similar to Yoast SEO for WordPress. It helps you optimize your site's visibility on search engines and social media platforms.

## Features

- **Global Configuration**: Set a base site name, default meta description, and robots behavior.
- **Per-Page Overrides**: An "SEO" tab is added to the Grav Admin page editor for customizing:
    - **SEO Title**: Override the page title in the `<title>` tag.
    - **Meta Description**: Custom descriptions for search snippets.
    - **Canonical URL**: Prevent duplicate content by specifying the preferred version of a page.
    - **Robots**: Fine-grained control over indexing and link following (Index/Noindex, Follow/Nofollow).
- **Open Graph (OG) Support**:
    - Custom Title and Description for Facebook/LinkedIn.
    - Page-specific images with easy selection.
    - Intelligent fallbacks to global defaults.
- **Twitter Cards Support**:
    - Support for various card types (Summary, Summary Large Image).
    - Custom Title, Description, and Image.
    - Twitter Site handle integration.
- **Smart Asset Handling**: Supports selecting images directly from page media or using remote/root-relative paths.

## Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `seo`.

Alternatively, you can install it via the [GPM](http://learn.getgrav.org/advanced/gpm) (Grav Package Manager):

```bash
bin/gpm install seo
```

## Configuration

Before configuring this plugin, you should copy the `user/plugins/seo/seo.yaml` file to `user/config/plugins/seo.yaml` and only edit that copy.

```yaml
enabled: true
site_name: 'My Awesome Grav Site'
description: 'Standard description for my site'
robots: 'index, follow'
og:
  enabled: true
  type: website
  image: 'user/plugins/seo/assets/default-og.png'
twitter:
  enabled: true
  card: summary_large_image
  site: '@yourtwitterhandle'
```

## Usage

Once installed and enabled, you'll see a new **SEO** tab when editing any page in the Grav Admin. 

### Page Media Support
For images (OG and Twitter), you can simply provide the filename of an image already uploaded to the page's media folder (e.g., `featured-image.jpg`). The plugin will automatically resolve the full URL.

## Credits

Developed by [Saleh Galiwala](https://github.com/salehgaliwala).
