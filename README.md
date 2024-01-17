# svg-toolkit

This is a Craft CMS plugin that provides a set of tools for working with SVG files.

## Features

- Adds SVG as a separate asset type, so you don't need to check asset type in the templates.
- Load SVGs from local file paths, template includes, assets, or markup.
- Modify SVG class attributes.
- Change SVG color to currentColor or specific color.
- Add or modify SVG title with aria-labelledby for better accessibility.
- Add role="img" for better accessibility

## Requirements

This plugin requires Craft CMS 4.5.0 or later, and PHP 8.0.2 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “svg-toolkit”. Then press “Install”.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require solvras/craft-svg-toolkit

# tell Craft to install the plugin
./craft plugin/install svg-toolkit
```


## Usage

This plugin provides several functions and filters that you can use in your Twig templates to work with SVG files.

### Functions

#### svgToolkit

This function takes an SVG (either an Asset,filepath (supports aliases like @root), filename (if file is in the defined path) or markup either markup in a var or markup from imported svg template) and an optional class (a string). It returns the SVG contents with modified attributes and adds role="img" for better accessibility .

```twig
{{ svgToolkit(svgAsset, 'example-class') }}
{{ svgToolkit(svgAsset) }}
{{ svgToolkit('example') }}
{{ svgToolkit('@root/path/to/asset/example.svg') }}
{{ svgToolkit('<svg>...</svg>') }}
```

### Filters

#### svgColor

This filter takes an SVG from the svgToolkit function and an optional color (a string). If no color is defined it uses currentColor and the svg will use the current text color. It returns the SVG with a modified color.

```twig
{{ svgToolkit(...)|svgColor('#ff0000') }}
{{ svgToolkit(...)|svgColor('red') }}
{{ svgToolkit(...)|svgColor }}
```

#### svgTitle

This filter takes an SVG (either an Asset or a string) and a title (a string). It returns the SVG with a modified or added title.

```twig
{{ svgToolkit(...)|svgTitle('Example Title') }}
```

