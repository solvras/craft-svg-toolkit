# svg-toolkit

The `svg-toolkit` is a Craft CMS plugin that provides a set of tools for working with SVG files in Twig, with a focus on improving accessibility. It adds SVG as a separate asset type in the Craft CMS Asset field and allowing you to load SVGs from various sources such as local file paths, template includes, assets, or markup.

The plugin provides several functions and filters to modify SVG attributes. For instance, you can change the SVG color, replace a specific color in multi-colored SVGs, add or modify the SVG title for better accessibility, and modify SVG class attributes.

It also allows you to define the folder path for SVGs in the plugin config file and supports the use of multiple paths. You can use aliases like `@root` in the config file.

## Features

- Adds SVG as a separate asset type, so you don't need to check asset type in the templates.
- define folder path for SVGs in plugin config file. accepts more than one path.
- Load SVGs from local file paths, template includes, assets, or markup.
- Modify SVG class attributes.
- Change SVG color to currentColor or specific color.
- Replace a specific color to currentColor or new color in multi colored svg.
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
# Config file
Copy the config.php file from the plugin directory to your config directory. Rename it to `svg-toolkit.php` and change the values to your needs. File accepts more multiple paths and you can use aliases like `@root`



## Usage

This plugin provides several functions and filters that you can use in your Twig templates to work with SVG files.

### Functions

#### svgToolkit

This function takes an SVG (either an Asset,filepath (supports aliases like @root), filename (if file is in the defined path) or markup (either markup in a variable or markup from imported svg template) and an options array. It returns the modified SVG contents and removes XML declaration and adds role="img" for better accessibility .

```twig
{{ svgToolkit(svgAsset) }}
{{ svgToolkit(svgAsset) }}
{{ svgToolkit('example') }}
{{ svgToolkit('@root/path/to/asset/example.svg') }}
{{ svgToolkit('<svg>...</svg>') }}
```
##### Modify single color svg
```twig
{% set options = {
    'class': 'example-class',
    'color': '#ff0000',
    'title': 'Example Title'
} %}
{{ svgToolkit(svgAsset, options) }}
```

##### Modify multi color svg
```twig
{% set options = {
    'class': 'example-class',
    'replaceColor': {
        '#00ff00': '#ff0000',
        '#0000ff': '#00ff00'
    },
    'title': 'Example Title'
} %}
{{ svgToolkit(svgAsset, options) }}
```

Options are:
- **class: string** adds class(es)) to svg
`{{ svgToolkit(svgAsset, { 'class': 'example-class' }) }}`
- **color: string** changes svg color to currentColor or specific color if color is added
`{{ svgToolkit(svgAsset, { 'color': '#ff0000' }) }}`
- **replaceColor: array** changes svg specific color to currentColor or new specific color
`{{ svgToolkit(svgAsset, { 'replaceColor': { '#00ff00': '#ff0000' } }) }}`
- **title: string** adds or changes svg title
`{{ svgToolkit(svgAsset, { 'title': 'Example Title' }) }}`

**Note: if color value is set to true it will use currentColor**

**Note: color and replaceColor can't be used together**
### Filters

Filters can be used with the svgToolkit function or with an SVG Asset. Filters can be chained together.

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

#### svgReplaceColor
This filter takes an SVG (either an Asset or a string) and array of colors to replace. It returns the SVG with a modified color.

```twig
{% set colors = {
    '#00ff00': '#ff0000',
    '#0000ff': '#00ff00'
} %}
{{ svgToolkit(...)|svgReplaceColor(colors) }}
```
