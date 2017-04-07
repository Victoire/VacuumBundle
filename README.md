# VacuumBundle

The VacuumBundle offer an Implementation for the import of external data
to populate a new or an existing Victoire Blog.

## Installation

Install it with composer:

    php composer.phar require victoire/vacuum-bundle

Then add it to your AppKernel:

    class AppKernel extends Kernel
        {
            public function registerBundles()
            {
                $bundles = array(
                    ...
                    new Victoire\DevTools\VacuumBundle\VictoireVacuumBundle(),
                );
    
                return $bundles;
            }
        }
        
Finally update your schema:

    php bin/console doctrine:schema:update --force

#### Import available

| Source    | Format |
|-----------|--------|
| WordPress | XML    |

### Doc

1. [Basic usage](doc/basic_usage.md)
1. [How it Works](doc/how_it_works.md)
