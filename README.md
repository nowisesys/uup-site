## UUP-SITE - Web application and site framework

This package is a micro framework that takes a object oriented approach at building 
large scale web sites and web applications in PHP. Designed with small memory footprint, easy of use and fast execution in mind.

Controllers are placed direct under public and derives from suitable base classes to 
define their behavior (i.e. being a public web page or a secured JSON service) and is
also responsible for loading their views. The dispatcher takes care of natural routing
from request path to controller.

If a public page don't contains a class, then its treated as a simple view by the 
dispatcher that takes care of wrapping it up inside a view controller before rendering
it with decorations.

### Quick start:

For those impatient to try this out without any further reading:

```bash
composer require nowise/uup-site
./vendor/bin/uup-site.sh --bootstrap
./vendor/bin/uup-site.sh --location /myapp --setup --auth --edit --locale --guide --examples
```

Create a symbolic link to public under your htdocs and point your favorite browser
at /myapp should get you started:

![Screenshot of getting started page](images/20190613_221103.png?raw=true)

Pretty simple, right?

### Features:

Supports for multiple theme, rendering page content direct or using a dispatcher (routing) script and designed with these goals:

* Suitable for web interfaces, AJAX or services (i.e. API in JSON or SOAP)
* Responsive design with multiple themes
* Small memory footprint (~500kB per request)
* Fast request handling (less than 0.01 ms per request) 
* Support for internationalization (I18N) and localization (L10N)

### Site config:

The config/defaults.site file is looked for in the package directory (vendor/nowise/uup-site)
or in the site directory (the virtual host directory). Support for auth and edit is disabled by default, enable them in config/defaults.site.

### Setup (web sites):

The recommended solution is to use the uup-site.sh for setting up instances using
uup-site. For installation thru composer its located under vendor/bin. When installing
a virtual host, make sure that public is the only directory exposed to outside
world.

```bash
cd /usr/local/bin
wget https://nowise.se/oss/uup-site/files/uup-site.sh
```

To bootstrap and initialize a new virtual host:

```bash
mkdir -p /var/www/example.com && cd mkdir -p /var/www/example.com
uup-site.sh --bootstrap
uup-site.sh --setup
```

Review content of the public and config directories once setup has finished. Decide upon if 
page routing (mod_rewrite) should be used or not. Complete the setup by defining the public 
directory as document root (if developing a web site).

See https://nowise.se/oss/uup-site/usage/setup/ for more setup alternatives,
including bootstrap, composer, archive or manual.

### Themes:

Themes are bundled under the theme directory and consists of public content 
and template files. During render phase, a matching theme and template (*.ui)
file is looked for under the template directory.

### Infrastructure:

Menus and publish information can either be defined by having custom files in
the page directory or programmatically by redefining menu content in the page 
constructor.

See example/context for infrastructure example. See example/multi for example
on programatically defined menus. The site config file has some influence on menu 
handling too.

See example/context/menus for more advanced menus, like dynamic update page content
or defining menus relative current site root.

### Controllers, dispatch and rendering:

A page class can be rendered either direct or using routing. Using dispatch routing
is the recommended method that in addition supports views and provides pretty
URL's.

##### Rendering (direct):

```php
// 
// Assume virtual host defines include path to root directory:
// 
require_once('vendor/autoload.php');

use UUP\Site\Page\Web\StandardPage;

class IndexPage extends StandardPage
{
    // Define the printContent() member function at least.
};

$page = new IndexPage();
$page->render();
```

##### Rendering (dispatch):

```php
// 
// The dispatcher.php (router) has already setup autoloading, no need
// to explicit call render() either.
// 
class IndexPage extends StandardPage
{
    // Define the printContent() member function at least.
};
```

More examples is included in example directory in the source package. It's recommended
that your application derives your own controllers from the provided base classes to
support JSON or file API.

### Views:

While pages are complete classes, views are simple files that contains just
the main section content (HTML/PHP fragments). Using routing is the recommended 
method for render views.

Using standard page class for rendering views makes the context (i.e. menus) 
available for decoration. Views are intended for web sites, while pages are 
more targeted at web application (more control).

### Authentication:

Enable authetication by running uup-site.sh. The config/auth.inc file needs to be 
tweaked with supported authenticators.

```bash 
uup-site.sh --auth
```
You will need to enabled authentication settings inside config/defaults.site. An 
controller can enforce authentication by deriving from a secure base class. It's also
possible to programmatically enforce authentication from within a public controller
i.e. based on requested view.

### Namespace:

The default dispatcher setup will not support namespaces in controllers. If you like
to use namespace in them, the modify public/dispatch.php:

```php
$router = new Router();
$router->setNamespace("\\");                        // Use global namespace
$router->handle();
```

If your controller is i.e. located inside public/api/customer, then use the namespace
API\Customer inside that controller. If you like to use application prefix in your namespace
names, i.e MyApp\Controllers\API\Customer, then define your namespace as:

```php
$router = new Router();
$router->setNamespace("\\MyApp\\Controllers\\");    // Harmonize with application namespace
$router->handle();
```

### Locales and translation:

Enable locale (gettext) support using uup-site.sh:

```bash
uup-site.sh --locale
(i) Edit settings in makefile, then run 'make new-locale' and 'make' in current directory.
```

This will create a locale directory and install a makefile in current directory. If the 
site/application is huge, then its recommended to use multiple text domains:

    uup-site
      ├── htdocs/                       // Document root
      │     ├── dir1/
      │     ├── dir2/
     ...   ...
      ├── locale/                       // Support for locale (gettext)
      │     ├── dir1.pot
      │     ├── dir2.pot
     ...   ...

Each page can initialize its own text domain in the constructor:

```php
class MyPage extends StandardPage
{

    public function __construct()
    {
        parent::__construct(_("My page"));
        $this->locale->setTextDomain("dir2");
    }
     
}
```

### Online content editor:

Enable CMS by running uup-site.sh. You need to install javascript libraries and
tweak the edit settings inside config/defaults.site

```bash 
uup-site.sh --edit
```

For small installations, it's sufficient to define allowed content editors using
an array (done inside config/defaults.site). To support i.e. LDAP this can be done
by configure an callable (class or function):

```php
// 
// Naive example support for LDAP using hypotetical object:
// 
'edit' => array(
    'user' => function($user) use($ldap) {
        return $ldap->exist(array('uid' => $user));
    },
)
```

### Enterprise (ISP):

It's possible to use uup-site to bootstrap multiple virtual hosts for your customers
if you're hosting an ISP. The full details can be read on https://nowise.se/oss/uup/uup-site/usage/setup/enterprise

The benefits are:

* Approximate 200kB if disk space used per virtual host.
* Define a central bank of themes that can be used.
* Single place for update (just the shared directory for uup-site)

### Further information:

For more information, please visit the [project page](https://nowise.se/oss/uup-site)
