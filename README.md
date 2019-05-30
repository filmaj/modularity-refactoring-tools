# Tools for module decomposition

## Extract UI modules

Allows to extract admin, storefront and WebAPI modules. Example usage

`php extract-ui-modules.php magento2ce/app/code/Magento`

## Prepare composer.json for storefront specific instance

Allows to remove packages that are not need on UI instance from `composer.json` (on admin instance we don't need storefront and WebAPI modules). Example usage

`php prepare-composer-json.php magento2ce/composer.json admin`

## Prepare admin instance

Should be able to install packages, Magento and login to admin.

1. Clone https://github.com/magento-architects/magento2ce/tree/distributed-deployment magento2ce
2. `cp -r magento2ce magento2ce-repo/` - this is needed to use composer path type repo
3. `php extract-ui-modules.php magento2ce/magento2ce/app/code/Magento`
4. `rm -rf magento2ce/app/code/Magento/*`
5. `rm -rf magento2ce/lib/internal/Magento/*`
6. `php prepare-composer-json.php magento2ce/composer.json admin-ui`
7. `cd magento2ce`
8. `composer install`
