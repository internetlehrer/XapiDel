# XapiDel ILIAS Plugin

Purpose: Automatic deletion of xAPI-Data for deleted xAPI-plugin-objects and for deleted users of xAPI-plugin-objects.

This is an OpenSource project by internetlehrer GmbH, Bruchsal.

This project is licensed under the GPL-3.0-only license.

## Requirements

* ILIAS 5.4.0 - 6.999
* PHP >=7.2
* Repository Trash enabled in ILIAS

## Installation

Start at your ILIAS root directory

```bash
mkdir -p Customizing/global/plugins/Services/Cron/CronHook
cd Customizing/global/plugins/Services/Cron/CronHook
git clone https://github.com/internetlehrer/xapidel.git xapidel
```

Update, activate and config the plugin in the ILIAS Plugin Administration.

## Description

### Base plugin

First you need to install the [XapiCmi5](https://github.com/internetlehrer/XapiCmi5) plugin with version > 3. 
